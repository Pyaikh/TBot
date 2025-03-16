<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Color;
use App\Models\Order;
use App\Models\Shoe;
use App\Models\Size;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $botToken;
    protected $apiUrl = 'https://api.telegram.org/bot';
    protected $appUrl;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN');
        $this->appUrl = env('APP_URL', 'http://localhost:8000');
    }

    public function handleUpdate($update)
    {
        if (isset($update['message'])) {
            return $this->handleMessage($update['message']);
        } elseif (isset($update['callback_query'])) {
            return $this->handleCallbackQuery($update['callback_query']);
        }
        
        return response()->json(['status' => 'success']);
    }

    protected function handleMessage($message)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        
        // Получаем или создаем пользователя
        $user = $this->getOrCreateUser($message['chat']);
        
        // Обрабатываем текстовые команды
        if ($text === '/start') {
            $user->update(['current_state' => 'start', 'temp_data' => null]);
            return $this->sendStartMessage($chatId);
        }
        
        // Обрабатываем состояния
        switch ($user->current_state) {
            case 'waiting_address':
                return $this->handleAddressInput($user, $text);
            case 'waiting_entrance':
                return $this->handleEntranceInput($user, $text);
            case 'waiting_apartment':
                return $this->handleApartmentInput($user, $text);
            default:
                return $this->sendMessage($chatId, 'Выберите опцию из меню:');
        }
    }
    
    protected function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['from']['id'];
        $data = json_decode($callbackQuery['data'], true);
        
        // Получаем пользователя
        $user = TelegramUser::where('chat_id', $chatId)->firstOrFail();
        
        // Обрабатываем различные действия
        switch ($data['action']) {
            case 'select_brand':
                return $this->handleBrandSelection($user, $data['id']);
            case 'select_shoe':
                return $this->handleShoeSelection($user, $data['id']);
            case 'select_size':
                return $this->handleSizeSelection($user, $data['id']);
            case 'select_color':
                return $this->handleColorSelection($user, $data['id']);
            case 'select_payment':
                return $this->handlePaymentSelection($user, $data['method']);
            default:
                return $this->sendMessage($chatId, 'Неизвестная команда');
        }
    }
    
    protected function getOrCreateUser($chat)
    {
        return TelegramUser::firstOrCreate(
            ['chat_id' => $chat['id']],
            [
                'username' => $chat['username'] ?? null,
                'first_name' => $chat['first_name'] ?? null,
                'last_name' => $chat['last_name'] ?? null,
                'current_state' => 'start'
            ]
        );
    }
    
    protected function sendStartMessage($chatId)
    {
        $brands = Brand::all();
        $keyboard = ['inline_keyboard' => []];
        
        foreach ($brands as $brand) {
            $keyboard['inline_keyboard'][] = [
                [
                    'text' => $brand->name,
                    'callback_data' => json_encode(['action' => 'select_brand', 'id' => $brand->id])
                ]
            ];
        }
        
        return $this->sendMessage(
            $chatId,
            '👟 Добро пожаловать в магазин обуви! Выберите бренд:',
            $keyboard
        );
    }
    
    protected function handleBrandSelection($user, $brandId)
    {
        $brand = Brand::findOrFail($brandId);
        $shoes = $brand->shoes;
        
        // Обновляем временные данные пользователя
        $tempData = $user->temp_data ?? [];
        $tempData['brand_id'] = $brandId;
        $user->update(['temp_data' => $tempData, 'current_state' => 'selecting_shoe']);
        
        // Если у бренда есть изображение, отправляем его
        if ($brand->image) {
            $this->sendPhoto($user->chat_id, $this->getImageUrl($brand->image));
        }
        
        $keyboard = ['inline_keyboard' => []];
        
        foreach ($shoes as $shoe) {
            $keyboard['inline_keyboard'][] = [
                [
                    'text' => $shoe->name . ' - ' . $shoe->price . ' руб.',
                    'callback_data' => json_encode(['action' => 'select_shoe', 'id' => $shoe->id])
                ]
            ];
        }
        
        return $this->sendMessage(
            $user->chat_id,
            "Вы выбрали бренд: {$brand->name}\nВыберите модель:",
            $keyboard
        );
    }
    
    protected function handleShoeSelection($user, $shoeId)
    {
        $shoe = Shoe::findOrFail($shoeId);
        
        // Обновляем временные данные пользователя
        $tempData = $user->temp_data ?? [];
        $tempData['shoe_id'] = $shoeId;
        $user->update(['temp_data' => $tempData, 'current_state' => 'selecting_size']);
        
        // Отправляем фото обуви
        if ($shoe->image) {
            $this->sendPhoto($user->chat_id, $this->getImageUrl($shoe->image), $shoe->description);
        }
        
        // Получаем доступные размеры
        $sizes = $shoe->sizes;
        $keyboard = ['inline_keyboard' => []];
        
        $row = [];
        foreach ($sizes as $index => $size) {
            $row[] = [
                'text' => $size->value,
                'callback_data' => json_encode(['action' => 'select_size', 'id' => $size->id])
            ];
            
            // По 3 размера в ряд
            if (count($row) === 3 || $index === count($sizes) - 1) {
                $keyboard['inline_keyboard'][] = $row;
                $row = [];
            }
        }
        
        return $this->sendMessage(
            $user->chat_id,
            "Вы выбрали модель: {$shoe->name}\nВыберите размер:",
            $keyboard
        );
    }
    
    protected function handleSizeSelection($user, $sizeId)
    {
        $size = Size::findOrFail($sizeId);
        
        // Обновляем временные данные пользователя
        $tempData = $user->temp_data ?? [];
        $tempData['size_id'] = $sizeId;
        $user->update(['temp_data' => $tempData, 'current_state' => 'selecting_color']);
        
        // Получаем доступные цвета
        $shoe = Shoe::findOrFail($tempData['shoe_id']);
        $colors = $shoe->colors;
        
        $keyboard = ['inline_keyboard' => []];
        
        foreach ($colors as $color) {
            $keyboard['inline_keyboard'][] = [
                [
                    'text' => $color->name,
                    'callback_data' => json_encode(['action' => 'select_color', 'id' => $color->id])
                ]
            ];
        }
        
        return $this->sendMessage(
            $user->chat_id,
            "Вы выбрали размер: {$size->value}\nВыберите цвет:",
            $keyboard
        );
    }
    
    protected function handleColorSelection($user, $colorId)
    {
        $color = Color::findOrFail($colorId);
        
        // Обновляем временные данные пользователя
        $tempData = $user->temp_data ?? [];
        $tempData['color_id'] = $colorId;
        $user->update(['temp_data' => $tempData, 'current_state' => 'waiting_address']);
        
        return $this->sendMessage(
            $user->chat_id,
            "Вы выбрали цвет: {$color->name}\nТеперь введите адрес доставки:"
        );
    }
    
    protected function handleAddressInput($user, $address)
    {
        // Обновляем временные данные пользователя
        $tempData = $user->temp_data ?? [];
        $tempData['address'] = $address;
        $user->update(['temp_data' => $tempData, 'current_state' => 'waiting_entrance']);
        
        return $this->sendMessage(
            $user->chat_id,
            "Адрес доставки: {$address}\nВведите номер подъезда:"
        );
    }
    
    protected function handleEntranceInput($user, $entrance)
    {
        // Обновляем временные данные пользователя
        $tempData = $user->temp_data ?? [];
        $tempData['entrance'] = $entrance;
        $user->update(['temp_data' => $tempData, 'current_state' => 'waiting_apartment']);
        
        return $this->sendMessage(
            $user->chat_id,
            "Номер подъезда: {$entrance}\nВведите номер квартиры:"
        );
    }
    
    protected function handleApartmentInput($user, $apartment)
    {
        // Обновляем временные данные пользователя
        $tempData = $user->temp_data ?? [];
        $tempData['apartment'] = $apartment;
        $user->update(['temp_data' => $tempData, 'current_state' => 'selecting_payment']);
        
        $keyboard = ['inline_keyboard' => [
            [
                ['text' => 'Банковской картой', 'callback_data' => json_encode(['action' => 'select_payment', 'method' => 'card'])],
            ],
            [
                ['text' => 'Наличными курьеру', 'callback_data' => json_encode(['action' => 'select_payment', 'method' => 'cash'])],
            ]
        ]];
        
        return $this->sendMessage(
            $user->chat_id,
            "Номер квартиры: {$apartment}\nВыберите способ оплаты:",
            $keyboard
        );
    }
    
    protected function handlePaymentSelection($user, $paymentMethod)
    {
        $tempData = $user->temp_data;
        
        // Создаем заказ
        $order = Order::create([
            'chat_id' => $user->chat_id,
            'shoe_id' => $tempData['shoe_id'],
            'color_id' => $tempData['color_id'],
            'size_id' => $tempData['size_id'],
            'address' => $tempData['address'],
            'entrance' => $tempData['entrance'],
            'apartment' => $tempData['apartment'],
            'payment_method' => $paymentMethod,
            'status' => 'pending'
        ]);
        
        // Сбрасываем состояние пользователя
        $user->update(['current_state' => 'start', 'temp_data' => null]);
        
        $paymentText = $paymentMethod === 'card' ? 'банковской картой' : 'наличными курьеру';
        
        // Формируем подтверждение заказа
        $shoe = Shoe::find($tempData['shoe_id']);
        $color = Color::find($tempData['color_id']);
        $size = Size::find($tempData['size_id']);
        
        $message = "✅ Заказ #{$order->id} успешно оформлен!\n\n"
            . "Модель: {$shoe->name}\n"
            . "Цвет: {$color->name}\n"
            . "Размер: {$size->value}\n"
            . "Цена: {$shoe->price} руб.\n\n"
            . "Адрес доставки: {$tempData['address']}, подъезд {$tempData['entrance']}, кв. {$tempData['apartment']}\n"
            . "Способ оплаты: {$paymentText}\n\n"
            . "Спасибо за заказ! Наш менеджер свяжется с вами для подтверждения.";
        
        $this->sendMessage($user->chat_id, $message);
        
        // Предлагаем сделать новый заказ
        return $this->sendStartMessage($user->chat_id);
    }
    
    /**
     * Получить полный URL для изображения
     */
    protected function getImageUrl($imagePath)
    {
        // Если путь начинается с http, значит это уже URL
        if (strpos($imagePath, 'http') === 0) {
            return $imagePath;
        }
        
        // Определяем категорию (brands или shoes)
        $category = strtok($imagePath, '/');
        $filename = substr($imagePath, strpos($imagePath, '/') + 1);
        
        return $this->appUrl . '/images/' . $category . '/' . $filename;
    }
    
    public function sendMessage($chatId, $text, $keyboard = null)
    {
        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];
        
        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }
        
        try {
            $response = Http::post($this->apiUrl . $this->botToken . '/sendMessage', $data);
            return response()->json(['status' => 'success', 'response' => $response->json()]);
        } catch (\Exception $e) {
            Log::error('Failed to send message to Telegram: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    
    public function sendPhoto($chatId, $photo, $caption = null, $keyboard = null)
    {
        $data = [
            'chat_id' => $chatId,
            'photo' => $photo,
            'parse_mode' => 'HTML'
        ];
        
        if ($caption) {
            $data['caption'] = $caption;
        }
        
        if ($keyboard) {
            $data['reply_markup'] = json_encode($keyboard);
        }
        
        try {
            $response = Http::post($this->apiUrl . $this->botToken . '/sendPhoto', $data);
            return response()->json(['status' => 'success', 'response' => $response->json()]);
        } catch (\Exception $e) {
            Log::error('Failed to send photo to Telegram: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
} 
<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Shoe;
use App\Models\Size;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Создаем бренды
        $brands = [
            ['name' => 'Adidas', 'image' => 'brands/adidas.png'],
            ['name' => 'Nike', 'image' => 'brands/nike.png'],
            ['name' => 'Puma', 'image' => 'brands/puma.png'],
            ['name' => 'Asics', 'image' => 'brands/asics.png']
        ];
        
        foreach ($brands as $brandData) {
            Brand::create($brandData);
        }
        
        // Создаем размеры
        $sizes = ['37', '38', '39', '40', '41', '42', '43'];
        foreach ($sizes as $size) {
            Size::create(['value' => $size]);
        }
        
        // Создаем цвета
        $colors = [
            ['name' => 'Черный', 'code' => '#000000'],
            ['name' => 'Белый', 'code' => '#FFFFFF'],
            ['name' => 'Синий', 'code' => '#0000FF'],
            ['name' => 'Красный', 'code' => '#FF0000']
        ];
        
        foreach ($colors as $colorData) {
            Color::create($colorData);
        }
        
        // Получаем все бренды
        $adidasBrand = Brand::where('name', 'Adidas')->first();
        $nikeBrand = Brand::where('name', 'Nike')->first();
        $pumaBrand = Brand::where('name', 'Puma')->first();
        $asicsBrand = Brand::where('name', 'Asics')->first();
        
        // Создаем модели обуви для Adidas
        $adidasShoes = [
            [
                'brand_id' => $adidasBrand->id,
                'name' => 'Adidas Superstar',
                'description' => 'Классические кроссовки с фирменными тремя полосками',
                'image' => 'shoes/adidas-superstar.jpg',
                'price' => 8990
            ],
            [
                'brand_id' => $adidasBrand->id,
                'name' => 'Adidas Stan Smith',
                'description' => 'Культовые кроссовки с минималистичным дизайном',
                'image' => 'shoes/adidas-stan-smith.jpg',
                'price' => 7990
            ],
            [
                'brand_id' => $adidasBrand->id,
                'name' => 'Adidas Ultraboost',
                'description' => 'Беговые кроссовки с амортизирующей подошвой Boost',
                'image' => 'shoes/adidas-ultraboost.jpg',
                'price' => 13990
            ],
            [
                'brand_id' => $adidasBrand->id,
                'name' => 'Adidas NMD',
                'description' => 'Стильные городские кроссовки для повседневной носки',
                'image' => 'shoes/adidas-nmd.jpg',
                'price' => 11990
            ],
            [
                'brand_id' => $adidasBrand->id,
                'name' => 'Adidas Gazelle',
                'description' => 'Ретро-кроссовки из замши с контрастными полосками',
                'image' => 'shoes/adidas-gazelle.jpg',
                'price' => 8990
            ]
        ];
        
        // Создаем модели обуви для Nike
        $nikeShoes = [
            [
                'brand_id' => $nikeBrand->id,
                'name' => 'Nike Air Force 1',
                'description' => 'Легендарные кроссовки с узнаваемым дизайном',
                'image' => 'shoes/nike-air-force.jpg',
                'price' => 9990
            ],
            [
                'brand_id' => $nikeBrand->id,
                'name' => 'Nike Air Max',
                'description' => 'Кроссовки с видимой воздушной подушкой',
                'image' => 'shoes/nike-air-max.jpg',
                'price' => 12990
            ],
            [
                'brand_id' => $nikeBrand->id,
                'name' => 'Nike Blazer',
                'description' => 'Классические кроссовки в стиле ретро',
                'image' => 'shoes/nike-blazer.jpg',
                'price' => 8490
            ],
            [
                'brand_id' => $nikeBrand->id,
                'name' => 'Nike Dunk Low',
                'description' => 'Низкие баскетбольные кроссовки в разных цветах',
                'image' => 'shoes/nike-dunk.jpg',
                'price' => 10990
            ],
            [
                'brand_id' => $nikeBrand->id,
                'name' => 'Nike React',
                'description' => 'Беговые кроссовки с пеной React для амортизации',
                'image' => 'shoes/nike-react.jpg',
                'price' => 11990
            ]
        ];
        
        // Создаем модели обуви для Puma
        $pumaShoes = [
            [
                'brand_id' => $pumaBrand->id,
                'name' => 'Puma Suede Classic',
                'description' => 'Культовые кроссовки из замши',
                'image' => 'shoes/puma-suede.jpg',
                'price' => 7990
            ],
            [
                'brand_id' => $pumaBrand->id,
                'name' => 'Puma RS-X',
                'description' => 'Массивные кроссовки в стиле 80-х',
                'image' => 'shoes/puma-rs-x.jpg',
                'price' => 9990
            ],
            [
                'brand_id' => $pumaBrand->id,
                'name' => 'Puma Cali',
                'description' => 'Женские кроссовки на платформе',
                'image' => 'shoes/puma-cali.jpg',
                'price' => 8990
            ],
            [
                'brand_id' => $pumaBrand->id,
                'name' => 'Puma Future Rider',
                'description' => 'Яркие кроссовки в стиле 80-х',
                'image' => 'shoes/puma-future-rider.jpg',
                'price' => 8490
            ],
            [
                'brand_id' => $pumaBrand->id,
                'name' => 'Puma Smash',
                'description' => 'Классические кожаные кроссовки',
                'image' => 'shoes/puma-smash.jpg',
                'price' => 6990
            ]
        ];
        
        // Создаем модели обуви для Asics
        $asicsShoes = [
            [
                'brand_id' => $asicsBrand->id,
                'name' => 'ASICS GEL-CONTEND T2F9N 9133',
                'description' => 'Легкие и удобные кроссовки для бега',
                'image' => 'shoes/asics-gel-contend.jpg',
                'price' => 7990
            ],
            [
                'brand_id' => $asicsBrand->id,
                'name' => 'Asics-Nimbus 25',
                'description' => 'Профессиональные кроссовки для марафонцев',
                'image' => 'shoes/asics-nimbus.jpg',
                'price' => 12990
            ],
            [
                'brand_id' => $asicsBrand->id,
                'name' => 'Asics GT-2000',
                'description' => 'Кроссовки для ежедневных тренировок',
                'image' => 'shoes/asics-gt-2000.jpg',
                'price' => 9990
            ],
            [
                'brand_id' => $asicsBrand->id,
                'name' => 'Asics GEL-Kayano 29',
                'description' => 'Стабильные кроссовки с поддержкой стопы',
                'image' => 'shoes/asics-gel-kayano.jpg',
                'price' => 14990
            ],
            [
                'brand_id' => $asicsBrand->id,
                'name' => 'Asics GEL-Cumulus 24',
                'description' => 'Комфортные кроссовки для длинных дистанций',
                'image' => 'shoes/asics-gel-cumulus.jpg',
                'price' => 11990
            ]
        ];
        
        // Объединяем все модели обуви
        $allShoes = array_merge($adidasShoes, $nikeShoes, $pumaShoes, $asicsShoes);
        
        // Создаем модели и привязываем размеры и цвета
        foreach ($allShoes as $shoeData) {
            $shoe = Shoe::create($shoeData);
            
            // Привязываем размеры и цвета к каждой модели
            $shoe->sizes()->attach(Size::all());
            $shoe->colors()->attach(Color::all());
        }
    }
}

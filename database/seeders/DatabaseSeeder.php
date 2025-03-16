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
        
        // Создаем модели обуви
        $asicsBrand = Brand::where('name', 'Asics')->first();
        
        $shoes = [
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
        
        foreach ($shoes as $shoeData) {
            $shoe = Shoe::create($shoeData);
            
            // Привязываем размеры и цвета к каждой модели
            $shoe->sizes()->attach(Size::all());
            $shoe->colors()->attach(Color::all());
        }
    }
}

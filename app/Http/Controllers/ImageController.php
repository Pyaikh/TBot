<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    /**
     * Отображает изображение бренда
     */
    public function showBrandImage($filename)
    {
        $path = public_path('images/brands/' . $filename);
        
        if (!file_exists($path)) {
            return response()->json(['message' => 'Изображение не найдено'], 404);
        }
        
        $file = file_get_contents($path);
        $type = mime_content_type($path);
        
        return response($file, 200)->header('Content-Type', $type);
    }
    
    /**
     * Отображает изображение модели обуви
     */
    public function showShoeImage($filename)
    {
        $path = public_path('images/shoes/' . $filename);
        
        if (!file_exists($path)) {
            return response()->json(['message' => 'Изображение не найдено'], 404);
        }
        
        $file = file_get_contents($path);
        $type = mime_content_type($path);
        
        return response($file, 200)->header('Content-Type', $type);
    }
    
    /**
     * Загружает изображение бренда
     */
    public function uploadBrandImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'brand_id' => 'required|exists:brands,id'
        ]);
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            
            $image->move(public_path('images/brands'), $imageName);
            
            $brand = \App\Models\Brand::find($request->brand_id);
            $brand->image = 'brands/' . $imageName;
            $brand->save();
            
            return response()->json(['success' => true, 'image_path' => 'brands/' . $imageName]);
        }
        
        return response()->json(['success' => false, 'message' => 'Ошибка загрузки изображения']);
    }
    
    /**
     * Загружает изображение модели обуви
     */
    public function uploadShoeImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'shoe_id' => 'required|exists:shoes,id'
        ]);
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->extension();
            
            $image->move(public_path('images/shoes'), $imageName);
            
            $shoe = \App\Models\Shoe::find($request->shoe_id);
            $shoe->image = 'shoes/' . $imageName;
            $shoe->save();
            
            return response()->json(['success' => true, 'image_path' => 'shoes/' . $imageName]);
        }
        
        return response()->json(['success' => false, 'message' => 'Ошибка загрузки изображения']);
    }
} 
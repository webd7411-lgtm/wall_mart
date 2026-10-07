<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryApiController extends Controller
{
    public function index()
    {
        $allCats = Category::all();
        
        // Filter categories where show_on_website is truthy
        $cats = $allCats->filter(function($category) {
            return (bool) $category->show_on_website;
        })->values();

        // If no category has show_on_website enabled yet, default to showing all DB categories
        if ($cats->isEmpty()) {
            $cats = $allCats;
        }

        $categories = $cats->map(function($category) {
            $imageUrl = null;
            if (!empty($category->web_image)) {
                $imageUrl = url($category->web_image);
            }

            return [
                'id' => $category->id,
                'name' => $category->name,
                'show_on_website' => (bool)$category->show_on_website,
                'web_image' => $category->web_image ?? null,
                'web_image_url' => $imageUrl,
            ];
        });
        
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }
}

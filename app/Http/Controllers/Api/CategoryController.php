<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\CategoryCollection;
use App\Models\BusinessSetting;
use App\Models\Category;
use App\Traits\ResponseAPI;

class CategoryController extends Controller
{
    use ResponseAPI;

    public function index()
    {
        try {
            $res =  new CategoryCollection(Category::latest()->get());
            return $this->success("Successfully fetched Categories", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function featured()
    {
        try {
            $res =  new CategoryCollection(Category::where('featured', 1)->get());
            return $this->success("Successfully fetched Featured Categories", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function home()
    {
        try {
            $homepageCategories = BusinessSetting::where('type', 'category_homepage')->first();

            $homepageCategories = json_decode($homepageCategories->value);
            return $homepageCategories;
            $categories = json_decode($homepageCategories->category);
            $res =  new CategoryCollection(Category::find($categories));
            return $this->success("Successfully fetched Home Categories", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}

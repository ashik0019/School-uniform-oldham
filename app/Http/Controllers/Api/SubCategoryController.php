<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SubCategoryCollection;
use App\Models\SubCategory;
use App\Traits\ResponseAPI;

class SubCategoryController extends Controller
{
    use ResponseAPI;
    public function index($id)
    {
        try {
            $res =  new SubCategoryCollection(SubCategory::where('category_id', $id)->get());
            return $this->success("Successfully fetched Sub Categories", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}

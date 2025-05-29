<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\HomeCategoryCollection;
use App\Models\HomeCategory;
use App\Traits\ResponseAPI;

class HomeCategoryController extends Controller
{
    use ResponseAPI;
    public function index()
    {
        try {
            $res =  new HomeCategoryCollection(HomeCategory::all());
            return $this->success("Successfully fetched Home Categories", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BrandCollection;
use App\Models\Brand;
use App\Traits\ResponseAPI;

class BrandController extends Controller
{
    use ResponseAPI;

    public function index()
    {
        try {
            $res =  new BrandCollection(Brand::all());
            return $this->success("Successfully fetched brands", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function top()
    {
        try {
            $res =  new BrandCollection(Brand::where('top', 1)->get());
            return $this->success("Successfully fetched top brands", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}

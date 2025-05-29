<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\BannerCollection;
use App\Models\Banner;
use App\Traits\ResponseAPI;

class BannerController extends Controller
{
    use ResponseAPI;
    public function index()
    {
        try {
            $res =  new BannerCollection(Banner::all());
            return $this->success("Successfully fetched banner", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}

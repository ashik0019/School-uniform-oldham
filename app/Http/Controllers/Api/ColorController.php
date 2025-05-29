<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ColorCollection;
use App\Models\Color;
use App\Traits\ResponseAPI;

class ColorController extends Controller
{
    use ResponseAPI;

    public function index()
    {
        try {
            $res =  new ColorCollection(Color::all());
            return $this->success("Successfully fetched colors", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}

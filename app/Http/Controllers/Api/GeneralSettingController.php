<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\GeneralSettingCollection;
use App\Models\GeneralSetting;
use App\Traits\ResponseAPI;

class GeneralSettingController extends Controller
{
    use ResponseAPI;
    public function index()
    {
        try {
            $res =  new GeneralSettingCollection(GeneralSetting::all());
            return $this->success("Successfully fetched General Settings", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}

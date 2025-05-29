<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\SettingsCollection;
use App\Models\AppSettings;
use App\Traits\ResponseAPI;

class SettingsController extends Controller
{
    use ResponseAPI;
    public function index()
    {
        try {
            $res =  new SettingsCollection(AppSettings::all());
            return $this->success("Successfully fetched settings", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}

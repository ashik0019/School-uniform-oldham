<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\AddressCollection;
use App\Address;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AddressController extends Controller
{
    use ResponseAPI;
    public function addresses($id)
    {
        try {
            $res =  new AddressCollection(Address::where('user_id', $id)->get());
            return $this->success("Successfully fetched address", $res);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function createShippingAddress(Request $request)
    {
        $address = new Address;
        $address->user_id = $request->user_id;
        $address->address = $request->address;
        $address->country = $request->country;
        $address->city = $request->city;
        $address->postal_code = $request->postal_code;
        $address->phone = $request->phone;
        $address->save();

        return response()->json([
            'message' => 'Shipping information has been added successfully'
        ]);
    }

    public function deleteShippingAddress($id)
    {
        $address = Address::findOrFail($id);
        $address->delete();
        return response()->json([
            'message' => 'Shipping information has been added deleted'
        ]);
    }
    public function addressMakeDefault(Request $request)
    {
        $prvAddress = Address::where('user_id',$request->user_id)->get();
        foreach ($prvAddress as $key => $address) {
            $address->set_default = 0;
            $address->save();
        }
        $addressA = Address::find($request->address_id);
        $addressA->set_default = 1;
        $addressA->save();
        return response()->json([
            'message' => 'Make default successfully saved'
        ]);
    }

}

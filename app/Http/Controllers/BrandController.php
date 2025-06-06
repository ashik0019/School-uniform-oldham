<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Brand;
use App\Product;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort_search =null;
        $brands = Brand::orderBy('created_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $brands = $brands->where('name', 'like', '%'.$sort_search.'%');
        }
        $brands = $brands->paginate(15);
        return view('brands.index', compact('brands', 'sort_search'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('brands.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $brand = new Brand;
        $brand->name = $request->name;
        $brand->meta_title = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $brand->slug = str_replace(' ', '-', $request->slug);
        }
        else {
            $brand->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
        }
        if($request->hasFile('logo')){
            $brand->logo = $request->file('logo')->store('uploads/brands');
        }

        if($brand->save()){
            flash(translate('Brand has been inserted successfully'))->success();
            return redirect()->route('brands.index');
        }
        else{
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $brand = Brand::findOrFail(decrypt($id));
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $brand->name = $request->name;
        $brand->meta_title = $request->meta_title;
        $brand->meta_description = $request->meta_description;
        if ($request->slug != null) {
            $brand->slug = str_replace(' ', '-', $request->slug);
        }
        else {
            $brand->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $request->name)).'-'.Str::random(5);
        }
        if($request->hasFile('logo')){
            $brand->logo = $request->file('logo')->store('uploads/brands');
        }

        if($brand->save()){
            flash(translate('Brand has been updated successfully'))->success();
            return redirect()->route('brands.index');
        }
        else{
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);
        Product::where('brand_id', $brand->id)->delete();
        if(Brand::destroy($id)){
            if($brand->logo != null){
                //unlink($brand->logo);
            }
            flash(translate('Brand has been deleted successfully'))->success();
            return redirect()->route('brands.index');
        }
        else{
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }
    public function refIdCreate()
    {
        //return 'ddd';
        $users = User::where('user_type', 'customer')->get();
        foreach ($users as $user)
        {
            $randId = mt_rand(10000000, 99999999);
            $check = User::where('referral_code', $randId)->first();
            if(!empty($check))
            {
                $randId = mt_rand(10000000, 99999999);
            }
            $user2 =  User::find($user->id);
            $user2->referred_by = '11111111';
            $user2->referral_code = $randId;
            $user2->email_verified_at = Carbon::now();
            //'email_verified_at' => Carbon::now()
            $user2->banned = 0;
            $user2->save();
        }
        return 'done';
    }
}

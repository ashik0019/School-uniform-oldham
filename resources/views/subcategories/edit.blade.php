@extends('layouts.app')

@section('content')

<div class="col-lg-6 col-lg-offset-3">
    <div class="panel">
        <div class="panel-heading">
            <h3 class="panel-title">{{translate('Subcategory Information')}}</h3>
        </div>

        <!--Horizontal Form-->
        <!--===================================================-->
        <form class="form-horizontal" action="{{ route('subcategories.update', $subcategory->id) }}" method="POST" enctype="multipart/form-data">
            <input name="_method" type="hidden" value="PATCH">
            @csrf
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{translate('Name')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" value="{{$subcategory->name}}" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{translate('Category')}}</label>
                    <div class="col-sm-9">
                        <select name="category_id" required class="form-control demo-select2">
                            @foreach($categories as $category)
                            <option value="{{$category->id}}" <?php if ($subcategory->category_id == $category->id) echo "selected"; ?>>{{__($category->name)}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{translate('Show Universal Products')}}</label>
                    <div class="col-sm-9">
                        <select name="universal" required class="form-control demo-select2-placeholder">
                            <option value="0" {{ $subcategory->universal == 0 ? 'selected' : '' }}>No</option>
                            <option value="1" {{ $subcategory->universal == 1 ? 'selected' : '' }}>Yes</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{translate('Meta Title')}}</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="meta_title" value="{{ $subcategory->meta_title }}" placeholder="{{translate('Meta Title')}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">{{translate('Description')}}</label>
                    <div class="col-sm-9">
                        <textarea name="meta_description" rows="8" class="form-control">{{ $subcategory->meta_description }}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" for="name">{{translate('Slug')}}</label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Slug')}}" id="slug" name="slug" value="{{ $subcategory->slug }}" class="form-control">
                    </div>
                </div>
            </div>
            <div class="panel-footer text-right">
                <button class="btn btn-purple" type="submit">{{translate('Save')}}</button>
            </div>
        </form>
        <!--===================================================-->
        <!--End Horizontal Form-->

    </div>
</div>

@endsection
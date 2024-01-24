@extends('layout.mainlayout_admin',['activePage' => 'blog'])

@section('title',__('Add Blog'))
@section('content')

@php
$languages = App\Models\Language::where('status', 1)->get();
@endphp
<section class="section">
    @include('layout.breadcrumb',[
            'title' => __('Add Blog'),
            'url' => url('blog'),
            'urlTitle' => __('Add Blog'),
        ])
    <div class="section_body">
        <div class="card">
            <form action="{{ url('blog') }}" method="post" enctype="multipart/form-data" class="myform">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-3 col-md-4">
                            <label for="category_image" class="col-form-label"> {{__('Blog image')}}</label>
                            <div class="avatar-upload avatar-box avatar-box-left">
                                <div class="avatar-edit">
                                    <input type='file' name="image" id="image" title="image" accept=".png, .jpg, .jpeg" />
                                    <label for="image"></label>
                                </div>
                                <div class="avatar-preview">
                                    <div id="imagePreview">
                                    </div>
                                </div>
                            </div>
                            @error('image')
                                <div class="custom_error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-lg-9 col-md-8">
                            <div class="form-group">
                                <label class="col-form-label">{{__('Blog Title')}} *</label>
                                <input type="text" value="{{ old('title') }}" name="title" class="form-control @error('title') is-invalid @enderror">
                                @error('title')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            @foreach ($languages as $index=>$language)
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <img width="25px" height="15px" alt="image" src="{{asset('/images/upload/'.$language->image)}}">
                                        &nbsp; {{ $language->name }}
                                    </span>
                                </div>
                                <input type="text" name="title_lang[]" value="{{ old('title_lang.'.$index) }}" id="title_lang_{{ Str::slug($language->name) }}" class="form-control" placeholder="{{__('Blog Title')}}" aria-label="{{__('Blog Title')}}">
                            </div>
                            <br>
                            @endforeach
                            <div class="form-group">
                                <label class="col-form-label">{{__('Blog Reference')}}</label>
                                <input type="text" value="{{ old('blog_ref') }}" name="blog_ref" class="form-control @error('blog_ref') is-invalid @enderror">
                                @error('blog_ref')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-4">
                        <label class="col-form-label">{{__('Content')}} *</label>
                        <textarea name="desc" class="summernote form-control @error('desc') is-invalid @enderror">{{ old('desc') }}</textarea>
                        @error('desc')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    @foreach ($languages as $index=>$language)
                    <div class="form-group mt-4">
                        <label class="col-form-label">{{__('Content')}}</label> &nbsp; 
                        ( <img width="25px" height="15px" alt="image" src="{{asset('/images/upload/'.$language->image)}}"> {{ $language->name }} )
                        <textarea name="desc_lang[]" id="desc_lang_{{ Str::slug($language->name) }}" class="summernote form-control">{{ old('desc_lang.'.$index) }}</textarea>
                    </div>
                    @endforeach
                    <div class="row">
                        <div class="col-lg-6 form-group">
                            <label class="col-form-label">{{__('Status')}}</label>
                            <select name="status" class="form-control">
                                <option value="1">{{__('active')}}</option>
                                <option value="0">{{__('Deactive')}}</option>
                            </select>
                        </div>
                        <div class="col-lg-6 form-group">
                            <label class="col-form-label">{{__('Release Now ?')}}</label>
                            <select name="release_now" class="form-control">
                                <option value="1">{{__('yes')}}</option>
                                <option value="0">{{__('no')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

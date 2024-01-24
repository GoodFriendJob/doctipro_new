@extends('layout.mainlayout_admin',['activePage' => 'blog'])

@section('title',__('Edit Blog'))
@section('content')
@php
$languages = App\Models\Language::where('status', 1)->get();
$title_array = array();
$desc_array = array();
$tlist = json_decode($blog->multi_language);
foreach ($languages as $index=>$language) {
    $temp_title = $blog->title;
    $temp_desc = $blog->desc;
    foreach ($tlist as $t) {
        if (strcmp($t->lang, $language['name'])==0) {
            $temp_title = $t->title;
            $temp_desc = $t->desc;
        }
    }
    array_push($title_array, $temp_title);
    array_push($desc_array, $temp_desc);
}
@endphp
<section class="section">

    <section class="section">
        @include('layout.breadcrumb',[
            'title' => __('Edit Blog'),
            'url' => url('blog'),
            'urlTitle' => __('Edit Blog'),
        ])
        <div class="section_body">
            <div class="card">
                <form action="{{ url('blog/'.$blog->id) }}" method="post" enctype="multipart/form-data" class="myform">
                    @csrf
                    @method('PUT')
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
                                        <div id="imagePreview" style="background-image: url({{ $blog->fullImage }});">
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
                                    <label class="col-form-label">{{__('Blog Title')}}</label>
                                    <input type="text" value="{{ $blog->title }}" name="title" class="form-control @error('title') is-invalid @enderror">
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
                                    <input type="text" name="title_lang[]" value="{{ $title_array[$index] }}" id="title_lang_{{ Str::slug($language->name) }}" class="form-control" placeholder="{{__('Blog Title')}}" aria-label="{{__('Blog Title')}}">
                                </div>
                                <br>
                                @endforeach
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Blog Reference')}}</label>
                                    <input type="text" value="{{ $blog->blog_ref }}" name="blog_ref" class="form-control @error('blog_ref') is-invalid @enderror">
                                    @error('blog_ref')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-4">
                            <label class="col-form-label">{{__('Content')}}</label>
                            <textarea name="desc" class="summernote form-control @error('desc') is-invalid @enderror">{{ $blog->desc }}</textarea>
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
                            <textarea name="desc_lang[]" id="desc_lang_{{ Str::slug($language->name) }}" class="summernote form-control">{{ $desc_array[$index] }}</textarea>
                        </div>
                        @endforeach
                        <div class="row">
                            <div class="col-lg-6 form-group">
                                <label class="col-form-label">{{__('Status')}}</label>
                                <select name="status" class="form-control">
                                    <option value="1" {{ $blog->status == 1 ? 'selected' : '' }}>{{__('active')}}</option>
                                    <option value="0" {{ $blog->status == 0 ? 'selected' : '' }}>{{__('Deactive')}}</option>
                                </select>
                            </div>
                            <div class="col-lg-6 form-group">
                                <label class="col-form-label">{{__('Release Now ?')}}</label>
                                <select name="release_now" class="form-control" {{ $blog->release_now == 1 ? 'disabled' : '' }}>
                                    <option value="1" {{ $blog->release_now == 1 ? 'selected' : '' }}>{{__('yes')}}</option>
                                    <option value="0" {{ $blog->release_now == 0 ? 'selected' : '' }}>{{__('no')}}</option>
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

</section>

@endsection

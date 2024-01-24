@extends('layout.mainlayout',['activePage' => 'ourblogs'])
@php
//updated by Polaris
$tlist = json_decode($blog->multi_language);
$temp_title = $blog->title;
$temp_desc = $blog->desc;
foreach ($tlist as $t) {
    if (strcmp($t->lang, session('locale'))==0) {
        $temp_title = $t->title;
        $temp_desc = $t->desc;
    }
}
@endphp
@section('content')
<div class="mt-20 border-b border-white-light ">
    <h1 class="font-fira-sans font-semibold text-5xl text-center leading-10">{{__('Our Blogs ')}}</h1>
    <div class="p-5">
        <p class="font-fira-sans font-normal text-lg text-center leading-5 text-gray">{{__('Lorem ipsum dolor sit
            amet, consectetur adipiscing elit.')}}</p>
    </div>
</div>

{{-- Ayurveda For Prostate --}}
<div class="xsm:mx-14 xsxm:mx-5 pb-6">
    <div class="">
        <div class="pt-10">
            <h1 class="text-center font-fira-sans text-black font-medium text-4xl">{{ $temp_title }}</h1>
            <p class="py-5 font-fira-sans font-medium text-base text-center leading-5 text-blue">{{ $blog->blog_ref }}
                <span class="text-gray font-normal leading-5">• {{ Carbon\Carbon::parse($blog->created_at)->format('d M,Y') }}</span>
            </p>
        </div>
    </div>
</div>
{{-- full image --}}
<div class="border-b border-white-light mb-10">
    <div class="flex justify-center">
        <img src="{{asset($blog->fullImage)}}" class="w-[60%] object-cover bg-cover" alt="Logo">
    </div>

    <div class="xsm:mx-24 xsxm:mx-5 mb-10 py-10">
        {!! $temp_desc !!}
    </div>
</div>
@endsection
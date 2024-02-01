@if (count($doctors) > 0)
@if (isset($doctors['data']))
@php
$data = $doctors['data'];
@endphp
@else
@php
$data = $doctors;
@endphp
@endif
@foreach ($data as $doctor)

<div class="mainDiv">
    <div class="mainDiv1 p-10 h-full border border-white-light 1xl:h-[350px] xxmd:h-[300px] xmd:h-[300px] msm:h-[300px]">
        <img class="2xl:w-36 2xl:h-36 xlg:h-32 xlg:w-32 xl:h-32 xl:w-32 lg:h-32 lg:w-32 xxmd:w-32 xxmd:h-32 md:h-32 md:w-32 sm:h-32 sm:w-32 xsm:h-16 xsm:w-16 msm:h-32 msm:w-32 xxsm:h-14 xxsm:w-14 1xl:mt-8 msm:mt-2 xsm:mt-0 xxsm:mt-0 border border-primary rounded-full p-0.5 m-auto mt-12 object-cover bg-cover" src="{{ url($doctor['fullImage']) }}" alt="" />
        <h5 class="font-fira-sans font-normal text-lg leading-6 text-black text-center md:text-md pt-5">
            {{ $doctor['name'] }}</h5>
        <p class="font-normal leading-4 text-sm text-primary text-center font-fira-sans md:text-md py-2">
            {{-- <img class="p-0 m-0 m-auto inline-block object-cover w-8 h-8" src="{{ url($doctor['category']['fullImage']) }}" alt="doctor category"/> --}}
            <b> &nbsp;{{ $doctor['category']['name'] ?? 'N/A' }}</b></p>
        <p class="font-normal leading-4 text-sm text-gray text-center md:text-md"><i class="fa-solid fa-star text-yellow"></i> {{ $doctor['rate'] }} ({{ $doctor['review'] }}{{__(' reviews')}})</p>
    </div>
    <div class="hoverDoc shadow-2xl overflow-hidden">
        <div class="w-full 1xl:h-[350px] lg:h-[300px] xxmd:h-[300px] xmd:h-[300px] md:h-[300px] msm:h-[320px] xsm:h-[300px] xxsm:h-[300px] p-4">
            <div class="flex flex-col">
                <div data-id="{{ $doctor['id'] }}" class="cursor-pointer absolute flex align-center justify-center shadow-2xl bg-white-50 add-favourite pt-2 rounded-full text-primary">
                    <i class="{{ $doctor['is_fav'] == 'true' ? 'fa fa-bookmark' : 'fa-regular fa-bookmark' }}"></i>
                </div>
                <div class="flex">
                    <img class="2xl:w-24 2xl:h-24 xlg:h-24 xlg:w-24 xl:h-24 xl:w-24 lg:h-24 lg:w-24 xxmd:w-24 xxmd:h-24 md:h-18 md:w-18 sm:h-18 sm:w-18 xsm:h-18 xsm:w-18 msm:h-24 msm:w-24 xxsm:h-24 xxsm:w-24 1xl:mt-8 msm:mt-8 xsm:mt-0 xxsm:mt-0 border border-primary rounded-full p-0.5 m-auto mt-12 object-cover bg-cover" src="{{ url($doctor['fullImage']) }}" alt="" />
                    <div class="flex flex-col justify-start ml-3 1xl:block">
                        <h5 class="font-fira-sans font-normal text-xl leading-6 text-black-dark pt-5">{{$doctor['name'] }}</h5>
                        <p class="font-normal leading-4 text-sm text-primary font-fira-sans py-2">{{$doctor['category']['name'] ?? 'N/A' }}</p>
                        <p class="font-normal leading-4 text-sm text-gray"><i class="fa-solid fa-star text-yellow"></i> {{ $doctor['rate'] }} ({{ $doctor['review']}}{{__(' reviews') }})</p>
                        {{-- <h1 class="font-fira-sans font-semibold text-2xl text-primary leading-7 pt-4 xmd:pt-2 sm:pt-1 mb-5">
                            <span class="font-light">{{ $currency }}</span> {{ $doctor['appointment_fees'] }}
                        </h1> --}}
                    </div>
                </div>
            </div>
            <div class="flex flex-col">
                <div class="1xl:h-24 xl:h-20 xlg:h-24 lg:h-20 xxmd:h-22 xmd:h-24 md:h-24 sm:h-24 msm:h-20 xsm:h-24 xxsm:h-22">
                    @foreach ($doctor['hospital'] as $hospital)
                        @if($loop->iteration <= 2) 
                            <p class="font-fira-sans font-medium text-base leading-5 text-black-dark text-left pt-2"><i class="fa-solid fa-house-medical"></i> {{ $hospital['name'] }}</p>
                            <p class="font-fira-sans font-normal text-sm leading-4 text-gray text-left pt-1">
                                <span class="ml-6 mr-2"><i class="fa-solid fa-location-dot"></i></span class="ml-2">{{ $hospital['address'] ?? 'Unknown' }}
                            </p>
                        @else
                            <a href="{{ url('doctor-profile/'.$doctor['id'].'/'.Str::slug($doctor['name'])) }}">
                                <p class="font-fira-sans font-normal text-sm leading-4 text-black text-right">{{__('More...')}} </p>
                            </a>
                            @break
                        @endif
                    @endforeach
                </div>
                <div class="flex xl:flex-row xlg:flex-col lg:flex-row xsm:flex-row xxsm:flex-col">
                    <a href="{{ url('booking/'.$doctor['id'].'/'.Str::slug($doctor['name'])) }}" data-te-ripple-init data-te-ripple-color="light" class="font-fira-sans text-white bg-primary hover:bg-primary text-sm text-center py-2 px-2 xxmd:w-32">{{__('Make Appointment')}}</a>
                    <a href="{{ url('doctor-profile/'.$doctor['id'].'/'.Str::slug($doctor['name'])) }}" data-te-ripple-init data-te-ripple-color="light" class="font-fira-sans text-primary text-sm font-normal leading-4 underline mx-5 py-2">{{__('View Profile')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endif
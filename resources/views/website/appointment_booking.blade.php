@extends('layout.mainlayout',['activePage' => 'doctors'])

@section('css')
<link rel="stylesheet" href="https://unpkg.com/flowbite@1.5.5/dist/flowbite.min.css" />
<style>
    .custom-error {
        font-size: 12px;
        color: #d20f0f;
        font-weight: bold;
    }

    .activeAddress {
        border-color: #5da1ff !important;
        background-color: #f4fbfd;
    }

    .activeTimeslots {
        border-color: var(--site_color) !important;
        background-color: var(--site_color) !important;
        color: white !important;
    }

    .activeTimeslots svg {
        display: inline !important
    }   

    svg {
        display: inline !important;
    }

    .datepicker-footer {
        position: inherit !important;
    }

    #offer-code {
        --tw-ring-shadow: 0px;
    }

    .datepicker-header {
        height: 45px;
        background-color: transparent;
    }

    .datepicker-cell.focused {
        border-radius: 50%;
        border-radius: 50%;
        height: 50px;
        width: 50px;
        line-height: 50px;
    }

    .datepicker-cell.focused {
        border-radius: 50%;
        border-radius: 50%;
        height: 50px;
        width: 50px;
        line-height: 50px;
    }

    .datepicker-cell {
        height: 50px;
        width: 50px;
        line-height: 50px !important;
    }

    .datepicker-cell:hover {
        border-radius: 50%;
    }

    #datepickerId .datepicker-picker.shadow-lg {
        box-shadow: none;
    }

    .datepicker.datepicker-inline.active.block {
        display: inline !important;
    }

    #datepickerId {
        text-align: center;
    }

    .datepicker-view {
        display: flow-root !important;
    }

    .datepicker-grid {
        width: 100% !important;
    }

    .paymentDiv {
        /* width: 148px !important; */
        height: 87px !important;
        /* padding: 1rem !important; */
        cursor: pointer;
    }

    .activePayment {
        color: #2563eb !important;
        background: #f4fbfd !important;
    }

    .mapClass {
        height: 235px;
        border-radius: 12px;
    }
</style>
@endsection

@section('content')
<div class="xl:w-3/4 mx-auto">
    <div class="xsm:mx-4 xxsm:mx-5 pt-10 mb-10 ">
        <h1 class="font-fira-sans font-medium text-4xl text-left leading-10 pb-5">{{__('Appointment Booking')}}</h1>

        <div class="Appointment-detail border border-white-light">
            <div class="progress-container">
                <div class="progress" id="progress"></div>
                <div class="circle progress_active">1</div>
                <div class="circle">2</div>
                <div class="circle">3</div>
            </div>
            <form id="appointmentForm">
                @php
                    $birthdate = Carbon\Carbon::parse(auth()->user()->dob);
                    $age = $birthdate->age;
                @endphp
                <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                <input type="hidden" name="currency" value="{{ $setting->currency_code }}">
                <input type="hidden" name="company_name" value="{{ $setting->business_name }}">
                <input type="hidden" name="patient_address" value="{{ $patient->address }}">
                <input type="hidden" name="user_name" value="{{ auth()->user()->name }}">
                <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                <input type="hidden" name="phone" value="{{ auth()->user()->phone }}">
                <input type="hidden" name="payment_type" value="COD">
                <input type="hidden" name="amount" step="any" value="{{ $doctor->appointment_fees }}">
                <input type="hidden" name="payment_token">
                <input type="hidden" name="payment_status" value="0">
                <input type="hidden" name="discount_price">
                <input type="hidden" name="discount_id">
                <input type="hidden" name="age" value={{$age}}>

                <div id="step1" class="block p-5">
                    <h1 class="font-fira-sans leading-6 text-xl font-medium pb-6">{{__('Patient Details')}}</h1>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                        <div class="text-center">
                            <a target="_blank" href="{{ url('user_profile') }}" class="inline-block text-center avatar avatar-sm mr-2">
                                <img class="rounded-full" src="{{ $patient->fullImage }}" alt="User Image" width="120px" height="120px">
                            </a>
                            <br>
                            <a target="_blank" href="{{ url('user_profile') }}">{{$patient->name }} {{$patient->last_name }}</a>
                        </div>
                        <div>
                            <h3>{{__('Birthday')}} : {{$patient->dob}}</h3>
                            <h3>{{__('Patient ID')}} : {{$patient->patient_id}}</h3>
                            <h3>{{__('Email')}} : {{$patient->email}}</h3>
                            <h3>{{__('Phone')}} : ({{$patient->phone_code}}) {{$patient->phone}}</h3>
                        </div>
                        <div>
                            @if (empty($patient->address))
                                <div class="form-group">
                                    <label class="col-form-label"> {{__('Country')}}</label>
                                    <select name="country" class="w-full text-sm font-fira-sans text-gray block p-2 z-20 border border-white-light">
                                        @foreach ($countries as $country)
                                            <option value="{{$country->nicename}}" {{ old('country', 'Luxembourg') == $country->nicename ? 'selected' : '' }}>{{ $country->nicename }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Postal Code')}} <b>*</b></label>
                                    <input type="text" value="{{ old('postal_code') }}" name="postal_code" class="w-full text-sm font-fira-sans text-gray block p-2 z-20 border border-white-light" required>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('Street')}} <b>*</b></label>
                                    <input type="text" value="{{ old('street') }}" name="street" class="w-full text-sm font-fira-sans text-gray block p-2 z-20 border border-white-light" required>
                                </div>
                                <div class="form-group">
                                    <label class="col-form-label">{{__('number')}} <b>*</b></label>
                                    <input type="text" value="{{ old('number') }}" name="number" class="w-full text-sm font-fira-sans text-gray block p-2 z-20 border border-white-light" required>
                                </div>
                            @else
                                <p>{!! str_replace(', ', '<br>', $patient->address) !!}</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-2 row">
                        <label class="text-base font-normal font-fira-sans pb-2" for="note">{{__('Any Note For Doctor ??')}}</label>
                        <textarea name="note" type="text" rows="5" class="block p-2 w-full text-sm bg-white-50 border font-normal font-fira-sans leading-5 !border-white-light mt-2"></textarea>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8 mt-5">
                        <label class="text-base font-normal font-fira-sans leading-5 pb-2">{{__('Upload Patient Image & Report')}}</label>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-4 md:gap-6 lg:gap-8">
                        <div>
                            <div class="drop-zone">
                                <h1 class="drop-zone__prompt text-center text-2xl"><i class="fa-solid fa-images"></i></h1>
                                <input type="file" name="report_image[]" class="drop-zone__input">
                                <h1 class="drop-zone__prompt font-fira-sans font-normal text-center text-xs leading-3 py-2">{{__('Drop your image or')}} <span class="text-primary">{{__('Browse')}}</span></h1>
                                <p class="drop-zone__prompt font-fira-sans font-normal text-center text-xs leading-3 text-gray">{{__('Support: JPEG, PNG')}}</p>
                                {{-- <span class="drop-zone__prompt">Drop file here or click to upload</span>
                            <input type="file" name="myFile" class="drop-zone__input"> --}}
                            </div>
                        </div>
                        <div>
                            <div class="drop-zone">
                                <h1 class="drop-zone__prompt text-center text-2xl"><i class="fa-solid fa-images"></i></h1>
                                <input type="file" name="report_image[]" class="drop-zone__input">
                                <h1 class="drop-zone__prompt font-fira-sans font-normal text-center text-xs leading-3 py-2">{{__('Drop your image or')}} <span class="text-primary">{{__('Browse')}}</span></h1>
                                <p class="drop-zone__prompt font-fira-sans font-normal text-center text-xs leading-3 text-gray">{{__('Support: JPEG, PNG')}}</p>
                            </div>
                        </div>
                        <div>
                            <div class="drop-zone">
                                <h1 class="drop-zone__prompt text-center text-2xl"><i class="fa-solid fa-images"></i></h1>
                                <input type="file" name="report_image[]" class="drop-zone__input">
                                <h1 class="drop-zone__prompt font-fira-sans font-normal text-center text-xs leading-3 py-2">{{__('Drop your image or')}} <span class="text-primary">{{__('Browse')}}</span></h1>
                                <p class="drop-zone__prompt font-fira-sans font-normal text-center text-xs leading-3 text-gray">{{__('Support: JPEG, PNG')}}</p>

                            </div>
                        </div>
                    </div>
                </div>
                <div id="step2" class="hidden">
                    <div class="flex xxsm:flex-col sm:flex-row">
                        <div class="2xl:w-1/2 border-r xxsm:w-full">
                            @php
                            $date = Carbon\Carbon::now(env('timezone'));
                            @endphp
                            <input type="hidden" name="date" value="{{ $date->format('Y-m-d') }}">
                            <div id="datepickerId" onclick="dateChange()" data-date="{{ $date->format('Y-m-d') }}"></div>
                            <div class="p-5">
                                <div class="mt-2 font-normal text-xl font-fira-sans">
                                    <span class="currentDate">{{ $date->format('d M') }}</span>{{ __(' Availibility') }}
                                </div>
                                <div class="mt-2">
                                    <div class="flex flex-wrap timeSlotRow">
                                        @if (count($today_timeslots)> 0)
                                        @foreach ($today_timeslots as $today_timeslot)
                                        @if ($loop->first)
                                        <input type="hidden" name="time" value="{{ $today_timeslot['start_time'] }}">
                                        @endif
                                        <a href="javascript:void(0)" onclick="thisTime({{ $loop->iteration }})" class="time timing{{ $loop->iteration }} border border-gray text-center py-1 2xl:text-sm 2xl:px-2 sm:text-sm sm:px-2 msm:text-sm msm:px-2 leading-4 font-fira-sans font-normal xl:text-xs xl:px-1 xlg:text-xs xlg:px-1 xlg:w-28 lg:w-28 xsm:w-28 xxsm:w-28 text-black m-1 {{ $loop->first ? 'activeTimeslots' : '' }}">
                                            <svg width="12" height="13" viewBox="0 0 12 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M6 11.75C4.60761 11.75 3.27226 11.1969 2.28769 10.2123C1.30312 9.22774 0.75 7.89239 0.75 6.5C0.75 5.10761 1.30312 3.77226 2.28769 2.78769C3.27226 1.80312 4.60761 1.25 6 1.25C7.39239 1.25 8.72774 1.80312 9.71231 2.78769C10.6969 3.77226 11.25 5.10761 11.25 6.5C11.25 7.89239 10.6969 9.22774 9.71231 10.2123C8.72774 11.1969 7.39239 11.75 6 11.75ZM6 12.5C7.5913 12.5 9.11742 11.8679 10.2426 10.7426C11.3679 9.61742 12 8.0913 12 6.5C12 4.9087 11.3679 3.38258 10.2426 2.25736C9.11742 1.13214 7.5913 0.5 6 0.5C4.4087 0.5 2.88258 1.13214 1.75736 2.25736C0.632141 3.38258 0 4.9087 0 6.5C0 8.0913 0.632141 9.61742 1.75736 10.7426C2.88258 11.8679 4.4087 12.5 6 12.5V12.5Z" fill="white" />
                                                <path d="M8.22727 4.22747C8.22192 4.23264 8.21691 4.23816 8.21227 4.24397L5.60752 7.56272L4.03777 5.99222C3.93113 5.89286 3.7901 5.83876 3.64437 5.84134C3.49865 5.84391 3.35961 5.90294 3.25655 6.006C3.15349 6.10906 3.09446 6.2481 3.09188 6.39382C3.08931 6.53955 3.14341 6.68059 3.24277 6.78722L5.22727 8.77247C5.28073 8.82583 5.34439 8.86788 5.41445 8.89611C5.48452 8.92433 5.55955 8.93816 5.63507 8.93676C5.7106 8.93536 5.78507 8.91876 5.85404 8.88796C5.92301 8.85716 5.98507 8.81278 6.03652 8.75747L9.03052 5.01497C9.13246 4.90796 9.1882 4.76514 9.18568 4.61737C9.18317 4.4696 9.12259 4.32875 9.01706 4.22529C8.91152 4.12182 8.76951 4.06405 8.62171 4.06446C8.47392 4.06486 8.33223 4.12342 8.22727 4.22747Z" fill="white" />
                                            </svg>
                                            {{ $today_timeslot['start_time'] }}
                                        </a>
                                        @endforeach
                                        @else
                                        <strong class="text-red-600 text-bs text-center w-100">{{ __('At this time doctor is not availabel please change the date.') }}</strong>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <div class="2xl:w-2/5 p-8 xxsm:w-full">
                            <div>
                                <h4 class="font-fira-sans font-normal text-1xl text-left pb-5">{{ __('Choose Clinic') }}</h4>
                            </div>
                            @foreach ($doctor->hospital as $hospital)
                            @if ($loop->first)
                            @php
                            $hospital_name = $hospital->name;
                            $address = $hospital->address;
                            @endphp
                            <input type="hidden" name="hospital_id" value="{{ $hospital->id }}">
                            @endif
                            <div onclick="changeHospital({{ $loop->iteration }})" data-attribute="{{ $hospital->id }}" class="border hospitals hospital{{ $loop->iteration }} border-1 border-gray-200 p-2 cursor-pointer {{ $loop->first ? 'activeAddress' : ' mt-4' }}">
                                <div>
                                    <h5 class="font-fira-sans font-medium text-1xl text-left hospitalName{{ $loop->iteration }}">{{ $hospital->name }}</h5>
                                </div>
                                <div class="flex mt-1 items-center">
                                    <i class="fa-solid fa-location-dot mr-3"></i>
                                    <p class="font-fira-sans hospitalAddress{{ $loop->iteration }}">{{ $hospital->address }}</p>
                                </div>
                                <div class="flex displayHospital justify-between mt-2">
                                    <p class="text-gray font-fira-sans">
                                        <span class="font-fira-sans displayKm{{ $hospital->id }}"></span>{{ (' km away') }}
                                    </p>
                                    @php
                                    $url = 'https://www.google.com/maps/dir/?api=1&destination='.$hospital->lat.','.$hospital->lng;
                                    @endphp
                                    <a href="{{ $url }}" target="_blank" class="font-medium font-fira-sans">{{ __('View Details') }}</a>
                                </div>
                            </div>
                            @endforeach
                        </div> --}}
                    </div>
                </div>
            </form>
        </div>
        <div class="Appointment-detail flex justify-between mt-3 mb-3">
            <button type="button" data-te-ripple-init data-te-ripple-color="light" class="border !border-primary text-white !bg-primary text-center w-32 h-11 text-base font-normal leading-5 font-fira-sans" id="prev" disabled>{{ __('Previous') }}</button>
            <button type="button" data-te-ripple-init data-te-ripple-color="light" class="!text-white !bg-primary text-center text-base font-normal font-fira-sans w-32 h-11" id="next">{{ __('Next')}}</button>
            <a href="javascript:void(0)" data-te-ripple-init data-te-ripple-color="light" onclick="booking()" id="payment" class="!text-white !bg-primary text-center w-32 h-11 text-base font-normal font-fira-sans hidden pt-2" type="button">{{ __('Submit') }}</a>
        </div>
    </div>

    <div class="fixed top-0 left-0 z-[1055] hidden h-full w-full overflow-y-auto overflow-x-hidden outline-none" id="exampleModalCenteredScrollable" tabindex="-1" role="dialog">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 transition-all duration-300 ease-in-out w-full max-w-2xl max-h-full">
            <div class="modal-content border-none shadow-lg relative flex flex-col w-full pointer-events-auto bg-white-50 bg-clip-padding rounded-md outline-none text-current">
                <div class="modal-header flex flex-shrink-0 items-center justify-between p-4 border-b border-white-light rounded-t-md">
                    <h5 class="text-xl font-medium leading-normal text-gray-800" id="exampleModalCenteredScrollableLabel">
                        {{ __('Add Address') }}
                    </h5>
                    <button type="button" data-te-modal-dismiss data-te-ripple-init data-te-ripple-color="light" class="btn-close box-content w-4 h-4 p-1 text-black border-none rounded-none opacity-50 focus:shadow-none focus:outline-none focus:opacity-100 hover:text-black hover:opacity-75 hover:no-underline" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body relative p-4">
                    <form class="addAddress" method="post">
                        <input type="hidden" name="from" value="add_new">
                        <div class="w-auto border border-white-light" id="map" style="height: 200px">{{ __('Rajkot') }}</div>
                        <input type="hidden" name="lat" id="lat" value="{{ $setting->lat }}">
                        <input type="hidden" name="lang" id="lng" value="{{ $setting->lang }}">
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        <textarea name="address" class="mt-2 w-full text-sm font-fira-sans text-gray block p-2 z-20 border border-white-light block w-full px-3 py-1.5 text-base font-normal text-gray-700 bg-white-50 bg-clip-padding border border-solid border-gray-300 rounded transition ease-in-out m-0 focus:text-gray-700 focus:bg-white focus:border-blue-600 focus:outline-none" id="exampleFormControlTextarea1" rows="3" placeholder="Your message"></textarea>
                        <span class="invalid-div text-red"><span class="address text-sm  text-red-600 font-fira-sans"></span></span>
                    </form>
                </div>
                <div class="modal-footer flex flex-shrink-0 flex-wrap items-center justify-end p-4 border-t border-white-light rounded-b-md">
                    <button type="button" data-modal-hide="exampleModalCenteredScrollable" data-te-ripple-color="light" class="modelCloseBtn inline-block px-6 py-2.5 bg-white-50 text-gray font-medium text-xs leading-tight uppercase rounded shadow-md active:shadow-lg transition duration-150 ease-in-out">{{ __('Close') }}</button>
                    <button type="button" data-te-ripple-init data-te-ripple-color="light" onclick="addAddress()" class="inline-block px-6 py-2.5 !bg-primary text-white font-medium text-xs leading-tight uppercase rounded shadow-md hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg transition duration-150 ease-in-out ml-1">{{ __('Add Address') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"> </script>
<script src="https://unpkg.com/flowbite-datepicker@1.2.2/dist/js/datepicker-full.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
@if(App\Models\Setting::first()->paystack_public_key)
<script src="{{ url('payment/paystack.js') }}"></script>
@endif
<script src="https://checkout.flutterwave.com/v3.js"></script>
<script src="{{ url('payment/razorpay.js')}}"></script>

@if ($setting->paypal)
<script src="https://www.paypal.com/sdk/js?client-id={{ App\Models\Setting::first()->paypal_client_id }}&currency={{ App\Models\Setting::first()->currency_code }}" data-namespace="paypal_sdk"></script>
@endif
<script src="{{ url('payment/stripe.js')}}"></script>
<script src="{{ url('assets/js/appointment.js') }}"></script>

@if (App\Models\Setting::first()->map_key)
<script src="https://maps.googleapis.com/maps/api/js?key={{App\Models\Setting::first()->map_key}}&callback=initAutocomplete&libraries=places&v=weekly" async></script>
@endif
<script>
    var datepicker = '';
    const datepickerEl = document.getElementById('datepickerId');
    datepicker = new Datepicker(datepickerEl, {
        format: 'yyyy-mm-dd',
        minDate: '{{ $date->format("Y-m-d") }}',
        todayHighlight: true,
        prevArrow: '<svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.68771 1.5649e-08L8 1.37357L2.62459 7L8 12.6264L6.68771 14L8.34742e-08 7L6.68771 1.5649e-08Z" fill="#000"/></svg>',
        nextArrow: '<svg width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.31229 14L-6.00408e-08 12.6264L5.37541 7L-5.51919e-07 1.37357L1.31229 -5.73622e-08L8 7L1.31229 14Z" fill="#000"/></svg>',
    });
</script>
@endsection

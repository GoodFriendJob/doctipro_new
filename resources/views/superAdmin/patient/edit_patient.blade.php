@extends('layout.mainlayout_admin',['activePage' => 'patients'])

@section('title',__('Edit patient'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Edit Patient'),
        'url' => url('patient'),
        'urlTitle' => __('Patient'),
    ])
    <div class="section_body">
        <div class="card">
            <form action="{{ url('patient/'.$patient->id) }}" method="post" enctype="multipart/form-data" class="myform">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row mt-4">
                        <div class="col-lg-3 col-md-4">
                            <label for="patient_image" class="ul-form__label"> {{__('patient image')}}</label>
                            <div class="avatar-upload avatar-box avatar-box-left">
                                <div class="avatar-edit">
                                    <input type='file' id="image" name="image" accept=".png, .jpg, .jpeg" />
                                    <label for="image"></label>
                                </div>
                                <div class="avatar-preview">
                                    <div id="imagePreview" style="background-image: url({{ $patient->fullImage }});">
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
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label class="col-form-label">{{__('Name')}} <b>*</b></label>
                                    <input type="text" value="{{ $patient->name }}" name="name" class="form-control @error('name') is-invalid @enderror">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 form-group">
                                    <label class="col-form-label">{{__('Last Name')}}</label>
                                    <input type="text" value="{{ $patient->last_name }}" name="last_name" class="form-control @error('last_name') is-invalid @enderror">
                                    @error('last_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-5 form-group">
                                    <label class="col-form-label">{{__('Patient ID')}} <b>*</b></label>
                                    <input type="text" value="{{ $patient->patient_id }}" name="patient_id" class="form-control @error('patient_id') is-invalid @enderror">
                                    @error('patient_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-lg-4 from-group">
                                    <label for="col-form-group">{{__('Date of birth')}}</label>
                                    <input type="text" id="dob" value="{{$patient->dob}}" class="form-control datePicker @error('dob') is-invalid @enderror" name="dob">
                                    @error('dob')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-lg-3 from-group">
                                    <label for="col-from-group">{{__('Gender')}}</label>
                                    <select name="gender" class="form-control @error('gender') is-invalid @enderror">
                                        <option value="male" {{ $patient->gender == 'male' ? 'selected' : '' }}>{{__('male')}}</option>
                                        <option value="female" {{ $patient->gender == 'female' ? 'selected' : '' }}>{{__('female')}}</option>
                                        <option value="other" {{ $patient->other == 'other' ? 'selected' : '' }}>{{__('other')}}</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="form-group col-md-7">
                            <div class="row">
                                <div class="col-sm-4 form-group">
                                    <label for="phone_number" class="col-form__label"> {{__('Phone Code')}}</label><br>
                                    <select name="phone_code" class="form-control phone_code_select2">
                                        @foreach ($countries as $country)
                                            <option value="+{{$country->phonecode}}" {{ old('phone_code', $patient->phone_code) == $country->phonecode ? 'selected' : '' }}>+{{ $country->phonecode }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-8 form-group">
                                    <label for="phone_number" class="col-form-label"> {{__('Phone Number')}}</label>
                                    <input type="number" min="1" value="{{ old('phone', $patient->phone) }}" name="phone" class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-md-5">
                            <label class="col-form-label">{{__('email')}} <b>*</b></label>
                            <input type="email" readonly value="{{ $patient->email }}" name="email" class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header text-primary">
                        {{__('Address')}}
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label class="col-form-label">{{__('number')}} <b>*</b></label>
                                <input type="text" value="{{ old('number', $patient->number) }}" name="number" class="form-control @error('number') is-invalid @enderror">
                                @error('number')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label class="col-form-label">{{__('Street')}} <b>*</b></label>
                                <input type="text" value="{{ old('street', $patient->street) }}" name="street" class="form-control @error('street') is-invalid @enderror">
                                @error('street')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-4">
                                <label class="col-form-label">{{__('State')}} <b>*</b></label>
                                <input type="text" value="{{ old('state', $patient->state) }}" name="state" class="form-control @error('state') is-invalid @enderror">
                                @error('state')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-form-label">{{__('Postal Code')}} <b>*</b></label>
                                <input type="text" value="{{ old('postal_code', $patient->postal_code) }}" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror">
                                @error('postal_code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group col-md-6">
                                <label class="col-form-label"> {{__('Country')}}</label>
                                <select name="country" class="form-control">
                                    @foreach ($countries as $country)
                                        <option value="{{$country->nicename}}" {{ old('country', $patient->country) == $country->nicename ? 'selected' : '' }}>{{ $country->nicename }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-right p-2">
                    <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection

@section('js')
<script>
    $(document).ready(function () {
        $('input[name="patient_id"]').on('input', function(){
            var newValue = $(this).val()+""; // Get the new value of the input field
            if (newValue.length>12) {
                let newDate = newValue.slice(0, 4)+'-'+newValue.slice(4, 6)+'-'+newValue.slice(6, 8);
                if (isValidDateString(newDate))
                    $('#dob').val(newDate);
            }
        });
    });
    function isValidDateString(dateString) {
        let date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    }
</script>
@endsection
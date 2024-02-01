@extends('layout.mainlayout_admin',['activePage' => 'doctor'])

@section('title',__('Add Doctor'))
@section('content')

<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('Add Doctor'),
        'url' => url('doctor'),
        'urlTitle' =>  __('Doctor'),
    ])
    @if (session('status'))
        @include('superAdmin.auth.status',['status' => session('status')])
    @endif

    <div class="section_body">
        <form action="{{ url('doctor') }}" method="post" enctype="multipart/form-data" class="myform">
            @csrf
            <div class="card">
                <div class="card-header text-primary">
                    {{__('personal information')}}
                </div>
                <div class="card-body">
                    <div class="row mt-4">
                        <div class="col-lg-3 col-md-4">
                            <label for="Doctor_image" class="col-form-label"> {{__('Doctor image')}}</label>
                            <div class="avatar-upload avatar-box avatar-box-left">
                                <div class="avatar-edit">
                                    <input type='file' id="image" name="image" accept=".png, .jpg, .jpeg"  />
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
                                <label class="col-form-label">{{__('Name')}}</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror">
                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label class="col-form-label">{{__('email')}}</label>
                                <input type="email"  name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror">
                                @error('email')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="row mt-4">
                                <div class="col-lg-4 form-group">
                                    <label class="col-form-group">{{__('eHealth ID')}}</label>
                                    <input type="text" value="{{ old('pshealthid')}}" class="form-control @error('pshealthid') is-invalid @enderror" name="pshealthid">
                                    @error('pshealthid')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="col-form-group">{{__('eHealth P12 Password')}}</label>
                                    <input type="text" value="{{ old('pshealthid_p12_pass')}}" class="form-control @error('pshealthid_p12_pass') is-invalid @enderror" name="pshealthid_p12_pass">
                                    @error('pshealthid_p12_pass')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-lg-4 form-group">
                                    <label class="col-form-group">{{__('eHealth .p12 file')}}</label>
                                    <input type="file" value="{{ old('pshealthid_p12')}}" id="pshealthid_p12" name="pshealthid_p12" class="form-control form-control-sm @error('pshealthid_p12') is-invalid @enderror" onchange="displayFileName()" />
                                    @error('pshealthid_p12')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                    <span id="fileNameLabel"><i class="fa fa-file-powerpoint"></i> {{ old('pshealthid_p12' )}}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-6 form-group">
                            <label class="col-form-label">{{__('Expertise')}}</label>
                            <select name="category_id" class="select2 @error('category_id') is-invalid @enderror">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-lg-6 form-group">
                            <label class="col-form-label">{{__('Hospital')}}</label>
                            <select name="hospital_id[]" class="select2 @error('hospital_id') is-invalid @enderror" multiple>
                                @foreach ($hospitals as $hospital)
                                    <option value="{{ $hospital->id }}" {{in_array($hospital->id, old("hospital_id") ?: []) ? "selected" : ""}}>{{ $hospital->name }}</option>
                                @endforeach
                            </select>
                            @error('hospital_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-4 form-group">
                            <label for="phone_number" class="col-form-label"> {{__('Phone number')}}</label>
                            <div class="d-flex @error('phone') is-invalid @enderror">
                                <select name="phone_code" class="phone_code_select2" value="{{ old('phone_code') }}">
                                    @foreach ($countries as $country)
                                        <option value="+{{$country->phonecode}}" {{(old('phone_code') == $country->phonecode) ? 'selected':''}}>+{{ $country->phonecode }}</option>
                                    @endforeach
                                </select>
                                <input type="number" min="1" name="phone" class="form-control" value="{{old('phone')}}" >
                            </div>
                            @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="col-lg-4 form-group">
                            <label class="col-form-label">{{__('Date of birth')}}</label>
                            <input type="text" class="form-control datePicker @error('dob') is-invalid @enderror" value="{{old('dob')}}" name="dob" >
                            @error('dob')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-lg-4">
                            <label class="col-form-label">{{__('Gender')}}</label>
                            <select name="gender" class="form-control @error('gender') is-invalid @enderror" >
                                <option value="male" {{ old('gender') == "male" ? 'selected' : '' }}>{{__('male')}}</option>
                                <option value="female" {{ old('gender') == "female" ? 'selected' : '' }}>{{__('female')}}</option>
                                <option value="other" {{ old('gender') == "other" ? 'selected' : '' }}>{{__('other')}}</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-12 form-group">
                            <label class="col-form-label">{{__('Professional Bio')}}</label>
                            <textarea style="min-height:84px;" name="desc" rows="10" cols="10"  class="form-control @error('desc') is-invalid @enderror">{{old('desc')}}</textarea>
                            @error('desc')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header text-primary">
                    {{__('Education and certificate(award details)')}}
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="col-form-label">{{__('Add Education')}}</label>
                        <div class="education-info">
                            <div class="row form-row education-cont">
                                <div class="col-12 col-md-10 col-lg-11">
                                    <div class="row form-row">
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{__('Degree')}}</label>
                                                <input type="text"  required name="degree[]" value="no degree" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{__('College/Institute')}}</label>
                                                <input type="text" required name="college[]" value="university" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 col-lg-4">
                                            <div class="form-group">
                                                <label>{{__('Year of Completion')}}</label>
                                                <input type="text" maxlength="4" pattern="^[0-9]{4}$"  required name="year[]" value="2024" class="form-control">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="add-more">
                            <a href="javascript:void(0);" class="add-education"><i class="fa fa-plus-circle"></i>{{__('Add More')}}</a>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-lg-12">
                            <div class="awards-info">
                                <div class="row form-row awards-cont">
                                    <div class="col-12 col-md-5">
                                        <div class="form-group">
                                            <label>{{__('certificate')}}</label>
                                            <input type="text"  required name="certificate[]" value="no certificate" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <div class="form-group">
                                            <label>{{__('Year')}}</label>
                                            <input type="text" required  name="certificate_year[]" maxlength="4" value="2024" pattern="^[0-9]{4}$" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="add-more">
                                <a href="javascript:void(0);" class="add-award"><i class="fa fa-plus-circle"></i> {{__('Add More')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header text-primary">
                    {{__('Other information')}}
                </div>
                <div class="card-body">
                    <div class="row mt-4">
                        <div class="col-lg-3 form-group">
                            <label class="col-form-label">{{__('Timeslots(In minutes)')}}</label>
                            <select name="timeslot" class="form-control @error('timeslot') is-invalid @enderror">
                                <option value="15">15</option>
                                <option value="30">30</option>
                                <option value="45">45</option>
                                <option value="60">60</option>
                                <option value="90">90</option>
                                <option value="other">{{__('Other')}}</option>
                            </select>
                            @error('timeslot')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-lg-3 form-group custom_timeslot hide">
                            <label class="col-form-label">{{__('Add timeslot value(In minutes)')}}</label>
                            <input type="number" min="1" value="{{ old('timeslot') }}" class="form-control custom_timeslot_text @error('timeslot') is-invalid @enderror">
                            @error('timeslot')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-lg-3 form-group">
                            <label class="col-form-label">{{__('Start Time')}}</label>
                            <input class="form-control timepicker @error('start_time') is-invalid @enderror"  name="start_time" value="{{old('start_time')}}" type="time">
                            @error('start_time')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="col-lg-3 form-group">
                            <label class="col-form-label">{{__('End Time')}}</label>
                            <input class="form-control timepicker @error('end_time') is-invalid @enderror" name="end_time"  value="{{old('end_time')}}" type="time">
                            @error('end_time')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-12 form-group">
                            <label class="col-form-label">{{__('Popular ?')}}</label>
                            <select name="is_popular" class="form-control">
                                <option value="1" {{ old('is_popular') == "1" ? 'selected' : '' }}>{{__('yes')}}</option>
                                <option value="0" {{ old('is_popular') == "0" ? 'selected' : '' }}>{{__('no')}}</option>
                            </select>
                            @error('is_popular')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="text-right p-2">
                    <button type="submit" class="btn btn-primary">{{__('Submit')}}</button>
                </div>
            </div>
        </form>
    </div>
</section>

@endsection


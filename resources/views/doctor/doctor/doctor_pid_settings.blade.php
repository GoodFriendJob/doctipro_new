@extends('layout.mainlayout_admin',['activePage' => 'pid_settings'])

@section('title',__('pid settings'))
@section('content')
@php
    $is_p12_exist = false;
    if (File::exists("/opt/doctipro/" . $doctor->pshealthid_p12)) $is_p12_exist = true;
@endphp
<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('pid settings'),
    ])
    <div class="card">
        <div class="card-body pt-0">
            <div class="row">
                @if (!empty($doctor->pshealthid) && $is_p12_exist)
                <div class="col-md-3 pb-2">
                    <a class="edit-link btn btn-primary" data-toggle="modal" href="#request_pid_dlg">
                        <i class="fa fa-calendar-plus mr-1"></i> {{__('Request A Simulation')}}
                    </a>
                </div>
                @endif
                <div class="col-md-3 form-group">
                    <p><i class="fa fa-user-md"></i>&nbsp; {{__('PsHealth ID')}} : 
                        @if (!empty($doctor->pshealthid))
                        <span class="bg-success text-white py-1 px-2">{{ $doctor->pshealthid }}<span>
                        @else
                        <span class="badge bg-warning text-white">{{__('Not Set')}}<span>
                        @endif
                    </p>
                </div>
                <div class="col-md-2 form-group">
                    <p><i class="fa fa-key"></i>&nbsp; {{__('P12 Key')}} : 
                        @if ($is_p12_exist)
                        <span class="badge bg-success text-white">{{__('Set')}}<span>
                        @else
                        <span class="badge bg-warning text-white">{{__('Not Set')}}<span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4 form-group">
                    <p><i class="fa fa-unlock"></i>&nbsp; {{__('Pasword')}} : 
                        @if (!empty($doctor->pshealthid_p12_pass))
                        <span class="bg-success text-white py-1 px-2">{{ $doctor->pshealthid_p12_pass }}<span>
                        @else
                        <span class="badge bg-warning text-white">{{__('Not Set')}}<span>
                        @endif
                    </p>
                </div>
            </div>
            <div class="table-responsive mt-4">
                <table class="datatable table table-hover table-center mb-0 text-center">
                    <thead>
                        <tr>
                            <th rowspan="2" class="border">{{__('ID')}}</th>
                            <th rowspan="2" class="border"><i class="fa fa-medkit"></i><br>{{__('Medial Code')}}</th>
                            <th rowspan="2" class="border"><i class="fa fa-h-square"></i><br>{{__('Service Place')}}</th>
                            <th rowspan="2" class="border"><i class="fa fa-user"></i><br>{{__('Patient Number')}}</th>
                            <th rowspan="2" class="border"><i class="fa fa-credit-card"></i><br>{{__('Biller ID')}}</th>
                            <th rowspan="2" class="border"><i class="fa fa-code"></i><br>{{__('Act Code')}}</th>
                            <th rowspan="2" class="border"><i class="fa fa-list"></i><br>{{__('Act Number')}}</th>
                            <th colspan="3" class="border"><i class="fa fa-university"></i> {{__('Action')}}</th>
                        </tr>
                        <tr>
                            <th class="border">{{__('Simulate')}}</th>
                            <th class="border">{{__('Validate')}}</th>
                            <th class="border">{{__('Contest')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($histories as $history)
                        @php
                            $simulate = !empty($history->ccss_token);
                            $validate = !empty($history->paye);
                            $contenst = !empty($history->contestation_id);
                        @endphp
                        <tr class="{{ $simulate && $validate==true ? 'bg-light':'' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $history->medical_code }}</td>
                            <td>{{ $history->service_place }}</td>
                            <td>{{ $history->patient_number }}</td>
                            <td>{{ $history->biller_id }}</td>
                            <td>{{ $history->act_code }}</td>
                            <td>{{ $history->act_number }}</td>
                            <td>
                            @if ($simulate)
                            @else
                                <a class="btn btn-sm btn-outline-success" href="#"><nobr><i class="fa fa-eye"></i> {{__('Simulate')}}</nobr></a>
                            @endif
                            </td>
                            <td><a class="btn btn-sm btn-primary" href="#"><nobr><i class="fa fa-arrow-right"></i> {{__('Validate')}}</nobr></a></td>
                            <td><a class="btn btn-sm btn-primary" href="#"><nobr><i class="fa fa-arrow-right"></i> {{__('Contest')}}</nobr></a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="request_pid_dlg" tabindex="-1" role="dialog" aria-labelledby="request_pid_dlg" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="post" class="myform" onsubmit="return false">
                <input type="hidden" name="doctor_id" value={{$doctor->id}} />
                <input type="hidden" name="psEHealthID" value={{$doctor->pshealthid}} />
                <input type="hidden" name="pshealthid_p12" value={{$doctor->pshealthid_p12}} />
                <input type="hidden" name="pshealthid_p12_pass" value={{$doctor->pshealthid_p12_pass}} />
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-calendar-plus mr-1"></i> {{__('Call Request')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center alert-success py-1 mb-2" style="font-weight:bold;color:#0abf3a;">{{__("Please fill up the following details to enhance the patient's experience.")}}</div>
                    <div class="row mt-4">
                        <div class="col-lg-6 form-group">
                            <label for="medical_code" class="col-form-label"> {{__('Medical Code')}}</label>
                            <div class="d-flex">
                                <input type="text" value="C1" id="medical_code" name="medical_code" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="biller_id" class="col-form-label"> {{__('Biller ID')}}</label>
                            <div class="d-flex">
                                <input type="text" value="90812100" id="biller_id" name="biller_id" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-6 form-group">
                            <label for="patient_number" class="col-form-label"> {{__('Patient Number')}}</label>
                            <div class="d-flex">
                                <input type="text" value="1900123456712" id="patient_number" name="patient_number" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="service_place" class="col-form-label"> {{__('Place of Service')}}</label>
                            <div class="d-flex">
                                <input type="text" value="01" id="service_place" name="service_place" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-lg-6 form-group">
                            <label for="act_code" class="col-form-label"> {{__('Type of Consultation (Act Code)')}}</label>
                            <div class="d-flex">
                                <input type="text" value="90813319" id="act_code" name="act_code" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6 form-group">
                            <label for="act_number" class="col-form-label"> {{__('Act Number')}}</label>
                            <div class="d-flex">
                                <input type="text" value="1" id="act_number" name="act_number" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <a class="btn btn-danger text-white" href="javascript:call_pid()"><i class="fa fa-handshake"></i> {{__('Request')}}</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


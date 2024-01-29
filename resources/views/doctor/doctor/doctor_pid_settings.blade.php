@extends('layout.mainlayout_admin',['activePage' => 'pid_settings'])
@section('css')
    <link rel="stylesheet" href="{{ url('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" />
@endsection
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
                        <span class="border text-primary py-1 px-2">{{ $doctor->pshealthid }}<span>
                        @else
                        <span class="border text-warning py-1 px-2">{{__('Not Set')}}<span>
                        @endif
                    </p>
                </div>
                <div class="col-md-2 form-group">
                    <p><i class="fa fa-key"></i>&nbsp; {{__('P12 Key')}} : 
                        @if ($is_p12_exist)
                        <span class="border text-primary py-1 px-2">{{__('Set')}}<span>
                        @else
                        <span class="border text-warning py-1 px-2">{{__('Not Set')}}<span>
                        @endif
                    </p>
                </div>
                <div class="col-md-4 form-group">
                    <p><i class="fa fa-unlock"></i>&nbsp; {{__('Pasword')}} : 
                        @if (!empty($doctor->pshealthid_p12_pass))
                        <span class="border text-primary py-1 px-2">{{ $doctor->pshealthid_p12_pass }}<span>
                        @else
                        <span class="border text-warning py-1 px-2">{{__('Not Set')}}<span>
                        @endif
                    </p>
                </div>
            </div>
            <form action="{{ url('pid_settings') }}" method="get" class="form">
                @csrf
                <div class="row">
                    <div class="col-sm-3 form-group mb-0">
                        <label for="date_type" class="col-form-label"> {{__('Date Type')}}</label>
                        <div class="d-flex date-type">
                            <select class="form-control" id="date_type" name="date_type">
                                <option value="guichet_date" {{(old('date_type') == 'guichet_date') ? 'selected':''}}>Guichet</option>
                                <option value="validation_date" {{(old('date_type') == 'validation_date') ? 'selected':''}}>Validation</option>
                                <option value="contestation_date" {{(old('date_type') == 'contestation_date') ? 'selected':''}}>Contestation</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3 form-group mb-0">
                        <label for="start_date" class="col-form-label"> {{__('Start Date')}}</label>
                        <div class="d-flex input-group date" id='start_date'>
                            <input type='text' name='start_date' class="form-control" value="{{ old('start_date', $data['start_date']) }}" />
                            <button class="btn btn-outline-primary" type="button" id="start_date_btn"><i class="fa fa-calendar"></i></button>
                        </div>
                    </div>
                    <div class="col-sm-1 form-group text-center mb-0"><h1 class="mt-4"> ~ </h1></div>
                    <div class="col-sm-3 form-group mb-0">
                        <label for="end_date" class="col-form-label"> {{__('End Date')}}</label>
                        <div class="d-flex input-group date" id='end_date'>
                            <input type='text' name='end_date' class="form-control" value="{{ old('end_date', $data['end_date']) }}" />
                            <button class="btn btn-outline-primary" type="button" id="end_date_btn"><i class="fa fa-calendar"></i></button>
                        </div>
                    </div>
                    <div class="col-sm-2 form-group text-right mb-0">
                        <input type="submit" value="{{__('Search')}}" class="mt-4 btn btn-lg btn-primary">
                    </div>
                </div>
            </form>
            <div class="table-responsive mt-4">
                <table class="datatable table table-hover table-center mb-0 text-center">
                    <thead>
                        <tr>
                            <th rowspan="2" class="border">{{__('ID')}}</th>
                            <th rowspan="2" width='10' class="border"><i class="fa fa-calendar"></i><br>{{__('Date')}}</th>
                            <th rowspan="2" width='10' class="border"><i class="fa fa-medkit"></i><br>{{__('Medial Code')}}</th>
                            <th rowspan="2" width='10' class="border"><i class="fa fa-h-square"></i><br>{{__('Service Place')}}</th>
                            <th rowspan="2" width='10' class="border"><i class="fa fa-user"></i><br>{{__('Patient Number')}}</th>
                            <th rowspan="2" width='10' class="border"><i class="fa fa-credit-card"></i><br>{{__('Biller ID')}}</th>
                            <th rowspan="2" width='10' class="border"><i class="fa fa-code"></i><br>{{__('Act Code')}}</th>
                            <th rowspan="2" width='10' class="border"><i class="fa fa-list"></i><br>{{__('Act Number')}}</th>
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
                            $guichet_date = strtotime($history->guichet_date);
                            $guichet_date+= 30*60;
                            $is_valid = $guichet_date < time() ? true: false;
                        @endphp
                        <tr class="{{ $simulate ? 'bg-light':'' }}">
                            <td onclick="javascript:open_detail_dlg({{$history->pid_id}})">{{ $loop->iteration }}</td>
                            <td onclick="javascript:open_detail_dlg({{$history->pid_id}})">{{ $history->guichet_date }}</td>
                            <td onclick="javascript:open_detail_dlg({{$history->pid_id}})">{{ $history->medical_code }}</td>
                            <td onclick="javascript:open_detail_dlg({{$history->pid_id}})">{{ $history->service_place }}</td>
                            <td onclick="javascript:open_detail_dlg({{$history->pid_id}})">{{ $history->patient_number }}</td>
                            <td onclick="javascript:open_detail_dlg({{$history->pid_id}})">{{ $history->biller_id }}</td>
                            <td onclick="javascript:open_detail_dlg({{$history->pid_id}})">{{ $history->act_code }}</td>
                            <td onclick="javascript:open_detail_dlg({{$history->pid_id}})">{{ $history->act_number }}</td>
                            <td>
                                @if ($is_valid && $simulate)
                                    <i class="fa fa-circle text-success"></i>
                                @else
                                    <i class="fa fa-exclamation-triangle text-warning"></i> <span class="text-warning">{{__('Expired')}}</span><br>
                                    <a class="btn btn-sm btn-outline-primary" href="javascript:call_pid_simulate({{$history->pid_id}})">{{__('Simulate')}}</a>
                                @endif
                            </td>
                            <td>
                            @if ($is_valid && $simulate)
                                @if ($validate)
                                    <i class="fa fa-circle text-success"></i>
                                @else
                                    <a class="btn btn-sm btn-outline-success" href="javascript:call_pid_validate()">{{__('Validate')}}</a>
                                @endif
                            @endif
                            </td>
                            <td>
                            @if ($is_valid && $simulate)
                                @if ($contenst)
                                    <i class="fa fa-circle text-success"></i>
                                @else
                                    <a class="btn btn-sm btn-outline-danger" href="javascript:call_pid_contest()">{{__('Contenst')}}</a>
                                @endif
                            @endif
                            </td>
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
                <input type="hidden" name="pid" id="pid" value="0" />
                <input type="hidden" name="doctor_id" value={{$doctor->id}} />
                <input type="hidden" name="psEHealthID" id="psEHealthID" value={{$doctor->pshealthid}} />
                <input type="hidden" name="pshealthid_p12" value={{$doctor->pshealthid_p12}} />
                <input type="hidden" name="pshealthid_p12_pass" value={{$doctor->pshealthid_p12_pass}} />
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-calendar-plus mr-1"></i> {{__('Simulation Request')}}</h5>
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
                    <a class="btn btn-danger text-white" href="javascript:call_pid_simulate(0)"><i class="fa fa-handshake"></i> {{__('Request')}}</a>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="detail_pid_dlg" tabindex="-1" role="dialog" aria-labelledby="request_pid_dlg" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detail_pid_dlg_title"><i class="fa fa-calendar-plus mr-1"></i> {{__('PID Detail')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="px-1 py-2 row">
                <input type="hidden" name="detail_pid" id="detail_pid" value="" />
                <div class="col-sm-2 text-center">
                    <div class="pid-steps">
                        <p class="mb-0"><a href="javascript:show_pid_step(1)" class="pid_step_btn pid_step_btn1">{{__('Simulation')}}</a></p>
                        <p class="mb-0"><i class="fa fa-arrow-down fa-2x text-gray"></i></p>
                        <p class="mb-0"><a href="javascript:show_pid_step(2)" class="pid_step_btn pid_step_btn2">{{__('Validate')}}</a></p>
                        <p class="mb-0"><i class="fa fa-arrow-down fa-2x text-gray"></i></p>
                        <p class="mb-0"><a href="javascript:show_pid_step(3)" class="pid_step_btn pid_step_btn3">{{__('Contestation')}}</a></p>
                    </div>
                </div>
                <div class="col-sm-10">
                    <div id="pid_xml"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
    <script src="{{ url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
        $('#start_date input').datepicker({
            format: "yyyy-mm-dd",
            maxViewMode: 3,
            language: "fr",
            daysOfWeekHighlighted: "0",
            autoclose: true,
            todayHighlight: true
        });
        $('#end_date input').datepicker({
            format: "yyyy-mm-dd",
            maxViewMode: 3,
            language: "fr",
            daysOfWeekHighlighted: "0",
            autoclose: true,
            todayHighlight: true
        });
    </script>
@endsection

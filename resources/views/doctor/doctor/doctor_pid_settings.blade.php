@extends('layout.mainlayout_admin',['activePage' => 'pid_settings'])
@section('css')
<link rel="stylesheet" href="{{ url('assets/plugins/jquery-ui/jquery-ui.min.css') }}" />
<link rel="stylesheet" href="{{ url('assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" />
    <style>
        .pid-search-form {
            border: 1px dashed #f2f2f2;
            background: #eee;
            padding: 10px 30px;
        }
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
            border: 1px solid #aaa;
        }
        .ui-autocomplete {
            z-index: 99999;
        }
        td, th {
            border: 1px solid #aaa;
            text-align: left;
            padding: 6px !important;
        }
        .sorting_1 {
            min-width:40px;
        }
        ul.ui-autocomplete {
            background-color: rgb(255, 255, 255);
            box-sizing: border-box;
            left: 0px;
            position: absolute;
            /* width: 100%; */
            width: 288px !important;
            z-index: 99;
            --webkit-overflow-scrolling: touch;
            box-shadow: none;
            overflow-y: auto;
            padding: 11px 0px 52px;
            height: 100%;
        }
        .atc-box {
            background: none;
            cursor: pointer;
            padding: 3px 12px;
            transition: all 0.2s ease-in-out 0s;
            display:block;
            overflow: hidden;
        }
        .atc-img {
            border-color: white;
            border-radius: 50%;
            height: 40px;
            margin-top: 1px;
            overflow: hidden;
            position: absolute;
        }
        .atc-img img {
            max-width: 100%;
            height: auto;
            -ms-interpolation-mode: bicubic;
            display: inline-block;
            vertical-align: middle;
            width: 40px;
        }
        .atc-desc {
            padding-left: 50px;
            padding-top: 4px;
        }
        .atc-title {
            color: #333333;
            font-size: 16px;
            line-height: 24px;
            letter-spacing: 0em;
            text-transform: none;
            padding: 0px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-size-adjust: none;
        }
        .atc-categ {
            color: #707070;
            font-size: 12px;
            line-height: 16px;
            letter-spacing: 0em;
            text-transform: none;
            overflow: hidden;
            text-overflow: ellipsis;
            text-size-adjust: none;
            white-space: nowrap;
        }
        .atc-title b {
            color: #ff7467 !important;
        }
        .atc-categ span {
            margin-left:4px;
            display:inline-block;
            padding-left:3px;
            color:#184394;
        }
        @media screen and (min-width: 768px) {
            ul.ui-autocomplete {
                padding-bottom: 8px;
                height: auto;
                border-top: 1px solid #e2e2e2;
                border-bottom: 1px solid #cecece;
                max-height: 496px;
                /* min-width: 532px; */
            }
            .atc-box {
                padding: 4px 25px;
            }
        }
        .ui-widget.ui-widget-content {
            z-index:9999;
        }
    </style>
@endsection
@section('title',__('pid settings'))
@section('content')
@php
    $is_p12_exist = false;
    if (!empty($doctor->pshealthid_p12)) {
        if (File::exists(env('p12_path') .'/'. $doctor->pshealthid_p12))
            $is_p12_exist = true;
    }
    $is_pid_active = true;
    if (empty($doctor->pshealthid) || empty($doctor->biller_id) || empty($doctor->pshealthid_p12_pass) || $is_p12_exist==false)
        $is_pid_active = false;
@endphp
<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('pid settings'),
    ])
    <div class="card">
        <div class="card-body pt-0">
            @if (!$is_pid_active)
            <div class="row">
                <div class="col text-center alert-warning p-2 mb-2 text-danger">
                    @if ($is_p12_exist==false)
                    {{__(".p12 file is not existed.")}}
                    @else
                    {{__("PsHealth ID | Password | Biller ID are not set.")}}
                    @endif
                    <br>
                    {{__("Please reach out to the site administrator for assistance with this matter.")}}
                </div>
            </div>
            @endif
            <div class="row">
                @if ($is_pid_active)
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
                <div class="col-md-3 form-group">
                    <p><i class="fa fa-credit-card"></i>&nbsp; {{__('Biller ID')}} : 
                        @if (!empty($doctor->biller_id))
                        <span class="border text-primary py-1 px-2">{{ $doctor->biller_id }}<span>
                        @else
                        <span class="border text-warning py-1 px-2">{{__('Not Set')}}<span>
                        @endif
                    </p>
                </div>
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-6 form-group">
                            <p><i class="fa fa-key"></i>&nbsp; {{__('P12 Key')}} : 
                                @if ($is_p12_exist)
                                <span class="border text-primary py-1 px-2">{{__('Set')}}<span>
                                @else
                                <span class="border text-warning py-1 px-2">{{__('Not Set')}}<span>
                                @endif
                            </p>
                        </div>
                        <div class="col-6 form-group">
                            <p><i class="fa fa-unlock"></i>&nbsp; {{__('Pasword')}} : 
                                @if (!empty($doctor->pshealthid_p12_pass))
                                <span class="border text-primary py-1 px-2">{{__('Set')}}<span>
                                @else
                                <span class="border text-warning py-1 px-2">{{__('Not Set')}}<span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ url('pid_settings') }}" method="get" class="form pid-search-form">
                {{-- @csrf --}}
                <div class="row">
                    <div class="col-sm-3 form-group mb-0">
                        <label class="col-form-label"> {{__('Date Type')}}</label>
                        <div class="d-flex date-type">
                            <select class="form-control" id="date_type" name="date_type">
                                <option value="guichet_date" {{(!isset($data['data_type']) || $data['data_type'] == 'guichet_date') ? 'selected':''}}>Guichet</option>
                                <option value="validation_date" {{(isset($data['data_type']) && $data['data_type'] == 'validation_date') ? 'selected':''}}>Validation</option>
                                <option value="contestation_date" {{(isset($data['data_type']) && $data['data_type'] == 'contestation_date') ? 'selected':''}}>Contestation</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-3 form-group mb-0">
                        <label class="col-form-label"> {{__('Start Date')}}</label>
                        <div class="d-flex input-group date" id='start_date'>
                            <input type='text' name='start_date' class="form-control" value="{{ isset($data['start_date']) ? $data['start_date']:'' }}" />
                            <button class="btn btn-outline-primary" type="button" id="start_date_btn"><i class="fa fa-calendar"></i></button>
                        </div>
                    </div>
                    <div class="col-sm-3 form-group mb-0">
                        <label class="col-form-label"> {{__('End Date')}}</label>
                        <div class="d-flex input-group date" id='end_date'>
                            <input type='text' name='end_date' class="form-control" value="{{ isset($data['end_date']) ? $data['end_date']:'' }}" />
                            <button class="btn btn-outline-primary" type="button" id="end_date_btn"><i class="fa fa-calendar"></i></button>
                        </div>
                    </div>
                    <div class="col-sm-3 form-group text-right mb-0">
                        <input type="submit" value="{{__('Search')}}" class="mt-4 btn btn-lg btn-primary">
                    </div>
                </div>
                <div class="row pt-2">
                    <div class="col-sm-4 form-group mb-0">
                        <div class="form-group">
                            <label class="col-form-label">{{__('Hide Invalidated')}}</label>
                            <label class="cursor-pointer">
                                <input type="checkbox" id="hide_invalidate" value="1" name="hide_invalidate" {{ $hide_invalidate == 1 ? 'checked' : "" }} class="custom-switch-input">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-sm-4 form-group mb-0">
                        <div class="form-group">
                            <label class="col-form-label">{{__('Hide Expired')}}</label>
                            <label class="cursor-pointer">
                                <input type="checkbox" id="hide_expired" value="1" name="hide_expired" {{ $hide_expired == 1 ? 'checked' : "" }} class="custom-switch-input">
                                <span class="custom-switch-indicator"></span>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
            <div class="table-responsive mt-4">
                <table class="datatable table table-hover table-center mb-0 text-center">
                    <thead>
                        <tr>
                            <th rowspan="2" width='40'>{{__('ID')}}</th>
                            <th rowspan="2" width='50'><i class="fa fa-calendar"></i><br>{{__('Request Date')}}</th>
                            <th rowspan="2" width='150'><i class="fa fa-user"></i><br>{{__('Patient')}}</th>
                            <th rowspan="2"><i class="fa fa-medkit"></i><br>{{__('Medial Code')}}</th>
                            <th rowspan="2"><i class="fa fa-h-square"></i><br>{{__('Service Place')}}</th>
                            <th rowspan="2"><i class="fa fa-code"></i><br>{{__('Act Number')}}</th>
                            <th rowspan="2"><i class="fa fa-university"></i><br>{{__('Part Statutaire')}}</th>
                            <th rowspan="2"><i class="fa fa-clipboard"></i><br>{{__('Recouv rement')}}</th>
                            <th rowspan="2"><i class="fa fa-shopping-cart"></i><br>{{__('Paye')}}</th>
                            <th colspan="4"><i class="fa fa-university"></i> {{__('Action')}}</th>
                        </tr>
                        <tr>
                            <th class="border"> <i class="fa fa-file-pdf"></i> </th>
                            <th class="border text-sm">{{__('Simulate')}}</th>
                            <th class="border text-sm">{{__('Validate')}}</th>
                            <th class="border text-sm">{{__('Contest')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($histories as $history)
                        <tr class="{{ $history->is_validation ? 'bg-light':'' }} {{ $history->is_valid ? '':'text-warning' }}">
                            <td data-sort="{{ $loop->iteration }}">
                                <input type="checkbox" style="height:20px;" class="float-left form-control-sm pid_id_check cursor-pointer" id="pid_id_{{ $history->pid_id }}" value="{{ $history->pid_id }}" /> &nbsp;
                                <span class="pt-2">{{ $loop->iteration }}</span>
                            </td>
                            <td onclick="javascript:open_view_dlg({{$history->pid_id}}, {{$history->is_validation}})">{{ $history->date_modified }}</td>
                            <td onclick="javascript:open_view_dlg({{$history->pid_id}}, {{$history->is_validation}})">
                                <img src="{{$history->patient_img}}" style="display:inline-block; width:32px; margin-bottom:3px;" />
                                {{$history->patient_name}}<br>
                                <i class="fa fa-id-card"></i> {{$history->patient_id}}<br>
                                {{-- <nobr><i class="fa fa-envelope"></i> {{$history->patient_email}}</nobr> --}}
                                
                            </td>
                            <td onclick="javascript:open_view_dlg({{$history->pid_id}}, {{$history->is_validation}})">{{ $history->medical_code }}</td>
                            <td onclick="javascript:open_view_dlg({{$history->pid_id}}, {{$history->is_validation}})">{{ $history->service_place }}</td>
                            <td onclick="javascript:open_view_dlg({{$history->pid_id}}, {{$history->is_validation}})">{{ $history->act_number }}</td>
                            <td onclick="javascript:open_view_dlg({{$history->pid_id}}, {{$history->is_validation}})">{{ $history->part_statutaire ? '€ '.$history->part_statutaire: '-' }}</td>
                            <td onclick="javascript:open_view_dlg({{$history->pid_id}}, {{$history->is_validation}})">{{ $history->recouvrement ? '€ '.$history->recouvrement: '-' }}</td>
                            <td onclick="javascript:open_view_dlg({{$history->pid_id}}, {{$history->is_validation}})"><nobr class="text-danger">{{ $history->paye ? '€ '.$history->paye: '-' }}</nobr></td>
                            <td class="text-center">
                                @if ($history->is_validation)
                                    <a class="text-primary" target="_blank" href="{{ url('pid_print/'.$history->pid_id) }}"><i class="fa fa-print"></i></a>
                                    <br>
                                    <a class="text-danger" href="{{ url('pid_pdf_download/'.$history->pid_id) }}"><i class="fa fa-file-pdf"></i></a>
                                @endif
                            </td>
                            <td>
                                @if ($history->is_valid)
                                    <i class="fa fa-circle text-success"></i>
                                @elseif($history->is_expired)
                                    <i class="fa fa-bell text-warning"></i> <span class="text-warning">{{__('Expired')}}</span><br>
                                    <a class="btn btn-sm btn-outline-primary" href="javascript:call_pid_simulate({{$history->pid_id}})">{{__('Simulate')}}</a>
                                @else
                                    <i class="fa fa-exclamation-triangle text-danger"></i><span class="text-danger">{{__('Auth Fail')}}</span><br>
                                    <a class="btn btn-sm btn-outline-primary" href="javascript:call_pid_simulate({{$history->pid_id}})">{{__('Simulate')}}</a>
                                @endif
                            </td>
                            <td>
                            @if ($history->is_valid)
                                @if ($history->is_validation)
                                    <i class="fa fa-circle text-success"></i>
                                @elseif ($history->is_contestation)
                                    <b> - </b>
                                @else
                                    <a class="btn btn-sm btn-outline-success" href="javascript:call_pid_validate({{$history->pid_id}})">{{__('Validate')}}</a>
                                @endif
                            @endif
                            </td>
                            <td>
                            @if ($history->is_valid)
                                @if ($history->is_contestation)
                                    <i class="fa fa-circle text-success"></i>
                                @elseif ($history->is_validation)
                                    <b> - </b>
                                @else
                                    <a class="btn btn-sm btn-outline-danger" href="javascript:call_pid_contest({{$history->pid_id}})">{{__('Contenst')}}</a>
                                @endif
                            @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <a href="javascript:export_excel()" class="btn btn-primary"><i class="fa fa-file-excel"></i>&nbsp; {{__('Export Excel')}}</a>
            </div>
        </div>
    </div>
</section>
    <div class="modal fade" id="request_pid_dlg" tabindex="-1" role="dialog" aria-labelledby="request_pid_dlg" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <form method="post" class="myform" onsubmit="return false">
                    <input type="hidden" name="pid" id="pid" value="0" />
                    <input type="hidden" name="doctor_id" value={{$doctor->id}} />
                    <input type="hidden" name="psEHealthID" id="psEHealthID" value={{$doctor->pshealthid}} />
                    <input type="hidden" name="pshealthid_p12" value={{$doctor->pshealthid_p12}} />
                    <input type="hidden" name="pshealthid_p12_pass" value={{$doctor->pshealthid_p12_pass}} />
                    <input type="hidden" name="biller_id" value={{$doctor->biller_id}} />
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle"><i class="fa fa-calendar-plus mr-1"></i> {{__('New Simulation')}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row mt-4">
                            <div class="col-lg-8 form-group">
                                <label class="col-form-label"> {{__('Select Patient')}} <b>*</b></label>
                                <div class="input-group mb-4">
                                    <input type="text" value="" id="search_patient" class="form-control" />
                                    <div class="input-group-append">
                                        <span class="input-group-text" id="search-icon">
                                            <i class="fa fa-search"></i>
                                        </span>
                                    </div>
                                    <input type="hidden" value="{{$doctor->patient_id}}" id="patient_id" name="patient_id" />
                                </div>
                            </div>
                            <div class="col-lg-4 form-group text-center" style="margin-top:-30px">
                                <img src="" id="sel_patient_img" class="patient_avatar" style="width:90px;" />
                                <h5 class="patient_name"><span id="sel_patient_name"></span></h5>
                                <p class="m-0"><span id="sel_patient_id"></span></p>
                                <p class="m-0"><span id="sel_patient_email"></span></p>
                            </div>
                        </div>
                        <div class="text-center alert-success py-1 mb-2" style="font-weight:bold;color:#0abf3a;">{{__("Please fill up the following details to enhance the patient's experience.")}}</div>
                        <div class="row mt-4">
                            <div class="col-lg-4 form-group">
                                <label class="col-form-label"> {{__('Place of Service')}} <b>*</b></label>
                                <div class="d-flex">
                                    <input type="text" value="01" id="service_place" name="service_place" class="form-control">
                                </div>
                            </div>
                            <div class="col-lg-4 form-group">
                                <label class="col-form-label"> {{__('Medical Code')}} <b>*</b></label>
                                <select id="medical_code" name="medical_code" class="form-control">
                                    <option value="C1">C1</option>
                                    <option value="C41">C41</option>
                                    <option value="C43">C43</option>
                                </select>
                            </div>
                            <div class="col-lg-4 form-group">
                                <label class="col-form-label"> {{__('Act Number')}} <b>*</b></label>
                                <div class="d-flex">
                                    <input type="number" min="1" value="1" id="act_number" name="act_number" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                        <a class="btn btn-danger text-white" href="javascript:call_pid_simulate(0)"><i class="fa fa-handshake"></i> {{__('Simulation')}}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detail_pid_dlg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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

    <div class="modal fade" id="view_pid_dlg" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa fa-calendar-plus mr-1"></i> {{__('PID')}} : <span id="pid_view_title"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row-fluid" id="view_pid_body">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{__('Close')}}</button>
                    <a class="btn btn-outline-danger" href="javascript:open_detail_dlg()"><i class="fa fa-binoculars"></i> {{__('Debug')}}</a>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('js')
    <script src="{{ url('assets/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script src="{{ url('assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js')}}"></script>
    <script>
    $(document).ready(function () {
        $("#search_patient").autocomplete({
            minLength: 1,
            source: function( request, response ) {
                $.ajax( {
                    headers:
                    {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: "POST",
                    data:{
                        search_txt: request.term
                    },
                    url: base_url + '/patient_search',
                    dataType: "json",
                    success: function( data ) {
                        response( data.data );
                    }
                });
            },
            focus: function( event, ui ) {
            //   $( "#search_patient" ).val( ui.item.name );
            //   $( "#sel_patient_img" ).attr('src', ui.item.img );
            //   $( "#sel_patient_name" ).html( ui.item.name );
            //   $( "#sel_patient_id" ).html( ui.item.name );
            return false;
            },
            select: function( event, ui ) {
                $( "#search_patient" ).val( ui.item.name );
                $( "#sel_patient_img" ).attr('src', base_url+'/images/upload/'+ui.item.img );
                $( "#sel_patient_name" ).html( ui.item.name );
                $( "#sel_patient_email" ).html( ui.item.email );
                $( "#sel_patient_id" ).html( ui.item.patient_id );
                $( "#patient_id" ).val( ui.item.patient_id );
                return false;
            }
        })
        .autocomplete( "instance" )._renderItem = function( ul, item ) {
            var searchInput = $( "#search_patient" ).val();
            var emphasizedText = item.name.replace(new RegExp(searchInput, 'gi'), function(match) {
                return '<b>' + match + '</b>';
            });
            return $( "<li>" )
            .append( "<div class='atc-box'><div class='atc-img'><img src='"+base_url+'/images/upload/'+item.img+"'/></div><div class='atc-desc'><div class='atc-title'>" + emphasizedText + "</div><div class='atc-categ'>" + item.patient_id + "</div></div></div>" )
            .appendTo( ul );
            
        };
        $('#start_date input').datepicker({
            format: "yyyy-mm-dd",
            maxViewMode: 3,
            language: "fr",
            daysOfWeekHighlighted: "0",
            autoclose: true,
            todayHighlight: true
        });
        // handle input group button click
        $('#start_date_btn').click(function (e) {
            e.preventDefault();
            $('#start_date input').datepicker('showWidget');
        });
        $('#end_date input').datepicker({
            format: "yyyy-mm-dd",
            maxViewMode: 3,
            language: "fr",
            daysOfWeekHighlighted: "0",
            autoclose: true,
            icons: {
				time: "fa fa-clock-o",
				date: "fa fa-calendar",
				up: "fa fa-arrow-up",
				down: "fa fa-arrow-down"
			},
            todayHighlight: true
        });
        $('#end_date_btn').click(function (e) {
            e.preventDefault();
            $('#end_date input').datepicker('showWidget');
        });
        $('#status').click(function (e) {
            $('#end_date input').datepicker('showWidget');
        });
        $('#hide_invalidate').click(function (e) {
            $('.pid-search-form').trigger('submit');
        });
        $('#hide_expired').click(function (e) {
            $('.pid-search-form').trigger('submit');
        });
    });
    </script>
@endsection

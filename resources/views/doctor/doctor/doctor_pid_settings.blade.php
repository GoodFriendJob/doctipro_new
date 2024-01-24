@extends('layout.mainlayout_admin',['activePage' => 'pid_settings'])

@section('title',__('pid settings'))
@section('content')
<section class="section">
    @include('layout.breadcrumb',[
        'title' => __('pid settings'),
    ])
    <div class="card">
        <div class="card-body pt-0">
            <div class="row text-right">
                <div class="col">
                <a class="edit-link btn btn-danger float-right" data-toggle="modal" href="#request_pid_dlg">
                    <i class="fa fa-calendar-plus mr-1"></i> {{__('Call Request')}}
                </a>
                </div>
            </div>
            <div class="table-responsive mt-4">
                <table class="datatable table table-hover table-center mb-0 text-center">
                    <thead>
                        <tr>
                            <th rowspan="2" class="border">{{__('ID')}}</th>
                            <th rowspan="2" class="border"><i class="fa fa-key"></i> {{__('pshealthid')}}</th>
                            <th colspan="2" class="border"><i class="fa fa-id-card"></i> {{__('Auth Action')}}</th>
                            <th colspan="3" class="border"><i class="fa fa-arrow-right"></i> {{__('Sync Exchange')}}</th>
                            <th colspan="3" class="border"><i class="fa fa-university"></i> {{__('CNS Business Call Validate')}}</th>
                        </tr>
                        <tr>
                            <th class="border">{{__('authn_ccss_date_added')}}</th>
                            <th class="border">{{__('ccss_token')}}</th>
                            <th class="border">{{__('id_memoire_honoraire')}}</th>
                            <th class="border">{{__('id_externe_prestation')}}</th>
                            <th class="border">{{__('id_response_contestation')}}</th>
                            <th class="border">{{__('part_statutaire')}}</th>
                            <th class="border">{{__('recouvrement')}}</th>
                            <th class="border">{{__('paye')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($histories as $history)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $history->pshealthid }}</td>
                            <td>{{ $history->authn_ccss_date_added }}</td>
                            <td>{{ $history->ccss_token }}</td>
                            <td>{{ $history->id_memoire_honoraire }}</td>
                            <td>{{ $history->id_externe_prestation }}</td>
                            <td>{{ $history->id_response_contestation }}</td>
                            <td>{{ $history->part_statutaire }}</td>
                            <td>{{ $history->recouvrement }}</td>
                            <td>{{ $history->paye }}</td>
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
                            <label for="service_date" class="col-form-label"> {{__('Service Start Date')}}</label>
                            <div class="d-flex">
                                <input type="text" value="{{ date('Y-m-d') }}" id="service_date" name="service_date" class="form-control">
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


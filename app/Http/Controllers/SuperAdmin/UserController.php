<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Country;
use App\Models\Doctor;
use App\Models\Offer;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('patient_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        if (auth()->user()->hasRole('doctor')) {
            $doctor = Doctor::where('user_id', auth()->user()->id)->first();
            // $users = User::doesntHave('roles')->where('doctor_id', $doctor->id)->whereNotNull('patient_id')->get();
            $users = User::where('doctor_id', $doctor->id)->whereNotNull('patient_id')->get();
        } else {
            // $users = User::doesntHave('roles')->whereNotNull('patient_id')->get();
            $users = User::whereNotNull('patient_id')->get();
        }
        return view('superAdmin.patient.patient', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('patient_add'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = Country::get();
        return view('superAdmin.patient.create_patient', compact('countries'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'bail|required',
                // 'last_name' => 'bail|required',
                'email' => 'bail|required|email|unique:users',
                'patient_id' => 'bail|required|unique:users',
                'dob' => 'bail|required',
                'gender' => 'bail|required',
                // 'phone' => 'bail|required|digits_between:6,12',
                'image' => 'bail|mimes:jpeg,png,jpg|max:1000',
                // 'address' => 'bail|required',
                // 'lat' => 'bail|required',
                // 'lng' => 'bail|required'
                'number' => 'bail|required',
                'street' => 'bail|required',
                'state' => 'bail|required',
                'postal_code' => 'bail|required',
                'country' => 'bail|required'
            ],
            ['image.max' => 'The Image May Not Be Greater Than 1 MegaBytes.']
        );
        $data = $request->all();
        $data['address'] = $data['number']. ', '.$data['street']. ', '.$data['state']. ', '.$data['country']. ', '.$data['postal_code'];

        if (auth()->user()->hasRole('doctor')) {
            $data['doctor_id'] = Doctor::where('user_id', auth()->user()->id)->first()->id;
        }
        $data['password'] = Hash::make(123456);
        $data['status'] = 1;
        if ($request->hasFile('image')) {
            $request->validate(
                ['image' => 'max:1000'],
                [
                    'image.max' => 'The Image May Not Be Greater Than 1 MegaBytes.',
                ]
            );
            $data['image'] = (new CustomController)->imageUpload($request->image);

        } else {
            $data['image'] = 'default_doctor.jpg';
        }
        $data['verify'] = 1;
        User::create($data);
        return redirect('patient')->withStatus(__('patient created successfully..!!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        (new CustomController)->cancel_max_order();
        $user = User::find($id);
        $appointments = Appointment::where('user_id', $id)->get();
        $currency = Setting::first()->currency_symbol;
        return view('superAdmin.patient.show_patient', compact('user', 'appointments', 'currency'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('patient_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = Country::get();
        $patient = User::find($id);
        $sub_address = explode(", ", $patient->address);
        if (sizeof($sub_address)>4) {
            $patient['number'] = $sub_address[0];
            $patient['street'] = $sub_address[1];
            $patient['state'] = $sub_address[2];
            $patient['country'] = $sub_address[3];
            $patient['postal_code'] = $sub_address[4];
        } else {
            $patient['number'] = '';
            $patient['street'] = '';
            $patient['state'] = '';
            $patient['country'] = '';
            $patient['postal_code'] = '';
        }
        return view('superAdmin.patient.edit_patient', compact('countries', 'patient'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate(
            [
                'name' => 'bail|required',
                'dob' => 'bail|required',
                'patient_id' => 'bail|required',
                'gender' => 'bail|required',
                'phone' => 'bail|required|digits_between:6,12',
                'image' => 'bail|mimes:jpeg,png,jpg|max:1000',
                // 'address' => 'bail|required',
                // 'lat' => 'bail|required',
                // 'lng' => 'bail|required',
                'country' => 'bail|required',
                'street' => 'bail|required',
                'postal_code' => 'bail|required',
                'number' => 'bail|required'
            ],
            ['image.max' => 'The Image May Not Be Greater Than 1 MegaBytes.']
        );
        $data = $request->all();
        $data['address'] = $data['number']. ', '.$data['street']. ', '.$data['state']. ', '.$data['country']. ', '.$data['postal_code'];
        $user = User::find($id);
        if ($request->hasFile('image')) {
            (new CustomController)->deleteFile($user->image);
            $data['image'] = (new CustomController)->imageUpload($request->image);
        }
        $data['verify'] = 1;
        $user->update($data);
        return redirect('patient')->withStatus(__('patient updated successfully..!!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $offers = Offer::all();
        foreach ($offers as $value) {
            $user_id = explode(',', $value['user_id']);
            if (($key = array_search($id, $user_id)) !== false) {
                return response(['success' => false, 'data' => 'This user connected with Offer first delete Offer']);
            }
        }
        $user = User::find($id);
        (new CustomController)->deleteFile($user->image);
        $user->delete();
        return response(['success' => true]);
    }

    public function change_status(Request $reqeust)
    {
        $User = User::find($reqeust->id);
        $data['status'] = $User->status == 1 ? 0 : 1;
        $User->update($data);
        return response(['success' => true]);
    }
}

<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Country;
use App\Models\Doctor;
use App\Models\DoctorPID;
use App\Models\DoctorSubscription;
use App\Models\Expertise;
use App\Models\Hospital;
use App\Models\Offer;
use App\Models\Setting;
use App\Models\Settle;
use App\Models\Subscription;
use App\Models\Treatments;
use App\Models\User;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Gate;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;


class DoctorPIDController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_if(Gate::denies('doctor_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $doctors = Doctor::with('expertise')->orderBy('id','desc')->get();
        foreach ($doctors as $doctor) {
            $doctor->user = User::find($doctor->user_id);
        }
        return view('superAdmin.doctor.doctor',compact('doctors'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_if(Gate::denies('doctor_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $countries = Country::get();
        $categories = Category::whereStatus(1)->get();
        $hospitals = Hospital::whereStatus(1)->get();
        return view('superAdmin.doctor.create_doctor',compact('countries', 'hospitals','categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = $request->validate([
            'name' => 'bail|required|unique:doctor',
            'email' => 'bail|required|email|unique:users',
            'biller_id' => 'bail|required|unique:doctor',
            'pshealthid' => 'bail|required|unique:doctor',
            'pshealthid_p12' => 'required',
            'pshealthid_p12_pass' => 'bail|required',
            'phone' => 'bail|required|digits_between:6,12',
            'category_id' => 'bail|required',
            // 'dob' => 'bail|required',
            // 'gender' => 'bail|required',
            // 'timeslot' => 'bail|required',
            // 'start_time' => 'bail|required',
            // 'end_time' => 'bail|required|after:start_time',
            // 'hospital_id' => 'bail|required',
            // 'custom_timeslot' => 'bail|required_if:timeslot,other',
            
            
            // 'treatment_id' => 'bail|required',
            // 'expertise_id' => 'bail|required',
            // 'desc' => 'required',
            // 'appointment_fees' => 'required|numeric',
            // 'experience' => 'bail|required|numeric',
            // 'commission_amount' => 'bail|required_if:based_on,commission'
        ]);

        if($request->hasFile('pshealthid_p12')==false) {
            $validator->errors()->add('pshealthid_p12', 'The pshealthid_p12 field is required.');
        }

        $data = $request->all();
        $password = mt_rand(100000,999999);
        $setting = Setting::first();
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($password),
            'verify' => 1,
            'phone' => $data['phone'],
            'phone_code' => $data['phone_code'],
            'address' => $data['address'],
            'lat' => $data['lat'],
            'lng' => $data['lng'],
            'image' => 'default_doctor.jpg'
        ],
        [
            'image.max' => 'The Image May Not Be Greater Than 1 MegaBytes.',
        ]);
        
        $message1 = 'Dear Doctor your password is : '.$password;
        try
        {
            $config = array(
                'driver'     => $setting->mail_mailer,
                'host'       => $setting->mail_host,
                'port'       => $setting->mail_port,
                'from'       => array('address' => $setting->mail_from_address, 'name' => $setting->mail_from_name),
                'encryption' => $setting->mail_encryption,
                'username'   => $setting->mail_username,
                'password'   => $setting->mail_password
            );
            Config::set('mail', $config);
            Mail::to($user->email)->send(new SendMail($message1,'Doctor Password'));
        }
        catch (\Throwable $th)
        {

        }
        $user->assignRole('doctor');
        $data['user_id'] = $user->id;
        //updated by Polaris
        if (!isset($data['dob'])) $data['dob'] = Carbon::now();
        if (!isset($data['gender'])) $data['gender'] = "male";
        if (!isset($data['hospital_id'])) $data['hospital_id'] = 1;
        if (!isset($data['start_time'])) $data['start_time'] = '08:00';
        if (!isset($data['end_time'])) $data['end_time'] = '17:00';
        if (!isset($data['degree'])) $data['degree'] = [];
        if (!isset($data['certificate'])) $data['certificate'] = [];
        if (!isset($data['language'])) $data['language'] = "English";
        if (!isset($data['timeslot'])) $data['timeslot'] = "30";
        if (!isset($data['experience'])) $data['experience'] = "5";
        if (!isset($data['is_filled'])) $data['is_filled'] = 0;


        $data['start_time'] = strtolower(Carbon::parse($data['start_time'])->format('h:i a'));
        $data['end_time'] = strtolower(Carbon::parse($data['end_time'])->format('h:i a'));
        if($request->hasFile('image'))
        {
            $data['image'] = (new CustomController)->imageUpload($request->image);
        }
        else
        {
            $data['image'] = 'default_doctor.jpg';
        }
        if($request->hasFile('pshealthid_p12'))
        {
            $data['pshealthid_p12'] = (new CustomController)->ext_fileUpload(env('p12_path'), $request->pshealthid_p12, $data['pshealthid']);
        } else {
            $data['pshealthid_p12'] = 'MIPIT.p12';
        }
        $education = array();
        for ($i=0; $i < count($data['degree']); $i++)
        {
            $temp['degree'] = $data['degree'][$i];
            $temp['college'] = $data['college'][$i];
            $temp['year'] = $data['year'][$i];
            array_push($education,$temp);
        }
        $data['education'] = json_encode($education);
        $certificate = array();
        for ($i=0; $i < count($data['certificate']); $i++)
        {
            $temp1['certificate'] = $data['certificate'][$i];
            $temp1['certificate_year'] = $data['certificate_year'][$i];
            array_push($certificate,$temp1);
        }
        $data['certificate'] = json_encode($certificate);
        $data['since'] = Carbon::now(env('timezone'))->format('Y-m-d , h:i A');
        $data['status'] = 1;
        $data['subscription_status'] = 1;
        // $data['is_filled'] = 1;
        // $data['hospital_id'] = implode(',',$request->hospital_id);
        //updated by Polaris
        if (!isset($data['commission_amount']) || empty($data['commission_amount']))
            $data['commission_amount'] = '10.00';
        if (!isset($data['based_on']) || empty($data['based_on']))
            $data['based_on'] = 'subscription';

        $doctor = Doctor::create($data);
        
        // if($doctor->based_on == 'subscription')
        // {
        //     $subscription = Subscription::where('name','free')->first();
        //     if($subscription)
        //     {
        //         $doctor_subscription['doctor_id'] = $doctor->id;
        //         $doctor_subscription['subscription_id'] = $subscription->id;
        //         $doctor_subscription['duration'] = 1;
        //         $doctor_subscription['start_date'] = Carbon::now(env('timezone'))->format('Y-m-d');
        //         $doctor_subscription['end_date'] = Carbon::now(env('timezone'))->addMonths(1)->format('Y-m-d');
        //         $doctor_subscription['status'] = 1;
        //         $doctor_subscription['payment_status'] = 1;
        //         DoctorSubscription::create($doctor_subscription);
        //     }
        // }
        $start_time = strtolower($doctor->start_time);
        $end_time = strtolower($doctor->end_time);
        $days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
        for($i = 0; $i < count($days); $i++)
        {
            $master = array();
            $temp2['start_time'] = $start_time;
            $temp2['end_time'] = $end_time;
            array_push($master,$temp2);
            $work_time['doctor_id'] = $doctor->id;
            $work_time['period_list'] = json_encode($master);
            $work_time['day_index'] = $days[$i];
            $work_time['status'] = 1;
            WorkingHour::create($work_time);
        }
        return redirect('doctor')->withStatus(__('Doctor created successfully..!!'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $currency = Setting::first()->currency_symbol;

        $hide_invalidate = 0;
        $hide_expired = 0;

        $data = $request->all();
        if (isset($data['hide_expired']))
            $hide_expired = $data['hide_expired'];
        if (isset($data['hide_invalidate']))
            $hide_invalidate = $data['hide_invalidate'];

        $h = DoctorPID::leftJoin("doctor", "doctor.id", "=", "doctor_pid.doctor_id");
        if (isset($data['date_type'])) {
            if (isset($data['start_date']) && $data['start_date']!='')
                $h = $h->where("doctor_pid.".$data['date_type'], '>=', $data['start_date'].' 00.00.00');
            if (isset($data['end_date']) && $data['end_date']!='')
                $h = $h->where("doctor_pid.".$data['date_type'], '<=', $data['end_date'].' 23.59.59');
        }        
        if ($hide_expired) {
            $expire_day = Carbon::now(env('timezone'))->subHours(17)->format('Y-m-d h:i:s');
            $h = $h->where(function ($query) use ($expire_day) {
                $query->where("doctor_pid.guichet_date", '>=', $expire_day)
                      ->whereNotNull("doctor_pid.paye");
            });
        }
        if ($hide_invalidate) {
            $h = $h->whereNotNull("doctor_pid.paye")->where("paye", "!=", "");
        }
        $h->selectRaw("doctor_pid.*, doctor.name as doctor_name, doctor.image as doctor_img");

        $histories = $h->get();
        foreach ($histories as $history)
        {
            $is_simulation = empty($history->ccss_token) ? 0:1;
            $is_validation = $history->paye!='' ? 1:0;
            $is_contestation = !empty($history->contestation_id);
            $guichet_date = Carbon::parse($history->guichet_date);
            $expiredDate = $guichet_date->copy()->addHours(17);

            $is_expired = Carbon::now(env('timezone'))->greaterThan($expiredDate);
            $is_valid = $is_validation || $is_contestation || (!$is_validation && !$is_contestation && !$is_expired && $is_simulation);

            $history['is_simulation'] = $is_simulation;
            $history['is_validation'] = $is_validation;
            $history['is_contestation'] = $is_contestation;
            $history['is_expired'] = $is_expired;
            $history['is_valid'] = $is_valid;

            $history['patient_img'] = 'images/upload/default_doctor.jpg';
            $history['patient_name'] = 'No set';
            $history['patient_email'] = '';
            if (!empty($history->patient_id))
            {
                $patient = User::where('patient_id', $history->patient_id)->first();
                if ($patient) {
                    $history['patient_img'] = $patient->fullImage;
                    $history['patient_name'] = $patient->name. ' '. $patient->lastname;
                    $history['patient_email'] = $patient->email;
                }
            }
        }
        return view('superAdmin.doctor_pid.doctor_pid', compact('hide_invalidate', 'hide_expired', 'histories', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_if(Gate::denies('doctor_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $doctor = Doctor::find($id);
        $doctor->user = User::find($doctor->user_id);
        $countries = Country::get();
        $treatments = Treatments::whereStatus(1)->get();
        $categories = Category::whereStatus(1)->get();
        $expertieses = Expertise::whereStatus(1)->get();
        $hospitals = Hospital::get();
        $doctor['start_time'] = Carbon::parse($doctor['start_time'])->format('H:i');
        $doctor['end_time'] = Carbon::parse($doctor['end_time'])->format('H:i');
        $doctor['hospital_id'] = explode(',',$doctor->hospital_id);
        return view('superAdmin.doctor.edit_doctor',compact('doctor','countries','treatments','hospitals','categories','expertieses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'bail|required|unique:doctor,name,' . $id . ',id',
            'pshealthid' => 'bail|required|unique:doctor,pshealthid,' . $id . ',id',
            'biller_id' => 'bail|required|unique:doctor,biller_id,' . $id . ',id',
            'pshealthid_p12_pass' => 'bail|required',
            // 'treatment_id' => 'bail|required',
            'category_id' => 'bail|required',
            'dob' => 'bail|required',
            'gender' => 'bail|required',
            'phone' => 'bail|required|digits_between:6,12',
            // 'expertise_id' => 'bail|required',
            'timeslot' => 'bail|required',
            'start_time' => 'bail|required',
            'end_time' => 'bail|required|after:start_time',
            'hospital_id' => 'bail|required',
            // 'desc' => 'required',
            // 'appointment_fees' => 'required|numeric',
            // 'experience' => 'bail|required|numeric',
            'image' => 'bail|mimes:jpeg,png,jpg|max:1000',
            'custom_timeslot' => 'bail|required_if:timeslot,other',
            // 'commission_amount' => 'bail|required_if:based_on,commission'
        ],
        [
            'image.max' => 'The Image May Not Be Greater Than 1 MegaBytes.',
        ]); 
        $doctor = Doctor::find($id);
        $data = $request->all();

        $data['start_time'] = Carbon::parse($data['start_time'])->format('h:i A');
        $data['end_time'] = Carbon::parse($data['end_time'])->format('h:i A');
        if($request->hasFile('image'))
        {
            $data['image'] = (new CustomController)->imageUpload($request->image);
        }
        if($request->hasFile('pshealthid_p12'))
        {
            (new CustomController)->ext_deleteFile(env('p12_path'), $doctor->pshealthid_p12);
            $data['pshealthid_p12'] = (new CustomController)->ext_fileUpload(env('p12_path'), $request->pshealthid_p12, $data['pshealthid']);
        } 

        //updated by Polaris
        if ($data['pshealthid'] != $doctor->pshealthid) {
            //pshealthid updated
            $data['pshealthid_p12'] = (new CustomController)->ext_renameFile(env('p12_path'), $doctor->pshealthid_p12, $data['pshealthid'].'.p12');
        }
        $education = array();
        for ($i=0; $i < count($data['degree']); $i++)
        {
            $temp['degree'] = $data['degree'][$i];
            $temp['college'] = $data['college'][$i];
            $temp['year'] = $data['year'][$i];
            array_push($education,$temp);
        }
        $data['education'] = json_encode($education);
        $certificate = array();
        for ($i=0; $i < count($data['certificate']); $i++)
        {
            $temp1['certificate'] = $data['certificate'][$i];
            $temp1['certificate_year'] = $data['certificate_year'][$i];
            array_push($certificate,$temp1);
        }
        $data['certificate'] = json_encode($certificate);
        // $data['is_filled'] = 1;
        $data['custom_timeslot'] = $request->custom_time == '' ? null : $request->custom_time;
        $data['hospital_id'] = implode(',',$request->hospital_id);
        // if ($request->based_on == 'subscription') {
        //     if (!DoctorSubscription::where('doctor_id',$id)->exists()) {
        //         $subscription = Subscription::where('name','free')->first();
        //         if($subscription)
        //         {
        //             $doctor_subscription['doctor_id'] = $doctor->id;
        //             $doctor_subscription['subscription_id'] = $subscription->id;
        //             $doctor_subscription['duration'] = 1;
        //             $doctor_subscription['start_date'] = Carbon::now(env('timezone'))->format('Y-m-d');
        //             $doctor_subscription['end_date'] = Carbon::now(env('timezone'))->addMonths(1)->format('Y-m-d');
        //             $doctor_subscription['status'] = 1;
        //             $doctor_subscription['payment_status'] = 1;
        //             DoctorSubscription::create($doctor_subscription);
        //         }
        //     }
        // }
        //updated by Polaris
        if (!isset($data['commission_amount']) || empty($data['commission_amount']))
            $data['commission_amount'] = '10.00';
        if (!isset($data['based_on']) || empty($data['based_on']))
            $data['based_on'] = 'subscription';
        try {
            $doctor->update($data);
            $user = User::find($doctor->user_id);
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'phone_code' => $data['phone_code'],
                'address' => $data['address'],
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'image' => isset($data['image']) ? $data['image']:$doctor->image
            ]);
        } catch (\Exception $e) {
            // Log or handle the exception
            dd($e->getMessage());
        }
        return redirect('doctor')->withStatus(__('Doctor updated successfully..!!'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        abort_if(Gate::denies('doctor_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $offers = Offer::all();
        foreach ($offers as $value)
        {
            $doctor_id = explode(',',$value['doctor_id']);
            if (($key = array_search($id, $doctor_id)) !== false)
            {
                return response(['success' => false , 'data' => 'This doctor connected with Offer first delete Offer']);
            }
        }
        $id = Doctor::find($id);
        $user = User::find($id->user_id);
        $user->removeRole('doctor');
        $user->delete();
        (new CustomController)->deleteFile($id->image);
        $id->delete();
        return response(['success' => true]);
    }

    public function display_timeslot($id)
    {
        $work = WorkingHour::find($id);
        return response(['success' => true , 'data' => $work]);
    }

    public function edit_timeslot($id)
    {
        $work = WorkingHour::find($id);
        return response(['success' => true , 'data' => $work]);
    }

    public function update_timeslot(Request $request)
    {
        $data = $request->all();
        $work = WorkingHour::find($request->working_id);
        $master = array();
        for ($i=0; $i < count($request->start_time); $i++)
        {
            $temp['start_time'] = strtolower($request->start_time[$i]);
            $temp['end_time'] = strtolower($request->end_time[$i]);
            array_push($master,$temp);
        }
        $data['period_list'] = json_encode($master);
        $data['status'] = $request->has('status') ? 1 : 0;
        $work->update($data);
        return redirect()->back();
    }

    public function change_password(Request $request)
    {
        $request->validate([
            'new_password' => 'bail|required|min:6',
            'confirm_new_password' => 'bail|required|min:6|same:new_password'
        ]);
        User::find(Doctor::find($request->doctor_id)->user_id)->update(['password' => Hash::make($request->new_password)]);
        return redirect()->back()->withStatus(__('password change successfully..!!'));
    }

    public function change_status(Request $reqeust)
    {
        $doctor = Doctor::find($reqeust->id);
        $data['status'] = $doctor->status == 1 ? 0 : 1;
        $doctor->update($data);
        return response(['success' => true]);
    }
}

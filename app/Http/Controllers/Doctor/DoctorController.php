<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Controllers\SuperAdmin\CustomController;
use App\Models\Appointment;
use App\Models\Category;
use App\Models\Country;
use App\Models\Doctor;
use App\Models\DoctorPID;
use App\Models\DoctorSubscription;
use App\Models\Expertise;
use App\Models\Hospital;
use App\Models\Language;
use App\Models\Review;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\Treatments;
use App\Models\User;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Gate;
use App;
use Spatie\Permission\Models\Permission;
use PDF;


class DoctorController extends Controller
{
    public function doctorLogin()
    {
        return view('doctor.auth.doctor_login');
    }

    public function doctorSignup()
    {
        $countries = Country::get();
        return view('doctor.auth.doctor_register', compact('countries'));
    }

    public function verify_doctor(Request $request)
    {
        $request->validate([
            'email' => 'bail|required|email',
            'password' => 'bail|required|min:6'
        ]);

        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $doctor = Auth::user()->load('roles');
            // return $doctor;
            if ($doctor->hasRole('doctor')) {
                if ($doctor->verify == 1) {
                    $doctor = Doctor::where('user_id', auth()->user()->id)->first();
                    if ($doctor && $doctor->status == 1) {
                        if ($doctor->based_on == 'subscription') {
                            $subscription = DoctorSubscription::with(['subscription:id,total_appointment'])->where([['doctor_id', $doctor->id], ['status', 1]])->first();
                            if ($subscription) {
                                if ($subscription['subscription']['total_appointment'] > $subscription['booked_appointment']) {
                                    $cDate = Carbon::parse($doctor['start_time'])->format('Y-m-d');
                                    if ($subscription->end_date > $cDate) {
                                        // subscription active
                                        $doctor->update(['subscription_status' => 1]);
                                    } else {
                                        // subscription expire
                                        $doctor->update(['subscription_status' => 0]);
                                    }
                                }
                            } else {
                                // subscription expire
                                $doctor->update(['subscription_status' => 0]);
                            }
                            return redirect('doctor_home');
                        } else {
                            return redirect('doctor_home');
                        }
                    } else {
                        Auth::logout();
                        return redirect()->back()->withErrors('you are disable by admin please contact admin');
                    }
                } else {
                    return redirect('doctor/send_otp/' . $doctor->id);
                }
            } else {
                Auth::logout();
                return redirect()->back()->withErrors('Only doctor can login');
            }
        } else {
            return redirect()->back()->withErrors('your creditial does not match our record');
        }
    }

    public function doctor_register(Request $request)
    {
        $request->validate([
            'name' => 'bail|required|unique:doctor',
            'email' => 'bail|required|email|unique:users',
            'dob' => 'bail|required',
            'gender' => 'bail|required',
            'phone' => 'bail|required|digits_between:6,12',
            'password' => 'bail|required|min:6',
            'repeat_password' => 'required|same:password'
        ]);
        $user = (new CustomController)->doctorRegister($request->all());
        if ($user->verify == 1) {
            if (Auth::attempt(['email' => $user['email'], 'password' => $request->password])) {
                return redirect('doctor_profile');
            }
        } else {
            return redirect('doctor/send_otp/' . $user->id);
        }
    }

    public function send_otp($user_id)
    {
        $user = User::find($user_id);
        $setting = Setting::first();

        (new CustomController)->sendOtp($user);
        $status = '';
        if ($setting->using_msg == 1 && $setting->using_mail == 1)
            $status = 'verification code sent in email and phone';

        if ($status == '') {
            if ($setting->using_msg == 1 || $setting->using_mail == 1) {
                if ($setting->using_msg == 1)
                    $status = 'verification code sent into phone';
                if ($setting->using_mail == 1)
                    $status = 'verification code sent into email';
            }
        }
        return view('doctor.auth.send_otp', compact('user'))->with('status', $status);
    }

    public function verify_otp(Request $request)
    {
        $data = $request->all();
        $otp = $data['digit_1'] . $data['digit_2'] . $data['digit_3'] . $data['digit_4'];
        $user = User::find($request->user_id);
        if ($user) {
            if ($user->otp == $otp) {
                $user->verify = 1;
                $user->save();
                if (Auth::loginUsingId($user->id))
                    return redirect('doctor_home');
            } else
                return redirect()->back()->with('error', __('otp does not match'));
        } else {
            return redirect()->back()->with('error', __('Oops.user not found.!'));
        }
    }

    public function doctor_home()
    {
        (new CustomController)->cancel_max_order();
        abort_if(Gate::denies('doctor_home'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        if (!$doctor) return redirect('/');
        $today_Appointments = Appointment::whereDate('created_at', Carbon::now(env('timezone')))->where('doctor_id', $doctor->id)->orderBy('id', 'DESC')->get();
        $currency = Setting::first()->currency_symbol;
        $orderCharts = $this->orderChart();
        $allUsers = User::where('doctor_id', $doctor->id)->doesntHave('roles')->orderBy('id', 'DESC')->get()->take(10);
        $totalUser = User::where('doctor_id', $doctor->id)->doesntHave('roles')->count();
        $totalAppointment = Appointment::where('doctor_id', $doctor->id)->count();
        $totalReview = Review::where('doctor_id', $doctor->id)->count();
        return view('doctor.doctor.home', compact('today_Appointments', 'allUsers', 'totalReview', 'totalAppointment', 'totalUser', 'orderCharts', 'currency'));
    }

    public function orderChart()
    {
        $masterYear = array();
        $labelsYear = array();
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        if (!$doctor) return redirect('/');
        array_push($masterYear, Appointment::where('doctor_id', $doctor->id)->whereMonth('created_at', Carbon::now(env('timezone')))->count());
        for ($i = 1; $i <= 11; $i++) {
            if ($i >= Carbon::now(env('timezone'))->month) {
                array_push($masterYear, Appointment::where('doctor_id', $doctor->id)->whereMonth('created_at', Carbon::now(env('timezone'))->subMonths($i))->whereYear('created_at', Carbon::now(env('timezone'))->subYears(1))->count());
            } else {
                array_push($masterYear, Appointment::where('doctor_id', $doctor->id)->whereMonth('created_at', Carbon::now(env('timezone'))->subMonths($i))->whereYear('created_at', Carbon::now(env('timezone'))->year)->count());
            }
        }

        array_push($labelsYear, Carbon::now(env('timezone'))->format('M-y'));
        for ($i = 1; $i <= 11; $i++) {
            array_push($labelsYear, Carbon::now(env('timezone'))->subMonths($i)->format('M-y'));
        }
        return ['data' => json_encode($masterYear), 'label' => json_encode($labelsYear)];
    }

    public function schedule()
    {
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        if (!$doctor) return redirect('/');
        $doctor->workingHours = WorkingHour::where('doctor_id', $doctor->id)->get();
        $doctor->firstHours = WorkingHour::where('doctor_id', $doctor->id)->first();
        return view('doctor.doctor.doctor_schedule', compact('doctor'))->withStatus(__('Doctor updated successfully..!!'));;
    }

    public function pid_settings(Request $request)
    {
        if (empty(auth()->user()->id))
            return view('doctor.auth.doctor_login');

        $hide_invalidate = 0;
        $hide_expired = 0;

        $data = $request->all();
        if (isset($data['hide_expired']))
            $hide_expired = $data['hide_expired'];
        if (isset($data['hide_invalidate']))
            $hide_invalidate = $data['hide_invalidate'];

        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        if (!$doctor) return redirect('/');
        $h = DoctorPID::where('doctor_id', $doctor->id);
        if (isset($data['date_type'])) {
            if (isset($data['start_date']) && $data['start_date']!='')
                $h = $h->where($data['date_type'], '>=', $data['start_date'].' 00.00.00');
            if (isset($data['end_date']) && $data['end_date']!='')
                $h = $h->where($data['date_type'], '<=', $data['end_date'].' 23.59.59');
        }        
        if ($hide_expired) {
            $expire_day = Carbon::now(env('timezone'))->subHours(17)->format('Y-m-d h:i:s');
            // $h = $h->where("guichet_date", '>=', $expire_day);
            $h = $h->where(function ($query) use ($expire_day) {
                $query->where("guichet_date", '>=', $expire_day)
                      ->whereNotNull("paye");
            });
        }
        if ($hide_invalidate) {
            $h = $h->whereNotNull("paye")->where("paye", "!=", "");
        }

        $histories = $h->get();
        foreach ($histories as $history)
        {
            $is_simulation = empty($history->ccss_token) ? 0:1;
            $is_validation = $history->paye!='' ? 1:0;
            $is_contestation = !empty($history->contestation_id);
            $guichet_date = Carbon::parse($history->guichet_date);
            // $expiredDate = $guichet_date->copy()->addMinutes(30)->addHours(16);
            $expiredDate = $guichet_date->copy()->addHours(17);

            // echo '<br>original: ' . $guichet_date->format('Y-m-d H:i');
            // echo '<br>original (timezone): ' . $guichet_date->setTimezone(env('timezone'))->format('Y-m-d H:i');
            // echo '<br>now: '.Carbon::now()->format('Y-m-d H:i');
            // echo '<br>now (timezone): '.Carbon::now(env('timezone'))->format('Y-m-d H:i');
            //  exit;
            
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
        return view('doctor.doctor.doctor_pid_settings', compact('hide_invalidate', 'hide_expired', 'doctor', 'histories', 'data'));
    }

    public function pid_excel_export(Request $request)
    {
        if (empty(auth()->user()->id))
            return json_encode(['status'=>0, 'data' => []]);
        $data = $request->all();
        $histories = DoctorPID::whereIn('pid_id', $data['pid_list'])->get();
  
        return json_encode(['status'=>0, 'data' => $histories]);
    }

    public function pid_pdf_save(Request $request)
    {
        if (empty(auth()->user()->id) || empty($data['pid_id']))
            return json_encode(['status'=>0, 'data' => []]);
        $data = $request->all();
        $pid_info = DoctorPID::where('pid_id', $data['pid_id'])->first();
        
        $pdf = PDF::loadView('temp', compact('medicineName'));
        $path = public_path() . '/prescription/upload';
        $fileName =  uniqid() . '.' . 'pdf' ;
        $pdf->save($path . '/' . $fileName);
        $pres->pdf = $fileName;
        $pres->save();
        return redirect('/appointment');
    }

    public function pid_detail($id)
    {
        $doctor_pid = DoctorPID::where('pid_id', $id)->first();
        if (empty($doctor_pid)) exit;
        $doctor = Doctor::where('id', $doctor_pid->doctor_id)->first();
        if (!$doctor) return redirect('doctor_home');

        $doctor->user = User::find($doctor->user_id);
        $doctor['start_time'] = Carbon::parse($doctor['start_time'])->format('H:i');
        $doctor['end_time'] = Carbon::parse($doctor['end_time'])->format('H:i');
        $doctor['hospital_id'] = explode(',', $doctor->hospital_id);
        $hospitals = Hospital::whereIn('id', $doctor['hospital_id'])->get();
        $patient = User::where('patient_id', $doctor_pid->patient_id)->first();
        if (empty($patient)) exit;

        return view('pdf.pid_pdf', compact('doctor_pid', 'doctor', 'hospitals', 'patient'));
    }

    public function pid_print($id)
    {
        $doctor_pid = DoctorPID::where('pid_id', $id)->first();
        if (empty($doctor_pid)) exit;
        $doctor = Doctor::where('id', $doctor_pid->doctor_id)->first();
        if (!$doctor) return redirect('doctor_home');

        $doctor->user = User::find($doctor->user_id);
        $doctor['start_time'] = Carbon::parse($doctor['start_time'])->format('H:i');
        $doctor['end_time'] = Carbon::parse($doctor['end_time'])->format('H:i');
        $doctor['hospital_id'] = explode(',', $doctor->hospital_id);
        $hospitals = Hospital::whereIn('id', $doctor['hospital_id'])->get();
        $patient = User::where('patient_id', $doctor_pid->patient_id)->first();
        if (empty($patient)) exit;

        return view('pdf.pid_print', compact('doctor_pid', 'doctor', 'hospitals', 'patient'));
    }

    public function pid_pdf_download($id)
    {

        // $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        $doctor_pid = DoctorPID::where('pid_id', $id)->first();
        if (empty($doctor_pid)) return redirect('/pid_settings');

        $doctor = Doctor::where('id', $doctor_pid->doctor_id)->first();
        if (!$doctor) return redirect('/doctor_home');

        $doctor->user = User::find($doctor->user_id);
        $doctor['start_time'] = Carbon::parse($doctor['start_time'])->format('H:i');
        $doctor['end_time'] = Carbon::parse($doctor['end_time'])->format('H:i');
        $doctor['hospital_id'] = explode(',', $doctor->hospital_id);
        $hospitals = Hospital::whereIn('id', $doctor['hospital_id'])->get();
        $patient = User::where('patient_id', $doctor_pid->patient_id)->first();
        if (empty($patient)) exit;

        $pdf = PDF::loadView('pdf.pid_pdf', compact('doctor_pid', 'doctor', 'hospitals', 'patient'));
        return $pdf->download('doctor_pid_ticket_'.$id.'.pdf');
    }

    public function doctor_profile()
    {
        abort_if(Gate::denies('doctor_profile'), Response::HTTP_FORBIDDEN, '403 Forbidden');
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        if (!$doctor) return redirect('/');

        $doctor->user = User::find($doctor->user_id);
        $countries = Country::get();
        $treatments = Treatments::whereStatus(1)->get();
        $categories = Category::whereStatus(1)->get();
        $expertieses = Expertise::whereStatus(1)->get();
        $hospitals = Hospital::whereStatus(1)->get();
        $doctor['start_time'] = Carbon::parse($doctor['start_time'])->format('H:i');
        $doctor['end_time'] = Carbon::parse($doctor['end_time'])->format('H:i');
        $doctor['hospital_id'] = explode(',', $doctor->hospital_id);
        $languages = Language::whereStatus(1)->get();
        return view('doctor.doctor.doctor_profile', compact('doctor', 'countries', 'treatments', 'hospitals', 'categories', 'expertieses', 'languages'));
    }

    public function update_doctor_profile(Request $request)
    {
        $id = Doctor::where('user_id', auth()->user()->id)->first()->id;
        $request->validate(
            [
                'name' => 'bail|required|unique:doctor,name,' . $id . ',id',
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
                'address' => 'bail|required',
                // 'desc' => 'required',
                // 'appointment_fees' => 'required|numeric',
                // 'experience' => 'bail|required|numeric',
                'image' => 'bail|mimes:jpeg,png,jpg|max:1000',
                'custom_timeslot' => 'bail|required_if:timeslot,other'
            ],
            [
                'image.max' => 'The Image May Not Be Greater Than 1 MegaBytes.',
            ]
        );
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        $data = $request->all();
        $data['start_time'] = Carbon::parse($data['start_time'])->format('h:i A');
        $data['end_time'] = Carbon::parse($data['end_time'])->format('h:i A');
        if ($request->hasFile('image')) {
            (new CustomController)->deleteFile($doctor->image);
            $data['image'] = (new CustomController)->imageUpload($request->image);
        }
        $education = array();
        for ($i = 0; $i < count($data['degree']); $i++) {
            $temp['degree'] = $data['degree'][$i];
            $temp['college'] = $data['college'][$i];
            $temp['year'] = $data['year'][$i];
            array_push($education, $temp);
        }
        $data['education'] = json_encode($education);
        $certificate = array();
        for ($i = 0; $i < count($data['certificate']); $i++) {
            $temp1['certificate'] = $data['certificate'][$i];
            $temp1['certificate_year'] = $data['certificate_year'][$i];
            array_push($certificate, $temp1);
        }
        $data['certificate'] = json_encode($certificate);
        $data['hospital_id'] = implode(',', $request->hospital_id);
        $data['custom_timeslot'] = $data['custom_timeslot'] == "" ? null : $data['custom_timeslot'];
        if ($data['timeslot'] != 'other')
            $data['custom_timeslot'] = null;
        // if ($request->based_on == 'subscription') {
        //     if (!DoctorSubscription::where('doctor_id', $id)->exists()) {
        //         $subscription = Subscription::where('name', 'free')->first();
        //         if ($subscription) {
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
        $doctor->update($data);
        if (isset($data['address']) && isset($data['lat']) && isset($data['lng']))
        {
            $doctor_user = User::where('doctor_id', $doctor->id)->first();
            if ($doctor_user) {
                $doctor_user['address'] = $data['address'];
                $doctor_user['lat'] = $data['lat'];
                $doctor_user['lng'] = $data['lng'];
                $doctor_user->save();
            }
        }
        $this->changeLanguage();
        return redirect('/doctor_home')->withStatus(__('Doctor updated successfully..!!'));
    }

    //updated by Polaris
    public function patient_search(Request $request)
    {
        $data = $request->all();
        $patients = User::whereNotNull('patient_id');
        if (isset($data['search_txt']) && $data['search_txt'] != '') {
            $patients = $patients->where(function ($query) use ($data) {
                $query->where('patient_id', 'like', '%' . $data['search_txt'] . '%')
                      ->orWhere('name', 'like', '%' . $data['search_txt'] . '%')
                      ->orWhere('last_name', 'like', '%' . $data['search_txt'] . '%');
            });
        } 
        $patients = $patients->orderBy('name', 'asc')->orderBy('last_name', 'asc')->take(10)->get();
        $result =[];
        foreach ($patients as $patient) {
            $result[] =[
                'name' => $patient->name.' '.$patient->last_name,
                'patient_id' => $patient->patient_id,
                'email' => $patient->email,
                'img' => $patient->image
            ];
        }
        return response()->json(['success' => true, 'data' => $result]);
    }

    public function changeLanguage()
    {
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        App::setLocale($doctor->language);
        session()->put('locale', $doctor->language);
        $direction = Language::where('name', $doctor->language)->first()->direction;
        session()->put('direction', $direction);
        return true;
    }

    public function changePassword()
    {
        return view('doctor.doctor.change_password');
    }

    public function doctor_review()
    {
        $doctor = Doctor::where('user_id', auth()->user()->id)->first();
        $reviews = Review::with(['appointment:id,appointment_id', 'user:id,name'])->where('doctor_id', $doctor->id)->get();
        return view('doctor.doctor.review', compact('reviews'));
    }
}

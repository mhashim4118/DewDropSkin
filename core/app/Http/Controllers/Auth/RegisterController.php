<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Frontend;
use App\Models\GeneralSetting;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserLogin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('regStatus')->except('registrationNotAllowed');

        $this->activeTemplate = activeTemplate();
    }

    public function showRegistrationForm(Request $request)
    { 
        

        $content = Frontend::where('data_keys', 'sign_up.content')->first();
        $pageTitle = "Sign Up";
   

        if ($request->ref && $request->position) {

            $refUser = User::where('username', $request->ref)->first();
            if ($refUser == null) {
                $notify[] = ['error', 'Invalid Referral link.'];
                return redirect()->route('home')->withNotify($notify);
            }

            $position = $request->position == 'left' ? 1 : 2;

            $pos = getPosition($refUser->id, $position);

            $referrer = User::find($pos['pos_id']);

            if ($pos['position'] == 1){
                $getPosition = 'Left';
			}else {
                $getPosition = 'Right';
            }
            $joining = "<span class='help-block2'><strong class='text--success'>You are joining under ".$referrer->username." at ".$getPosition." </strong></span>";
        }else{
            $refUser = null;
            $joining = null;
            $position = null;
            $pos = null;
            $referrer = null;
            $getPosition = null;
        }

        
        return view($this->activeTemplate . 'user.auth.register', compact('pageTitle','position','pos','refUser','referrer','getPosition','joining'));
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $general = GeneralSetting::first();
        $password_validation = Password::min(6);
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }
        $agree = 'nullable';
        if ($general->agree) {
            $agree = 'required';
        }
     
        $validate = Validator::make($data, [
            'referral'      => 'required|string|max:160',
            'position'      => 'required|integer',
            'firstname' => 'sometimes|required|string|max:50',
            'lastname' => 'sometimes|required|string|max:50',
            'email' => 'required|string|email|max:100|unique:users',
            'mobile' => 'required|string|max:50|unique:users',
            'password' => ['required','confirmed',$password_validation],
            'captcha' => 'sometimes|required',
            'agree' => $agree
        ]);
        return $validate;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
		$check_ref = User::where('username', $request->referral)->first();
        $productCheck = DB::connection('mysql_store')->table('order_details')
        ->leftJoin('orders', 'orders.id', '=','order_details.order_id')
        ->whereIn('order_details.product_id', [43, 44, 48])
        ->where('order_details.payment_status', 'paid')
        ->where('orders.customer_dds', $request->referral)
        ->exists();

        if(!$productCheck && $check_ref->plan_id == 0 ) {
            $notify[] = ['error', 'This user has not purchased any dsp plan yet'];
            return back()->withNotify($notify)->withInput();
        }
		
        //$this->validator($request->all())->validate();
		$mobile = '+92'.$request->mobile;
		
        $exist = User::where('mobile',$mobile)->first();
        if ($exist) {
            $notify[] = ['error', 'The mobile number already exists'];
            return back()->withNotify($notify)->withInput();
        }

		$cnic_no_count = strlen(preg_replace("/[^\d]/", "", $request->cnicnumber));
        if($cnic_no_count != 13) {
            $notify[] = ['error', 'Provide a valid CNIC Number'];
            return back()->withNotify($notify)->withInput();
        }
        
        // check CINIC Number
        $is_cnicnumber = User::where('cnicnumber',$request->cnicnumber)->first();
        if ($is_cnicnumber) {
          $notify[] = ['error', 'The CNIC Number already exists'];
          return back()->withNotify($notify)->withInput();
      }


        if (isset($request->captcha)) {
            if (!captchaVerify($request->captcha, $request->captcha_secret)) {
                $notify[] = ['error', "Invalid captcha"];
                return back()->withNotify($notify)->withInput();
            }
        }

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $general = GeneralSetting::first();
 
        if (str_contains($data['referral'], 'dsp')) {
            $getuserRef = User::where('username', $data['referral'])->first();
            $userCheck = User::where('id', $getuserRef->ref_id)->first();
            $pos = getPosition($getuserRef->id, $data['position']);
        }else{
            $userCheck = User::where('username', $data['referral'])->first();
            $pos = getPosition($userCheck->id, $data['position']);
        }
        
        $last_dds = User::where('username','LIKE','dds%')->count();
       
        

        //User Create
        $user = new User();
        $user->ref_id       = $userCheck->id;
        $user->pos_id       = $pos['pos_id'];
        $user->position     = $pos['position'];
        $user->firstname = isset($data['firstname']) ? $data['firstname'] : null;
        $user->lastname = isset($data['lastname']) ? $data['lastname'] : null;
        $user->email = strtolower(trim($data['email']));
        $user->password = Hash::make($data['password']);
        $user->username = 'dds000'.++$last_dds;
        $user->fathername = trim($data['fathername']);
        $user->cnicnumber = trim($data['cnicnumber']);
        $city = isset($data['city'])?$data['city']:'';
        $state = isset($data['state'])?$data['state']:'';
        $user->country_code = 'PK';
        $user->mobile = '+92'.$data['mobile'];
        $user->address = [
            'address' => isset($data['address']) ? $data['address'] : null,
            'state' => trim($state),
            'zip' => '',
            'country' => isset($data['country']) ? $data['country'] : null,
            'city' => trim($city)
        ];
        $user->status = 1;
        $user->ev = $general->ev ? 0 : 1;
        $user->sv = $general->sv ? 0 : 1;
        $user->ts = 0;
        $user->tv = 1;
        $user->save();
		


        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New member registered';
        $adminNotification->click_url = urlPath('admin.users.detail',$user->id);
        $adminNotification->save();





        return $user;
    }

    public function checkUser(Request $request){
        $exist['data'] = null;
        $exist['type'] = null;
        if ($request->email) {
            $exist['data'] = User::where('email',$request->email)->first();
            $exist['type'] = 'email';
        }
        if ($request->mobile) {
            $exist['data'] = User::where('mobile',$request->mobile)->first();
            $exist['type'] = 'mobile';
        }
        if ($request->username) {
            $exist['data'] = User::where('username',$request->username)->first();
            $exist['type'] = 'username';
        }
        return response($exist);
    }

    public function registered(Request $request, $user)
    {
        $user_extras = new UserExtra();
        $user_extras->user_id = $user->id;
        $user_extras->save();
        updateFreeCount($user->id);
        return redirect()->route('user.home');
    }

}
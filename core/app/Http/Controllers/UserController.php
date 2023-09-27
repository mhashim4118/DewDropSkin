<?php

namespace App\Http\Controllers;

use App\Lib\GoogleAuthenticator;
use App\Models\AdminNotification;
use App\Models\BvLog;
use App\Models\Deposit;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\WithdrawMethod;
use App\Models\Withdrawal;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    public function home()
    {

        $i = $this->ref_ranking();
        $o = $this->ranking();

        $pageTitle = 'Dashboard';
        $totalDeposit       = Deposit::where('user_id', auth()->id())->where('status', 1)->sum('amount');
        $totalWithdraw      = Withdrawal::where('user_id', auth()->id())->where('status', 1)->sum('amount');
        $completeWithdraw   = Withdrawal::where('user_id', auth()->id())->where('status', 1)->count();
        $pendingWithdraw    = Withdrawal::where('user_id', auth()->id())->where('status', 2)->count();
        $rejectWithdraw     = Withdrawal::where('user_id', auth()->id())->where('status', 3)->count();
        $totalRef          = User::where('ref_id', auth()->id())->count();
        $totalBvCut         = BvLog::where('user_id', auth()->id())->where('trx_type', '-')->sum('amount');

        $pairBonus = UserExtra::where('user_id', auth()->id())->firstOrFail();
        $totalPair = $pairBonus->pair_bonus;
        $childDsp = User::where('ref_id', auth()->id())->get();
        foreach ($childDsp as $key => $value) {
            if (str_contains($value['username'], 'dsp')) {
                $childPairBonus = UserExtra::where('user_id', $value['id'])->firstOrFail();
                // echo $value['id']." ".$value['username']." ".$childPairBonus->pair_bonus."<br>";
                $totalPair = $totalPair + $childPairBonus->pair_bonus;
            }
        }
        $totalPairBonus = $totalPair * 200;


        $user_dds = User::where('id', auth()->user()->id)->first();

        //update reference bonus
        $user_dsp = User::where('ref_id', auth()->user()->id)->where('reference_bonus', '<>', 0)->where('username', 'like', '%dsp%')->get();

        $dsp_rb = 0;
        if ($user_dsp->count() != 0) {
            foreach ($user_dsp as $dsp) {
                $dsp_rb += $dsp->reference_bonus;
            }

            if ($dsp_rb != 0) {
                $user_update_rb = $user_dds->reference_bonus += $dsp_rb;
                User::where('id', auth()->user()->id)->update([
                    'reference_bonus' => $user_update_rb
                ]);
                User::where('ref_id', auth()->user()->id)->where('username', 'like', '%dsp%')->update([
                    'reference_bonus' => '0'
                ]);
            }
        }

        //update store reference
        $user_dsp_ = User::where('ref_id', auth()->user()->id)->where('store_reference', '<>', 0)->where('username', 'like', '%dsp%')->get();

        $dsp_sr = 0;
        if ($user_dsp_->count() != 0) {
            foreach ($user_dsp_ as $dsp) {
                $dsp_sr += $dsp->store_reference;
                echo $dsp->store_reference . '<br>';
            }

            if ($dsp_sr != 0) {
                $user_update_sr = $user_dds->store_reference += $dsp_sr;
                User::where('id', auth()->user()->id)->update([
                    'store_reference' => $user_update_sr
                ]);
                User::where('ref_id', auth()->user()->id)->where('username', 'like', '%dsp%')->update([
                    'store_reference' => '0'
                ]);
            }
        }

        $user = DB::table('users')->where('id', auth()->user()->id)->first();
        $admin_wallets = array();
        if($user->username == 'dds0002'){
            $admin_wallets = DB::table('admin_wallets')->where('id',1)->first();
        }

        $pairCount = UserExtra::where('user_id', auth()->user()->id)->first();
        $pairs=0;

        if($pairCount->paid_left > $pairCount->paid_right) {
            $pairs = $pairCount->paid_right;
        }elseif($pairCount->paid_left < $pairCount->paid_right) {
            $pairs = $pairCount->paid_left;
        }else {
            $pairs = $pairCount->paid_left; 
        }
  
        $partner = DB::table('partner_earnings')->where('dds_username', $user->username)->first();
		
		

        $products = Product::where('status',1)->get();
        $productCheck = DB::connection('mysql_store')->table('order_details')
        ->leftJoin('orders', 'orders.id', '=','order_details.order_id')
        ->whereIn('order_details.product_id', [43, 44, 48])
        ->where('orders.payment_status', 'paid')
        ->where('orders.customer_dds', auth()->user()->username)
        ->exists();

		
        return view($this->activeTemplate . 'user.dashboard', compact('pageTitle','productCheck', 'products','pairs','admin_wallets','partner','totalDeposit', 'totalWithdraw', 'completeWithdraw', 'pendingWithdraw', 'rejectWithdraw', 'totalRef', 'totalBvCut', 'totalPairBonus'));
    }

    private function ref_ranking()
    {
        $users = User::where('ref_id', auth()->user()->id)->get();
        if ($users->count() > 0) {
            foreach ($users as $user) {

                $ue = UserExtra::where('user_id', $user->id)->first();

                if (!empty($ue)) {
                    if ($ue->paid_right >= 32760 && $ue->paid_left >= 32760) {
                        User::where('id', $user->id)->update([
                            'rank' => 'Emperor'
                        ]);
                    } elseif ($ue->paid_right >= 16384 && $ue->paid_left >= 16384) {
                        User::where('id', auth()->user()->id)->update([
                            'rank' => 'King'
                        ]);
                    } elseif ($ue->paid_right >= 8192 && $ue->paid_left >= 8192) {
                        User::where('id', auth()->user()->id)->update([
                            'rank' => 'Ambassador'
                        ]);
                    } elseif ($ue->paid_right >= 1024 && $ue->paid_left >= 1024) {
                        User::where('id', auth()->user()->id)->update([
                            'rank' => 'Royal Mentor'
                        ]);
                    } elseif ($ue->paid_right >= 256 && $ue->paid_left >= 256) {
                        User::where('id', auth()->user()->id)->update([
                            'rank' => 'Chief'
                        ]);
                        echo 9;
                    } elseif ($ue->paid_right >= 64 && $ue->paid_left >= 64) {
                        User::where('id', auth()->user()->id)->update([
                            'rank' => 'Exective'
                        ]);
                    } elseif ($ue->paid_right >= 32 && $ue->paid_left >= 32) {
                        echo 9;
                        User::where('id', auth()->user()->id)->update([
                            'rank' => 'Director'
                        ]);
                    } elseif ($ue->paid_right >= 1 && $ue->paid_left >= 1) {
                        User::where('id', $user->id)->update([
                            'rank' => 'Master'
                        ]);
                    } else {
                        User::where('id', $user->id)->update([
                            'rank' => 'User'
                        ]);
                    }
                }
            }
        }
        return $users->count();
    }


    private function ranking()
    {
        $ue = UserExtra::where('user_id', auth()->user()->id)->first();
        if ($ue->paid_right >= 32760 && $ue->paid_left >= 32760) {

            $user = User::where('id', auth()->user()->id)->first();
            if ($user->rank != 'Emperor') {
                User::where('id', auth()->user()->id)->update([
                    'rank' => 'Emperor'
                ]);

                Transaction::insert([
                    'user_id' => auth()->user()->id,
                    'trx_type' => '+',
                    'remark' => 'MG ZS EV',
                    'details' => 'Congratulations, "You have won the Emperor reward"'
                ]);
                Transaction::insert([
                    'user_id' => 1,
                    'trx_type' => '-',
                    'remark' => 'MG ZS EV',
                    'details' => $user->username . ' have won the Emperor reward'
                ]);
            }
        } elseif ($ue->paid_right >= 16384 && $ue->paid_left >= 16384) {
            User::where('id', auth()->user()->id)->update([
                'rank' => 'King'
            ]);
            $user = User::where('id', auth()->user()->id)->first();
            if ($user->rank != 'King') {
                User::where('id', auth()->user()->id)->update([
                    'rank' => 'King'
                ]);
                
                Transaction::insert([
                    'user_id' => auth()->user()->id,
                    'trx_type' => '+',
                    'remark' => 'Corolla X',
                    'details' => 'Congratulations, "You have won the King reward"'
                ]);
                Transaction::insert([
                    'user_id' => 1,
                    'trx_type' => '-',
                    'remark' => 'Corolla X',
                    'details' => $user->username . ' have won the King reward'
                ]);
            }
        } elseif ($ue->paid_right >= 8192 && $ue->paid_left >= 8192) {
            $user = User::where('id', auth()->user()->id)->first();
            if ($user->rank != 'Ambassador') {
                User::where('id', auth()->user()->id)->update([
                    'rank' => 'Ambassador'
                ]);

                Transaction::insert([
                    'user_id' => auth()->user()->id,
                    'trx_type' => '+',
                    'remark' => 'Jahaz Package',
                    'details' => 'Congratulations, "You have won the Ambassador reward"'
                ]);
                Transaction::insert([
                    'user_id' => 1,
                    'trx_type' => '-',
                    'remark' => 'Jahaz Package',
                    'details' => $user->username . ' have won the Ambassador reward'
                ]);
            }
        } elseif ($ue->paid_right >= 1024 && $ue->paid_left >= 1024) {

            $user = User::where('id', auth()->user()->id)->first();
            if ($user->rank != 'Royal Mentor') {
                User::where('id', auth()->user()->id)->update([
                    'rank' => 'Royal Mentor'
                ]);

                Transaction::insert([
                    'user_id' => auth()->user()->id,
                    'trx_type' => '+',
                    'remark' => 'Laptop',
                    'details' => 'Congratulations, "You have won the Royal Mentor reward"'
                ]);
                Transaction::insert([
                    'user_id' => 1,
                    'trx_type' => '-',
                    'remark' => 'Laptop',
                    'details' => $user->username . ' have won the Royal Mentor reward'
                ]);
            }
        } elseif ($ue->paid_right >= 256 && $ue->paid_left >= 256) {

            $user = User::where('id', auth()->user()->id)->first();
            if ($user->rank != 'Chief') {
                User::where('id', auth()->user()->id)->update([
                    'rank' => 'Chief'
                ]);

                Transaction::insert([
                    'user_id' => auth()->user()->id,
                    'trx_type' => '+',
                    'remark' => 'Mobile',
                    'details' => 'Congratulations, "You have won the Chief reward"'
                ]);
                Transaction::insert([
                    'user_id' => 1,
                    'trx_type' => '-',
                    'remark' => 'Mobile',
                    'details' => $user->username . ' have won the Chief reward'
                ]);
            }
        } elseif ($ue->paid_right >= 64 && $ue->paid_left >= 64) {

            $user = User::where('id', auth()->user()->id)->first();
            if ($user->rank != 'Executive') {
                User::where('id', auth()->user()->id)->update([
                    'rank' => 'Executive'
                ]);

                Transaction::insert([
                    'user_id' => auth()->user()->id,
                    'trx_type' => '+',
                    'remark' => 'DSP Voucher',
                    'details' => 'Congratulations, "You have won the Executive reward"'
                ]);
                Transaction::insert([
                    'user_id' => 1,
                    'remark' => 'DSP Voucher',
                    'trx_type' => '-',
                    'details' => $user->username . ' have won the Executive reward'
                ]);
            }
        } elseif ($ue->paid_right >= 32 && $ue->paid_left >= 32) {
            $user = User::where('id', auth()->user()->id)->first();
            if ($user->rank != 'Director') {
                User::where('id', auth()->user()->id)->update([
                    'rank' => 'Director'
                ]);

                Transaction::insert([
                    'user_id' => auth()->user()->id,
                    'remark' => 3200,
                    'trx_type' => '+',
                    'details' => 'Congratulations, "You have won the Director reward"'
                ]);
                Transaction::insert([
                    'user_id' => 1,
                    'remark' => 3200,
                    'trx_type' => '-',
                    'details' => $user->username . ' have won the Director reward'
                ]);
            }
        } elseif ($ue->paid_right >= 1 && $ue->paid_left >= 1) {
            $user = User::where('id', auth()->user()->id)->first();
            if ($user->rank != 'Master') {
                User::where('id', auth()->user()->id)->update([
                    'rank' => 'Master'
                ]);

                Transaction::insert([
                    'user_id' => auth()->user()->id,
                    'trx_type' => '+',
                    'remark' => 'Pair Earning',
                    'details' => 'Congratulations, "You have won the Master reward"'
                ]);
                Transaction::insert([
                    'user_id' => 1,
                    'trx_type' => '-',
               
                    'remark' => 'Pair Earning',
                    'details' => $user->username . ' have won the Master reward'
                ]);
            }
        } else {
            User::where('id', auth()->user()->id)->update([
                'rank' => 'User'
            ]);
        }
        return $ue;
    }

    public function profile()
    {
        $pageTitle = "Profile Setting";
        $user = Auth::user();
        return view($this->activeTemplate . 'user.profile_setting', compact('pageTitle', 'user'));
    }
    public function submitProfile(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'address' => 'sometimes|required|max:80',
            'state' => 'sometimes|required|max:80',
            'zip' => 'sometimes|required|max:40',
            'city' => 'sometimes|required|max:50',
            'image' => ['image', new FileTypeValidate(['jpg', 'jpeg', 'png'])]
        ], [
            'firstname.required' => 'First name field is required',
            'lastname.required' => 'Last name field is required'
        ]);

        $user = Auth::user();

        $in['firstname'] = $request->firstname;
        $in['lastname'] = $request->lastname;

        $in['address'] = [
            'address' => $request->address,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$user->address->country,
            'city' => $request->city,
        ];


        if ($request->hasFile('image')) {
            $location = imagePath()['profile']['user']['path'];
            $size = imagePath()['profile']['user']['size'];
            $filename = uploadImage($request->image, $location, $size, $user->image);
            $in['image'] = $filename;
        }
        $user->fill($in)->save();
        $notify[] = ['success', 'Profile updated successfully.'];
        return back()->withNotify($notify);
    }

    public function changePassword()
    {
        $pageTitle = 'Change password';
        return view($this->activeTemplate . 'user.password', compact('pageTitle'));
    }

    public function submitPassword(Request $request)
    {

        $password_validation = Password::min(6);
        $general = GeneralSetting::first();
        if ($general->secure_password) {
            $password_validation = $password_validation->mixedCase()->numbers()->symbols()->uncompromised();
        }

        $this->validate($request, [
            'current_password' => 'required',
            'password' => ['required', 'confirmed', $password_validation]
        ]);


        try {
            $user = auth()->user();
            if (Hash::check($request->current_password, $user->password)) {
                $password = Hash::make($request->password);
                $user->password = $password;
				DB::connection('mysql_store')->table('users')->where('name', $user->username)->update([
                    'password' => $password
                ]);
                DB::connection('mysql_store')->table('sellers')->where('dds_username', $user->username)->update([
                    'password' => $password
                ]);
                $user->save();
                $notify[] = ['success', 'Password changes successfully.'];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', 'The password doesn\'t match!'];
                return back()->withNotify($notify);
            }
        } catch (\PDOException $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }
    }

    /*
     * Deposit History
     */
    public function depositHistory()
    {
        $pageTitle = 'Deposit History';
        $emptyMessage = 'No history found.';
        $logs = auth()->user()->deposits()->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'emptyMessage', 'logs'));
    }

    /*
     * Withdraw Operation
     */

    public function withdrawMoney()
    {
        $productCheck = DB::connection('mysql_store')->table('order_details')
        ->leftJoin('orders', 'orders.id', '=','order_details.order_id')
        ->whereIn('order_details.product_id', [43, 44, 48])
        ->where('orders.payment_status', 'paid')
        ->where('orders.customer_dds', auth()->user()->username)
        ->exists();
        if(!$productCheck && auth()->user()->plan_id == 0) {
            $notify[] = ['error', 'Please Purchase a DSP Plan to access this Page!'];
            return redirect()->route('user.plan.index')->withNotify($notify);
        }
		
    
        $withdrawMethod = WithdrawMethod::where('status', 1)->get();
        $pageTitle = 'Withdraw Money';
        return view($this->activeTemplate . 'user.withdraw.methods', compact('pageTitle', 'withdrawMethod'));
    }

    public function withdrawStore(Request $request)
    {
        $this->validate($request, [
            'method_code' => 'required',
            'amount' => 'required|numeric'
        ]);
        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->firstOrFail();
        $user = auth()->user();
        if ($request->amount < $method->min_limit) {
            $notify[] = ['error', 'Your requested amount is smaller than minimum amount.'];
            return back()->withNotify($notify);
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = ['error', 'Your requested amount is larger than maximum amount.'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $user->balance) {
            $notify[] = ['error', 'You do not have sufficient balance for withdraw.'];
            return back()->withNotify($notify);
        }


        $charge = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;
        $finalAmount = $afterCharge * $method->rate;

        $withdraw = new Withdrawal();
        $withdraw->method_id = $method->id; // wallet method ID
        $withdraw->user_id = $user->id;
        $withdraw->amount = $request->amount;
        $withdraw->currency = $method->currency;
        $withdraw->rate = $method->rate;
        $withdraw->charge = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->trx = getTrx();
        $withdraw->save();
        session()->put('wtrx', $withdraw->trx);
        return redirect()->route('user.withdraw.preview');
    }

    public function withdrawPreview()
    {
        $withdraw = Withdrawal::with('method', 'user')->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id', 'desc')->firstOrFail();
        $pageTitle = 'Withdraw Preview';
        return view($this->activeTemplate . 'user.withdraw.preview', compact('pageTitle', 'withdraw'));
    }


    public function withdrawSubmit(Request $request)
    {
        $general = GeneralSetting::first();
        $withdraw = Withdrawal::with('method', 'user')->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id', 'desc')->firstOrFail();

        $rules = [];
        $inputField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($withdraw->method->user_data as $key => $cus) {
                $rules[$key] = [$cus->validation];
                if ($cus->type == 'file') {
                    array_push($rules[$key], 'image');
                    array_push($rules[$key], new FileTypeValidate(['jpg', 'jpeg', 'png']));
                    array_push($rules[$key], 'max:2048');
                }
                if ($cus->type == 'text') {
                    array_push($rules[$key], 'max:191');
                }
                if ($cus->type == 'textarea') {
                    array_push($rules[$key], 'max:300');
                }
                $inputField[] = $key;
            }
        }

        $this->validate($request, $rules);

        $user = auth()->user();
        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }
        }


        if ($withdraw->amount > $user->balance) {
            $notify[] = ['error', 'Your request amount is larger then your current balance.'];
            return back()->withNotify($notify);
        }

        $directory = date("Y") . "/" . date("m") . "/" . date("d");
        $path = imagePath()['verify']['withdraw']['path'] . '/' . $directory;
        $collection = collect($request);
        $reqField = [];
        if ($withdraw->method->user_data != null) {
            foreach ($collection as $k => $v) {
                foreach ($withdraw->method->user_data as $inKey => $inVal) {
                    if ($k != $inKey) {
                        continue;
                    } else {
                        if ($inVal->type == 'file') {
                            if ($request->hasFile($inKey)) {
                                try {
                                    $reqField[$inKey] = [
                                        'field_name' => $directory . '/' . uploadImage($request[$inKey], $path),
                                        'type' => $inVal->type,
                                    ];
                                } catch (\Exception $exp) {
                                    $notify[] = ['error', 'Could not upload your ' . $request[$inKey]];
                                    return back()->withNotify($notify)->withInput();
                                }
                            }
                        } else {
                            $reqField[$inKey] = $v;
                            $reqField[$inKey] = [
                                'field_name' => $v,
                                'type' => $inVal->type,
                            ];
                        }
                    }
                }
            }
            $withdraw['withdraw_information'] = $reqField;
        } else {
            $withdraw['withdraw_information'] = null;
        }


        $withdraw->status = 2;
        $withdraw->save();
        $user->balance  -=  $withdraw->amount;
        $user->save();



        $transaction = new Transaction();
        $transaction->user_id = $withdraw->user_id;
        $transaction->amount = $withdraw->amount;
        $transaction->post_balance = $user->balance;
        $transaction->charge = $withdraw->charge;
        $transaction->trx_type = '-';
        $transaction->details = showAmount($withdraw->final_amount) . ' ' . $withdraw->currency . ' Withdraw Via ' . $withdraw->method->name;
        $transaction->trx =  $withdraw->trx;
        $transaction->remark = 'withdraw';
        $transaction->save();

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New withdraw request from ' . $user->username;
        $adminNotification->click_url = urlPath('admin.withdraw.details', $withdraw->id);
        $adminNotification->save();

        notify($user, 'WITHDRAW_REQUEST', [
            'method_name' => $withdraw->method->name,
            'method_currency' => $withdraw->currency,
            'method_amount' => showAmount($withdraw->final_amount),
            'amount' => showAmount($withdraw->amount),
            'charge' => showAmount($withdraw->charge),
            'currency' => $general->cur_text,
            'rate' => showAmount($withdraw->rate),
            'trx' => $withdraw->trx,
            'post_balance' => showAmount($user->balance),
            'delay' => $withdraw->method->delay
        ]);

        $notify[] = ['success', 'Withdraw request sent successfully'];
        return redirect()->route('user.withdraw.history')->withNotify($notify);
    }

    public function withdrawLog()
{
    $pageTitle = "Withdraw Log";
    $withdraws = Withdrawal::where('user_id', Auth::id())->where('status', '!=', 0)->with('method')->orderBy('id', 'desc')->paginate(getPaginate());
    $emptyMessage = "No Data Found!";
    return view($this->activeTemplate . 'user.withdraw.log', compact('pageTitle', 'withdraws', 'emptyMessage'));
}




    public function show2faForm()
    {
        $general = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->sitename, $secret);
        $pageTitle = 'Two Factor';
        return view($this->activeTemplate . 'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts = 1;
            $user->save();
            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_ENABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
            ]);
            $notify[] = ['success', 'Google authenticator enabled successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }


    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts = 0;
            $user->save();
            $userAgent = getIpInfo();
            $osBrowser = osBrowser();
            notify($user, '2FA_DISABLE', [
                'operating_system' => @$osBrowser['os_platform'],
                'browser' => @$osBrowser['browser'],
                'ip' => @$userAgent['ip'],
                'time' => @$userAgent['time']
            ]);
            $notify[] = ['success', 'Two factor authenticator disable successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }


    public function indexTransfer()
    {
        $pageTitle = 'Balance Transfer';
        return view($this->activeTemplate . '.user.balanceTransfer', compact('pageTitle'));
    }

    public function balanceTransfer(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'amount' => 'required|numeric|min:0',
        ]);
        $gnl = GeneralSetting::first();
        $user = User::find(Auth::id());
        $trans_user = User::where('username', $request->username)->orWhere('email', $request->username)->first();
        
        
        if ($trans_user == '') {
            $notify[] = ['error', 'Username Not Found'];
            return back()->withNotify($notify);
        }
        if ($trans_user->username == $user->username) {
            $notify[] = ['error', 'Own Account Balance Transfers are not Allowed.'];
            return back()->withNotify($notify);
        }
        if ($trans_user->email == $user->email) {
            $notify[] = ['error', 'Own Account Balance Transfers are not Allowed.'];
            return back()->withNotify($notify);
        }
        if ($trans_user->member_ship == 0) {
            $notify[] = ['error', 'User-to-User Balance Transfers are not Allowed.'];
            return back()->withNotify($notify);
        }
        if ($trans_user->member_ship == 1 && $user->member_ship == 1) {
            $notify[] = ['error', 'Seller-to-Seller Balance Transfers are not Allowed.'];
            return back()->withNotify($notify);
        }

        $charge = $gnl->bal_trans_fixed_charge + (($request->amount * $gnl->bal_trans_per_charge) / 100);
        $amount = $request->amount + $charge;
        if ($user->balance >= $amount) {
            $user->balance -= $amount;
            $user->save();

            $trx = getTrx();

            Transaction::create([
                'trx' => $trx,
                'user_id' => $user->id,
                'trx_type' => '-',
                'remark' => 'balance_transfer',
                'details' => 'Balance Transferred To ' . $trans_user->username,
                'amount' => getAmount($request->amount),
                'post_balance' => getAmount($user->balance),
                'charge' => $charge
            ]);

            notify($user, 'BAL_SEND', [
                'amount' => getAmount($request->amount),
                'username' => $trans_user->username,
                'trx' => $trx,
                'currency' => $gnl->cur_text,
                'charge' => getAmount($charge),
                'balance_now' => getAmount($user->balance),
            ]);

            $trans_user->balance += $request->amount;
            $trans_user->save();

            Transaction::create([
                'trx' => $trx,
                'user_id' => $trans_user->id,
                'remark' => 'balance_receive',
                'details' => 'Balance receive From ' . $user->username,
                'amount' => getAmount($request->amount),
                'post_balance' => getAmount($trans_user->balance),
                'charge' => 0,
                'trx_type' => '+'
            ]);

            notify($trans_user, 'BAL_RECEIVE', [
                'amount' => getAmount($request->amount),
                'currency' => $gnl->cur_text,
                'trx' => $trx,
                'username' => $user->username,
                'charge' => 0,
                'balance_now' => getAmount($trans_user->balance),
            ]);

            $notify[] = ['success', 'Balance Transferred Successfully.'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Insufficient Balance.'];
            return back()->withNotify($notify);
        }
    }


    public function searchUser(Request $request)
    {
        $trans_user = User::where('username', $request->username)->orwhere('email', $request->username)->first();
        if (!empty($trans_user)) {
            return response()->json(['success' => true, 'fullname' => $trans_user->firstname.' '.$trans_user->lastname]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function purchase(Request $request)
    {


        $user = User::where('id', auth()->user()->id)->first();

        $complete_address = '';

        if ($request->check == 'new') {
            if ($request->address == null) {
                return redirect()->back()->with('err', 'Please provide your current address');
            }
            $complete_address = $request->address;
        } elseif ($request->check == 'prev') {
            $array = json_decode(json_encode($user->address), true);
            $address = $array['address'];
            $country = $array['country'];
            $city = $array['city'];
            $state = $array['state'];
            $complete_address = $address . ', ' . $city . ', ' . $state . ', ' . $country;
        }


        $request->validate([
            'quantity' => 'required|integer|gt:0',
            'product_id' => 'required|integer|gt:0'
        ]);
        $product = Product::hasCategory()->where('status', 1)->findOrFail($request->product_id);
        if ($request->quantity > $product->quantity) {
            $notify[] = ['error', 'Requested quantity is not available in stock'];
            return back()->withNotify($notify);
        }
        $user = auth()->user();
        $totalPrice = $product->price * $request->quantity;
        if ($user->balance < $totalPrice) {
            $notify[] = ['error', 'Balance is not sufficient'];
            return back()->withNotify($notify);
        }

        if ($product->bv_per != 0) {
            $bvper = ($product->price * $product->bv_per) / 100;
        } else {
            $bvper = $product->bv;
        }



        $user->balance -= ($totalPrice - $bvper);
        $user->bv += $bvper;
        $user->pv += $product->pv;
        $user->save();



        $product->quantity -= $request->quantity;
        $product->save();

        $ref_bonus = 0;
        if ($product->shop_reference != null)
            $ref_bonus = $product->shop_reference;

        $sponser = User::find($user->ref_id);
        $sponser->balance += $ref_bonus;
        $sponser->shop_reference += $ref_bonus;
        $sponser->save();


        //customer statements
        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $totalPrice;
        $transaction->post_balance = $user->balance;
        $transaction->charge = 0;
        $transaction->trx_type = '-';
        $transaction->details = $product->name . ' item purchase';
        $transaction->trx =  getTrx();
        $transaction->save();

        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $product->bv;


        $transaction->trx_type = '+';
        $transaction->details = 'you have received ' . $product->bv . ' BV from online store';
        //$transaction->trx =  getTrx();
        $transaction->save();


        $transaction = new Transaction();
        $transaction->user_id = $user->id;
        $transaction->amount = $product->pv;


        $transaction->trx_type = '+';
        $transaction->details = 'you have received ' . $product->pv . ' PV from online store';
        //$transaction->trx =  getTrx();
        $transaction->save();


        //ref bonus statement
        if ($ref_bonus != 0) {
            $transaction = new Transaction();
            $transaction->user_id = $user->ref_id;
            $transaction->amount = $ref_bonus;


            $transaction->trx_type = '+';
            $transaction->details = 'you have received ' . $ref_bonus . ' Shop Reference Bonus from online store';
            //$transaction->trx =  getTrx();
            $transaction->save();
        }

        $order = new Order();
        $order->user_id = $user->id;
        $order->product_id = $product->id;
        $order->quantity = $request->quantity;
        $order->price = $product->price;
        $order->total_price = $totalPrice;
        $order->address = $complete_address;
        $order->trx = $transaction->trx;
        $order->status = 0;
        $order->save();

        $general = GeneralSetting::first();
        notify($user, 'order_placed', [
            'product_name' => $product->name,
            'quantity' => $request->quantity,
            'price' => showAmount($product->price),
            'total_price' => showAmount($totalPrice),
            'currency' => $general->cur_text,
            'trx' => $transaction->trx,
        ]);

        $notify[] = ['success', 'Order placed successfully'];
        return back()->withNotify($notify);
    }

    public function orders()
    {
        $pageTitle = 'Orders';
       
        $orders = Order::where('user_id', auth()->user()->id)->with('product')->orderBy('id', 'desc')->paginate(getPaginate());
        $orders = DB::connection('mysql_store')->table('orders')
        //->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
        ->leftJoin('users', 'users.id', '=', 'orders.customer_id')
       
        ->where('users.name', auth()->user()->username)
        ->select(['orders.*','orders.id as o_id', 'users.*'])
        ->orderBy('orders.id', 'DESC')
        ->paginate(); 
        return view($this->activeTemplate . 'user.orders', compact('pageTitle', 'orders'));
    }


    public function display_rewards()
    {
        $productCheck = DB::connection('mysql_store')->table('order_details')
        ->leftJoin('orders', 'orders.id', '=','order_details.order_id')
        ->whereIn('order_details.product_id', [43, 44, 48])
        ->where('orders.payment_status', 'paid')
        ->where('orders.customer_dds', auth()->user()->username)
        ->exists();
        if(!$productCheck && auth()->user()->plan_id == 0) {
            $notify[] = ['error', 'Please Purchase a DSP Plan to access this Page!'];
            return redirect()->route('user.plan.index')->withNotify($notify);
        }
        $pageTitle = 'Rewards';

        $user = UserExtra::where('user_id', auth()->id())->first();
        $minusNo = 0;
        $greaterNo = 0;
        $pairs = 0;

        if ($user->paid_left > $user->paid_right) {
            $minusNo = $user->paid_left - $user->paid_right;
            $greaterNo = $user->paid_left;
            $pairs = $greaterNo - $minusNo;
        } elseif ($user->paid_left < $user->paid_right) {
            $minusNo =  $user->paid_right - $user->paid_left;
            $greaterNo = $user->paid_right;
            $pairs = $greaterNo - $minusNo;
        } else {
            $pairs = $user->paid_right;
        }
        return view('templates/basic/user/rewards', compact('pageTitle', 'pairs'));
    }
	
	public function lucky_draw()
    {
        $pageTitle = 'Lucky Draw PROMO';
        $emptyMessage = 'No records found.';
		
	
		
		$myRefs = User::where('ref_id', auth()->user()->id)->pluck('username')->toArray();
		$uniqueUsernames = DB::connection('mysql')->table('dlp_serial')->whereIn('username', $myRefs)->get();
		$allOrders = $uniqueUsernames->count();
	
        return view('templates/basic/user/lucky_draw', compact('pageTitle', 'emptyMessage','allOrders', 'uniqueUsernames'));
    }
	
	public function lucky_draw_myorders()
    {
        $pageTitle = 'Lucky Draw PROMO';
        $emptyMessage = 'No records found.';
		$ldpsCount = DB::connection('mysql')->table('dlp_serial')->where('username', auth()->user()->username)->count();
		$ldps = DB::connection('mysql')->table('dlp_serial')->where('username', auth()->user()->username)->paginate(10);
		return view('templates/basic/user/lucky_draw_myorders', compact('pageTitle', 'emptyMessage','ldps','ldpsCount'));
    }

    public function price_list()
    {
        $pageTitle = 'Price List';
        $emptyMessage = 'No records found.';
        $price_lists = DB::table('price_list')->latest()->paginate(getPaginate());
        return view('templates/basic/user/price_list', compact('pageTitle', 'emptyMessage', 'price_lists'));
    }


    public function showEmailForgetForm()
    {
        $pageTitle = "Find Email Address";
        return view('templates/basic/user.find_email', compact('pageTitle'));
    }
    public function showEmailUsername(Request $request)
    {
        $email = '';
        $username = '';
        $user = User::where('cnicnumber', $request->cnic)->first();
        if (!empty($user)) {
            $email = $user->email;
            $username = $user->username;

            return response()->json([
                'status' => 200,
                'email' => $email,
                'username' => $username
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'User not found!'
            ]);
        }
    }

    public function shops_franchises(){
        $pageTitle = 'Shops & Franchises';
        $emptyMessage = 'No precords found.';
        $users = User::leftJoin('memberships','memberships.id','users.membership_id')
        ->where('users.member_ship',1)->paginate();
        $shops = User::where('membership_id',1)->count();
        $franchises = User::where('membership_id',2)->count();
        return view('templates/basic/user/shops_franchises', compact('pageTitle', 'emptyMessage','users','shops','franchises'));
    }
	
	
	
	
	public function storeReference(Request $request)
    {
        if ($request->search) {
            $references = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'store_reference')->where('user_id', auth()->user()->id)
				->whereDate('created_at', $request->search)
                ->paginate();
        } else {
            $references = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'store_reference')->where('user_id', auth()->user()->id)
                ->paginate();
        }
        $pageTitle = 'Store References';
        $emptyMessage = 'No records found';
        return view($this->activeTemplate . 'user.details.storeReference', compact('pageTitle', 'emptyMessage', 'references'));
    }
    public function storeBonus(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'store_bonus')->where('user_id', auth()->user()->id)
				->whereDate('created_at', $request->search)
                ->paginate();
        } else {
            $bonuses = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'store_bonus')->where('user_id', auth()->user()->id)
                ->paginate();
        }
        $pageTitle = 'Store Bonus';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.storeBonus', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }
    public function reference(Request $request)
    {
        if ($request->search) {
             $references = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'reference_bonus')->where('user_id', auth()->user()->id)
				 ->whereDate('created_at', $request->search)
                ->paginate();
        } else {
            $references = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'reference_bonus')->where('user_id', auth()->user()->id)
                ->paginate();
        }

        $pageTitle = 'Reference Bonus';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.reference', compact('pageTitle', 'emptyMessage', 'references'));
    }
    public function city_reference(Request $request)
    {
        if ($request->search) {
           $references = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'city_reference')->where('user_id', auth()->user()->id)
			   ->whereDate('created_at', $request->search)
                ->paginate();
        } else {
            $references = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'city_reference')->where('user_id', auth()->user()->id)
                ->paginate();
        }

        $pageTitle = 'City Reference';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.city_reference', compact('pageTitle', 'emptyMessage', 'references'));
    }
    public function franchise_bonus(Request $request)
    {
        if ($request->search) {
            $references = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'franchise_bonus')->where('user_id', auth()->user()->id)
				->whereDate('created_at', $request->search)
                ->paginate();
        } else {
              $references = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'franchise_bonus')->where('user_id', auth()->user()->id)
                ->paginate();
        }

        $pageTitle = 'Franchise Bonus';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.franchise_bonus', compact('pageTitle', 'emptyMessage', 'references'));
    }
    public function franchise_ref_bonus(Request $request)
    {
        if ($request->search) {
           $references = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'franchise_ref_bonus')->where('user_id', auth()->user()->id)
			   ->whereDate('created_at', $request->search)
                ->paginate();
        } else {
             $references = DB::table('transactions')
                ->where('amount', '<>', 0)->where('remark', 'franchise_ref_bonus')->where('user_id', auth()->user()->id)
                ->paginate();
        }

        $pageTitle = 'Franchise Reference Bonus';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.franchise_ref_bonus', compact('pageTitle', 'emptyMessage', 'references'));
    }

    public function stockistReferenceBonus(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('transactions')
				->where('amount', '<>', 0)->where('remark', 'stockist_ref_bonus')->where('user_id', auth()->user()->id)
           ->whereDate('created_at', $request->search)
                ->paginate();
        } else {
           $bonuses = DB::table('transactions')
				->where('amount', '<>', 0)->where('remark', 'stockist_ref_bonus')->where('user_id', auth()->user()->id)
           
                ->paginate();
        }
        $pageTitle = 'Stockist Reference Bonus';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.stockistReferenceBonus', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }
    public function stockistBonus(Request $request)
    {
        if ($request->search) {
           $bonuses = DB::table('transactions')
				->where('amount', '<>', 0)->where('remark', 'stockist_bonus')->where('user_id', auth()->user()->id)
           ->whereDate('created_at', $request->search)
                ->paginate();
        } else {
           $bonuses = DB::table('transactions')
				->where('amount', '<>', 0)->where('remark', 'stockist_bonus')->where('user_id', auth()->user()->id)
           
                ->paginate();
        }
        $pageTitle = 'Stockist Bonus';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.stockistBonus', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }
    public function pv(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('transactions')
			->where('remark', 'pv')->where('user_id', auth()->user()->id)
           ->whereDate('created_at', $request->search)
                ->paginate();
        } else {
  $bonuses = DB::table('transactions')
			->where('remark', 'pv')->where('user_id', auth()->user()->id)
           
                ->paginate();
        }

        $pageTitle = 'PV';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.pv', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }


    public function bv(Request $request)
    {
		//return $request->search;
        if ($request->search) {
             $bonuses = DB::table('transactions')
				->where('amount', '<>', 0)->where('remark', 'bv_bonus')->whereDate('created_at', $request->search)
				 ->where('user_id', auth()->user()->id)
           		->paginate();
        } else {
           $bonuses = DB::table('transactions')
				->where('amount', '<>', 0)->where('remark', 'bv_bonus')->where('user_id', auth()->user()->id)
           
                ->paginate();
        }
        $pageTitle = 'BV';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.bv', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }
    public function EPinCredit(Request $request)
    {
        if ($request->search) {
           $transactions = DB::table('transactions')
				->where('amount', '<>', 0)->where('remark', 'epin_credit')->where('user_id', auth()->user()->id)
           ->whereDate('created_at', $request->search)
                ->paginate();
        } else {
        $transactions = DB::table('transactions')
				->where('amount', '<>', 0)->where('remark', 'epin_credit')->where('user_id', auth()->user()->id)
           
                ->paginate();
        }
        $pageTitle = 'E Pin Credits';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.EPinCredit', compact('pageTitle', 'emptyMessage', 'transactions'));
    }

    public function shopReferenceBonus(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('users')
                ->where('shop_reference', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $bonuses = DB::table('users')
                ->where('shop_reference', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'shop Reference Bonus';
        $emptyMessage = 'No records found';
        return view('admin.details.shopReferenceBonus', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }

    public function balance(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('users')
                ->where('balance', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $bonuses = DB::table('users')
                ->where('balance', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'Balance';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.balance', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }
    public function dsp_ref_bonus(Request $request)
    {
        if ($request->search) {
             
			$transactions = Transaction::where('remark', 'referral_commission')
				->whereDate('created_at', $request->search)
				->where('user_id', auth()->user()->id)->paginate();
        } else {
            $transactions = Transaction::where('remark', 'referral_commission')->where('user_id', auth()->user()->id)->paginate();
        }
    
        $pageTitle = 'DSP Reference Bonus';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.dsp', compact('pageTitle', 'emptyMessage', 'transactions'));
    }
	
	    
	public function productPartnerBonus(Request $request)
    {
        if ($request->search) {
             
			$transactions = Transaction::where('remark', 'product_partner_bonus')
				->whereDate('created_at', $request->search)
				->where('user_id', auth()->user()->id)->paginate();
        } else {
            $transactions = Transaction::where('remark', 'product_partner_bonus')->where('user_id', auth()->user()->id)->paginate();
        }
    
        $pageTitle = 'Product Partner Bonus';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.product_partner_bonus', compact('pageTitle', 'emptyMessage', 'transactions'));
    }
	
    public function pairs_bonus(Request $request)
    {
         
            $pairs = DB::table('users')
                ->leftJoin('user_extras', 'user_extras.user_id', '=', 'users.id')
				->where('user_id', auth()->user()->id)
                ->orderBy('users.id', 'DESC')
                ->paginate();
        
        $pageTitle = 'Pairs';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.pairs_bonus', compact('pageTitle', 'emptyMessage', 'pairs'));
    }
    public function total_invest(Request $request)
    {

		if ($request->search) {
        $transactions = Transaction::where('remark', 'purchased_plan')
			 ->whereDate('created_at', $request->search)
			->where('user_id', auth()->user()->id)->paginate();
		} else  {
        $transactions = Transaction::where('remark', 'purchased_plan')->where('user_id', auth()->user()->id)->paginate();

		}
        

        $pageTitle = 'Wallet Statements';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.walletStatements', compact('pageTitle', 'emptyMessage', 'transactions'));
    }
	
	
	public function deposit(Request $request)
    {

	if ($request->search) {
        $transactions = Transaction::where('details','like', "%deposit via%")
			->whereDate('created_at', $request->search)
			->where('user_id', auth()->user()->id)->paginate();
} else  {
      $transactions = Transaction::where('details','like', "%deposit via%")->where('user_id', auth()->user()->id)->paginate();
	}

        $pageTitle = 'Deposits';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.deposit', compact('pageTitle', 'emptyMessage', 'transactions'));
    }
	
	
	public function total_withdraw(Request $request)
    {

if ($request->search) {
        $transactions = Transaction::where('details','like', "%withdraw via%")
			->whereDate('created_at', $request->search)
			->where('user_id', auth()->user()->id)->paginate();
} else {
        $transactions = Transaction::where('details','like', "%withdraw via%")->where('user_id', auth()->user()->id)->paginate();
}
        $pageTitle = 'Total Withdraw';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.total_withdraw', compact('pageTitle', 'emptyMessage', 'transactions'));
    }
	
	public function completed_withdraw(Request $request)
    {

if ($request->search) {
        $transactions = Transaction::where('remark',"completed_withdraw")
			->whereDate('created_at', $request->search)
			->where('user_id', auth()->user()->id)->paginate();
} else {
	 $transactions = Transaction::where('remark',"completed_withdraw")->where('user_id', auth()->user()->id)->paginate();
}

        $pageTitle = 'Completed Withdraw';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.completed_withdraw', compact('pageTitle', 'emptyMessage', 'transactions'));
    }
		
	public function pending_withdraw(Request $request)
    {

if ($request->search) {
        $transactions = Transaction::where('remark',"pending_withdraw")
			->whereDate('created_at', $request->search)
			->where('user_id', auth()->user()->id)->paginate();
} else {
	 $transactions = Transaction::where('remark',"pending_withdraw")->where('user_id', auth()->user()->id)->paginate();
}
        $pageTitle = 'Pending Withdraw';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.pending_withdraw', compact('pageTitle', 'emptyMessage', 'transactions'));
    }
	
	public function rejected_withdraw(Request $request)
    {

if ($request->search) {
        $transactions = Transaction::where('remark',"rejected_withdraw")
			->whereDate('created_at', $request->search)
			->where('user_id', auth()->user()->id)->paginate();
} else {
  $transactions = Transaction::where('remark',"rejected_withdraw")->where('user_id', auth()->user()->id)->paginate();
}

        $pageTitle = 'Rejected Withdraw';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.rejected_withdraw', compact('pageTitle', 'emptyMessage', 'transactions'));
    }


    public function promo(Request $request)
    {
        if ($request->search) {
          $promo = DB::table('transactions')
               ->where('amount', '<>', 0)->where('remark', 'promo')->where('user_id', auth()->user()->id)
			  ->whereDate('created_at', $request->search)
                ->paginate();
        } else {
              $promo = DB::table('transactions')
               ->where('amount', '<>', 0)->where('remark', 'promo')->where('user_id', auth()->user()->id)
                ->paginate();
        }
        $pageTitle = 'Promo';
        $emptyMessage = 'No records found';
        return view('templates.basic.user.details.promo', compact('pageTitle', 'emptyMessage', 'promo'));
    }
}
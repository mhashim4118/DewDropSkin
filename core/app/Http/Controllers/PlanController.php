<?php

namespace App\Http\Controllers;

use App\Models\BvLog;
use App\Models\AdminNotification;
use App\Models\GeneralSetting;
use App\Models\Plan;
use App\Models\Stockist;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserLogin;
use App\Models\Stocklist_buy;
use App\Models\Membership;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    public function __construct()
    {
        $this->activeTemplate = activeTemplate();
    }

    function planIndex()
    {
        $pageTitle = "Plans";
        $plans = Plan::where('status', 1)->get();
        return view($this->activeTemplate . '.user.plan', compact('pageTitle', 'plans'));
    }

    function planStore(Request $request)
    {	
		
        $this->validate($request, ['plan_id' => 'required|integer']);
        $plan = Plan::where('id', $request->plan_id)->where('status', 1)->firstOrFail();
        
        $gnl = GeneralSetting::first();
        $user = auth()->user();
        $payment = 0;
        $epin_credit=0;
        $stockist_bonus=0;
        $refferal_bonus=0;
        $is_bonus=0;
     
        if($request->fav_language =='bank_account'){
            $aw = DB::table('admin_wallets')->where('id', 1)->first();
            $byWallet = $aw->byWallet;
           
            DB::table('admin_wallets')->where('id', 1)->update([
                'byWallet' => ++$byWallet
            ]);
        }
        if($request->fav_language=='epin'){

            if($request->sponser_id==""){
                $notify[] = ['error', 'E-Pin is required'];
                return back()->withNotify($notify);
            }
            $e_pin= Stocklist_buy::where('e_pin',$request->sponser_id)->first();
    
		
            if(empty($e_pin)){
                $notify[] = ['error', 'This E-Pin does not exists'];
                return back()->withNotify($notify);
            }
            
            if($e_pin->used==1){
                $notify[] = ['error', 'This E-Pin has been already used'];
                return back()->withNotify($notify);
            }
            else{
				
                $stockist = Stockist::where('id',$e_pin->stockists_id)->first();
			
				
                if(!empty($stockist)) {
               
                $e_pin->used = 1;
                $e_pin->used_at = auth()->user()->id;
                $e_pin->save();

                $u = User::where('id', $e_pin->user_id)->first();
                $u->stockist_bonus += $stockist->stockist_bonus;
			   
                $u->balance += $stockist->stockist_bonus;
				
                $u->save();

                $u_ref = User::where('id', $u->ref_id)->first();
                $u_ref->stockist_ref_bonus += $stockist->refferal_bonus;
                $u_ref->balance += $stockist->refferal_bonus;
                $u_ref->save();
                if($stockist->stockist_bonus != 0){
                Transaction::insert([
                    'user_id' => $u->id,
                    'amount' => $stockist->stockist_bonus,
                    'post_balance' =>$u->balance,
                    'trx' => getTrx(),
                    'trx_type' => '+',
                    'remark' => 'stockist_bonus',
                    'details' => "You have received ".$stockist->stockist_bonus." PKR Stockist bonus when ".$user->username." used your E-Pin"
                ]);}
				if($stockist->refferal_bonus != 0){
                Transaction::insert([
                    'user_id' => $u_ref->id,
                    'amount' => $stockist->refferal_bonus,
                    'post_balance' =>$u_ref->balance,
                    'trx' => getTrx(),
                    'trx_type' => '+',
                    'remark' => 'stockist_reference_bonus',
                    'details' => "You have received ".$stockist->refferal_bonus." PKR Stockist Refferal bonus when ".$u->username."'s E-Pin is used"
                ]);
				}
                $payment = 1;
                $stockist_bonus=$stockist->stockist_bonus;
                $is_bonus=$e_pin->is_bonus;
                $refferal_bonus =$stockist->refferal_bonus;
            }
				
				elseif($e_pin->stockists_id == 0) {
               
                    $e_pin->used = 1;
                    $e_pin->used_at = auth()->user()->id;
                    $e_pin->save();
                }

                else {
                    $notify[] = ['error', 'Something went wrong'];
                    return back()->withNotify($notify);
                }
            }
        }
		
		
		
        if ($request->fav_language=='bank_account' && $user->balance < $plan->price) {
            $notify[] = ['error', 'Insufficient Balance'];
            return back()->withNotify($notify);
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
		
		if($pairs>0){
			$pairs *= 200;
			$_74per = ($pairs * 74)/100;
        	$_26per = ($pairs * 26)/100;
			
			
			$u = User::where('id', auth()->user()->id)->first();
			$u->epin_credit += $u->epin_credit;
			$u->balance += $u->balance;
			$u->save();

		 	Transaction::insert([
				'user_id' => $u->id,
				'amount' => $_74per,
				'post_balance' =>$u->balance,
				'trx' => getTrx(),
				'trx_type' => '+',
                'remark' => 'free_pair_balance',
				'details' => "You have received ".$_74per." PKR balance from your free pairs when you subscribed the plan"
			]);
							 
			Transaction::insert([
				'user_id' => $u->id,
				'amount' => $_26per,
				'post_balance' =>$u->epin_credit,
				'trx' => getTrx(),
				'trx_type' => '+',
                'remark' => 'free_pair_E_Pin_Credit',
				'details' => "You have received ".$_26per." E-Pin Credit from your free pairs when you subscribed the plan"
			]);
		}
		
		
        $bv_74per = ($plan->bv * 74)/100;
        $bv_26per = ($plan->bv * 26)/100;

        $last_dspid = User::where('username','LIKE','dsp%')->count();
        $last_nwdspid = User::where('dsp_username','LIKE','dsp%')->count();

        $oldPlan = $user->plan_id;
        $user->plan_id = $plan->id;
        $user->dsp_username = 'dsp'.($last_dspid+$last_nwdspid+1);
        if($payment==1 && $is_bonus==1){
            $n = $stockist_bonus + $refferal_bonus;
       
            $aw = DB::table('admin_wallets')->where('id', 1)->first();
            $byPV = $aw->byPV;
            $byEpinCredit = $aw->byEpinCredit;
            $byCurrentBalance = $aw->byCurrentBalance;
            if($e_pin->payment_type == 'pv'){
              
                DB::table('admin_wallets')->where('id', 1)->update([
                    'byPV' => ++$byPV
                ]);
            }
            if($e_pin->payment_type == 'bank_account'){
              
                DB::table('admin_wallets')->where('id', 1)->update([
                    'byCurrentBalance' => ++$byCurrentBalance
                ]);
            }
            if($e_pin->payment_type == 'epin_credit'){
               
                DB::table('admin_wallets')->where('id', 1)->update([
                    'byEpinCredit' => ++$byEpinCredit
                ]);
            }
            DB::table('admin_wallets')->where('id', 1)->update([
                'e_PinStockistBalance' => $aw->e_PinStockistBalance -= $n
            ]);
        }
		
	
			if($epin_credit==1){
				$user->epin_credit += ($bv_26per-$plan->price);
			}else{
				$user->epin_credit += $bv_26per;
			}
			if($request->fav_language=='bank_account'){
				$user->balance -= ($plan->price - $bv_74per);
				$user->bv += $plan->bv;
			}
			if($request->fav_language=='epin'){
				$user->balance +=  $bv_74per;
				$user->bv -= ($bv_74per);
			}
			//$user->epin_credit += $bv_26per;
			$user->total_invest += $plan->price;
		
        $user->save();

        admin_wallet_distribution();
		

        $trx = new Transaction();
        $trx->user_id = $user->id;
        $trx->amount = $plan->price;
        $trx->trx_type = '-';
        $trx->details = 'Purchased ' . $plan->name;
        $trx->remark = 'purchased_plan';
        $trx->trx = getTrx();
        $trx->post_balance = getAmount($user->balance);
        $trx->save();

        $trxn = new Transaction();
        $trxn->user_id = $user->id;
        $trxn->amount = $plan->bv;
        $trxn->trx_type = '+';
        $trxn->details = 'BV added on Purchased ' . $plan->name;
        $trxn->remark = 'bv_bonus';
        $trxn->trx = getTrx();
        $trxn->post_balance = getAmount($user->balance);
        $trxn->save();



        notify($user, 'plan_purchased', [
            'plan' => $plan->name,
            'amount' => getAmount($plan->price),
            'currency' => $gnl->cur_text,
            'trx' => $trx->trx,
            'post_balance' => getAmount($user->balance) . ' ' . $gnl->cur_text,
        ]);
        if ($oldPlan == 0) {
            updatePaidCount($user->id);
        }
        $details = auth()->user()->username . ' Subscribed to ' . $plan->name . ' plan.';

        updateBV($user->id, $plan->bv, $details);

        referralComission($user->id, $details);
        join_the_entry($user->pos_id);
        // die();
        $notify[] = ['success', 'Purchased ' . $plan->name . ' Successfully'];
        return redirect()->route('user.home')->withNotify($notify);

    }
    function planDSPStore(Request $request)
    {
        $plan = Plan::where('id', 1)->where('status', 1)->firstOrFail();
        $gnl = GeneralSetting::first();
        $user = auth()->user();

        $user1 = User::orderBy('id', 'DESC')->where('dsp_username', '<>', "")->first();
        $user2 = User::orderBy('id', 'DESC')->where('dsp_username', "")->first();
        
        $newDSP = 1;
         if(substr($user1->dsp_username, 3) > substr($user2->username, 3))
        { 
            $newDSP += $user1->id;
        }
        else {
            $newDSP += $user2->id;
        }

        $payment = 0;
        $epin_credit=0;
        $stockist_bonus=0;
        $refferal_bonus=0;
        
        if($request->transfer_type=='epin'){
			if($request->epin == ""){
                $notify[] = ['error', 'E-Pin is required'];
                return back()->withNotify($notify);
            };
			
            $e_pin= Stocklist_buy::where('e_pin',$request->epin)->first();
			if(empty($e_pin)){
                $notify[] = ['error', 'This E-Pin does not exists'];
                return back()->withNotify($notify);
            };
			  if($e_pin->used==1){
                $notify[] = ['error', 'This E-Pin has been already used'];
                return back()->withNotify($notify);
            }
            $stockist = Stockist::where('id',$e_pin->stockists_id)->first();
           
			
            if(!empty($stockist)) {
          		$e_pin->used = 1;
                $e_pin->used_at = $newDSP;
                $e_pin->save();
                $payment = 1;
              
                $u = User::where('id', $e_pin->user_id)->first();
                $u->stockist_bonus += $stockist->stockist_bonus;
                $u->balance += $stockist->stockist_bonus;
                $u->save();

                $u_ref = User::where('id', $u->ref_id)->first();
                $u_ref->stockist_ref_bonus += $stockist->refferal_bonus;
                $u_ref->balance += $stockist->refferal_bonus;
                $u_ref->save();
                
				if($stockist->stockist_bonus != 0){
                Transaction::insert([
                    'user_id' => $u->id,
                    'amount' => $stockist->stockist_bonus,
                    'post_balance' =>$u->balance,
                    'trx' => getTrx(),
                    'trx_type' => '+',
                    'remark' => 'stockist_bonus',
                    'details' => "You have received ".$stockist->stockist_bonus." PKR Stockist bonus when ".$user->username." used your E-Pin"
                ]);
				}
				if($stockist->refferal_bonus != 0){
                Transaction::insert([
                    'user_id' => $u_ref->id,
                    'amount' => $stockist->refferal_bonus,
                    'post_balance' =>$u_ref->balance,
                    'trx' => getTrx(),
                    'trx_type' => '+',
                    'remark' => 'stockist_reference_bonus',
                    'details' => "You have received ".$stockist->refferal_bonus." PKR Stockist Refferal bonus when ".$u->username."'s E-Pin is used"
                ]);
				}
				Transaction::insert([
                    'user_id' => $e_pin->user_id,
                    'amount' => $e_pin->pay_price,
                    'trx_type' => '-',
                    'remark' => 'e-pin_used',
                    'post_balance' => $u->balance,
                    'details' => 'Your E-Pin '. $e_pin->e_pin. ' has been used for purchasing DSP'
                ]);

              
            } 
        
			
			elseif($e_pin->stockists_id == 0) {
               
                    $e_pin->used = 1;
                    $e_pin->used_at =$newDSP;
                    $e_pin->save();
                }

                else {
                    $notify[] = ['error', 'Something went wrong'];
                    return back()->withNotify($notify);
                }
        }
        if ($request->transfer_type == "wallet" && $user->balance < $plan->price) {
            $notify[] = ['error', 'Insufficient Balance'];
            return back()->withNotify($notify);
        }

        $bv_74per = ($plan->bv * 74)/100;
        $bv_26per = ($plan->bv * 26)/100;
        // $ref_com_74per = ($plan->ref_com * 74)/100;
        // $ref_com_26per = ($plan->ref_com * 26)/100;

        $oldPlan = $user->plan_id;
        $user->plan_id = $plan->id;
		//$user->balance -= ($plan->price - $bv_74per);
        if($request->transfer_type=='wallet'){
            $user->balance -= $plan->price;

            $aw = DB::table('admin_wallets')->where('id', 1)->first();
            $byWallet = $aw->byWallet;
            
            DB::table('admin_wallets')->where('id', 1)->update([
                'byWallet' => ++$byWallet
            ]);
          
        }
      
        if($payment==1){

     
            $aw = DB::table('admin_wallets')->where('id', 1)->first();
            $byPV = $aw->byPV;
            $byEpinCredit = $aw->byEpinCredit;
            $byCurrentBalance = $aw->byCurrentBalance;
            if($e_pin->payment_type == 'pv'){
                
                DB::table('admin_wallets')->where('id', 1)->update([
                    'byPV' => ++$byPV
                ]);
            }
            if($e_pin->payment_type == 'bank_account'){
              
                DB::table('admin_wallets')->where('id', 1)->update([
                    'byCurrentBalance' => ++$byCurrentBalance
                ]);
            }
            if($e_pin->payment_type == 'epin_credit'){
              
                DB::table('admin_wallets')->where('id', 1)->update([
                    'byEpinCredit' => ++$byEpinCredit
                ]);
            }
            DB::table('admin_wallets')->where('id', 1)->update([
                'e_PinStockistBalance' => $aw->e_PinStockistBalance -= ($stockist_bonus + $refferal_bonus)
            ]);
        }
	
        if($epin_credit==1){
           
            $user->epin_credit += ($bv_26per-$plan->price);
        }else{
            $user->epin_credit += $bv_26per;
        }
	
        $user->total_invest += $plan->price;
        $user->save();

        $trx = new Transaction();
        $trx->user_id = $user->id;
        $trx->amount = $plan->price;
        $trx->trx_type = '-';
        $trx->details = 'Purchased ' . $plan->name;
        $trx->remark = 'purchased_plan';
        $trx->trx = getTrx();
        $trx->post_balance = getAmount($user->balance);
        $trx->save();
        $trxn = new Transaction();
        $trxn->user_id = $user->id;
        $trxn->amount = $plan->bv;
        $trxn->trx_type = '+';
        $trxn->details = 'BV added on Purchased ' . $plan->name;
        $trxn->remark = 'bv_bonus';
        $trxn->trx = getTrx();
        $trxn->post_balance = getAmount($user->balance);
        $trxn->save();

        $dspuser_reg = event(new Registered($user = $this->create($request->all())));

        admin_wallet_distribution();
        notify($user, 'plan_purchased', [
            'plan' => $plan->name,
            'amount' => getAmount($plan->price),
            'currency' => $gnl->cur_text,
            'trx' => $trx->trx,
            'post_balance' => getAmount($user->balance) . ' ' . $gnl->cur_text,
        ]);

        $details = auth()->user()->username . ' Subscribed to ' . $plan->name . ' plan.';

        referralComission($user->id, $details);

        join_the_entry($request->pos_id);
        // die();

        $notify[] = ['success', 'Purchased ' . $plan->name . ' Successfully'];
        return redirect()->route('user.my.tree')->withNotify($notify);
    }
    
    protected function create(array $data)
    {
        $general = GeneralSetting::first();

        $userCheck = User::where('username', $data['referral'])->first();
        $pos = getPosition($userCheck->id, $data['position']);

        //User Create
        $user = new User();
        $user->ref_id       = $userCheck->id;
        $user->pos_id       = $data['pos_id'];
        $user->position     = $pos['position'];
        $user->plan_id      = 1;
        $user->username = trim($data['username']);
        $user->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => isset($data['country']) ? $data['country'] : null,
            'city' => ''
        ];
        $user->status = 1;
        $user->ev = $general->ev ? 0 : 1;
        $user->sv = $general->sv ? 0 : 1;
        $user->ts = 0;
        $user->tv = 1;
        $user->save();

        $user_extras = new UserExtra();
        $user_extras->user_id = $user->id;
        $user_extras->save();
        updateDspPaidCount($user->id);

        $adminNotification = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->title = 'New dsp plan registered';
        $adminNotification->click_url = urlPath('admin.users.detail', $user->id);
        $adminNotification->save();

        return $user;
    }

    function stockistIndex()
    {
        $pageTitle = "Stockist";
        $plans = Stockist::where('status', 1)->where('id','<>',0)->get();
        return view($this->activeTemplate . '.user.stockist', compact('pageTitle', 'plans'));
    }
	
	public function stockistPurchaseFromDash(){
		$plans = Plan::where('status', 1)->get();
        $price = $plans[0]->price;
		$user = auth()->user();
		    if($user->pv >= 100){
				$user->pv = $user->pv - (100 );
				$user->save();

				$stocklist =  new Stocklist_buy;
				$stocklist->stockists_id = 0;
				$stocklist->pay_price = $price;
				$stocklist->user_id = auth()->id();
				$stocklist->e_pin = Str::random(12);
				$stocklist->payment_type = 'pv';
				$stocklist->is_bonus = 0;
				$stocklist->save();

				$trx = new Transaction();
				$trx->user_id = $user->id;
				$trx->amount = 100;
				$trx->trx_type = '-';
				$trx->details = 'You have purchased 1 E Pins of using PV '.intval(100) ;
				$trx->remark = 'purchased_E_Pin';
				$trx->trx = getTrx();
				$trx->post_balance = getAmount($user->pv);
				$trx->save();

				$notify[] = ['success', 'Purchased 1 E-pin Successfully'];
				return back()->withNotify($notify);
			} else {
				$notify[] = ['error', 'You have Less than 100 PV'];
				return back()->withNotify($notify);
			}
	}

    function stockistStore2(Request $request)
    {
        $plans = Plan::where('status', 1)->get();
        $price = $plans[0]->price;
        $this->validate($request, ['pin_qty' => 'required|integer']);
        
        $stockist = Stockist::where('id', $request->plan_id)->where('status', 1)->first();
      
        $gnl = GeneralSetting::first();
        $user = auth()->user();
        $payment_type = $request->payment_type;
   		
		
		

        if ($request->pin_qty <= 0) {
            $notify[] = ['error', 'Pin must be grater then  0 '];
            return back()->withNotify($notify);
        }

        if ($payment_type == 'bank_account' && $user->balance < ($price * $request->pin_qty)) {
            $notify[] = ['error', 'Insufficient Current  Balance'];
            return back()->withNotify($notify);
        }
        if ($payment_type == 'epin_credit' && $user->epin_credit < ($price * $request->pin_qty)) {
            $notify[] = ['error', 'Insufficient E-pin credit Balance'];
            return back()->withNotify($notify);
        }
        if ($payment_type == 'pv' && $user->pv < (100 * $request->pin_qty)) {
            $notify[] = ['error', 'Insufficient PV Balance '];
            return back()->withNotify($notify);
        }
        $user = User::find(auth()->id());
        if ($payment_type == 'bank_account') {
            $user->balance = ($user->balance - ($price * $request->pin_qty));
        }
        if ($payment_type == 'epin_credit') {
            $user->epin_credit = ($user->epin_credit - ($price * $request->pin_qty));
        }
        if ($payment_type == 'pv') {
            $user->pv = ($user->pv - (100 * $request->pin_qty));
        }

        $user->save();

        $trx = new Transaction();
        $trx->user_id = $user->id;
        $trx->amount = $price;
        $trx->trx_type = '-';
        $trx->details = 'You have purchased ' . $request->pin_qty . ' E Pins of  using' . $payment_type . intval($price) . ' PKR';
        $trx->remark = 'purchased_E_Pin';
        $trx->trx = getTrx();
        $trx->post_balance = getAmount($user->balance);
        $trx->save();
        $is_bonus = ($request->is_package == 'is_package' ? 1 : 0);
        for ($i = 0; $i < $request->pin_qty; $i++) {
            $stocklist =  new Stocklist_buy;
            if($request->plan_id != null){

                $stocklist->stockists_id = $stockist->id;
            } else {

                $stocklist->stockists_id = 0;
            }
            $stocklist->pay_price = $price;
            $stocklist->user_id = auth()->id();
            $stocklist->e_pin = Str::random(12);
            $stocklist->payment_type = $payment_type;
            $stocklist->is_bonus = $is_bonus;
            $stocklist->save();
        }

        $notify[] = ['success', 'Purchased ' . $request->qty . ' E-pins Successfully'];
        return back()->withNotify($notify);
    }

   
    function stockistlist(Request $request)
    {

        $pageTitle = "Stockist";
        $totalPin =  Stocklist_buy::where('user_id', auth()->id())->count('id');
        $usedPin =  Stocklist_buy::where(['user_id' => auth()->id(), 'used' => 1])->count('id');
        $stockPin =  Stocklist_buy::where(['user_id' => auth()->id(), 'used' => 0])->count('id');
        $stocklist = Stocklist_buy::with('Stockist')->where('user_id', auth()->id())->paginate(getPaginate());
        $plan = Plan::get('price');;
        return view($this->activeTemplate . '.user.stockist.list', compact('pageTitle', 'stocklist', 'usedPin', 'totalPin', 'stockPin', 'plan'));
    }
    function pinPassword(Request $request)
    {

        $user = auth()->user();
        if (password_verify($request->password, $user->password)) {

            $pin =  Stocklist_buy::where('id', $request->epin_id)->get('e_pin');
            return  array('status' => true, 'epin' => $pin[0]->e_pin, 'attr' => 'e_pin_' . $request->epin_id);
        } else {
            return  array('status' => false, 'message' => 'Invalid password.');
        }
    }

    public function membership()
    {

        $pageTitle = "Member Ship";
        $member = Membership::where('status', 1)->get();
        $member_id = auth()->user()->member_ship;

        return view($this->activeTemplate . '.user.membership', compact('pageTitle', 'member', 'member_id'));
    }

    public function memberStore(Request $request)
    {
        $member = Membership::find($request->id);
       
        $user = auth()->user();
        if($member->id == 2) {
            $frenchises = User::where('membership_id', 2)->get();
            foreach ($frenchises as $frenchise) {
                if($user->address->city == $frenchise->address->city){
                    $notify[] = ['error', 'There is a Frenchise in "'.$frenchise->address->city.'" city already'];
                    return back()->withNotify($notify);
                }
            }
        }

       
        if ($user->balance < $member->price) {
            $notify[] = ['error', 'Insufficient Balance'];
            return back()->withNotify($notify);
        }
        $user->member_ship = 1;
        $user->membership_id = $member->id;
        $user->balance -= $request->price;
        $user->save();

        Transaction::insert([
            'user_id' => auth()->user()->id,
            'amount' => $member->price,
            'post_balance' => $user->balance,
            'trx' => getTrx(),
            'trx_type' => '-',
            'remark' => 'membership_subscription',
            'details' => 'You have successfully purchased to ' . $member->name
        ]);

        $notify[] = ['success', 'Purchased Membership, Successfully'];
        return redirect()->route('user.membership')->withNotify($notify);
    }

    public function binaryCom()
    {
        $pageTitle = "Binary Commission";
        $logs = Transaction::where('user_id', auth()->id())->where('remark', 'binary_commission')->orderBy('id', 'DESC')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view($this->activeTemplate . '.user.transactions', compact('pageTitle', 'logs', 'emptyMessage'));
    }


    public function binarySummery()
    {
        $productCheck = DB::connection('mysql_store')->table('order_details')
        ->leftJoin('orders', 'orders.id', '=','order_details.order_id')
        ->whereIn('order_details.product_id', [43, 44])
        ->where('order_details.payment_status', 'paid')
        ->where('orders.customer_dds', auth()->user()->username)
        ->exists();
        if(!$productCheck && auth()->user()->plan_id == 0) {
            $notify[] = ['error', 'Please Purchase a DSP Plan to access this Page!'];
            return redirect()->route('user.plan.index')->withNotify($notify);
        }
        $pageTitle = "Binary Summery";
        $logs = UserExtra::where('user_id', auth()->id())->firstOrFail();

        $total_users = UserExtra::where('user_id', auth()->id())->first();
        $total_free_users = $total_users->free_left + $total_users->free_right;
        $total_paid_users = $total_users->paid_left + $total_users->paid_right;

        $total_dsp = User::where('ref_id', auth()->id())->where('username', 'like', '%dsp%')->get();

        $users = User::where('ref_id', auth()->id())->paginate(20);
        $me = User::where('id', auth()->id())->first();

        $total_ref_earnings = 0;
        $ref_earnings = User::where('id', auth()->id())->first();
        $total_ref_earnings =  $ref_earnings->reference_bonus + $ref_earnings->store_reference + $ref_earnings->shop_reference + $ref_earnings->total_ref_com;

        $total_earings = $total_ref_earnings + $ref_earnings->store_bonus + $ref_earnings->weekly_bonus + $ref_earnings->bv + $ref_earnings->total_ref_com;



        return view($this->activeTemplate . '.user.binarySummery', compact(
            'pageTitle',
            'logs',
            'total_free_users',
            'total_paid_users',
            'total_dsp',
            'total_ref_earnings',
            'total_earings',
            'users',
            'me'
        ));
    }
    public function bvlog(Request $request)
    {

        if ($request->type) {
            if ($request->type == 'leftBV') {
                $pageTitle = "Left BV";
                $logs = BvLog::where('user_id', auth()->id())->where('position', 1)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            } elseif ($request->type == 'rightBV') {
                $pageTitle = "Right BV";
                $logs = BvLog::where('user_id', auth()->id())->where('position', 2)->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            } elseif ($request->type == 'cutBV') {
                $pageTitle = "Cut BV";
                $logs = BvLog::where('user_id', auth()->id())->where('trx_type', '-')->orderBy('id', 'desc')->paginate(getPaginate());
            } else {
                $pageTitle = "All Paid BV";
                $logs = BvLog::where('user_id', auth()->id())->where('trx_type', '+')->orderBy('id', 'desc')->paginate(getPaginate());
            }
        } else {
            $pageTitle = "BV LOG";
            $logs = BvLog::where('user_id', auth()->id())->orderBy('id', 'desc')->paginate(getPaginate());
        }

        $emptyMessage = 'No data found';
        return view($this->activeTemplate . '.user.bvLog', compact('pageTitle', 'emptyMessage', 'logs'));
    }

    public function myRefLog()
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

        $pageTitle = "My Referrals";
        $emptyMessage = 'No data found';
        $logs = User::where('ref_id', auth()->id())->latest()->paginate(30);
        return view($this->activeTemplate . '.user.myRef', compact('pageTitle', 'emptyMessage', 'logs'));
    }

    public function myTree()
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
      

        $tree = showTreePage(auth()->user()->id);
        $pageTitle = "My Tree";

        $usernm = auth()->user()->username;
        // $userid = auth()->user()->id;
        // die();
        $refUser = User::where('username', $usernm)->first();
        if ($refUser == null) {
            $notify[] = ['error', 'Invalid Referral link.'];
            return redirect()->route('home')->withNotify($notify);
        }

        // $position = $request->position == 'left' ? 1 : 2;
        $position = 1;

        $pos = getPosition($refUser->id, $position);

        $referrer = User::find($pos['pos_id']);

        if ($pos['position'] == 1) {
            $getPosition = 'Left';
        } else {
            $getPosition = 'Right';
        }
        $joining = "<span class='help-block2'><strong class='text--success'>You are joining under " . $referrer->username . " at " . $getPosition . " </strong></span>";

        // $last_data = User::latest()->first();
        // $last_id = $last_data->id;

        $last_id = User::where('username', 'LIKE', 'dsp%')->count();
        $last_nwdspid = User::where('dsp_username', 'LIKE', 'dsp%')->count();

        $last_id = $last_id + $last_nwdspid;

        return view($this->activeTemplate . 'user.myTree', compact('pageTitle', 'tree', 'position', 'pos', 'refUser', 'referrer', 'getPosition', 'joining', 'last_id'));
    }


    public function otherTree(Request $request, $username = null)
    {
        if ($request->username) {
            $user = User::where('username', $request->username)->first();
        } else {
            $user = User::where('username', $username)->first();
        }
        if ($user && treeAuth($user->id, auth()->id())) {
            $tree = showTreePage($user->id);
            $pageTitle = "Tree of " . $user->fullname;

            // $refUser = $user;
            $usernm = auth()->user()->username;
            $refUser = User::where('username', $usernm)->first();

            $position = 1;

            $pos = getPosition($refUser->id, $position);

            $referrer = User::find($pos['pos_id']);

            if ($pos['position'] == 1) {
                $getPosition = 'Left';
            } else {
                $getPosition = 'Right';
            }
            $joining = "<span class='help-block2'><strong class='text--success'>You are joining under " . $referrer->username . " at " . $getPosition . " </strong></span>";

            $last_id = User::where('username', 'LIKE', 'dsp%')->count();
            $last_nwdspid = User::where('dsp_username', 'LIKE', 'dsp%')->count();

            $last_id = $last_id + $last_nwdspid;

            return view($this->activeTemplate . 'user.myTree', compact('pageTitle', 'tree', 'position', 'pos', 'refUser', 'referrer', 'getPosition', 'joining', 'last_id'));
        }

        $notify[] = ['error', 'Tree Not Found or You do not have Permission to view that!!'];
        return redirect()->route('user.my.tree')->withNotify($notify);
    }
}
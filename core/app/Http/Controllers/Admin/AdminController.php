<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\Transaction;
use App\Models\UserExtra;
use App\Models\Withdrawal;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    public function company_dashboard()
    {

        $pageTitle = 'Company Dashboard';

        // User Info
        $widget['total_users'] = User::count();
        $widget['verified_users'] = User::where('status', 1)->count();
        $widget['email_unverified_users'] = User::where('ev', 0)->count();
        $widget['sms_unverified_users'] = User::where('sv', 0)->count();
  		

        // Monthly Deposit & Withdraw Report Graph
        $report['months'] = collect([]);
        $report['deposit_month_amount'] = collect([]);
        $report['withdraw_month_amount'] = collect([]);


        $depositsMonth = Deposit::where('created_at', '>=', Carbon::now()->subYear())
            ->where('status', 1)
            ->selectRaw("SUM( CASE WHEN status = 1 THEN amount END) as depositAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')->get();

        $depositsMonth->map(function ($depositData) use ($report) {
            $report['months']->push($depositData->months);
            $report['deposit_month_amount']->push(showAmount($depositData->depositAmount));
        });
        $withdrawalMonth = Withdrawal::where('created_at', '>=', Carbon::now()->subYear())->where('status', 1)
            ->selectRaw("SUM( CASE WHEN status = 1 THEN amount END) as withdrawAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')->get();
        $withdrawalMonth->map(function ($withdrawData) use ($report){
            if (!in_array($withdrawData->months,$report['months']->toArray())) {
                $report['months']->push($withdrawData->months);
            }
            $report['withdraw_month_amount']->push(showAmount($withdrawData->withdrawAmount));
        });

        $months = $report['months'];

        for($i = 0; $i < $months->count(); ++$i) {
            $monthVal      = Carbon::parse($months[$i]);
            if(isset($months[$i+1])){
                $monthValNext = Carbon::parse($months[$i+1]);
                if($monthValNext < $monthVal){
                    $temp = $months[$i];
                    $months[$i]   = Carbon::parse($months[$i+1])->format('F-Y');
                    $months[$i+1] = Carbon::parse($temp)->format('F-Y');
                }else{
                    $months[$i]   = Carbon::parse($months[$i])->format('F-Y');
                }
            }
        }

        // Withdraw Graph
        $withdrawal = Withdrawal::where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))->where('status', 1)
            ->selectRaw('sum(amount) as totalAmount')
            ->selectRaw('DATE(created_at) day')
            ->groupBy('day')->get();

        $withdrawals['per_day'] = collect([]);
        $withdrawals['per_day_amount'] = collect([]);
        $withdrawal->map(function ($withdrawItem) use ($withdrawals) {
            $withdrawals['per_day']->push(date('d M', strtotime($withdrawItem->day)));
            $withdrawals['per_day_amount']->push($withdrawItem->totalAmount + 0);
        });


        // Deposit Graph
        $deposit = Deposit::where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))->where('status', 1)
            ->selectRaw('sum(amount) as totalAmount')
            ->selectRaw('DATE(created_at) day')
            ->groupBy('day')->get();
        $deposits['per_day'] = collect([]);
        $deposits['per_day_amount'] = collect([]);
        $deposit->map(function ($depositItem) use ($deposits) {
            $deposits['per_day']->push(date('d M', strtotime($depositItem->day)));
            $deposits['per_day_amount']->push($depositItem->totalAmount + 0);
        });


        // user Browsing, Country, Operating Log
        $userLoginData = UserLogin::where('created_at', '>=', \Carbon\Carbon::now()->subDay(30))->get(['browser', 'os', 'country']);

        $chart['user_browser_counter'] = $userLoginData->groupBy('browser')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_os_counter'] = $userLoginData->groupBy('os')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_country_counter'] = $userLoginData->groupBy('country')->map(function ($item, $key) {
            return collect($item)->count();
        })->sort()->reverse()->take(5);


        $payment['total_deposit_amount'] = Deposit::where('status',1)->sum('amount');
        $payment['total_deposit_charge'] = Deposit::where('status',1)->sum('charge');
        $payment['total_deposit_pending'] = Deposit::where('status',2)->count();

        $paymentWithdraw['total_withdraw_amount'] = Withdrawal::where('status',1)->sum('amount');
        $paymentWithdraw['total_withdraw_charge'] = Withdrawal::where('status',1)->sum('charge');
        $paymentWithdraw['total_withdraw_pending'] = Withdrawal::where('status',2)->count();
        
        $total_sb = User::sum('store_bonus');
        $total_srb = User::sum('store_reference');
        $total_rb = User::sum('reference_bonus');
        $total_cb = User::sum('balance');
        $total_db = User::sum('total_invest');
        $total_pb = UserExtra::sum('pair_bonus');
        $total_product_partner_bonus = User::sum('product_partner_bonus');
        $total_stockist_ref_bonus = User::sum('stockist_ref_bonus');
        $total_stockist_bonus = User::sum('stockist_bonus');
        $total_shop_reference = User::sum('shop_reference');
        $total_epin_credit = User::sum('epin_credit');

        $pos_orders = DB::connection('mysql_store')->table('orders')
        ->where('order_type','POS')->get();
        
        $default_orders = DB::connection('mysql_store')->table('orders')
        ->where('order_type','default_type')->get();
        
        $approved_sellers = DB::connection('mysql_store')->table('sellers')
        ->where('status','approved')->get();
        
        $pending_sellers = DB::connection('mysql_store')->table('sellers')
        ->where('status','pending')->get();
        
        $admin_products = DB::connection('mysql_store')->table('products')
        ->where('added_by','admin')->get();
        
        $seller_products = DB::connection('mysql_store')->table('products')
        ->where('added_by','seller')->get();

        $subs = DB::table('wallets')->where('id',1)->first();

        $userexs = UserExtra::all();
     
        $pairs=0;

        foreach ($userexs as $user) {
       
            if($user->paid_left > $user->paid_right) {
               $pairs += 1;
            }
            elseif($user->paid_left < $user->paid_right) {
               $pairs += 1;
            } 
            else {
                $pairs += 1; 
            }
        }

        $admin_wallets = DB::table('admin_wallets')->where('id',1)->first();
       // return $pairs;// $seller_products->count();
        
        return view('admin.company_dashboard', compact(
            'admin_wallets',
            'total_product_partner_bonus',
            'total_stockist_ref_bonus',
            'total_stockist_bonus',
            'total_shop_reference',
            'total_epin_credit',
            'pairs',
            'subs',
            'pos_orders',
            'default_orders',
            'approved_sellers',
            'pending_sellers',
            'admin_products',
            'seller_products',
            'total_pb',
            'total_sb',
            'total_srb',
            'total_rb',
            'total_cb',
            'total_db',
            'pageTitle', 
            'widget',
            'report', 
            'withdrawals', 
            'chart',
            'payment',
            'paymentWithdraw',
            'depositsMonth',
            'withdrawalMonth',
            'months',
            'deposits'
        ));
    
    }
    public function users_dashboard()
    {

        $pageTitle = 'Users Dashboard';

        // User Info
        $widget['total_users'] = User::count();
        $widget['verified_users'] = User::where('status', 1)->count();
        $widget['email_unverified_users'] = User::where('ev', 0)->count();
        $widget['sms_unverified_users'] = User::where('sv', 0)->count();
		$widget['city_reference'] = User::sum('city_reference');
        $widget['franchise_bonus'] = User::sum('franchise_bonus');
        $widget['franchise_ref_bonus'] = User::sum('franchise_ref_bonus');
        // Monthly Deposit & Withdraw Report Graph
        $report['months'] = collect([]);
        $report['deposit_month_amount'] = collect([]);
        $report['withdraw_month_amount'] = collect([]);


        $depositsMonth = Deposit::where('created_at', '>=', Carbon::now()->subYear())
            ->where('status', 1)
            ->selectRaw("SUM( CASE WHEN status = 1 THEN amount END) as depositAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')->get();

        $depositsMonth->map(function ($depositData) use ($report) {
            $report['months']->push($depositData->months);
            $report['deposit_month_amount']->push(showAmount($depositData->depositAmount));
        });
        $withdrawalMonth = Withdrawal::where('created_at', '>=', Carbon::now()->subYear())->where('status', 1)
            ->selectRaw("SUM( CASE WHEN status = 1 THEN amount END) as withdrawAmount")
            ->selectRaw("DATE_FORMAT(created_at,'%M-%Y') as months")
            ->orderBy('created_at')
            ->groupBy('months')->get();
        $withdrawalMonth->map(function ($withdrawData) use ($report){
            if (!in_array($withdrawData->months,$report['months']->toArray())) {
                $report['months']->push($withdrawData->months);
            }
            $report['withdraw_month_amount']->push(showAmount($withdrawData->withdrawAmount));
        });

        $months = $report['months'];

        for($i = 0; $i < $months->count(); ++$i) {
            $monthVal      = Carbon::parse($months[$i]);
            if(isset($months[$i+1])){
                $monthValNext = Carbon::parse($months[$i+1]);
                if($monthValNext < $monthVal){
                    $temp = $months[$i];
                    $months[$i]   = Carbon::parse($months[$i+1])->format('F-Y');
                    $months[$i+1] = Carbon::parse($temp)->format('F-Y');
                }else{
                    $months[$i]   = Carbon::parse($months[$i])->format('F-Y');
                }
            }
        }

        // Withdraw Graph
        $withdrawal = Withdrawal::where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))->where('status', 1)
            ->selectRaw('sum(amount) as totalAmount')
            ->selectRaw('DATE(created_at) day')
            ->groupBy('day')->get();

        $withdrawals['per_day'] = collect([]);
        $withdrawals['per_day_amount'] = collect([]);
        $withdrawal->map(function ($withdrawItem) use ($withdrawals) {
            $withdrawals['per_day']->push(date('d M', strtotime($withdrawItem->day)));
            $withdrawals['per_day_amount']->push($withdrawItem->totalAmount + 0);
        });


        // Deposit Graph
        $deposit = Deposit::where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))->where('status', 1)
            ->selectRaw('sum(amount) as totalAmount')
            ->selectRaw('DATE(created_at) day')
            ->groupBy('day')->get();
        $deposits['per_day'] = collect([]);
        $deposits['per_day_amount'] = collect([]);
        $deposit->map(function ($depositItem) use ($deposits) {
            $deposits['per_day']->push(date('d M', strtotime($depositItem->day)));
            $deposits['per_day_amount']->push($depositItem->totalAmount + 0);
        });


        // user Browsing, Country, Operating Log
        $userLoginData = UserLogin::where('created_at', '>=', \Carbon\Carbon::now()->subDay(30))->get(['browser', 'os', 'country']);

        $chart['user_browser_counter'] = $userLoginData->groupBy('browser')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_os_counter'] = $userLoginData->groupBy('os')->map(function ($item, $key) {
            return collect($item)->count();
        });
        $chart['user_country_counter'] = $userLoginData->groupBy('country')->map(function ($item, $key) {
            return collect($item)->count();
        })->sort()->reverse()->take(5);


        $payment['total_deposit_amount'] = Deposit::where('status',1)->sum('amount');
        $payment['total_deposit_charge'] = Deposit::where('status',1)->sum('charge');
        $payment['total_deposit_pending'] = Deposit::where('status',2)->count();

        $paymentWithdraw['total_withdraw_amount'] = Withdrawal::where('status',1)->sum('amount');
        $paymentWithdraw['total_withdraw_charge'] = Withdrawal::where('status',1)->sum('charge');
        $paymentWithdraw['total_withdraw_pending'] = Withdrawal::where('status',2)->count();
        
        $total_sb = User::sum('store_bonus');
        $total_srb = User::sum('store_reference');
        $total_rb = User::sum('reference_bonus');
        $total_cb = User::sum('balance');
        
		$transactions = Transaction::where('remark', 'purchased_plan')->with('user')->get();
        $total_db =  $transactions->count() * 6800;//User::sum('total_invest');
		$total_dsp = $transactions->count();
        
        $total_pb = UserExtra::sum('pair_bonus');
      
        $total_stockist_ref_bonus = User::sum('stockist_ref_bonus');
        $total_stockist_bonus = User::sum('stockist_bonus');
        $total_shop_reference = User::sum('shop_reference');
        $total_epin_credit = User::sum('epin_credit');

        $pos_orders = DB::connection('mysql_store')->table('orders')
        ->where('order_type','POS')->get();
        
        $default_orders = DB::connection('mysql_store')->table('orders')
        ->where('order_type','default_type')->get();
        
        $approved_sellers = DB::connection('mysql_store')->table('sellers')
        ->where('status','approved')->get();
        
        $pending_sellers = DB::connection('mysql_store')->table('sellers')
        ->where('status','pending')->get();
        
        $admin_products = DB::connection('mysql_store')->table('products')
        ->where('added_by','admin')->get();
        
        $seller_products = DB::connection('mysql_store')->table('seller_products')
        ->get();

        $subs = DB::table('wallets')->where('id',1)->first();

        $userexs = UserExtra::all();
     
        $pairs=0;

        foreach ($userexs as $user) {
       
            if($user->paid_left > $user->paid_right) {
               $pairs += 1;
            }
            elseif($user->paid_left < $user->paid_right) {
               $pairs += 1;
            } 
            else {
                $pairs += 1; 
            }
        }

        $total_pv = User::sum('pv');
        $total_bv = User::sum('bv');
        $total_pv_dsp = DB::table('admin_wallets')->sum('byPV');
        $total_epin_dsp = DB::table('admin_wallets')->sum('byEpinCredit');
        $total_balance_dsp = DB::table('admin_wallets')->sum('byCurrentBalance');
        $total_wallet_dsp = DB::table('admin_wallets')->sum('byWallet');
        
        $total_product_partner_bonus = DB::connection('mysql_office')->table('product_partners')
        ->sum('share');
        
        $total_promo = User::sum('promo');
       // return $pairs;// $seller_products->count();
        
        return view('admin.users_dashboard', compact(
            'total_promo',
            'total_product_partner_bonus',
            'total_stockist_ref_bonus',
            'total_stockist_bonus',
            'total_shop_reference',
            'total_epin_credit',
            'pairs',
            'subs',
            'pos_orders',
            'default_orders',
            'approved_sellers',
            'pending_sellers',
            'admin_products',
            'seller_products',
            'total_pb',
            'total_pv',
            'total_bv',
            'total_sb',
            'total_srb',
            'total_rb',
            'total_cb',
            'total_db',
            'total_dsp',
            'total_pv_dsp',
            'total_epin_dsp',
            'total_balance_dsp',
            'total_wallet_dsp',
            'pageTitle', 
            'widget',
            'report', 
            'withdrawals', 
            'chart',
            'payment',
            'paymentWithdraw',
            'depositsMonth',
            'withdrawalMonth',
            'months',
            'deposits'
        ));
    
    }

    public function expense_dashboard()
    {
        $pageTitle = 'Expense Dashboard';
        $bills = DB::connection('mysql_office')->table('bill_payments')->sum('amount');
        $products = DB::connection('mysql_office')->table('product_services')->get();
        $admin_expense = DB::table('admin_expense')->where('id',1)->first();
        return view('admin.expense_dashboard', compact(
            'admin_expense',
            'products',
            'pageTitle'
        ));
    
    }
	
    public function summary(Request $request){
	
		$emptyMessage = 'No Records Found';
        $pageTitle = 'Summary';
		$total = 0;
		$transactions = Transaction::orderBY('id', 'DESC')->paginate();
		$transactionsAll = Transaction::orderBY('id', 'DESC')->get();
		$total = $transactionsAll->sum('amount');
		
		if($request->username){
			
			$user = User::where('username', $request->username)->first();
			if($user){
			$transactions = Transaction::where('user_id', $user->id)
			->orderBy('id', 'DESC')->paginate();
			$transactionsAll = Transaction::where('user_id', $user->id)
			->get();
			 $total = $transactionsAll->sum('amount');
			} else {
				$notify[] = ['error', "User not found"];
            return back()->withNotify($notify);
			}
		}
		if($request->search_word){
			$user = User::where('username', $request->username)->first();
			$transactions = Transaction::where('remark',$request->search_word)
			->orderBY('id','DESC')->paginate();
			$transactionsAll = Transaction::where('remark',$request->search_word)
			->orderBY('id','DESC')->get();
			 $total = $transactionsAll->sum('amount');
		} 
		if($request->start_date_search){
			$user = User::where('username', $request->username)->first();
			$transactions = Transaction::whereBetween( DB::raw('DATE(`created_at`)'),[$request->start_date_search, $request->end_date_search])
			->orderBY('id','DESC')->paginate();
			
			$transactionsAll = Transaction::whereBetween( DB::raw('DATE(`created_at`)'),[$request->start_date_search, $request->end_date_search])
			->get();
			
			 $total = $transactionsAll->sum('amount');
		} 
		
		if($request->search_word && $request->username){
			$user = User::where('username', $request->username)->first();
			if($user){
			$transactions = Transaction::where('remark',$request->search_word)
			->where('user_id', $user->id)
			->orderBY('id','DESC')->paginate();
			
			$transactionsAll = Transaction::where('remark',$request->search_word)
			->where('user_id', $user->id)
			->get();
			
			 $total = $transactionsAll->sum('amount');
				} else {
				$notify[] = ['error', "User not found"];
        return back()->withNotify($notify);
			}
		} 
		
	    if($request->search_word && $request->username && $request->start_date_search){
			$user = User::where('username', $request->username)->first();
			if($user){
			$transactions = Transaction::where('remark',$request->search_word)
			->where('user_id', $user->id)
			->whereBetween( DB::raw('DATE(`created_at`)'),[$request->start_date_search, $request->end_date_search])
			->orderBY('id','DESC')->paginate();
			
			 $transactionsAll = Transaction::where('remark',$request->search_word)
			->where('user_id', $user->id)
			->whereBetween( DB::raw('DATE(`created_at`)'),[$request->start_date_search, $request->end_date_search])
			->get();
			
			$total = $transactionsAll->sum('amount');
			} else {
				$notify[] = ['error', "User not found"];
        return back()->withNotify($notify);
			}
		} 
		
		
       
        

        


   

       
	
   
        return view('admin.summary', compact('transactions','pageTitle','total','emptyMessage'));
    }

    public function office_statements(Request $request){
        
        $pageTitle = 'Office Statements';
        if($request->search_word) {
            $transactions = DB::connection('mysql_office')
            ->table('transactions')
            ->leftJoin('users', 'users.id', '=', 'transactions.created_by')
            ->orderBy('transactions.id', 'DESC')
            ->where('category', $request->search_word)
            ->paginate(); 
         

        } else {
            $transactions = DB::connection('mysql_office')
            ->table('transactions')
            ->leftJoin('users', 'users.id', '=', 'transactions.created_by')
            ->orderBy('transactions.id', 'DESC')
            ->paginate(); 
        }
  
        return view('admin.office_statements', compact(
            
            'transactions',
            'pageTitle'
        ));
    }
    public function warehouse_products(Request $request){
        $pageTitle = 'Warehouse Products';
        if($request->search) {
            $products = DB::connection('mysql_office')
            ->table('product_services')
            ->leftJoin('users', 'users.id', '=', 'product_services.warehouse_id')
            ->where('product_services.name', 'Like', "%$request->search%")
            ->select(['users.name as username', 'product_services.*'])
            ->paginate(); 
        } else {
            $products = DB::connection('mysql_office')
            ->table('product_services')
            ->leftJoin('users', 'users.id', '=', 'product_services.warehouse_id')
            ->select(['users.name as username', 'product_services.*'])
            ->paginate(); 
        }
  
        return view('admin.warehouse_products', compact(
            
            'products',
            'pageTitle'
        ));
    }
  

    public function partner_earnings(Request $request)
    {
		
      $emptyMessage = 'No Records found';
      $partner_earnings = DB::table('partner_earnings')->paginate(10);
      $pageTitle = 'Partners';
      return view('admin.partners.partner_earnings', compact('partner_earnings', 'pageTitle', 'emptyMessage'));
    }
	
	public function lucky_draws(Request $request)
    {
      $emptyMessage = 'No Records found';
      $lucky_draw_promos = DB::table('lucky_draw_promos')->paginate(10);
	  $dlps = DB::table('dlp_serial')->count();
	  $dlp = DB::table('dlp_serial')->orderBy('id', 'DESC')->first();
			
      $pageTitle = 'Lucky Draw PROMO Winners';
      return view('admin.lucky_draw_promos', compact('lucky_draw_promos', 'pageTitle','dlps','dlp', 'emptyMessage'));
    }
	
	public function lucky_draw_all(Request $request)
    {
      $emptyMessage = 'No Records found';
		if ($request->search != null && $request->city == null) {
		
      $lucky_draw_all = DB::table('dlp_serial')->where('username', $request->search)->paginate(10);
		} elseif ($request->search == null && $request->city != null) { $lucky_draw_all = DB::table('dlp_serial')->where('city', 'like', "%$request->city%")->paginate(10);
																	  } elseif ($request->search != null && $request->city != null) { $lucky_draw_all = DB::table('dlp_serial')->where('username', $request->search)->where('city', 'like',"%$request->city%")->paginate(10);} else
		{ $lucky_draw_all = DB::table('dlp_serial')->paginate(10); }
			
	
      $pageTitle = 'Lucky Draw PROMO All';
      return view('admin.lucky_draw_all', compact('lucky_draw_all', 'pageTitle','emptyMessage'));
    }
	
	public function lucky_draw_complete(Request $request)
    {
      $emptyMessage = 'No Records found';
      DB::table('lucky_draw_promos')->where('id', $request->id)->update(['status'=>1]);
      $notify[] = ['success', 'Lucky Draw PROMO completed, successfully'];
      return back()->withNotify($notify);
    }
	
	
    public function add_partner(Request $request)
    {
  
      $user = DB::table('admins')->first();
      if (Hash::check($request->password, $user->password)) {
  
        DB::table('partner_earnings')->insert([
          'name' => $request->name,
          'dds_username' => $request->username,
          'percentage' => $request->percentage,
  
        ]);
  
        $notify[] = ['success', "Partner added, successfully"];
        return back()->withNotify($notify);
      } else {
        $notify[] = ['error', "Wrong Password"];
        return back()->withNotify($notify);
      }
    }
    public function edit_partner($id)
    {
      $price_list = DB::table('partner_earnings')->find($id);
      $pageTitle = 'Edit Partner:' . $price_list->name;
      return view('admin.partners.edit', compact('pageTitle', 'price_list'));
    }
    public function update_partner(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'percentage' => 'required',
        ]);
  
        DB::table('partner_earnings')->where('id', $id)->update([
            'name' => $request->name,
            'percentage' => $request->percentage,
        ]);
  
        $notify[] = ['success', 'Price List has been updated successfully'];
        return back()->withNotify($notify);
    }
    public function delete_partner($id)
    {
      DB::table('partner_earnings')->where('id',$id)->delete();
      $notify[] = ['success', 'Partner has been deleted, successfully'];
      return back()->withNotify($notify);
    }
  
    public function clear_company_wallet(Request $request)
    {
    
      $user = DB::table('admins')->first();
      if (Hash::check($request->password, $user->password)) {
  
        $amount = DB::table('admin_wallets')->where('id', 1)->first();
  
        if($request->wallet == 'PPB'){
            if($request->amount <= $amount->pintaPayBalance){
                $postBalance = $amount->pintaPayBalance-=$request->amount;
                DB::table('admin_wallets')->where('id', 1)->update([
                    'pintaPayBalance' => $postBalance
                ]);
                
                $this->walletStatement($user->id,$request->amount,$postBalance,$request->note);
                $notify[] = ['success', "Balance reduced from PintaPay's Wallet"];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "0 Balance in PintaPay's Wallet"];
                return back()->withNotify($notify);
            }
        }
        if($request->wallet == 'PB'){
            if($request->amount <= $amount->productsBalance){
                $postBalance = $amount->productsBalance-=$request->amount;
                DB::table('admin_wallets')->where('id', 1)->update([
                    'productsBalance' => $postBalance
                ]);
                
                $this->walletStatement($user->id,$request->amount,$postBalance,$request->note);
                $notify[] = ['success', "Balance reduced from Products' Wallet"];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "0 Balance in Products' Wallet"];
                return back()->withNotify($notify);
            }
        }
        if($request->wallet == 'SB'){
            if($request->amount <= $amount->shippingBalance){
                $postBalance = $amount->shippingBalance-=$request->amount;
                DB::table('admin_wallets')->where('id', 1)->update([
                    'shippingBalance' => $postBalance
                ]);
                
                $this->walletStatement($user->id,$request->amount,$postBalance,$request->note);
                $notify[] = ['success', "Balance reduced from Shipping's Wallet"];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "0 Balance in Shipping's Wallet"];
                return back()->withNotify($notify);
            }
        }
        if($request->wallet == 'CB'){
            if($request->amount <= $amount->companyBalance){
                $postBalance = $amount->companyBalance-=$request->amount;
                DB::table('admin_wallets')->where('id', 1)->update([
                    'companyBalance' => $postBalance
                ]);
                
                $this->walletStatement($user->id,$request->amount,$postBalance,$request->note);
                $notify[] = ['success', "Balance reduced from Company's Wallet"];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "0 Balance in Company's Wallet"];
                return back()->withNotify($notify);
            }
        }
        if($request->wallet == 'EB'){
            if($request->amount <= $amount->eventBalance){
                $postBalance = $amount->eventBalance-=$request->amount;
                DB::table('admin_wallets')->where('id', 1)->update([
                    'eventBalance' => $postBalance
                ]);
                
                $this->walletStatement($user->id,$request->amount,$postBalance,$request->note);
                $notify[] = ['success', "Balance reduced from Event's Wallet"];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "0 Balance in Event's Wallet"];
                return back()->withNotify($notify);
            }
        }
        if($request->wallet == 'SEB'){
            if($request->amount <= $amount->samiExpenseBalance){
                $postBalance = $amount->samiExpenseBalance-=$request->amount;
                DB::table('admin_wallets')->where('id', 1)->update([
                    'samiExpenseBalance' => $postBalance
                ]);
                
                $this->walletStatement($user->id,$request->amount,$postBalance,$request->note);
                $notify[] = ['success', "Balance reduced from Sami Expense's Wallet"];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "0 Balance in Sami Expense's Wallet"];
                return back()->withNotify($notify);
            }
        }
        if($request->wallet == 'OB'){
            if($request->amount <= $amount->officeBalance){
                $postBalance = $amount->officeBalance-=$request->amount;
                DB::table('admin_wallets')->where('id', 1)->update([
                    'officeBalance' => $postBalance
                ]);
                
                $this->walletStatement($user->id,$request->amount,$postBalance,$request->note);
                $notify[] = ['success', "Balance reduced from Office's Wallet"];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "0 Balance in Office's Wallet"];
                return back()->withNotify($notify);
            }
        }
        if($request->wallet == 'VOB'){
            if($request->amount <= $amount->visitOutsideBalance){
                $postBalance = $amount->visitOutsideBalance-=$request->amount;
                DB::table('admin_wallets')->where('id', 1)->update([
                    'visitOutsideBalance' => $postBalance
                ]);
                
                $this->walletStatement($user->id,$request->amount,$postBalance,$request->note);
                $notify[] = ['success', "Balance reduced from Visit Outside's Wallet"];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "0 Balance in Visit Outside's Wallet"];
                return back()->withNotify($notify);
            }
        }
        if($request->wallet == 'ITB'){
            if($request->amount <= $amount->iTBalance){
                $postBalance = $amount->iTBalance-=$request->amount;
                DB::table('admin_wallets')->where('id', 1)->update([
                    'iTBalance' => $postBalance
                ]);
                
                $this->walletStatement($user->id,$request->amount,$postBalance,$request->note);
                $notify[] = ['success', "Balance reduced from Visit IT's Wallet"];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "0 Balance in Visit IT's Wallet"];
                return back()->withNotify($notify);
            }
        }
        if($request->wallet == 'ExB'){
            if($request->amount <= $amount->extraBalance){
                $postBalance = $amount->extraBalance-=$request->amount;
                DB::table('admin_wallets')->where('id', 1)->update([
                    'extraBalance' => $postBalance
                ]);
                
                $this->walletStatement($user->id,$request->amount,$postBalance,$request->note);
                $notify[] = ['success', "Balance reduced from Visit Extra's Wallet"];
                return back()->withNotify($notify);
            } else {
                $notify[] = ['error', "0 Balance in Visit Extra's Wallet"];
                return back()->withNotify($notify);
            }
        }
      
        if ($amount->company_wallet == 0) {
          $notify[] = ['error', "0 Balance in Company's Wallet"];
          return back()->withNotify($notify);
        }
    
  
        $partners = DB::table('partner_earnings')->get();
        foreach ($partners as $p) {
  
          $e = DB::table('partner_earnings')->where('id', $p->id)->first();
          DB::table('partner_earnings')->where('id', $p->id)->update([
            'amount' => $e->amount += ($p->percentage*$amount->company_wallet)/100,
          ]);
        }
  
        DB::table('admin_wallets')->where('id', 1)->update([
          'company_wallet' => 0
        ]);
  
        $notify[] = ['success', "Company's wallet has been cleared successfully"];
        return back()->withNotify($notify);
      } else {
        $notify[] = ['error', "Wrong Password"];
        return back()->withNotify($notify);
      }
    }


    protected function walletStatement($id,$amount,$postBalance,$note){
        $transaction = new Transaction();
        $transaction->user_id = $id;
        $transaction->amount = $amount;
        $transaction->post_balance = $postBalance;
        $transaction->trx_type = '-';
        $transaction->remark = 'admin_wallet';
        $transaction->details = $note;
        $transaction->trx =  getTrx();
        $transaction->save();
    }

    public function profile()
    {
        $pageTitle = 'Profile';
        $admin = Auth::guard('admin')->user();
        return view('admin.profile', compact('pageTitle', 'admin'));
    }

    public function profileUpdate(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'image' => ['nullable','image',new FileTypeValidate(['jpg','jpeg','png'])]
        ]);
        $user = Auth::guard('admin')->user();

        if ($request->hasFile('image')) {
            try {
                $old = $user->image ?: null;
                $user->image = uploadImage($request->image, imagePath()['profile']['admin']['path'], imagePath()['profile']['admin']['size'], $old);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Image could not be uploaded.'];
                return back()->withNotify($notify);
            }
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        $notify[] = ['success', 'Your profile has been updated.'];
        return redirect()->route('admin.profile')->withNotify($notify);
    }


    public function password()
    {
        $pageTitle = 'Password Setting';
        $admin = Auth::guard('admin')->user();
        return view('admin.password', compact('pageTitle', 'admin'));
    }

    public function passwordUpdate(Request $request)
    {
        $this->validate($request, [
            'old_password' => 'required',
            'password' => 'required|min:5|confirmed',
        ]);

        $user = Auth::guard('admin')->user();
        if (!Hash::check($request->old_password, $user->password)) {
            $notify[] = ['error', 'Password do not match !!'];
            return back()->withNotify($notify);
        }
        $user->password = bcrypt($request->password);
        $user->save();
        $notify[] = ['success', 'Password changed successfully.'];
        return redirect()->route('admin.password')->withNotify($notify);
    }

    public function notifications(){
        $notifications = AdminNotification::orderBy('id','desc')->with('user')->paginate(getPaginate());
        $pageTitle = 'Notifications';
        return view('admin.notifications',compact('pageTitle','notifications'));
    }


    public function notificationRead($id){
        $notification = AdminNotification::findOrFail($id);
        $notification->read_status = 1;
        $notification->save();
        return redirect($notification->click_url);
    }

    public function requestReport()
    {
        $pageTitle = 'Your Listed Report & Request';
        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASE_CODE');
        $url = "https://license.viserlab.com/issue/get?".http_build_query($arr);
        $response = json_decode(curlContent($url));
        if ($response->status == 'error') {
            return redirect()->route('admin.dashboard')->withErrors($response->message);
        }
        $reports = $response->message[0];
        return view('admin.reports',compact('reports','pageTitle'));
    }

    public function reportSubmit(Request $request)
    {
        $request->validate([
            'type'=>'required|in:bug,feature',
            'message'=>'required',
        ]);
        $url = 'https://license.viserlab.com/issue/add';

        $arr['app_name'] = systemDetails()['name'];
        $arr['app_url'] = env('APP_URL');
        $arr['purchase_code'] = env('PURCHASE_CODE');
        $arr['req_type'] = $request->type;
        $arr['message'] = $request->message;
        $response = json_decode(curlPostContent($url,$arr));
        if ($response->status == 'error') {
            return back()->withErrors($response->message);
        }
        $notify[] = ['success',$response->message];
        return back()->withNotify($notify);
    }

    public function systemInfo(){
        $laravelVersion = app()->version();
        $serverDetails = $_SERVER;
        $currentPHP = phpversion();
        $timeZone = config('app.timezone');
        $pageTitle = 'System Information';
        return view('admin.info',compact('pageTitle', 'currentPHP', 'laravelVersion', 'serverDetails','timeZone'));
    }

    public function readAll(){
        AdminNotification::where('read_status',0)->update([
            'read_status'=>1
        ]);
        $notify[] = ['success','Notifications read successfully'];
        return back()->withNotify($notify);
    }
	
	    public function reward_users() {
        //echo auth('admin')->user()->id;
        $statements = Transaction::where('user_id', auth('admin')->user()->id)
        ->where('details', 'Like', '%have won the%')
        ->paginate(10);
        $pageTitle = 'User Rewards';
		$emptyMessage = 'No Records found';

        return view('admin/reward-users', compact('statements', 'pageTitle','emptyMessage'));
    }

    public function deliver_reward(Request $request){
        $amo =   Transaction::where('details', $request->id)->first();
        $aw = DB::table('admin_wallets')->where('id', 1)->first();

        if($amo->trx_type != '-'){
            $notify[] = ['error', "This Reward has been already delivered"];
            return back()->withNotify($notify);
        }
        if($aw->rewardBalance >=  $amo->remark){

            $username = strtok($request->id, " ");
            $user = User::where('username', $username)->first();
            $user->reward += $amo->remark;
            $user->save(); 
            
            DB::table('admin_wallets')->where('id', 1)->update([
                'rewardBalance' => $aw->rewardBalance -= $amo->remark
            ]);
            
            Transaction::where('trx_type', '-')
            ->where('details', '=', $request->id)
            ->update(['trx_type'=>'+']);
            
            Transaction::insert([
                'user_id' => $user->id,
                'amount' => $amo->remark,
                'trx_type'=>'+',
                'trx'=> getTrx(),
                'remark' => 'reward_delivered',
                'details' => 'Reward has been delivered'
            ]);
            Transaction::insert([
                'user_id' => $user->id,
                'amount' => $amo->remark,
                'trx_type'=>'+',
                'trx'=> getTrx(),
                'remark' => 'reward_note',
                'details' => $request->line
            ]);
            
            $notify[] = ['success', "Reward has been delivered, successfully"];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', "Less Balance in Reward Balance Wallet of admin, successfully"];
            return back()->withNotify($notify);
        }
    }

    
    public function checkUsername(Request $request)
    {
        $userCheck = DB::table('partner_earnings')
        ->where('dds_username', $request->username)->first();
        $user = User::where('username', $request->username)->first();
        if (!empty($userCheck)) {
            return response()->json([
                'message' => 'This User is already added!'

            ]);
        } else {

            return response()->json([
                'name' => $user->firstname . ' ' . $user->lastname

            ]);
        }
    }
	
	
	public function calculator(){
		$pageTitle = 'Calculator';
		return view('admin.calculator', compact('pageTitle'));
	}


}
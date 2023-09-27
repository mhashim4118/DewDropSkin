<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BvLog;
use App\Models\Deposit;
use App\Models\EmailLog;
use App\Models\Gateway;
use App\Models\GeneralSetting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserExtra;
use App\Models\UserLogin;
use App\Models\WithdrawMethod;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManageUsersController extends Controller
{
    public function allUsers()
    {
        $pageTitle = 'Manage Users';
        $emptyMessage = 'No user found';
        $users = User::orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function activeUsers()
    {
        $pageTitle = 'Manage Active Users';
        $emptyMessage = 'No active user found';
        $users = User::active()->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function bannedUsers()
    {
        $pageTitle = 'Banned Users';
        $emptyMessage = 'No banned user found';
        $users = User::banned()->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function emailUnverifiedUsers()
    {
        $pageTitle = 'Email Unverified Users';
        $emptyMessage = 'No email unverified user found';
        $users = User::emailUnverified()->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }
    public function emailVerifiedUsers()
    {
        $pageTitle = 'Email Verified Users';
        $emptyMessage = 'No email verified user found';
        $users = User::emailVerified()->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }


    public function smsUnverifiedUsers()
    {
        $pageTitle = 'SMS Unverified Users';
        $emptyMessage = 'No sms unverified user found';
        $users = User::smsUnverified()->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }


    public function smsVerifiedUsers()
    {
        $pageTitle = 'SMS Verified Users';
        $emptyMessage = 'No sms verified user found';
        $users = User::smsVerified()->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }


    public function usersWithBalance()
    {
        $pageTitle = 'Users with balance';
        $emptyMessage = 'No sms verified user found';
        $users = User::where('balance', '!=', 0)->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }



    public function search(Request $request, $scope)
    {
        $search = $request->search;
        $users = User::where(function ($user) use ($search) {
            $user->where('username', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%")
                ->orWhere('mobile', 'like', "%$search%")
                ->orWhere('cnicnumber', 'like', "%$search%")
                ->orWhere('address', 'like', "%$search%")
                ->orWhere(User::raw('CONCAT(firstname, " ", lastname)'), 'like', "%$search%");
        });
        $pageTitle = '';
        if ($scope == 'active') {
            $pageTitle = 'Active ';
            $users = $users->where('status', 1);
        } elseif ($scope == 'banned') {
            $pageTitle = 'Banned';
            $users = $users->where('status', 0);
        } elseif ($scope == 'emailUnverified') {
            $pageTitle = 'Email Unverified ';
            $users = $users->where('ev', 0);
        } elseif ($scope == 'smsUnverified') {
            $pageTitle = 'SMS Unverified ';
            $users = $users->where('sv', 0);
        } elseif ($scope == 'withBalance') {
            $pageTitle = 'With Balance ';
            $users = $users->where('balance', '!=', 0);
        }

        $users = $users->paginate(getPaginate());
        $pageTitle .= 'User Search - ' . $search;
        $emptyMessage = 'No search result found';
        return view('admin.users.list', compact('pageTitle', 'search', 'scope', 'emptyMessage', 'users'));
    }

    public function searchDetails(Request $request, $scope)
    {
        $search = $request->search;
        $users = User::where(function ($user) use ($search) {
            $user->where('username', $search);
        });
        $pageTitle = '';
        if ($scope == 'active') {
            $pageTitle = 'Active ';
            $users = $users->where('status', 1);
        } elseif ($scope == 'banned') {
            $pageTitle = 'Banned';
            $users = $users->where('status', 0);
        } elseif ($scope == 'emailUnverified') {
            $pageTitle = 'Email Unverified ';
            $users = $users->where('ev', 0);
        } elseif ($scope == 'smsUnverified') {
            $pageTitle = 'SMS Unverified ';
            $users = $users->where('sv', 0);
        } elseif ($scope == 'withBalance') {
            $pageTitle = 'With Balance ';
            $users = $users->where('balance', '!=', 0);
        }

        $users = $users->paginate(getPaginate());
        $pageTitle .= 'User Search - ' . $search;
        $emptyMessage = 'No search result found';
        return view('admin.users.list', compact('pageTitle', 'search', 'scope', 'emptyMessage', 'users'));
    }

    public function detail($id)
    {
        $pageTitle = 'User Detail';
        $user = User::findOrFail($id);
        $totalDeposit = Deposit::where('user_id', $user->id)->where('status', 1)->sum('amount');
        $totalWithdraw = Withdrawal::where('user_id', $user->id)->where('status', 1)->sum('amount');
        $totalTransaction = Transaction::where('user_id', $user->id)->count();
        $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
        $totalBvCut         = BvLog::where('user_id', $user->id)->where('trx_type', '-')->sum('amount');
        $ref_id             = User::find($user->ref_id);
        return view('admin.users.detail', compact('pageTitle', 'user', 'totalDeposit', 'totalWithdraw', 'totalTransaction', 'countries', 'totalBvCut', 'ref_id'));
    }



    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $countryData = json_decode(file_get_contents(resource_path('views/partials/country.json')));

        $request->validate([
            'firstname' => 'required|max:50',
            'lastname' => 'required|max:50',
            'email' => 'required|email|max:90|unique:users,email,' . $user->id,
            'mobile' => 'required|unique:users,mobile,' . $user->id,
            'country' => 'required',
        ]);
        $countryCode = $request->country;
        $user->mobile = $request->mobile;
        $user->country_code = $countryCode;
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->address = [
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => @$countryData->$countryCode->country,
        ];
        $user->status = $request->status ? 1 : 0;
        $user->ev = $request->ev ? 1 : 0;
        $user->sv = $request->sv ? 1 : 0;
        $user->ts = $request->ts ? 1 : 0;
        $user->tv = $request->tv ? 1 : 0;
        $user->save();

        $notify[] = ['success', 'User detail has been updated'];
        return redirect()->back()->withNotify($notify);
    }

    public function addSubBalance(Request $request, $id)
    {
        $request->validate(['amount' => 'required|numeric|gt:0']);

        $user = User::findOrFail($id);
        $amount = $request->amount;
        $general = GeneralSetting::first(['cur_text', 'cur_sym']);
        $trx = getTrx();

        if ($request->act) {
            $user->balance += $amount;
            $user->save();
            $notify[] = ['success', $general->cur_sym . $amount . ' has been added to ' . $user->username . '\'s balance'];

            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = 'Added Balance Via Admin';
            $transaction->trx =  $trx;
            $transaction->save();

            notify($user, 'BAL_ADD', [
                'trx' => $trx,
                'amount' => showAmount($amount),
                'currency' => $general->cur_text,
                'post_balance' => showAmount($user->balance),
            ]);
        } else {
            if ($amount > $user->balance) {
                $notify[] = ['error', $user->username . '\'s has insufficient balance.'];
                return back()->withNotify($notify);
            }
            $user->balance -= $amount;
            $user->save();



            $transaction = new Transaction();
            $transaction->user_id = $user->id;
            $transaction->amount = $amount;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '-';
            $transaction->details = 'Subtract Balance Via Admin';
            $transaction->trx =  $trx;
            $transaction->save();


            notify($user, 'BAL_SUB', [
                'trx' => $trx,
                'amount' => showAmount($amount),
                'currency' => $general->cur_text,
                'post_balance' => showAmount($user->balance)
            ]);
            $notify[] = ['success', $general->cur_sym . $amount . ' has been subtracted from ' . $user->username . '\'s balance'];
        }
        return back()->withNotify($notify);
    }


    public function userLoginHistory($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'User Login History - ' . $user->username;
        $emptyMessage = 'No users login found.';
        $login_logs = $user->login_logs()->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('admin.users.logins', compact('pageTitle', 'emptyMessage', 'login_logs'));
    }



    public function showEmailSingleForm($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'Send Email To: ' . $user->username;
        return view('admin.users.email_single', compact('pageTitle', 'user'));
    }

    public function sendEmailSingle(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        $user = User::findOrFail($id);
        sendGeneralEmail($user->email, $request->subject, $request->message, $user->username);
        $notify[] = ['success', $user->username . ' will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function transactions(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search User Transactions : ' . $user->username;
            $transactions = $user->transactions()->where('trx', $search)->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
            $emptyMessage = 'No transactions';
            return view('admin.reports.transactions', compact('pageTitle', 'search', 'user', 'transactions', 'emptyMessage'));
        }
        $pageTitle = 'User Transactions : ' . $user->username;
        $transactions = $user->transactions()->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
        $emptyMessage = 'No transactions';
        return view('admin.reports.transactions', compact('pageTitle', 'user', 'transactions', 'emptyMessage'));
    }

    public function deposits(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $userId = $user->id;
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search User Deposits : ' . $user->username;
            $deposits = $user->deposits()->where('trx', $search)->orderBy('id', 'desc')->paginate(getPaginate());
            $emptyMessage = 'No deposits';
            return view('admin.deposit.log', compact('pageTitle', 'search', 'user', 'deposits', 'emptyMessage', 'userId'));
        }

        $pageTitle = 'User Deposit : ' . $user->username;
        $deposits = $user->deposits()->orderBy('id', 'desc')->with(['gateway', 'user'])->paginate(getPaginate());
        $successful = $user->deposits()->orderBy('id', 'desc')->where('status', 1)->sum('amount');
        $pending = $user->deposits()->orderBy('id', 'desc')->where('status', 2)->sum('amount');
        $rejected = $user->deposits()->orderBy('id', 'desc')->where('status', 3)->sum('amount');
        $emptyMessage = 'No deposits';
        $scope = 'all';
        return view('admin.deposit.log', compact('pageTitle', 'user', 'deposits', 'emptyMessage', 'userId', 'scope', 'successful', 'pending', 'rejected'));
    }


    public function depViaMethod($method, $type = null, $userId)
    {
        $method = Gateway::where('alias', $method)->firstOrFail();
        $user = User::findOrFail($userId);
        if ($type == 'approved') {
            $pageTitle = 'Approved Payment Via ' . $method->name;
            $deposits = Deposit::where('method_code', '>=', 1000)->where('user_id', $user->id)->where('method_code', $method->code)->where('status', 1)->orderBy('id', 'desc')->with(['user', 'gateway'])->paginate(getPaginate());
        } elseif ($type == 'rejected') {
            $pageTitle = 'Rejected Payment Via ' . $method->name;
            $deposits = Deposit::where('method_code', '>=', 1000)->where('user_id', $user->id)->where('method_code', $method->code)->where('status', 3)->orderBy('id', 'desc')->with(['user', 'gateway'])->paginate(getPaginate());
        } elseif ($type == 'successful') {
            $pageTitle = 'Successful Payment Via ' . $method->name;
            $deposits = Deposit::where('status', 1)->where('user_id', $user->id)->where('method_code', $method->code)->orderBy('id', 'desc')->with(['user', 'gateway'])->paginate(getPaginate());
        } elseif ($type == 'pending') {
            $pageTitle = 'Pending Payment Via ' . $method->name;
            $deposits = Deposit::where('method_code', '>=', 1000)->where('user_id', $user->id)->where('method_code', $method->code)->where('status', 2)->orderBy('id', 'desc')->with(['user', 'gateway'])->paginate(getPaginate());
        } else {
            $pageTitle = 'Payment Via ' . $method->name;
            $deposits = Deposit::where('status', '!=', 0)->where('user_id', $user->id)->where('method_code', $method->code)->orderBy('id', 'desc')->with(['user', 'gateway'])->paginate(getPaginate());
        }
        $pageTitle = 'Deposit History: ' . $user->username . ' Via ' . $method->name;
        $methodAlias = $method->alias;
        $emptyMessage = 'Deposit Log';
        return view('admin.deposit.log', compact('pageTitle', 'emptyMessage', 'deposits', 'methodAlias', 'userId'));
    }



    public function withdrawals(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->search) {
            $search = $request->search;
            $pageTitle = 'Search User Withdrawals : ' . $user->username;
            $withdrawals = $user->withdrawals()->where('trx', 'like', "%$search%")->orderBy('id', 'desc')->paginate(getPaginate());
            $emptyMessage = 'No withdrawals';
            return view('admin.withdraw.withdrawals', compact('pageTitle', 'user', 'search', 'withdrawals', 'emptyMessage'));
        }
        $pageTitle = 'User Withdrawals : ' . $user->username;
        $withdrawals = $user->withdrawals()->orderBy('id', 'desc')->paginate(getPaginate());
        $emptyMessage = 'No withdrawals';
        $userId = $user->id;
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'user', 'withdrawals', 'emptyMessage', 'userId'));
    }

    public  function withdrawalsViaMethod($method, $type, $userId)
    {
        $method = WithdrawMethod::findOrFail($method);
        $user = User::findOrFail($userId);
        if ($type == 'approved') {
            $pageTitle = 'Approved Withdrawal of ' . $user->username . ' Via ' . $method->name;
            $withdrawals = Withdrawal::where('status', 1)->where('user_id', $user->id)->with(['user', 'method'])->orderBy('id', 'desc')->paginate(getPaginate());
        } elseif ($type == 'rejected') {
            $pageTitle = 'Rejected Withdrawals of ' . $user->username . ' Via ' . $method->name;
            $withdrawals = Withdrawal::where('status', 3)->where('user_id', $user->id)->with(['user', 'method'])->orderBy('id', 'desc')->paginate(getPaginate());
        } elseif ($type == 'pending') {
            $pageTitle = 'Pending Withdrawals of ' . $user->username . ' Via ' . $method->name;
            $withdrawals = Withdrawal::where('status', 2)->where('user_id', $user->id)->with(['user', 'method'])->orderBy('id', 'desc')->paginate(getPaginate());
        } else {
            $pageTitle = 'Withdrawals of ' . $user->username . ' Via ' . $method->name;
            $withdrawals = Withdrawal::where('status', '!=', 0)->where('user_id', $user->id)->with(['user', 'method'])->orderBy('id', 'desc')->paginate(getPaginate());
        }
        $emptyMessage = 'Withdraw Log Not Found';
        return view('admin.withdraw.withdrawals', compact('pageTitle', 'withdrawals', 'emptyMessage', 'method'));
    }

    public function showEmailAllForm()
    {
        $pageTitle = 'Send Email To All Users';
        return view('admin.users.email_all', compact('pageTitle'));
    }

    public function sendEmailAll(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:65000',
            'subject' => 'required|string|max:190',
        ]);

        foreach (User::where('status', 1)->cursor() as $user) {
            sendGeneralEmail($user->email, $request->subject, $request->message, $user->username);
        }

        $notify[] = ['success', 'All users will receive an email shortly.'];
        return back()->withNotify($notify);
    }

    public function login($id)
    {
        $user = User::findOrFail($id);
        Auth::login($user);
        return redirect()->route('user.home');
    }

    public function emailLog($id)
    {
        $user = User::findOrFail($id);
        $pageTitle = 'Email log of ' . $user->username;
        $logs = EmailLog::where('user_id', $id)->with('user')->orderBy('id', 'desc')->paginate(getPaginate());
        $emptyMessage = 'No data found';
        return view('admin.users.email_log', compact('pageTitle', 'logs', 'emptyMessage', 'user'));
    }

    public function emailDetails($id)
    {
        $email = EmailLog::findOrFail($id);
        $pageTitle = 'Email details';
        return view('admin.users.email_details', compact('pageTitle', 'email'));
    }


    public function userRef($id)
    {

        $emptyMessage = 'No user found';
        $user = User::findOrFail($id);
        $pageTitle = 'Referred By ' . $user->username;
        $users = User::where('ref_id', $id)->latest()->paginate(getPaginate());
        return view('admin.users.list', compact('pageTitle', 'emptyMessage', 'users'));
    }

    public function tree($username)
    {

        $user = User::where('username', $username)->first();

        if ($user) {
            $tree = showTreePage($user->id);
            $pageTitle = "Tree of " . $user->fullname;
            return view('admin.users.tree', compact('tree', 'pageTitle'));
        }

        $notify[] = ['error', 'Tree Not Found!!'];
        return redirect()->route('admin.dashboard')->withNotify($notify);
    }

    public function otherTree(Request $request, $username = null)
    {
        if ($request->username) {
            $user = User::where('username', $request->username)->first();
        } else {
            $user = User::where('username', $username)->first();
        }
        if ($user) {
            $tree = showTreePage($user->id);
            $pageTitle = "Tree of " . $user->fullname;
            return view('admin.users.tree', compact('tree', 'pageTitle'));
        }

        $notify[] = ['error', 'Tree Not Found!!'];
        return redirect()->route('admin.dashboard')->withNotify($notify);
    }


    public function approvedSellers(Request $request)
    {
        if ($request->search != null && $request->city == null) {

            $sellers = DB::connection('mysql_store')
                ->table('sellers')
                ->leftJoin('users', 'users.name', '=', 'sellers.dds_username')
                ->where('status', 'approved')
                ->where('sellers.dds_username', $request->search)
                ->paginate();
        } elseif ($request->search == null && $request->city != null) {
            $sellers = DB::connection('mysql_store')
                ->table('sellers')
                ->leftJoin('users', 'users.name', '=', 'sellers.dds_username')
                ->where('status', 'approved')
                ->where('users.city', $request->city)
                ->paginate();
        } elseif ($request->search != null && $request->city != null) {

            $sellers = DB::connection('mysql_store')
                ->table('sellers')
                ->leftJoin('users', 'users.name', '=', 'sellers.dds_username')
                ->where('status', 'approved')
                ->where('sellers.dds_username', $request->search)
                ->where('users.city', $request->city)
                ->paginate();
        } else {
            $sellers = DB::connection('mysql_store')
                ->table('sellers')
                ->leftJoin('users', 'users.name', '=', 'sellers.dds_username')
                ->where('status', 'approved')
                ->orderBy('sellers.id', 'DESC')
                ->paginate();
        }

        $pageTitle = 'Approved Sellers';
        $emptyMessage = 'No records found';
        return view('admin.details.approvedSellers', compact('pageTitle', 'emptyMessage', 'sellers'));
    }

    public function pendingSellers(Request $request)
    {
        if ($request->search != null && $request->city == null) {

            $sellers = DB::connection('mysql_store')
                ->table('sellers')
                ->leftJoin('users', 'users.name', '=', 'sellers.dds_username')
                ->where('status', 'pending')
                ->where('sellers.dds_username', $request->search)
                ->paginate();
        } elseif ($request->search == null && $request->city != null) {
            $sellers = DB::connection('mysql_store')
                ->table('sellers')
                ->leftJoin('users', 'users.name', '=', 'sellers.dds_username')
                ->where('status', 'pending')
                ->where('users.city', $request->city)
                ->paginate();
        } elseif ($request->search != null && $request->city != null) {

            $sellers = DB::connection('mysql_store')
                ->table('sellers')
                ->leftJoin('users', 'users.name', '=', 'sellers.dds_username')
                ->where('status', 'pending')
                ->where('sellers.dds_username', $request->search)
                ->where('users.city', $request->city)
                ->paginate();
        } else {
            $sellers = DB::connection('mysql_store')
                ->table('sellers')
                ->leftJoin('users', 'users.name', '=', 'sellers.dds_username')
                ->where('status', 'pending')
                ->orderBy('sellers.id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'Pending Sellers';
        $emptyMessage = 'No records found';
        return view('admin.details.pendingSellers', compact('pageTitle', 'emptyMessage', 'sellers'));
    }
    public function posOrders(Request $request)
    {
        if ($request->search) {
            $orders = DB::connection('mysql_store')
                ->table('orders')
                ->leftJoin('sellers', 'sellers.id', '=', 'orders.seller_id')
                ->leftJoin('users', 'users.id', '=', 'orders.customer_id')
                ->where('users.name', $request->search)
                ->where('order_type', 'pos')
                ->paginate();
        } else {
            $orders = DB::connection('mysql_store')
                ->table('orders')
                ->leftJoin('sellers', 'sellers.id', '=', 'orders.seller_id')
                ->leftJoin('users', 'users.id', '=', 'orders.customer_id')
                ->where('order_type', 'pos')
                ->orderBy('orders.id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'POS Orders';
        $emptyMessage = 'No records found';
        return view('admin.details.posOrders', compact('pageTitle', 'emptyMessage', 'orders'));
    }
    public function defaultOrders(Request $request)
    {
        if ($request->search) {
            $orders = DB::connection('mysql_store')
                ->table('orders')
                ->leftJoin('users', 'users.id', '=', 'orders.customer_id')
                ->where('users.name', $request->search)
                ->where('orders.order_type', 'default_type')
                ->paginate();
        } else {
            $orders = DB::connection('mysql_store')
                ->table('orders')
                ->leftJoin('users', 'users.id', '=', 'orders.customer_id')
                ->where('orders.order_type', 'default_type')
                ->orderBy('orders.id', 'DESC')
                ->paginate();
        }

        $pageTitle = 'Default Orders';
        $emptyMessage = 'No records found';
        return view('admin.details.defaultOrders', compact('pageTitle', 'emptyMessage', 'orders'));
    }
    public function adminProducts(Request $request)
    {
        if ($request->search) {
            $products = DB::connection('mysql_store')
                ->table('products')
                ->where('added_by', 'admin')
                ->where('name', $request->search)
                ->paginate();
        } else {
            $products = DB::connection('mysql_store')
                ->table('products')
                ->where('added_by', 'admin')
                ->orderBy('id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'Admin Products';
        $emptyMessage = 'No records found';
        return view('admin.details.adminProducts', compact('pageTitle', 'emptyMessage', 'products'));
    }

    public function sellerProducts(Request $request)
    {
        $allSellerProducts = DB::connection('mysql_store')
            ->table('seller_products')->get();
        $show = false;
        if ($request->search != null && $request->p_id != null) {

            $products = DB::connection('mysql_store')
                ->table('seller_products')
                ->leftJoin('sellers', 'sellers.id', '=', 'seller_products.seller_id')
                ->select(['sellers.dds_username', 'sellers.id as s_id', 'sellers.*', 'seller_products.*'])
                ->where('sellers.dds_username', $request->search)
                ->where('seller_products.name','like', "%$request->p_id%")->distinct()
                ->paginate();
            $show = true;
        } elseif ($request->search != null && $request->p_id == null) {

            $products = DB::connection('mysql_store')
                ->table('seller_products')
                ->leftJoin('sellers', 'sellers.id', '=', 'seller_products.seller_id')

                ->select(['sellers.dds_username', 'sellers.id as s_id', 'sellers.*', 'seller_products.*'])
                ->where('sellers.dds_username', $request->search)->distinct()
                ->paginate(10);
        } elseif ($request->search == null && $request->p_id != null) {

            $products = DB::connection('mysql_store')
                ->table('seller_products')
                ->leftJoin('sellers', 'sellers.id', '=', 'seller_products.seller_id')
                ->select(['sellers.dds_username', 'sellers.id as s_id', 'sellers.*', 'seller_products.*'])
                ->where('seller_products.name','like', "%$request->p_id%")->distinct()
                ->paginate();
        } else {
            $products = DB::connection('mysql_store')
                ->table('seller_products')
                ->leftJoin('sellers', 'sellers.id', '=', 'seller_products.seller_id')
                ->select(['sellers.dds_username', 'sellers.id as s_id', 'sellers.*', 'seller_products.*'])
                ->orderBy('seller_products.id', 'DESC')
                ->paginate();
        }
        // return $products->count();

        $pageTitle = 'Seller Products';
        $emptyMessage = 'No records found';
        return view('admin.details.sellerProducts', compact('pageTitle', 'show', 'emptyMessage', 'products', 'allSellerProducts'));
    }

    public function storeReference(Request $request)
    {
        if ($request->search) {
            $references = DB::table('users')
                ->where('store_reference', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $references = DB::table('users')
                ->where('store_reference', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'Store References';
        $emptyMessage = 'No records found';
        return view('admin.details.storeReference', compact('pageTitle', 'emptyMessage', 'references'));
    }
    public function storeBonus(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('users')->where('username', $request->search)
                ->where('store_bonus', '<>', 0)
                ->paginate();
        } else {
            $bonuses = DB::table('users')
                ->where('store_bonus', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'Store Bonus';
        $emptyMessage = 'No records found';
        return view('admin.details.storeBonus', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }
    public function reference(Request $request)
    {
        if ($request->search) {
            $references = DB::table('users')
                ->where('reference_bonus', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $references = DB::table('users')
                ->where('reference_bonus', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }

        $pageTitle = 'Reference Bonus';
        $emptyMessage = 'No records found';
        return view('admin.details.reference', compact('pageTitle', 'emptyMessage', 'references'));
    }
    public function city_reference(Request $request)
    {
        if ($request->search) {
            $references = DB::table('users')
                ->where('city_reference', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $references = DB::table('users')
                ->where('city_reference', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }

        $pageTitle = 'City Reference';
        $emptyMessage = 'No records found';
        return view('admin.details.city_reference', compact('pageTitle', 'emptyMessage', 'references'));
    }
    public function franchise_bonus(Request $request)
    {
        if ($request->search) {
            $references = DB::table('users')
                ->where('franchise_bonus', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $references = DB::table('users')
                ->where('franchise_bonus', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }

        $pageTitle = 'Franchise Bonus';
        $emptyMessage = 'No records found';
        return view('admin.details.franchise_bonus', compact('pageTitle', 'emptyMessage', 'references'));
    }
    public function franchise_ref_bonus(Request $request)
    {
        if ($request->search) {
            $references = DB::table('users')
                ->where('franchise_ref_bonus', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $references = DB::table('users')
                ->where('franchise_ref_bonus', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }

        $pageTitle = 'Franchise Reference Bonus';
        $emptyMessage = 'No records found';
        return view('admin.details.franchise_ref_bonus', compact('pageTitle', 'emptyMessage', 'references'));
    }
    public function productPartnerBonus(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('users')
                ->where('product_partner_bonus', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $bonuses = DB::table('users')
                ->where('product_partner_bonus', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'Product Partner Bonus';
        $emptyMessage = 'No records found';
        return view('admin.details.productPartnerBonus', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }
    public function stockistReferenceBonus(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('users')
                ->where('stockist_ref_bonus', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $bonuses = DB::table('users')
                ->where('stockist_ref_bonus', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'Stockist Reference Bonus';
        $emptyMessage = 'No records found';
        return view('admin.details.stockistReferenceBonus', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }
    public function stockistBonus(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('users')
                ->where('stockist_bonus', '<>', 0)
                ->where('username', $request->search)
                ->paginate();
        } else {
            $bonuses = DB::table('users')
                ->where('stockist_bonus', '<>', 0)
                ->orderBy('id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'Stockist Bonus';
        $emptyMessage = 'No records found';
        return view('admin.details.stockistBonus', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }
    public function pv(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('users')
                ->where('pv', '<>', 0)
                ->where('username', $request->search)
                ->paginate();
        } else {
            $bonuses = DB::table('users')
                ->where('pv', '<>', 0)
                ->orderBy('id', 'DESC')
                ->paginate();
        }

        $pageTitle = 'PV';
        $emptyMessage = 'No records found';
        return view('admin.details.pv', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }


    public function bv(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('users')
                ->where('bv', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $bonuses = DB::table('users')
                ->where('bv', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'BV';
        $emptyMessage = 'No records found';
        return view('admin.details.bv', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }
    public function EPinCredit(Request $request)
    {
        if ($request->search) {
            $bonuses = DB::table('users')
                ->where('epin_credit', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $bonuses = DB::table('users')
                ->where('epin_credit', '<>', 0)->orderBy('id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'E Pin Credits';
        $emptyMessage = 'No records found';
        return view('admin.details.EPinCredit', compact('pageTitle', 'emptyMessage', 'bonuses'));
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
        return view('admin.details.balance', compact('pageTitle', 'emptyMessage', 'bonuses'));
    }
    public function dsp(Request $request)
    {
        if ($request->search) {
            $user = User::where('username', $request->search)->first();
            $transactions = Transaction::where('remark', 'purchased_plan')->where('user_id', $user->id)->orderBy('id', 'DESC')->with('user')->paginate();
        } else {
            $transactions = Transaction::where('remark', 'purchased_plan')->orderBy('id', 'DESC')->with('user')->paginate();
        }
        $pageTitle = 'DSP';
        $emptyMessage = 'No records found';
        return view('admin.details.dsp', compact('pageTitle', 'emptyMessage', 'transactions'));
    }
    public function pairs(Request $request)
    {
        if ($request->search) {
            $pairs = DB::table('users')
                ->leftJoin('user_extras', 'user_extras.user_id', '=', 'users.id')
                ->where('users.username', $request->search)
                ->paginate();
        } else {
            $pairs = DB::table('users')
                ->leftJoin('user_extras', 'user_extras.user_id', '=', 'users.id')
                ->orderBy('users.id', 'DESC')
                ->paginate();
        }
        $pageTitle = 'Pairs';
        $emptyMessage = 'No records found';
        return view('admin.details.pairs', compact('pageTitle', 'emptyMessage', 'pairs'));
    }
    public function walletStatements()
    {


        $transactions = Transaction::where('remark', 'admin_wallet')->with('user')->paginate();

        $bonuses = DB::table('users')
            ->where('balance', '<>', 0)
            ->paginate();

        $pageTitle = 'Wallet Statements';
        $emptyMessage = 'No records found';
        return view('admin.details.walletStatements', compact('pageTitle', 'emptyMessage', 'transactions'));
    }

    public function promo(Request $request)
    {
        if ($request->search) {
            $promo = DB::table('users')->where('promo', '<>', 0)->where('username', $request->search)
                ->paginate();
        } else {
            $promo = DB::table('users')->where('promo', '<>', 0)
                ->paginate();
        }
        $pageTitle = 'Promo';
        $emptyMessage = 'No records found';
        return view('admin.details.promo', compact('pageTitle', 'emptyMessage', 'promo'));
    }
}

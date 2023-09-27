<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Plan;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MyRecordsContorller extends Controller {

  public function __construct()
  {
      $this->activeTemplate = activeTemplate();
  }


  public function index(Request $request) {
    $pageTitle = 'My Statement';
    $user = Auth::user();

    if(!$request->search_word) {
      $userPlan=DB::table('transactions')
	            ->select('transactions.*')
	            ->where('transactions.user_id',$user->id)
	            ->orderBy('id','desc')->paginate();
      $userPlanAll=DB::table('transactions')
	            ->select('transactions.*')
	            ->where('transactions.user_id',$user->id)
	            ->orderBy('id','desc')->get();
    } else {
    
      if($request->start_date_search) {

        $userPlan=DB::table('transactions')
        ->select('transactions.*')
        ->where('transactions.user_id',$user->id)
        ->where('remark',$request->search_word)
        ->whereBetween( DB::raw('DATE(`created_at`)'),[$request->start_date_search, $request->end_date_search])
        ->orderBy('id','desc')->paginate();
        
        $userPlanAll=DB::table('transactions')
        ->select('transactions.*')
        ->where('transactions.user_id',$user->id)
        ->where('remark',$request->search_word)
        ->whereBetween( DB::raw('DATE(`created_at`)'),[$request->start_date_search, $request->end_date_search])
        ->orderBy('id','desc')->get();
      }
      else {
        $userPlan=DB::table('transactions')
        ->select('transactions.*')
        ->where('transactions.user_id',$user->id)
        ->where('remark',$request->search_word)
       ->orderBy('id','desc')->paginate();
        
        $userPlanAll=DB::table('transactions')
        ->select('transactions.*')
        ->where('transactions.user_id',$user->id)
        ->where('remark',$request->search_word)
        ->orderBy('id','desc')->get();
      }
    }
  
    $page_total = $userPlan->sum('amount');
    $all_total = $userPlanAll->sum('amount');
	  
	  
    return view($this->activeTemplate. 'user.mystatement', compact('pageTitle','all_total', 'page_total', 'user','userPlan'));
  }
}
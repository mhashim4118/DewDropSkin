<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Plan;
use App\Models\Stockist;
use App\Models\Stocklist_buy;
use App\Models\Membership;
use Illuminate\Http\Request;

class MlmController extends Controller
{
    public function plan()
    {
        $pageTitle = 'MLM Plans';
        $emptyMessage = 'No Plan found';
        $plans = Plan::paginate(getPaginate());;
        return view('admin.plan.index', compact('pageTitle', 'plans', 'emptyMessage'));
    }

    public function planStore(Request $request)
    {
        $this->validate($request, [
            'name'              => 'required',
            'price'             => 'required|numeric|min:0',
            'bv'                => 'required|min:0|integer',
            'ref_com'           => 'required|numeric|min:0',
            'tree_com'          => 'required|numeric|min:0',
        ]);


        $plan = new Plan();
        $plan->name             = $request->name;
        $plan->price            = $request->price;
        $plan->bv               = $request->bv;
        $plan->ref_com          = $request->ref_com;
        $plan->tree_com         = $request->tree_com;
        $plan->status           = $request->status?1:0;
        $plan->save();

        $notify[] = ['success', 'New Plan created successfully'];
        return back()->withNotify($notify);
    }


    public function planUpdate(Request $request)
    {
        $this->validate($request, [
            'id'                => 'required',
            'name'              => 'required',
            'price'             => 'required|numeric|min:0',
            'bv'                => 'required|min:0|integer',
            'ref_com'           => 'required|numeric|min:0',
            'tree_com'          => 'required|numeric|min:0',
        ]);

        $plan                   = Plan::find($request->id);
        $plan->name             = $request->name;
        $plan->price            = $request->price;
        $plan->bv               = $request->bv;
        $plan->ref_com          = $request->ref_com;
        $plan->tree_com         = $request->tree_com;
        $plan->status           = $request->status?1:0;
        $plan->save();

        $notify[] = ['success', 'Plan Updated Successfully.'];
        return back()->withNotify($notify);
    }



    public function matchingUpdate(Request $request)
    {
        $this->validate($request, [
            'bv_price' => 'required|min:0',
            'total_bv' => 'required|min:0|integer',
            'max_bv' => 'required|min:0|integer',
        ]);

        $setting = GeneralSetting::first();

        if ($request->matching_bonus_time == 'daily') {
            $when = $request->daily_time;
        } elseif ($request->matching_bonus_time == 'weekly') {
            $when = $request->weekly_time;
        } elseif ($request->matching_bonus_time == 'monthly') {
            $when = $request->monthly_time;
        }


        $setting->bv_price = $request->bv_price;
        $setting->total_bv = $request->total_bv;
        $setting->max_bv = $request->max_bv;
        $setting->cary_flash = $request->cary_flash;
        $setting->matching_bonus_time = $request->matching_bonus_time;
        $setting->matching_when = $when;
        $setting->save();

        $notify[] = ['success', 'Matching bonus has been updated.'];
        return back()->withNotify($notify);

    }

    public function stockists(){
        $pageTitle = 'Stockists Plans';
        $emptyMessage = 'No Plan found';
        $stockist = Stockist::paginate(getPaginate());
        return view('admin.stockists.index', compact('pageTitle', 'stockist', 'emptyMessage'));
    }

    public function stockistStore(Request $request)
    {
        $this->validate($request, [
            'name'              => 'required',
            'price'             => 'required|numeric|min:0',
            'e_pin_qty'         => 'required|min:0|integer',
            'ref_bonus'         => 'required|min:0',
            'stockist_bonus'    => 'required|numeric|min:0',
        ]);
        $stockist = new Stockist();
        $stockist->name             = $request->name;
        $stockist->price            = $request->price;
        $stockist->e_pin_qty       = $request->e_pin_qty;
        $stockist->refferal_bonus   = $request->ref_bonus;
        $stockist->stockist_bonus   = $request->stockist_bonus;
       // $stockist->e_pin            = '84381';
        $stockist->status           = $request->status?1:0;
        $stockist->save();

        $notify[] = ['success', 'New Plan created successfully'];
        return back()->withNotify($notify);
    }

    public function stockistUpdate(Request $request)
    {
        $this->validate($request, [
            'name'              => 'required',
            'price'             => 'required|numeric|min:0',
            'e_pin_qty'         => 'required|min:0|integer',
            'ref_bonus'         => 'required|min:0',
            'stockist_bonus'    => 'required|numeric|min:0',
        ]);
        $stockist = Stockist::find($request->id);
        $stockist->name             = $request->name;
        $stockist->price            = $request->price;
        $stockist->e_pin_qty       = $request->e_pin_qty;
        $stockist->refferal_bonus   = $request->ref_bonus;
        $stockist->stockist_bonus   = $request->stockist_bonus;
        // $stockist->e_pin            = '84381';
        $stockist->status           = $request->status?1:0;
        $stockist->save();
        $notify[] = ['success', 'Plan Updated Successfully.'];
        return back()->withNotify($notify);
    }
    public function stockistList()
    {
        $pageTitle = 'Stockists Detail';
        $emptyMessage = 'No Plan found';
        $stockist = Stocklist_buy::with('Stockist','user')->paginate(getPaginate());
        return view('admin.stockists.list', compact('pageTitle', 'stockist', 'emptyMessage'));
       
    }
	public function memberShip(){
        $pageTitle = 'Memberships';
        $emptyMessage = 'No Plan found';
        $member = Membership::paginate(getPaginate());
        return view('admin.memberShip', compact('pageTitle', 'member', 'emptyMessage'));
    }

    public function memberShip_Store(Request $request){
        $this->validate($request, [
            'name'              => 'required',
            'price'             => 'required|numeric|min:0', 
        ]);
        $member = new Membership;
        $member->name = $request->name;
        $member->price = $request->price;
        $member->type = $request->type;
        $member->save();
        $notify[] = ['success', 'Membership added Successfully.'];
        return back()->withNotify($notify);
    }

    public function memberShip_update(Request $request){
        $member = Membership::find($request->id);
         $member->name = $request->name;
         $member->price = $request->price;
         $member->type = $request->type;
         $member->status = $request->status?1:0;
         $member->save();
         $notify[] = ['success', 'Membership Updated Successfully.'];
         return back()->withNotify($notify);
    }
}

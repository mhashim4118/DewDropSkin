<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function admin_orders(Request $request){
        $pageTitle = 'Admin Orders';
        
        if($request->o_id != null) {

            $orders = DB::connection('mysql_store')->table('orders')
            //->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
            ->leftJoin('users', 'users.id', '=', 'orders.customer_id')
            ->leftJoin('sellers', 'sellers.id', '=', 'orders.seller_id')
            ->where('orders.seller_is', 'admin')
            ->where('orders.id', $request->o_id)
            ->select(['orders.*','orders.id as o_id', 'users.*'])
            ->paginate(); 
            
        } else {

            $orders = DB::connection('mysql_store')->table('orders')
            //->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
            ->leftJoin('users', 'users.id', '=', 'orders.customer_id')
            ->leftJoin('sellers', 'sellers.id', '=', 'orders.seller_id')
            ->where('orders.seller_is', 'admin')
            ->select(['orders.*','orders.id as o_id', 'users.*'])
            ->orderBy('orders.id', 'DESC')
            ->paginate(); 
        }
        
     
        //Order::with('product','user')->orderBy('id','desc')->paginate(10);
        $emptyMessage = 'Order not found';
        return view('admin.store_orders.admin_orders',compact('pageTitle','orders','emptyMessage'));
    }
    public function seller_orders(Request $request){
        $pageTitle = 'Seller Orders';

        if($request->o_id != null && $request->search == null) {

            $orders = DB::connection('mysql_store')->table('orders')
            //->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
            //->leftJoin('seller_products', 'seller_products.id', '=', 'order_details.product_id')
            ->leftJoin('users', 'users.id', '=', 'orders.customer_id')
            ->leftJoin('sellers', 'sellers.id', '=', 'orders.seller_id')
             ->where('orders.seller_is', 'seller')
            ->where('orders.id', $request->o_id)
            ->select(['sellers.dds_username','orders.*','orders.id as o_id', 'users.*'])
            ->paginate(); 
        } 

        elseif($request->o_id == null && $request->search != null) {

            $orders = DB::connection('mysql_store')->table('orders')
            //->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
            //->leftJoin('seller_products', 'seller_products.id', '=', 'order_details.product_id')
            ->leftJoin('users', 'users.id', '=', 'orders.customer_id')
            ->leftJoin('sellers', 'sellers.id', '=', 'orders.seller_id')
             ->where('orders.seller_is', 'seller')
           
            ->where('sellers.dds_username', $request->search)
            ->select(['sellers.dds_username','orders.*','orders.id as o_id', 'users.*'])
            ->paginate(); 
        } 
        elseif($request->o_id != null && $request->search != null) {

            $orders = DB::connection('mysql_store')->table('orders')
            //->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
            //->leftJoin('seller_products', 'seller_products.id', '=', 'order_details.product_id')
            ->leftJoin('users', 'users.id', '=', 'orders.customer_id')
            ->leftJoin('sellers', 'sellers.id', '=', 'orders.seller_id')
             ->where('orders.seller_is', 'seller')
            ->where('orders.id', $request->o_id)
            ->where('sellers.dds_username', $request->search)
            ->select(['sellers.dds_username','orders.*','orders.id as o_id', 'users.*'])
            ->paginate(); 
        } 
        
        else {

            $orders = DB::connection('mysql_store')->table('orders')
            //->leftJoin('order_details', 'order_details.order_id', '=', 'orders.id')
            //->leftJoin('seller_products', 'seller_products.id', '=', 'order_details.product_id')
            ->leftJoin('users', 'users.id', '=', 'orders.customer_id')
            ->leftJoin('sellers', 'sellers.id', '=', 'orders.seller_id')
            ->where('customer_type', 'customer')
            ->select(['sellers.dds_username','orders.*','orders.id as o_id', 'users.*'])
            ->orderBy('orders.id', 'DESC')
            ->paginate(); 
        
        }
     
        //Order::with('product','user')->orderBy('id','desc')->paginate(10);
        $emptyMessage = 'Order not found';
        return view('admin.store_orders.seller_orders',compact('pageTitle','orders','emptyMessage'));
    }

    public function status(Request $request,$id){
        $request->validate([
            'status'=>'required|in:1,2'
        ]);

        $order = Order::where('status',0)->findOrFail($id);
        $product = $order->product;
        $user = $order->user;

        if($request->status == 1){
            $order->status = 1;
            $details = $product->name.' product purchase';
            updateBV($user->id, $product->bv, $details);
            $template = 'order_shipped';
        }else{
            $order->status = 2;
            $user->balance += $order->total_price;
            $user->save();

            $transaction = new Transaction();
            $transaction->user_id = $order->user_id;
            $transaction->amount = $order->total_price;
            $transaction->post_balance = $user->balance;
            $transaction->charge = 0;
            $transaction->trx_type = '+';
            $transaction->details = $product->name.' Order cancel';
            $transaction->trx =  $order->trx;
            $transaction->save();

            $product->quantity += $order->quantity;
            $product->save();

            $template = 'order_cancelled';

        }

        $order->save();

        $general = GeneralSetting::first();

        notify($user,$template,[
            'product_name'=>$product->name,
            'quantity'=>$request->quantity,
            'price'=>showAmount($product->price),
            'total_price'=>showAmount($order->total_price),
            'currency'=>$general->cur_text,
            'trx'=>$order->trx,
        ]);

        $notify[] = ['success','Product status updated successfully'];
        return back()->withNotify($notify);

    }
}

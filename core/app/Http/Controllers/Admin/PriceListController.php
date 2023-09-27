<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PriceListController extends Controller
{
  public function index()
  {

    $pageTitle = 'Price List';
    $emptyMessage = 'No precords found.';
    $price_lists = DB::table('price_list')->latest()->paginate(getPaginate());
    return view('admin.price_list.index', compact('pageTitle', 'emptyMessage', 'price_lists'));
  }

  public function create()
  {
    $pageTitle = 'Create Price List';
    return view('admin.price_list.create', compact('pageTitle'));
  }

  public function store(Request $request)
  {
    $request->validate([
      'product_name'                     =>    'required',
      'price'                            =>    'required|numeric|gt:0',
      'bv'                               =>    'required|numeric|gt:0',
      'pv'                               =>    'required|numeric|gt:0',


    ]);

    $filename = "";
    if ($request->hasFile('thumbnail')) {
      $file = $request->file('thumbnail');
      $filename = time() . '_' . $file->getClientOriginalName();
      $location = 'assets/images/products/';
      $file->move($location, $filename);
    }



    DB::table('price_list')->insert([
      'product_name' => $request->product_name,
      'pv' => $request->pv,
      'bv' => $request->bv,
      'price' => $request->price,
      'thumbnail' => $filename,
    ]);


    $notify[] = ['success', 'Price List has been saved successfully'];
    return back()->withNotify($notify);
  }


  public function edit($id)
  {
    $price_list = DB::table('price_list')->find($id);
    $pageTitle = 'Edit Price List:' . $price_list->product_name;
    return view('admin.price_list.edit', compact('pageTitle', 'price_list'));
  }


  public function update(Request $request, $id)
  {

    $request->validate([
      'product_name'                     =>    'required',
      'price'                            =>    'required|numeric|gt:0',
      'bv'                               =>    'required|numeric|gt:0',
      'pv'                               =>    'required|numeric|gt:0',

    ]);




    DB::table('price_list')->where('id', $id)->update([
      'product_name' => $request->product_name,
      'pv' => $request->pv,
      'bv' => $request->bv,
      'price' => $request->price,
    ]);


    if ($request->hasFile('thumbnail')) {
      $file = $request->file('thumbnail');
      $filename = time() . '_' . $file->getClientOriginalName();
      $location = 'assets/images/products/';
      $file->move($location, $filename);

      DB::table('price_list')->where('id', $id)->update([
        'thumbnail' => $filename,
      ]);
    }




    $notify[] = ['success', 'Price List has been updated successfully'];
    return back()->withNotify($notify);
  }

  public function delete($id){
    DB::table('price_list')->delete($id);
    $notify[] = ['success', 'Price List has been deleted successfully'];
    return back()->withNotify($notify);
  }

  public  function uploadThumbnail($image, $old = null)
  {

    $path = imagePath()['products']['path'];
    $size = imagePath()['products']['size'];
    $thumb = imagePath()['products']['thumb'];
    $thumbnail = uploadImage($image, $path, $size, $old, $thumb);

    return $thumbnail;
  }
}
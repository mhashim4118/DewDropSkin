@extends('admin.layouts.app')

@section('panel')
      @if(@json_decode($general->sys_version)->version > systemDetails()['version'])
        <div class="row">
            <div class="col-md-12">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">
                        <h3 class="card-title"> @lang('New Version Available') <button class="btn btn--dark float-right">@lang('Version') {{json_decode($general->sys_version)->version}}</button> </h3>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-dark">@lang('What is the Update ?')</h5>
                        <p><pre  class="f-size--24">{{json_decode($general->sys_version)->details}}</pre></p>
                    </div>
                </div>
            </div>
        </div>
        @endif
        @if(@json_decode($general->sys_version)->message)
        <div class="row">
            @foreach(json_decode($general->sys_version)->message as $msg)
              <div class="col-md-12">
                  <div class="alert border border--primary" role="alert">
                      <div class="alert__icon bg--primary"><i class="far fa-bell"></i></div>
                      <p class="alert__message">@php echo $msg; @endphp</p>
                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
              </div>
            @endforeach
        </div>
        @endif

    <div class="row mb-30">
      

        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--orange b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{number_format($total_cb,2)}}</span>
                    </div>
                    <div class="desciption">
                        <span class="text-small" style="font-weight: bold;">@lang('Total Current Balance')</span>
                    </div>
                    <a href="{{route('admin.users.balance')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>

               </div>
            </div>
        </div><!-- dashboard-w1 end -->
		 <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ number_format($total_epin_credit, 2)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total E-Pin Credit')</span>
                    </div>
                    <a href="{{route('admin.users.EPinCredit')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        
		
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{number_format($total_dsp)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total DSP')</span>
                     </div>
               </div>
            </div>
        </div><!-- dashboard-w1 end -->
<div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{number_format($total_db,2)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total DSP Purchased')</span>
                     </div>
                     <a href="{{route('admin.users.dsp')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>

               </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$total_pb}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Pair Bonus')</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;"><b>{{$total_pb*200}}</b>@lang(' PKR Distributed')</span>
                    </div>
               </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--orange b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="la la-envelope"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{number_format($pairs)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Pair Users')</span>
                    </div>
                    <a href="{{route('admin.users.pairs')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
     
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--orange b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="la la-envelope"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{number_format($total_rb,2)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total DDS Reference')</span>
                    </div>
                    <a href="{{route('admin.users.reference')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
     
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--cyan b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{number_format($total_sb,2)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Store Bonus')</span>
                    </div>
                    <a href="{{route('admin.users.storeBonus')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
               </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--orange b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{number_format($total_srb,2)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Store Reference')</span>
                    </div>
                    <a href="{{route('admin.users.storeReference')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->

        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$approved_sellers->count()}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Approved Sellers')</span>
                    </div>
                    <a href="{{route('admin.users.approvedSellers')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--primary b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$pending_sellers->count()}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Pending Sellers')</span>
                    </div>
                    <a href="{{route('admin.users.pendingSellers')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--orange b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$pos_orders->count()}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Store POS Orders')</span>
                    </div>
                    <a href="{{route('admin.users.posOrders')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$default_orders->count()}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Seller Orders')</span>
                    </div>
                    <a href="{{route('admin.users.defaultOrders')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--primary b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$admin_products->count()}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Admin Products')</span>
                    </div>
                    <a href="{{route('admin.users.adminProducts')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$seller_products->count()}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Store Seller Products')</span>
                    </div>
                    <a href="{{route('admin.users.sellerProducts')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
       
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ number_format($total_product_partner_bonus, 2)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Products Partner')</span>
                    </div>
                    <a href="{{route('admin.users.productPartnerBonus')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$total_stockist_ref_bonus}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Stockist Reference Bonus')</span>
                    </div>
                    <a href="{{route('admin.users.stockistReferenceBonus')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$total_stockist_bonus}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Stockist Bonus')</span>
                    </div>
                    <a href="{{route('admin.users.stockistBonus')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$total_shop_reference}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Online Store Reference')</span>
                    </div>
                    <a href="{{route('admin.users.shopReferenceBonus')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
       
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ number_format($total_pv, 2)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total PV')</span>
                    </div>
                    <a href="{{route('admin.users.pv')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
         <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ number_format($total_bv, 2)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total BV')</span>
                    </div>
                    <a href="{{route('admin.users.bv')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
		<div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ number_format($total_promo, 2)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Promo')</span>
                    </div>
                    <a href="{{route('admin.users.promo')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
               
                </div>
            </div>
        </div><!-- dashboard-w1 end -->


        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{$widget['total_users']}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('Total Users')</span>
                    </div>
                    <a href="{{route('admin.users.all')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
                </div>
            </div>
        </div><!-- dashboard-w1 end -->
		
		    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{$widget['city_reference']}}</span>
                </div>
                <div class="desciption">
                    <span class="text-small" style="font-weight: bold;">@lang('Total City Reference')</span>
                </div>
                <a href="{{route('admin.users.city_reference')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{$widget['franchise_bonus']}}</span>
                </div>
                <div class="desciption">
                    <span class="text-small" style="font-weight: bold;">@lang('Total Franchise Bonus')</span>
                </div>
                <a href="{{route('admin.users.franchise_bonus')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->
    <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
        <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
            <div class="details">
                <div class="numbers">
                    <span class="amount">{{$widget['franchise_ref_bonus']}}</span>
                </div>
                <div class="desciption">
                    <span class="text-small" style="font-weight: bold;">@lang('Total Franchise Reference Bonus')</span>
                </div>
                <a href="{{route('admin.users.franchise_ref_bonus')}}" class="btn btn-sm text--small bg--white text--black box--shadow3 mt-3">@lang('View All')</a>
            </div>
        </div>
    </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ number_format($total_pv_dsp)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('DSP By PV')</span>
                    </div>

                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ number_format($total_balance_dsp)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('DSP by Current Balance')</span>
                    </div>

                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ number_format($total_epin_dsp)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('DSP by E-Pin Credit')</span>
                    </div>

                </div>
            </div>
        </div><!-- dashboard-w1 end -->
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--pink b-radius--10 box-shadow ">
                <div class="icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">{{ number_format($total_wallet_dsp)}}</span>
                    </div>
                    <div class="desciption">
						<span class="text-small" style="font-weight: bold;">@lang('DSP by Wallet')</span>
                    </div>

                </div>
            </div>
        </div><!-- dashboard-w1 end -->
       
        


    </div><!-- row end-->

    <div id="matrixSettingModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang("Clear Company's wallet")</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="GET">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="matrix_height" class="form-control-label font-weight-bold">@lang('Admin Password') <sup
                                    class="text--danger">*</sup></label>
                            <input type="text" class="form-control form-control-lg" name="password"
                                placeholder="@lang('Enter Password')" required="">

                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Back')</button>
                        <button type="submit" class="btn btn--primary">@lang('Forward')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    

@endsection
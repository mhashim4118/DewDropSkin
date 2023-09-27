@extends($activeTemplate.'layouts.master')

@section('content')
<style>
    #generate-epin-btn {
        display: block;
        margin: 0 auto;
        text-align: center;
        color: #000;
        font-size: 16px;
        padding: 10px 20px;
        border: none;
        box-shadow: 0 0 10px blue;
        transition: all 0.3s ease;
    }

    #generate-epin-btn:hover {
        background-color: blue;
        box-shadow: 0 0 20px blue;
        color: #fff;
    }
</style>
<div class="row">
    @if($general->notice != null)
    <div class="col-12 mb-30">
        <div class="card border--light mb-4">
            <div class="card-header">@lang('Notice')</div>
            <div class="card-body">
                <p class="card-text">@php echo $general->notice; @endphp</p>
            </div>
        </div>
    </div>
    @endif
    @if($general->free_user_notice != null)
    <div class="col-12 mb-30">
        <div class="card border--light mb-2">
            @if($general->notice == null)
            @endif
            <div class="card-body">
                <h5 class="name">Dear, {{ auth()->user()->getFullnameAttribute() }}</h5>
                <p class="card-text"> @php echo $general->free_user_notice; @endphp </p>
            </div>
        </div>
    </div>
    @endif
</div>
<br>
@if(auth()->user()->plan_id == 1 || $productCheck)
<div class="col-lg-12 mb-5">
    <!-- Required product link -->
    <input type="hidden" id="username" value="{{Auth::user()->username}}">

    <!-- Required input for DewDropSkin username -->
    <div class="form-group">
        <label for="products">Products:</label>
        <select name="" class="form-control" id="productLink">
            <option value="0">--Select Product--</option>
            @foreach ($products as $product)

            <option value="https://localhost/vb/dewdrop-main/product/{{$product->id}}/{{slug($product->name)}}">{{$product->name}}</option>
            @endforeach
        </select>

    </div>

    <!-- Select position -->
    <div class="form-group">
        <label for="position">Position:</label>
        <select class="form-control" id="position">
            <option value="">Select position</option>
            <option value="left">Left</option>
            <option value="right">Right</option>
        </select>
    </div>

    <!-- Generate button -->
    <button class="btn btn-primary" onclick="generateLink()">Generate Link</button>

    <!-- Shareable link -->
    <div id="shareableLink" style="display: none;">
        <hr>
        <label for="ref">Shareable Link:</label>
        <div class="input-group">
            <input type="text" class="form-control" id="ref" readonly>
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard()">Copy</button>
            </div>
        </div>
    </div>
</div>


 
@endif


   




<div class="row justify-content-center g-3">
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Current Balance')</h6>
                    <h3 class="ammount theme-two">{{getAmount(auth()->user()->balance)}} {{$general->cur_sym }}</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-wallet"></i></div>
                </div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('E-Pin Credit')</h6>
                    <h3 class="ammount theme-one">{{getAmount(auth()->user()->epin_credit)}} PKR</h3>
                </div>
                <div class="icon"><i class="flaticon-fees"></i></div>
				<a href="{{route('user.EPinCredit')}}" class="btn btn-primary">
						View All
				</a>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>


    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total DSP Ref Bonus')</h6>
                    <h3 class="ammount theme-one"> {{getAmount(auth()->user()->total_ref_com)}} PKR</h3>
                </div>
                <div class="icon"><i class="flaticon-clipboards"></i></div>
					<a href="{{route('user.dsp-ref-bonus')}}" class="btn btn-primary">
						View All
					</a>
            </div>
            <div class="dashboard-item-body">
			
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Pair Bonus')</h6>
                    <h3 class="ammount theme-one"> {{$totalPairBonus}} PKR</h3>
                </div>
                <div class="icon"><i class="flaticon-money-bag"></i></div>
				<a href="{{route('user.pairs_bonus')}}" class="btn btn-primary">
						View All
					</a>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    <!-- new Py -->
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total BV (Bonus Value)')</h6>
                    <h3 class="ammount theme-two"> {{getAmount(auth()->user()->bv)}} PKR</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-growth"></i></div>

                </div>
									<a href="{{route('user.bv')}}" class="btn btn-primary">
						View All
					</a>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('PV (Point Voucher)')</h6>
                    <h3 class="ammount text--base">{{getAmount(auth()->user()->pv)}}</h3>
                </div>
                <div class="icon"><i class="flaticon-growth-1"></i></div>
				<a href="{{route('user.pv')}}" class="btn btn-primary">
						View All
					</a>
            </div>

            @if (auth()->user()->pv >= 100)
            <br>
            <div style="text-align: center;">


                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    Generate FREE E-Pin
                </button>

            </div>
            @endif

            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total DDS Ref Bonus')</h6>
                    <h3 class="ammount theme-two"> {{getAmount(auth()->user()->reference_bonus)}} PKR</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>     </div>
					<a href="{{route('user.reference')}}" class="btn btn-primary">
						View All
					</a>
           
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Shop Bonus')</h6>
                    <h3 class="ammount theme-two"> {{getAmount(auth()->user()->store_bonus)}} PKR</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>           </div>
					<a href="{{route('user.storeBonus')}}" class="btn btn-primary">
						View All
					</a>
     
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Shop Ref Bonus')</h6>
                    <h3 class="ammount theme-two"> {{getAmount(auth()->user()->store_reference)}} PKR</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>  </div>
					<a href="{{route('user.storeReference')}}" class="btn btn-primary">
						View All
					</a>
              
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>


    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total City Reference')</h6>
                    <h3 class="ammount theme-two"> {{getAmount(auth()->user()->city_reference)}} PKR</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>  </div>
					<a href="{{route('user.city_reference')}}" class="btn btn-primary">
						View All
					</a>
              
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
	
	    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Franchise Bonus')</h6>
                    <h3 class="ammount theme-one"> {{auth()->user()->franchise_bonus}} PKR</h3>
                </div>
                <div class="icon"><i class="flaticon-fees"></i></div>
				<a href="{{route('user.franchise_bonus')}}" class="btn btn-primary">
						View All
					</a>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Franchise Ref Bonus')</h6>
                    <h3 class="ammount theme-one"> {{auth()->user()->franchise_ref_bonus}} PKR</h3>
                </div>
                <div class="icon"><i class="flaticon-fees"></i></div>
				<a href="{{route('user.franchise_ref_bonus')}}" class="btn btn-primary">
						View All
					</a>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Weekly Bonus')</h6>
                    <h3 class="ammount theme-two"> {{getAmount(auth()->user()->weekly_bonus)}} PKR</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div> </div>
					<a href="{{route('user.dsp-ref-bonus')}}" class="btn btn-primary">
						View All
					</a>
               
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Product Partner Bonus')</h6>
                    <h3 class="ammount theme-two"> {{getAmount(auth()->user()->product_partner_bonus)}} PKR</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>   </div>
					<a href="{{route('user.productPartnerBonus')}}" class="btn btn-primary">
						View All
					</a>
             
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Pairs')</h6>
                    <h3 class="ammount theme-two"> {{$pairs}} </h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>                </div>
				

            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    <!-- <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Shop Reference')</h6>
                    <h3 class="ammount theme-two"> {{getAmount(auth()->user()->shop_reference)}} PKR</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>
                </div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div> -->

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Stockist Ref Bonus')</h6>
                    <h3 class="ammount theme-two"> {{getAmount(auth()->user()->stockist_ref_bonus)}} PKR</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>                </div>
					<a href="{{route('user.stockistReferenceBonus')}}" class="btn btn-primary">
						View All
					</a>

            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Stockist Bonus')</h6>
                    <h3 class="ammount theme-two"> {{getAmount(auth()->user()->stockist_bonus)}} PKR</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>     </div>
					<a href="{{route('user.stockistBonus')}}" class="btn btn-primary">
						View All
					</a>
           
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Promo')</h6>
                    <h3 class="ammount theme-two"> {{getAmount(auth()->user()->promo)}} PKR</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>     </div>
					<a href="{{route('user.promo')}}" class="btn btn-primary">
						View All
					</a>
           
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total DSP Purchase')</h6>
                    <h3 class="ammount theme-one"> {{getAmount(auth()->user()->total_invest)}} PKR</h3>
                </div>
                <div class="icon"><i class="flaticon-tag-1"></i></div>
				<a href="{{route('user.total_invest')}}" class="btn btn-primary">
						View All
					</a>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Deposit')</h6>
                    <h3 class="ammount text--base">{{getAmount($totalDeposit)}} {{$general->cur_sym}}</h3>
                </div>
                <div class="icon"><i class="flaticon-save-money"></i></div>
				<a href="{{route('user.deposits')}}" class="btn btn-primary">
						View All
					</a>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Withdraw')</h6>
                    <h3 class="ammount theme-one">{{getAmount($totalWithdraw)}} PKR</h3>
                </div>
                <div class="icon"><i class="flaticon-withdraw"></i></div>
				<a href="{{route('user.total_withdraw')}}" class="btn btn-primary">
						View All
					</a>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Complete Withdraw')</h6>
                    <h3 class="ammount theme-two"> {{$completeWithdraw}}</h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-wallet"></i></div>        </div>
					<a href="{{route('user.complete_withdraw')}}" class="btn btn-primary">
						View All
					</a>
        
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Pending Withdraw')</h6>
                    <h3 class="ammount text--base"> {{$pendingWithdraw}}</h3>
                </div>
                <div class="icon"><i class="flaticon-withdrawal"></i></div>
				<a href="{{route('user.pending_withdraw')}}" class="btn btn-primary">
						View All
					</a>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Reject Withdraw')</h6>
                    <h3 class="ammount theme-one"> {{$rejectWithdraw}}</h3>
                </div>
                <div class="icon"><i class="flaticon-fees"></i></div>
				<a href="{{route('user.rejected_withdraw')}}" class="btn btn-primary">
						View All
					</a>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>

    @if(auth()->user()->plan_id == 0)
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Pending Amount')</h6>
                    <h3 class="ammount theme-one"> {{$pairs*200}}</h3>
                </div>
                <div class="icon"><i class="flaticon-fees"></i></div>
			
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    @endif

    @if(!empty($partner))
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Total Company Partnership')</h6>
                    <h3 class="ammount theme-two"> {{getAmount($partner->amount)}} PKR <span style="font-size:15px"> (Percentage : {{$partner->percentage}})</span></h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>
					<a href="{{route('user.company_partnership')}}" class="btn btn-primary">
						View All
					</a>
                </div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    @endif
    @if(!empty($admin_wallets))
    <div class="col-sm-6 col-md-6 col-lg-6 col-xl-4">
        <div class="dashboard-item">
            <div class="dashboard-item-header">
                <div class="header-left">
                    <h6 class="title">@lang('Sami Expense Wallet')</h6>
                    <h3 class="ammount theme-two"> {{getAmount($admin_wallets->samiExpenseBalance)}} PKR </h3>
                </div>
                <div class="right-content">
                    <div class="icon"><i class="flaticon-money-bag"></i></div>
					<a href="{{route('user.samiExpenseBalance')}}" class="btn btn-primary">
						View All
					</a>
                </div>
            </div>
            <div class="dashboard-item-body">
            </div>
        </div>
    </div>
    @endif

</div>
<!-- Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Confirm E-Pin Generation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to generate a FREE E-Pin voucher?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmGenerateBtn">Yes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade mt-4" style="margin-top:100px !important;" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p> Are you sure you want to generate a FREE E-Pin voucher?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>

                <form method="post" action="{{route('user.stockist.stockistPurchaseFromDash')}}">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        Yes</button>
                </form>

            </div>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">E-Pin Generated Successfully</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Congratulations! You have successfully generated a FREE E-Pin voucher with 100 PV.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

@endsection
@push('script')

<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script>
    function generateLink() {
        var productLink = document.getElementById('productLink').value;
        var username = document.getElementById('username').value;
        var position = document.getElementById('position').value;

        // Validate input
        if (username == '') {
            alert('Please enter your DewDropSkin username.');
            return;
        }
        if (position == '') {
            alert('Please select a position.');
            return;
        }

        // Generate shareable link
        var shareableLink = productLink + '?ref=' + username + '&pos=' + position;

        // Display shareable link
        document.getElementById('ref').value = shareableLink;
        document.getElementById('shareableLink').style.display = 'block';
    }

    function copyToClipboard() {
        var copyText = document.getElementById('ref');
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand('copy');
        notify('success', 'Url copied successfully ' + copyText.value);
    }
</script>
<script>
    // Get the button element
    const generateBtn = document.getElementById('generate-epin-btn');

    // Add a click event listener to the button
    generateBtn.addEventListener('click', function() {
        // Show the confirm modal
        $('#confirmModal').modal('show');
    });

    // Get the confirm button element
    const confirmGenerateBtn = document.getElementById('confirmGenerateBtn');

    // Add a click event listener to the confirm button
    confirmGenerateBtn.addEventListener('click', function() {
        // Hide the confirm modal
        $('#confirmModal').modal('hide');

        // Show the success modal
        $('#successModal').modal('show');
    });
</script>

@endpush
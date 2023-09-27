@extends($activeTemplate.'layouts.master')

@section('content')
<div class="row">
    @foreach($member as $data)
    <div class="col-xl-4 col-md-4 mb-30">
        <div class="card rounded shadow border-0">
            <div class="card-body pt-5 pb-5 ">
                <div class="pricing-table text-center mb-4">
                    <h2 class="package-name mb-20 text-"><strong>@lang($data->name)</strong></h2>
                    <span class="price text--dark font-weight-bold d-block">{{getAmount($data->price)}} {{$general->cur_sym}}</span>
                    <hr>
                   <!-- <ul class="package-features-list mt-30">
                        <li><i class="las la-business-time __plan_info text--primary" data="bv"></i> <span>@lang('E-Pins Qty'): {{getAmount($data->e_pin_qty)}} </span> </li>
                        <li><i class="las la-comment-dollar __plan_info text--primary" data="ref_com"></i> <span> @lang('Stockist Ref Bonus'):   {{getAmount($data->refferal_bonus)}} {{$general->cur_sym}}</span>
                        </li>
                        <li>
                            <i class="las la-comments-dollar __plan_info text--primary" data="tree_com"></i> <span>@lang('Stockist Bonus'):  {{getAmount($data->stockist_bonus)}} {{$general->cur_sym}}</span>
                        </li>
                    </ul> -->
                </div>
                <div class="text-center">
@if (auth()->check())
    @if (auth()->user()->membership_id == 0)
        <a href="#" class="cmn--btn active __subscribe"  data-id="{{ $data->id }}" data-price="{{$data->price }}"><span>@lang('Subscribe')</span></a>
    @elseif (auth()->user()->membership_id == 1)
        <p>You have already subscribed to the shop membership.</p>
    @elseif (auth()->user()->membership_id == 2)
        <p>You have already subscribed to the franchise membership.</p>
    @endif
@endif






					<div class="text-center">
						
					
@php
    $seller = DB::connection('mysql_store')->table('sellers')
              ->where('dds_username', auth()->user()->username)
              ->where('status', 'approved')->first();
    $link = $seller ? 'https://dewdropskin.com/store/seller/auth/login' : 'https://dewdropskin.com/store/shop/apply';
    $buttonText = $seller ? __('Seller Dashboard') : __('Become a Seller');
@endphp

@if(Auth::user()->member_ship == 1)
						<br>
    <a href="{{ $link }}" class="cmn--btn"><span>{{ $buttonText }}</span></a>
@endif


</div>

                </div>
				
            </div>
         </div>
    </div>
    @endforeach
</div>

@endsection

@push('modal')

<div class="modal fade" id="subscribe_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{route('user.membership.purchase')}}">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"> @lang('Confirm Purchase')?</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" name="price" id="price">
                    <input type="hidden" name="id" id="id">
                    <input type="radio" id="html" name="blance" value="blance" checked>
                    <label for="html">Current Balance</label>
					<h6>@lang('Are you sure to subscribe this') "<span>Membership</span>"</h6>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                <button style="display: ;" type="submit" class="btn btn--success subc-btn"> @lang('Subscribe')</button>
            </div>

            </form>

        </div>
    </div>
</div>


@endpush

@push('script')
<script>
    'use strict';
    (function($) {
        $('.__subscribe').on('click', function(e) {
            let id = $(this).attr('data-id');
            let price = $(this).attr('data-price');
            $('#price').attr('value',price);
            $('#id').attr('value', id);
            $("#subscribe_modal").modal('show');
        })
    })(jQuery)

</script>
@endpush

@push('style')

@endpush

@extends($activeTemplate.'layouts.master')

@section('content')
<div class="row">
    @foreach($plans as $data)
    <div class="col-xl-4 col-md-4 mb-30">
        <div class="card rounded shadow border-0">
            <div class="card-body pt-5 pb-5 ">
                <div class="pricing-table text-center mb-4">
                    <h2 class="package-name mb-20 text-"><strong>@lang($data->name)</strong></h2>
                    <span class="price text--dark font-weight-bold d-block">{{getAmount($data->price)}} {{$general->cur_sym}}</span>
                    <hr>
                    <ul class="package-features-list mt-30">
                        <li><i class="las la-business-time __plan_info text--primary" data="bv"></i> <span>@lang('E-Pins Qty'): {{getAmount($data->e_pin_qty)}} </span> </li>
                        <li><i class="las la-comment-dollar __plan_info text--primary" data="ref_com"></i> <span> @lang('Stockist Ref Bonus'):   {{getAmount($data->refferal_bonus)}} {{$general->cur_sym}}</span>
                        </li>
                        <li>
                            <i class="las la-comments-dollar __plan_info text--primary" data="tree_com"></i> <span>@lang('Stockist Bonus'):  {{getAmount($data->stockist_bonus)}} {{$general->cur_sym}}</span>
                        </li>
                    </ul>
                </div>
                <div class="text-center">
                    @if($data->used==0)
                    <a href="#" class="cmn--btn active __subscribe" pin-qty="{{ $data->e_pin_qty }}" data-id="{{ $data->id }}"><span>@lang('Subscribe')</span></a>
                    @else
                    <a class="cmn--btn active"><span>@lang('Already Subscribe')</span></a>
                    @endif
                </div>
            </div>
         </div>
    </div>
    @endforeach
</div>

@endsection

@push('modal')
<div class="modal fade" id="plan_info_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="plan_info_modal_title">@lang('Commission to tree info')</h5>

            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer text-right ">
                <div class="row">
                    <div class="col-lg-12">
                        <button type="button" class="btn btn-danger" id="__modal_close">@lang('Close')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="subscribe_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{route('user.stockist.purchase')}}">
            @csrf
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel"> @lang('Confirm Purchase')?</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <input type="hidden" value="is_package" name="is_package">
                    <input type="hidden" name="pin_qty" id="pin_qty">
                    <input type="radio" id="html" name="payment_type" value="bank_account" checked>
                    <label for="html">Current Balance</label>
                </div>

                
                <!-- <h5 class="text-danger text-center">@lang('Are you sure to purchase?')</h5> -->
            </div>

            <div class="modal-footer">
                
                    <input class="form-control form--control" type="hidden" class="d-none" name="plan_id" id="plan_id">
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
        $('.__plan_info').on('click', function(e) {
            let html = "";
            let data = $(this).attr('data');
            let modal = $("#plan_info_modal");
            console.log(data)
            if (data == 'bv') {
                html = ` <h5>   <span class="text-danger">@lang('When someone from your below tree subscribe this plan, You will get this Business Volume  which will be used for matching bonus').</span>
                </h5>`
                modal.find('#plan_info_modal_title').html("@lang('Business Volume (BV) info')")

            }
            if (data == 'ref_com') {
                html = `  <h5>  <span class=" text-danger">@lang('When Your Direct-Referred/Sponsored  User Subscribe in') <b> @lang('ANY PLAN') </b>, @lang('You will get this amount').</span>
                        <br>
                        <br>
                        <span class="text-success"> @lang('This is the reason You should Choose a Plan With Bigger Referral Commission').</span> </h5>`
                modal.find('#plan_info_modal_title').html("@lang('Referral Commission info')")

            }
            if (data == 'tree_com') {
                html = ` <h5 class=" text-danger">@lang('When someone from your below tree subscribe this plan, You will get this amount as Tree Commission'). </h5>`
                modal.find('#plan_info_modal_title').html("@lang('Referral Commission info')")

            }
            modal.find('.modal-body').html(html)
            $(modal).modal('show')
        });

        $('body').on('click', '#__modal_close', function(e) {
            $("#plan_info_modal").modal('hide');
        });

        $('#css').on('click', function(e) {
            $(".modal-body").append(`<div class="form-group spdiv">
                    <label for="InputFirstname" class="col-form-label">Sponser Id:</label>
                    <input type="text" class="form-control form--control" id="InputFirstname" name="sponser_id" placeholder="Sponser Id" value="" minlength="3" required>
                </div>`);
            $(".subc-btn").show();
        });
        $('#html').on('click', function(e) {
            $(".spdiv").remove();
            $(".subc-btn").show();
        });

        $('.check').on('click', function(e) {
            let html = "";
            let data = $(this).attr('data');
            let modal = $("#plan_info_modal");
            console.log(data)
            if (data == 'bv') {
                html = ` <h5>   <span class="text-danger">@lang('When someone from your below tree subscribe this plan, You will get this Business Volume  which will be used for matching bonus').</span>
                </h5>`
                modal.find('#plan_info_modal_title').html("@lang('Business Volume (BV) info')")

            }
            if (data == 'ref_com') {
                html = `  <h5>  <span class=" text-danger">@lang('When Your Direct-Referred/Sponsored  User Subscribe in') <b> @lang('ANY PLAN') </b>, @lang('You will get this amount').</span>
                        <br>
                        <br>
                        <span class="text-success"> @lang('This is the reason You should Choose a Plan With Bigger Referral Commission').</span> </h5>`
                modal.find('#plan_info_modal_title').html("@lang('Referral Commission info')")

            }
            if (data == 'tree_com') {
                html = ` <h5 class=" text-danger">@lang('When someone from your below tree subscribe this plan, You will get this amount as Tree Commission'). </h5>`
                modal.find('#plan_info_modal_title').html("@lang('Referral Commission info')")

            }
            modal.find('.modal-body').html(html)
            $(modal).modal('show')
        });

        $('.__subscribe').on('click', function(e) {
            let id = $(this).attr('data-id');
            let pin_qty = $(this).attr('pin-qty');
            $('#pin_qty').val(pin_qty);
            $('#plan_id').attr('value', id);
            $("#subscribe_modal").modal('show');
        })
    })(jQuery)

</script>
@endpush

@push('style')

@endpush
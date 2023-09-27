@extends($activeTemplate . 'layouts.master')
@section('content')
    <h4 class="mb-2 col-lg-4">@lang('My Statement')</h4>
    <form action="{{ route('user.my.records') }}" method="GET" class="form-inline bg--white">
  @csrf
  <div class="row">
    <div class="form-group col-lg-3 col-md-6 col-sm-12">
      <label>From:</label>
      <input type="date" name="start_date_search" class="form-control">
    </div>
    <div class="form-group col-lg-3 col-md-6 col-sm-12">
      <label>To:</label>
      <input type="date" name="end_date_search" class="form-control">
    </div>
    <div class="form-group col-lg-3 col-md-6 col-sm-12">
      <label>Keywords:</label>
      <select name="search_word" class="form-control" id="search_word">
        <option value="0">Select</option>
        <option value="city_reference">City Reference</option>
        <option value="Store_reference">Store Reference</option>
        <option value="reference_bonus">Reference Bonus</option>
        <option value="pv_bonus">PV</option>
        <option value="bv_bonus">BV</option>
        <option value="store_bonus">Store Bonus</option>
        <option value="customer_order_generated">Customer Orders</option>
        <option value="user_promo">User Promo</option>
        <option value="seller_promo">Seller Promo</option>
        <option value="stockist_bonus">Stockist Bonus</option>
        <option value="stockist_reference_bonus">Stockist Reference Bonus</option>
        <option value="free_pair_E_Pin_Credit">Free Pair E-Pin Credit</option>
        <option value="free_pair_balance">Free Pair Balance</option>
        <option value="BV add on purchase plan">BV on Purchasing Plan</option>
        <option value="purchased_plan">Purchased Plan</option>
        <option value="e-pin_used">E-Pin Used</option>
        <option value="purchased_E_Pin">Purchased E-Pin</option>
        <option value="membership_subscription">Membership Subscription</option>
        <option value="balance_receive">Balance Receive</option>
        <option value="balance_transfer">Balance Transfer</option>
        <option value="withdraw">Withdraw</option>
        <option value="reward_delivered">Reward Delivered</option>
      </select>
    </div>
    <div class="form-group col-lg-3 col-md-6 col-sm-12">
		<label></label>
      <button class="btn btn--primary btn-block" style="width:100%" type="submit"><i class="fa fa-search"></i></button>
    </div>
  </div>
</form>



<table class="transection-table-2">
    <thead>
        <tr>
            <th>@lang('Sl No.')</th>
            <th>@lang('Transaction Type')</th>
            <th>@lang('Debit')</th>
            <th>@lang('Credit')</th>
            <th>@lang('Status')</th>
            <th>@lang('Time & Date')</th>
            <th>@lang('Invoice')</th>
            {{-- <th>@lang('Details')</th> --}}
        </tr>
    </thead>
    <tbody>
        <?php
        // dd($userPlan);
        if (!empty($userPlan) && count($userPlan) > 0) { ?>

            @foreach ($userPlan as $key => $plan)
            <?php
            // $depositStatus="Pending";
            // if($plan->deposit_status==1){
            //     $depositStatus="Completed";
            // }

            $PlanDetails = '';
            if ($plan->details == 'BV added on Purchased DSP') {
                $PlanDetails = 'Direct Bonus';
            } else {
                $PlanDetails = $plan->details;
            }
            ?>
            <tr style="cursor: pointer; margin-bottom:20px; box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;" onclick="myFunction(<?php echo $plan->id; ?>)">
                <td data-label="@lang('S No.')">{{ $userPlan->total() - ($userPlan->firstItem() + $key) + 1 }}</td>
                <td data-label="@lang('TransactionType')">{{ $PlanDetails }}</td>
                <td data-label="@lang('Debit')">
                    <?php
                    if ($plan->trx_type == '-') {
                        echo round($plan->amount, 2) . ' PKR';
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td data-label="@lang('Credit')">
                    <?php
                    if ($plan->trx_type == '+') {
                        echo round($plan->amount, 2) . ' PKR';
                    } else {
                        echo '-';
                    }
                    ?>
                </td>
                <td data-label="@lang('Status')">Completed</td>
                <td data-label="@lang('Time&Date')">
                    <?php
                    echo date('h:i:s A / d-M-Y', strtotime($plan->created_at));
                    ?>
                </td>

                <td data-label="@lang('Invoice')" class="text-center">
                    @if ($plan->remark == 'customer_order_generated')
                    <a class="btn btn--primary px-4" target="_blank" href="https://dewdropskin.com/store/customer-generate-invoice/{{ basename(str_replace(' ', '/', $PlanDetails)) }}">
                        <i class="fa fa-print"></i>
                    </a>
                    @elseif($plan->remark == 'seller_order_generated')
                    <a class="btn btn--primary px-4" target="_blank" href="https://dewdropskin.com/store/seller-generate-invoice/{{ basename(str_replace(' ', '/', $PlanDetails)) }}">
                        <i class="fa fa-print"></i>
                    </a>
                    @else -
                    @endif
                </td>
                {{-- <td data-label="@lang('Details')">{{$PlanDetails}}</td> --}}
            <tr id="extra-details<?php echo $plan->id; ?>" class="text-center extra-details" style="background: aliceblue;border: 1px solid rgb(5 25 64 / 10%);">
                <td colspan="100%">
                    <?php
                    if ($plan->details == 'Deposit Via Bank Alfalah') {
                        echo 'You have successfully deposited ' . round($plan->amount, 2) . ' PKR into your current balance via Bank Alfalah.';
                    } elseif ($plan->details == 'BV added on Purchased DSP') {
                        echo 'DSP purchase bonus ' . round($plan->amount, 2) . ' PKR has been credited into your DDS account.';
                    } elseif ($plan->details == '') {
                        $checkWord = str_contains($plan->details, 'Subscribed to DSP plan');
                        echo 'DSP referral bonus ' . round($plan->amount, 2) . ' PKR been credited into your DDS account.';
                    } elseif ($plan->details == 'Pair Bonus Credit') {
                        echo 'DSP pair bonus ' . round($plan->amount, 2) . ' PKR  has been credited into your DDS account.';
                    } elseif ($plan->details == 'Purchased DSP') {
                        echo 'You have successfuly subscribed to DSP plan';
                    } else {
                        echo $plan->details;
                    }
                    ?>
                </td>
            </tr>
            </tr>
            @endforeach
        <?php } else { ?>

            <tr>
                <td colspan="100%" class="text-center">@lang('No order found')</td>
            </tr>
        <?php } ?>

    </tbody>
</table>
<div class="card-footer pb-4">
    {{ paginateLinks($userPlan)  }}
</div>

<h4>Total Amount: {{number_format($all_total, 2)}} PKR </h4>

@endsection


@push('script')
<script>
    $('.extra-details').hide();

    function myFunction(id) {
        $(`#extra-details${id}`).toggle();
    }
</script>
@endpush
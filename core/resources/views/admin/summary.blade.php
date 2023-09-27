@extends('admin.layouts.app')

@section('panel')

<style>
    @media (max-width: 767px) {
        table.style--two tbody tr {
            border: 2px dashed #0000005c;
            display: block;
            margin-bottom: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);

            transition: box-shadow 0.3s ease-in-out;
        }

        table.style--two tbody tr:hover {
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.4);
            transition: box-shadow 0.3s ease-in-out;
        }
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('User')</th>
                                <th>@lang('Trx')</th>
                                <th>@lang('Transacted')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Post Balance')</th>
                                <th>@lang('Detail')</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($transactions as $trx)
                            <tr>
                                <td data-label="@lang('User')">
                                    <span class="font-weight-bold">{{ $trx->user->fullname }}</span>
                                    <br>
                                    <span class="small"> <a href="{{ route('admin.users.detail', $trx->user_id) }}"><span>@</span>{{ $trx->user->username }}</a> </span>
                                </td>

                                <td data-label="@lang('Trx')">
                                    <strong>{{ $trx->trx }}</strong>
                                </td>

                                <td data-label="@lang('Transacted')">
                                    {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                </td>

                                <td data-label="@lang('Amount')" class="budget">
                                    <span class="font-weight-bold @if($trx->trx_type == '+')text-success @else text-danger @endif">
                                        {{ $trx->trx_type }} {{showAmount($trx->amount)}} {{ $general->cur_text }}
                                    </span>
                                </td>

                                <td data-label="@lang('Post Balance')" class="budget">
                                    {{ showAmount($trx->post_balance) }} {{ __($general->cur_text) }}
                                </td>


                                <td data-label="@lang('Detail')">{{ __($trx->details) }}</td>
                            </tr>
                            @empty

                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse


                        </tbody>
                    </table><!-- table end -->
                </div><!-- table-responsive end -->
            </div>
        </div>
        <div class="card-footer py-4">

            {{ paginateLinks($transactions) }}

        </div>
    </div><!-- card end -->

    <h4>Total Amount: {{number_format($total, 2)}} PKR</h4>
</div>

@endsection

@push('breadcrumb-plugins')
@if(request()->routeIs('admin.users.transactions'))
<form action="{{ route('admin.users.transactions') }}" method="GET" class="form-inline float-sm-right bg--white">
    <div class="input-group has_append">
        <div class="form-group">
            <label for="date_search">Start Date</label>
            <input type="date" id="date_search" name="date_search" class="form-control">
        </div>
        <input type="text" name="username" class="form-control" placeholder="@lang('Username')" value="{{ $username ?? '' }}">
        <input type="text" name="search" class="form-control" placeholder="@lang('Search Keyword')" value="{{ $search ?? '' }}">
        <div class="input-group-append">
            <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
@else

<form action="{{ route('admin.summary') }}" method="POST" class="my-3 bg-white rounded shadow-sm" style="padding: 10px;">
    @csrf
    <div class="form-row align-items-center">
        <div class="col-12 col-md-3 mb-3 mb-md-0">
            <label for="start_date_search" class="form-label" style="display: flex; color: black; text-align: left; font-weight: bold;">From</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                </div>
                <input type="date" name="start_date_search" class="form-control" id="start_date_search" placeholder="From">
            </div>
        </div>
        <div class="col-12 col-md-3 mb-3 mb-md-0">
            <label for="end_date_search" class="form-label" style="display: flex; color: black; text-align: left; font-weight: bold;">To</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                </div>
                <input type="date" name="end_date_search" class="form-control" id="end_date_search" placeholder="To">
            </div>
        </div>
        <div class="col-12 col-md-2">
            <label for="search_word" class="form-label" style="display: flex; color: black; text-align: left; font-weight: bold;">Username</label>
            <div class="input-group">
                <input type="text" name="username" class="form-control" id="username" placeholder="Username">
             
            </div>
        </div>
        <div class="col-12 col-md-4">
            <label for="search_word" class="form-label" style="display: flex; color: black; text-align: left; font-weight: bold;">Search Keywords</label>
            <div class="input-group">
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
                    <option value="BV add on purchase plan">BV on Purchaseing Plan</option>
                    <option value="purchased_plan">Purchased Plan</option>
                    <option value="e-pin_used">E-Pin Used</option>
                    <option value="purchased_E_Pin">Purchased E-Pin</option>
                    <option value="membership_subscription">Membership Subscription</option>
                    <option value="balance_receive">Balance Receive</option>
                    <option value="balance_transfer">Balance Transfer</option>
                    <option value="withdraw">Withdraw</option>
                    <option value="reward_delivered">Reward Delivered</option>
                </select>
                
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </div>
    </div>
</form>
@endif
@endpush
@section('scripts')

<script>
    "use strict";
    (function($) {
        $(document).ready(function() {
            $('#search').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('table tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            $('input[name="date_search"]').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                autoUpdateInput: false,
            }).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });

            $('input[name="start_date_search"]').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                autoUpdateInput: false,
            }).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });

            $('input[name="end_date_search"]').daterangepicker({
                singleDatePicker: true,
                locale: {
                    format: 'YYYY-MM-DD'
                },
                autoUpdateInput: false,
            }).on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('YYYY-MM-DD'));
            });
        });
    })(jQuery);
</script>
@endsection
@push('script-lib')

<script src="{{ asset('assets/admin/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')

<link rel="stylesheet" href="{{ asset('assets/admin/css/daterangepicker.min.css') }}">
@endpush
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
                                <th>@lang('Transacted')</th>
                                <th>@lang('Amount')</th>
                                <th>@lang('Detail')</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($transactions as $trx)
                            <tr>
                                <td data-label="@lang('User')">
                                    <span class="font-weight-bold">{{ $trx->name }}</span>
                                    <br>
                                    <span class="small">{{ $trx->email }} </span>
                                </td>

                              

                                <td data-label="@lang('Transacted')">
                                    {{ showDateTime($trx->date) }}<br>{{ diffForHumans($trx->created_at) }}
                                </td>

                                <td data-label="@lang('Amount')" class="budget">
                                    <span class="font-weight-bold ">
                                        {{showAmount($trx->amount)}} {{ $general->cur_text }}
                                    </span>
                                </td>

                             


                                <td data-label="@lang('Detail')">{{ __($trx->description) }}</td>
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

<form action="{{ route('admin.office.statements') }}" method="GET" class="my-3 bg-white rounded shadow-sm" style="padding: 10px;">
    @csrf
    <div class="form-row align-items-center">
      
        
        <div class="col-12 col-md-6 ml-auto">
            <label for="search_word" class="form-label" style="display: flex; color: black; text-align: left; font-weight: bold;">Search Keywords</label>
            <div class="input-group">
                <select name="search_word" class="form-control" id="search_word">
                    <option value="0">Select</option>
                    <option value="lessStock">Less Stock</option>
                    <option value="bill">Bill</option>
                    <option value="purchase">Purchase</option>
               
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
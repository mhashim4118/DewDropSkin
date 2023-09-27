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
        }}
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th scope="col">@lang('Seller')</th>
                                <th scope="col">@lang('Email')</th>
                                <th scope="col">@lang('Product Name')</th>
                                <th scope="col">@lang('Price')</th>
                                <th scope="col">@lang('Current Stock')</th>
                                <th scope="col">@lang('Sold Stock')</th>
                                <th scope="col">@lang('Total Purchased Stock')</th>
                                <th scope="col">@lang('Given PV')</th>
                                <th scope="col">@lang('Given BV')</th>
                                <th scope="col">@lang('Given Reference')</th>
                                <th scope="col">@lang('Given Store bonus')</th>
                                <th scope="col">@lang('Given Store Reference')</th>
                                <th scope="col">@lang('Given Office Expense')</th>
                                <th scope="col">@lang('Given Event Expense')</th>
                                <th scope="col">@lang('Given Shipping Expense')</th>
                                <th scope="col">@lang('Remaining Price')</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $currentStock = 0;
                            $soldStock = 0;
                            $totalStock = 0;
                            $given_pv = 0;
                            $given_bv = 0;
                            $given_ref = 0;
                            $given_store_bonus = 0;
                            $given_store_ref = 0;
                            $given_office = 0;
                            $given_event = 0;
                            $given_shipping = 0;
                            $all_remaining = 0;
                            ?>
                            @forelse($products as $key => $product)
                            <?php $all_bonuses = 0; ?>
                            <tr>
                                <td data-label="@lang('Seller')">{{ __($product->dds_username) }}
                                    <br>
                                    {{$product->f_name}} {{$product->l_name}}
                                </td>
                                <td data-label="@lang('Email')">{{ __($product->email) }} </td>
                                <td data-label="@lang('Product Name')">{{ __($product->name) }} </td>
                                <td data-label="@lang('Price')">{{number_format($product->unit_price, 2) }} PKR </td>
                                <td data-label="@lang('Current Stock')">
                                    {{ __($product->current_stock) }}
                                    <br>
                                    {{ number_format(($product->current_stock * $product->unit_price), 2) }} PKR

                                </td>
                                <?php
                                $pros = DB::connection('mysql_store')
                                ->table('order_details')
                                ->leftJoin('orders', 'orders.id', '=', 'order_details.order_id')
                                ->where('order_details.product_id', $product->id)
                                ->where('order_details.seller_id', $product->s_id)
                                ->where('orders.seller_is', 'seller')
                                ->get();
                                $q = 0;
                                foreach ($pros as $p) {
                                    $q += $p->qty;
                                }


                                $currentStock += ($product->current_stock * $product->unit_price);
                                $soldStock += ($q * $product->unit_price);
                                $totalStock += (($product->current_stock + $q) * $product->unit_price);
                                $given_pv += $product->pv*$q;
                                $given_bv += $product->bv*$q;
                                $given_ref += $product->reference_bonus*$q;
                                $given_store_bonus += $product->store_bonus*$q;
                                $given_store_ref += $product->store_reference*$q;
                                $given_office += $product->office_expense*$q;
                                $given_event += $product->event_expense*$q;
                                $given_shipping += $product->shipping_expense*$q;

                                $all_bonuses  =  ($product->bv*$q)+($product->pv*$q)+
                                                    ($product->reference_bonus*$q)+
                                                    ($product->store_bonus*$q)+
                                                    ($product->store_reference*$q)+
                                                    ($product->office_expense*$q)+
                                                    ($product->event_expense*$q)+
                                                    ($product->shipping_expense*$q);
                                
                                $all_remaining += (($q * $product->unit_price) - $all_bonuses);
                                //$given_bv+$given_pv+$given_ref+$given_store_bonus+$given_store_ref+$given_office+$given_event+$given_shipping;
                                
                                ?>

                                <td data-label="@lang('Sold Stock')">{{ __($q) }}
                                    <br>
                                    {{ number_format(($q * $product->unit_price), 2) }} PKR
                                    
                                </td>
                                <td data-label="@lang('Total Stock')">{{ __($product->current_stock + $q) }}
                                    <br>
                                    {{ number_format((($product->current_stock + $q) * $product->unit_price), 2) }} PKR
                                    
                                </td>
                                <td data-label="@lang('Given PV')">{{number_format($product->pv*$q) }}  </td>
                                <td data-label="@lang('Given BV')">{{number_format($product->bv*$q) }}  </td>
                                <td data-label="@lang('Given Reference')">{{number_format($product->reference_bonus*$q) }}  </td>
                                <td data-label="@lang('Given Store Bonus')">{{number_format($product->store_bonus*$q) }}  </td>
                                <td data-label="@lang('Given Store Reference')">{{number_format($product->store_reference*$q) }}  </td>
                                <td data-label="@lang('Given Reference')">{{number_format($product->office_expense*$q) }}  </td>
                                <td data-label="@lang('Given Store Bonus')">{{number_format($product->event_expense*$q) }}  </td>
                                <td data-label="@lang('Given Store Reference')">{{number_format($product->shipping_expense*$q) }}  </td>
                                <td data-label="@lang('Given Store Reference')">{{number_format(($q * $product->unit_price) - $all_bonuses) }} PKR </td>
                              
                            </tr>

                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                            <thead>
                                <tr>
                                    <th colspan="4">@lang('Totals:')</th>
                                    <th scope="col">{{number_format($currentStock, 2)}} PKR</th>
                                    <th scope="col">{{number_format($soldStock, 2)}} PKR</th>
                                    <th scope="col">{{number_format($totalStock, 2)}} PKR</th>
                                    <th scope="col">{{number_format($given_pv)}} </th>
                                    <th scope="col">{{number_format($given_bv)}} </th>
                                    <th scope="col">{{number_format($given_ref)}} </th>
                                    <th scope="col">{{number_format($given_store_bonus)}} </th>
                                    <th scope="col">{{number_format($given_store_ref)}} </th>
                                    <th scope="col">{{number_format($given_office)}} </th>
                                    <th scope="col">{{number_format($given_event)}} </th>
                                    <th scope="col">{{number_format($given_shipping)}} </th>
                                    <th scope="col">{{number_format($all_remaining) }} PKR</th>
                                </tr>
                            </thead>
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>

            <div class="card-footer py-4">
                {{ paginateLinks($products) }}
            </div>

        </div>
    </div>
</div>


@endsection

@push('breadcrumb-plugins')
<form action="{{ route('admin.users.sellerProducts')}}" method="GET" class="form-inline float-sm-right bg--white">
    <div class="row ">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <input type="text" name="p_id" class="form-control" placeholder="@lang('product')" value="{{ $search ?? '' }}">



            </div>
        </div>
        <div class="col-12 col-md-6">

            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('username')" value="{{ $search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>

            </div>
        </div>
    </div>
</form>
@endpush
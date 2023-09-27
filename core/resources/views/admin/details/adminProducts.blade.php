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
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th scope="col">@lang('Product Name')</th>
                                <th scope="col">@lang('Price')</th>
                                <th scope="col">@lang('Current Stock')</th>
                                <th scope="col">@lang('Sold Stock')</th>
                                <th scope="col">@lang('Total Purchased Stock')</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $currentStock = 0;
                            $soldStock = 0;
                            $totalStock = 0;
                            ?>
                            @forelse($products as $key => $product)
                            <tr>
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
                                    ->where('orders.seller_is', 'admin')
                                    ->get();
                                $q = 0;
                                foreach ($pros as $p) {
                                    $q += $p->qty;
                                }


                                $currentStock += ($product->current_stock * $product->unit_price);
                                $soldStock += ($q * $product->unit_price);
                                $totalStock += (($product->current_stock + $q) * $product->unit_price);

                                ?>
                                                       <td data-label="@lang('Sold Stock')">{{ __($q) }}
                                    <br>
                                    {{ number_format(($q * $product->unit_price), 2) }} PKR

                                </td>
                                <td data-label="@lang('Total Stock')">{{ __($product->current_stock + $q) }}
                                    <br>
                                    {{ number_format((($product->current_stock + $q) * $product->unit_price), 2) }} PKR

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                            <thead>
                                <tr>
                                    <th colspan="2">@lang('Totals:')</th>

                                    <th scope="col">{{number_format($currentStock, 2)}} PKR</th>
                                    <th scope="col">{{number_format($soldStock, 2)}} PKR</th>
                                    <th scope="col">{{number_format($totalStock, 2)}} PKR</th>

                                </tr>
                            </thead>
                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            <div class="card-footer py-4">
                {{paginateLinks($products) }}
            </div>
        </div>
    </div>
</div>


@endsection

@push('breadcrumb-plugins')
    <form action="{{ route('admin.users.adminProducts')}}" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Name, username or email')" value="{{ $search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush
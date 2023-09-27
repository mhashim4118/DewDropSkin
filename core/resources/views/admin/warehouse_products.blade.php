@extends('admin.layouts.app')

@section('panel')

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th scope="col">@lang('Warehouse')</th>
                                <th scope="col">@lang('Product Name')</th>
                                <th scope="col">@lang('Quantity')</th>
                                <th scope="col">@lang('Sale Price')</th>
                                <th scope="col">@lang('Purchase Price')</th>
                                <th scope="col">@lang('Company')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($products as $key => $plan)
                                <tr>
                                    <td data-label="@lang('Warehouse')">{{ __($plan->username) }}</td>
                                    <td data-label="@lang('Name')">{{ __($plan->name) }}</td>
                                    <td data-label="@lang('Quantity')">{{ __($plan->quantity) }}</td>
                                    <td data-label="@lang('Sale Price')">{{ getAmount($plan->sale_price) }} {{$general->cur_text}}</td>
                                    <td data-label="@lang('Purchase Price')">{{ getAmount($plan->purchase_price) }} {{$general->cur_text}}</td>
                                    <?php
                                    $by = DB::connection('mysql_office')
                                    ->table('users')->where('id', $plan->created_by)->first();
                                    ?>

                                    <td data-label="@lang('Created_by')">
                                     {{$by->name}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse

                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ $products->links('admin.partials.paginate') }}
                </div>
            </div>
        </div>
    </div>




@endsection




@push('breadcrumb-plugins')
<form action="{{route('admin.warehouse.products')}}" method="GET" class="form-inline float-sm-right bg--white">
    <div class="input-group has_append">
        <input type="text" name="search" class="form-control" placeholder="@lang('Product Name')" value="{{ $search ?? '' }}">
        <div class="input-group-append">
            <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
@endpush

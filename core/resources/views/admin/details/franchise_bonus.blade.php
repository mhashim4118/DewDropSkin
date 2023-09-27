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
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th scope="col">@lang('Username')</th>
                                <th scope="col">@lang('Full Name')</th>
                                <th scope="col">@lang('Franchise Bonus')</th>
                                <th scope="col">@lang('Updated Date')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($references as $key => $product)
                            <tr>
                               <td data-label="@lang('Username')">{{ __($product->username) }} </td>
                                <td data-label="@lang('Full Name')">{{ __($product->firstname) }} {{$product->lastname}}</td>
                                <td data-label="@lang('Franchise Bonus')">{{number_format($product->franchise_bonus, 2) }} PKR </td>
                                <td data-label="@lang('Updated Date')">
                                   {{  showDateTime($product->updated_at)}}
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
                {{ $references->links('admin.partials.paginate') }}
            </div>
        </div>
    </div>
</div>


@endsection

@push('breadcrumb-plugins')
    <form action="{{ route('admin.users.reference')}}" method="GET" class="form-inline float-sm-right bg--white">
        <div class="input-group has_append">
            <input type="text" name="search" class="form-control" placeholder="@lang('Name, username or email')" value="{{ $search ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </form>
@endpush
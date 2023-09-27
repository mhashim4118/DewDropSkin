@extends($activeTemplate.'layouts.master')

@section('content')
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

<div class="row align-items-center">
    <h4 class="mb-2 col-lg-8">@lang('Reference Bonus')</h4>
    <form action="{{ route('user.reference') }}" method="GET" class="col-lg-4 form-inline bg--white">
	  @csrf
	  <div class="row">
		<div class="form-group col-lg-9 col-md-6 col-sm-12">
		  <label>Select Date</label>
		  <input type="date" name="search" class="form-control">
		</div>
			  <div class="form-group col-lg-3 col-md-6 col-sm-12">
			<label></label>
		  <button class="btn btn--primary btn-block" style="width:100%" type="submit"><i class="fa fa-search"></i></button>
		</div>
	  </div>
	</form>
</div>
    <div class="col-lg-12 ">
            <div class="card b-radius--10 overflow-hidden border-0">
                <div class="card-body p-0">

                    <table class="transection-table-2">
                        <thead>
                            <tr>
                             <th scope="col">@lang('S No.')</th>
                                <th scope="col">@lang('DDS Reference Bonus')</th>
                                <th scope="col">@lang('Date')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($references as $key => $product)
                            <tr>
                              <td data-label="@lang('S No.')" >{{ ($references->total() - ($references->firstItem() + $key)) + 1}}</td>
                                <td data-label="@lang('DDS Reference Bonus')">{{number_format($product->amount, 2) }} PKR </td>
                                <td data-label="@lang('Updated Date')">
                                   {{  showDateTime($product->created_at)}}
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
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
    <h4 class="mb-2 col-lg-8">@lang('Deposit')</h4>
    <form action="{{ route('user.deposits') }}" method="GET" class="col-lg-4 form-inline bg--white">
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
            </div>
        </div>
        <div class="card-footer py-4">
            {{ $transactions->links('admin.partials.paginate') }}
        </div>
    </div><!-- card end -->
</div>
</div>

@endsection




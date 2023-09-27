@extends($activeTemplate.'layouts.master')
@section('content')
<h4 class="mb-2">@lang('Orders')</h4>
<table class="transection-table-2">
    <thead>
        <tr>
			<th>@lang('S No.')</th>
      
            <th>@lang('Total Price')</th>
            <th>@lang('Payment Status')</th>
            <th>@lang('Status')</th>
            <th>@lang('Invoice')</th>
        </tr>
    </thead>
    <tbody>
        @forelse($orders as $key=>$order)
            <tr  onclick="myFunction(<?php echo $order->id; ?>)" style="cursor:pointer; margin-bottom:20px; box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;">
				<td data-label="@lang('S No.')">{{($orders->total() - ($orders->firstItem() + $key)) + 1 }}</td>
                <td data-label="@lang('Total Price')">{{ __($order->order_amount) }}</td>
                <td data-label="@lang('Quantity')">{{ __($order->payment_status) }}</td>
                <td data-label="@lang('Status')">
                    {{$order->order_status}}
                
                </td>
                <td data-label="@lang('Invoice')" class="text-center">
                    @if ($order->customer_type == 'customer')
                    <a class="btn btn--primary px-4" target="_blank" href="https://dewdropskin.com/store/customer-generate-invoice/{{ $order->o_id }}">
                        <i class="fa fa-print"></i>
                    </a>
                    @elseif($order->customer_type == 'Seller')
                    <a class="btn btn--primary px-4" target="_blank" href="https://dewdropskin.com/store/seller-generate-invoice/{{ $order->o_id }}">
                        <i class="fa fa-print"></i>
                    </a>
                    @else -
                    @endif
                </td>
            </tr>
	
        @empty
            <tr>
                <td colspan="100%" class="text-center">@lang('No order found')</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="card-footer pb-4">
  {{$orders->links()}}
</div>

@endsection
@push('script')
  <script>
  $('.extra-details').hide();

  function myFunction(id) {
    $(`#extra-details${id}`).toggle();
  }
      
  </script>
@endpush

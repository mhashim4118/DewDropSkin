@extends($activeTemplate.'layouts.master')
@section('content')
<h4 class="mb-2">@lang('Price List')</h4>
<table class="transection-table-2">
    <thead>
        <tr>
          <th scope="col">@lang('Sl')</th>
          <th scope="col">@lang('Name')</th>
          <th scope="col">@lang('')</th>
          <th scope="col">@lang('PV')</th>
          <th scope="col">@lang('BV')</th>
          <th scope="col">@lang('Price')</th>
        </tr>
    </thead>
    <tbody>
      @forelse(@$price_lists as $key => $product)
      <tr style="cursor: pointer; margin-bottom:20px; box-shadow: rgba(50, 50, 93, 0.25) 0px 13px 27px -5px, rgba(0, 0, 0, 0.3) 0px 8px 16px -8px;">
          <td data-label="@lang('Sl')">{{ $price_lists->firstItem() + $loop->index }}</td>
          <td data-label="@lang('Name')">{{ __($product->product_name) }}</td>
          <td data-label="@lang('')"> <img style="width:150px; height:150px; display:block; margin:auto; object-fit: cover" src="{{ getImage(imagePath()['products']['path'].'/'.$product->thumbnail,imagePath()['products']['size'])}}" alt="" class="shadow rounded __img"></td>
         
          <td data-label="@lang('PV')">{{ __($product->pv) }}</td>
          <td data-label="@lang('BV')">{{ __($product->bv) }}</td>
          <td data-label="@lang('Price')">{{ __(showAmount($product->price)) }} {{ $general->cur_text }}</td>
         
      
      </tr>
      @empty
      <tr>
          <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
      </tr>
      @endforelse

  </tbody>
</table>
<div class="card-footer pb-4">
  {{$price_lists->links()}}
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
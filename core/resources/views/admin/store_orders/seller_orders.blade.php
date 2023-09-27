@extends('admin.layouts.app')

@section('panel')

    <div class="row">

        <div class="col-lg-12">
            <div class="card b-radius--10 ">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                            
                                <th>@lang('ID')</th>
                                <th>@lang('Customer')</th>
                                <th>@lang('Seller')</th>
                                
                                <th>@lang('Total Price')</th>
                                <th>@lang('Payment Status')</th>
                                <th>@lang('Order Status')</th>
                                <th>@lang('Invoice')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td data-label="@lang('Product')">{{ __($order->o_id) }}</td>
                            <td data-label="@lang('User')">
                                    <span class="font-weight-bold">{{$order->name}}</span>
                                   
                                </td>
                            <td data-label="@lang('User')">
                                    <span class="font-weight-bold">{{$order->dds_username}}</span>
                                   
                                </td>
              
                                <td data-label="@lang('Total Price')">{{ __($order->order_amount) }}</td>
                                <td data-label="@lang('Quantity')">{{ __($order->payment_status) }}</td>
                               <td data-label="@lang('Status')">
                             
                                    <span>{{$order->order_status }}</span>
                                 
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
                                    <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>
                <div class="card-footer py-4">
                    {{ paginateLinks($orders) }}
                </div>
            </div><!-- card end -->
        </div>
    </div>

        <div class="modal fade" id="statusModal">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">@lang('Update Order Status')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
              </div>
              <form action="" method="post">
                  @csrf
                    <div class="modal-body">
                      <div class="form-group">
                          <label>@lang('Order Status')</label>
                          <select class="form-control" name="status">
                              <option value="1">@lang('Shipped')</option>
                              <option value="2">@lang('Cancel')</option>
                          </select>
                      </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" data-dismiss="modal" class="btn btn--dark">@lang('Cancel')</button>
                        <button type="submit" class="btn btn--primary">@lang('Submit')</button>
                    </div>
                </form>
            </div>
          </div>
        </div>
@endsection
@push('breadcrumb-plugins')
<form action="{{ route('admin.orders.seller_orders')}}" method="GET" class="form-inline float-sm-right bg--white">
    <div class="row ">
        <div class="col-12 col-md-6">
            <div class="input-group">
                <input type="text" name="o_id" class="form-control" placeholder="@lang('Order ID')" value="{{ $search ?? '' }}">



            </div>
        </div>
        <div class="col-12 col-md-6">

            <div class="input-group has_append">
                <input type="text" name="search" class="form-control" placeholder="@lang('Seller Username')" value="{{ $search ?? '' }}">
                <div class="input-group-append">
                    <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
                </div>

            </div>
        </div>
    </div>
</form>
@endpush
@push('script')
<script>
    (function($){
        "use strict";

        $('.orderBtn').click(function(){
            var modal = $('#statusModal');
            modal.find('form').attr('action',$(this).data('action'));
            modal.modal('show');
        });
    })(jQuery);
</script>
@endpush

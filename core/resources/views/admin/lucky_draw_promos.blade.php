@extends('admin.layouts.app')

@section('panel')
        <div class="col-xl-3 col-lg-4 col-sm-6 mb-30">
            <div class="dashboard-w1 bg--primary b-radius--10 box-shadow">
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <div class="details">
                    <div class="numbers">
                        <span class="amount">Total DLP: {{ $dlps }} </span>

                    </div>
                    <div class="desciption">
                        <span class="text--small">@lang('Current DLP '): @if($dlp){{$dlp->dlp}}@endif</span>
                    </div>
  					<div class="desciption">
                        <a href="{{ route('admin.lucky_draw_all') }}"
                            class="btn btn-sm text--small bg--white text--black box--shadow3 mt3">
                            @lang('View All')
                        </a>
                    </div>
                </div>
            </div>
        </div>


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                            <tr>
                                <th scope="col">@lang('Sl')</th>
                                <th scope="col">@lang('Username')</th>
                                <th scope="col">@lang('PROMO Name')</th>
                             
                                <th scope="col">@lang('Status')</th>
                                <th scope="col">@lang('Action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($lucky_draw_promos as $key => $promo)
                                <tr>
                                    <td data-label="@lang('Sl')">{{$key+1}}</td>
                                    <td data-label="@lang('Name')">{{ __($promo->username) }}</td>
                                    <td data-label="@lang('Name')">{{ __($promo->promo) }}</td>
                                    <td data-label="@lang('Status')">
                                        @if($promo->status == 1)
                                            <span class="text--small badge font-weight-normal badge--success">@lang('Completed')</span>
                                        @else
                                            <span class="text--small badge font-weight-normal badge--danger">@lang('Pending')</span>
                                        @endif
                                    </td>
									 <td data-label="@lang('Action')">
                                        <button type="button" class="icon-btn edit" data-toggle="tooltip"
                                                data-id="{{ $promo->id }}"
                                                data-name="{{ $promo->username }}"
                                                
                                                data-original-title="Complete">
                                            <i class="la la-pencil"></i>
                                        </button>
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
                    {{ $lucky_draw_promos->links('admin.partials.paginate') }}
                </div>
            </div>
        </div>
    </div>



    <div id="edit-plan" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Complete Lucky Draw PROMO')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="post" action="{{route('admin.lucky_draw_complete')}}">
                    @csrf
                    <div class="modal-body">

                        <input class="form-control plan_id" type="hidden" name="id">

                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label class="font-weight-bold"> @lang('Username') :</label>
                                <input class="form-control name" name="name" required>
                            </div>
                   
                        </div>


                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-block btn--primary">@lang('Complete')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    

@endsection




@push('script')
    <script>
        "use strict";
        (function ($) {
            $('.edit').on('click', function () {
                var modal = $('#edit-plan');
              
                modal.find('.name').val($(this).data('name'));
                modal.find('.type').val($(this).data('type'));
                modal.find('.price').val($(this).data('price'));
                if($(this).data('status')){
                    modal.find('.toggle').removeClass('btn--danger off').addClass('btn--success');
                    modal.find('input[name="status"]').prop('checked',true);

                }else{
                    modal.find('.toggle').addClass('btn--danger off').removeClass('btn--success');
                    modal.find('input[name="status"]').prop('checked',false);
                }

                modal.find('input[name=id]').val($(this).data('id'));
                modal.modal('show');
            });

            $('.add-plan').on('click', function () {
                var modal = $('#add-plan');
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush


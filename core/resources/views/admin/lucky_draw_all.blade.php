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
                                <th scope="col">@lang('Sl')</th>
                                <th scope="col">@lang('Username')</th>
                                <th scope="col">@lang('LDP')</th>
                             
                                <th scope="col">@lang('Full Name')</th>
                                <th scope="col">@lang('City')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $reversedLuckyDrawAll = $lucky_draw_all->reverse(); @endphp

@forelse($reversedLuckyDrawAll as $key => $promo)
    <tr>
        <td data-label="@lang('Sl')">{{$key+1}}</td>
        <td data-label="@lang('Username')">{{ __($promo->username) }}</td>
        <td data-label="@lang('LDP')">{{ __($promo->dlp) }}</td>
        <?php $user = \App\Models\User::where('username', $promo->username)->first(); ?>
        <td data-label="@lang('Full Name')">{{$user->firstname}} {{$user->lastname }}</td>
        <td data-label="@lang('City')">{{$user->address->city}}</td>
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
                    {{ $lucky_draw_all->links('admin.partials.paginate') }}
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
@push('breadcrumb-plugins')

    <form action="{{ route('admin.lucky_draw_all')}}" method="GET" class="form-inline float-sm-right bg--white"><div class="col-12 col-md-6">
		
		<div class="input-group">

            <input type="text" name="city" class="form-control" placeholder="@lang('City')" value="{{ $search ?? '' }}">

        </div></div><div class="col-12 col-md-6">

		

	

	<div class="input-group has_append">

            <input type="text" name="search" class="form-control" placeholder="@lang(' Username')" value="{{ $search ?? '' }}">

            <div class="input-group-append">

                <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>

            </div>

        </div></div>

    </form>

@endpush



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


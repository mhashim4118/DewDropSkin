@extends($activeTemplate.'layouts.master')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header card-header-bg d-flex flex-wrap justify-content-between align-items-center">
                <h5 class="card-title mt-0">
                    @if($my_ticket->status == 0)
                        <span class="badge bg--success">@lang('Open')</span>
                    @elseif($my_ticket->status == 1)
                        <span class="badge bg--primary ">@lang('Answered')</span>
                    @elseif($my_ticket->status == 2)
                        <span class="badge bg--warning">@lang('Replied')</span>
                    @elseif($my_ticket->status == 3)
                        <span class="badge bg--dark">@lang('Closed')</span>
                    @endif
                    [@lang('Ticket')#{{ $my_ticket->ticket }}] {{ $my_ticket->subject }}
                </h5>
                <button class="btn btn-sm btn--danger close-button" type="button" data-bs-toggle="modal" data-bs-target="#DelModal"><i class="fa fa-times"></i> @lang('Close Ticket')
                </button>
            </div>
            <div class="card-body">
                @if($my_ticket->status != 4)
                    <form method="post" action="{{ route('ticket.reply', $my_ticket->id) }}"
                            enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-between">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea name="message" class="form-control form--control form-control form--control-lg" id="inputMessage" placeholder="@lang('Your Reply') ..." rows="4" cols="10" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>@lang('Attachments')</label>
                            <input name="attachments[]" type="file" id="customFile" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                        </div>

                        <div class="fileUploadsContainer"></div>

                        <p class="text-muted m-2">
                            <i class="la la-info-circle"></i> @lang("Allowed File Extensions: .jpg, .jpeg, .png, .pdf")
                        </p>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="javascript:void(0)" class="btn btn--success add-more-btn">
                                <i class="la la-plus"></i>
                                @lang('Add More')
                            </a>
                            <button type="submit" class="btn btn--success btn-block" name="replayTicket" value="1"><i class="fa fa-reply"></i> @lang('Reply')</button>
                        </div>


                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                @foreach($messages as $message)
                    @if($message->admin_id == 0)
                        <div class="row border border-primary border-radius-3 my-3 py-3 mx-2">
                            <div class="col-md-3 border-right text-right">
                                <h5 class="my-3">{{ $message->ticket->name }}</h5>
                            </div>
                            <div class="col-md-9">
                                <p class="text-muted font-weight-bold my-3">
                                    @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                <p>{{$message->message}}</p>
                                @if($message->attachments()->count() > 0)
                                    <div class="mt-2">
                                        @foreach($message->attachments as $k=> $image)
                                            <a href="{{route('ticket.download',encrypt($image->id))}}"
                                            class="mr-3"><i
                                                    class="fa fa-file"></i> @lang('Attachment') {{++$k}}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="row border border-warning border-radius-3 my-3 py-3 mx-2"
                            style="background-color: #ffd96729">
                            <div class="col-md-3 border-right text-right">
                                <h5 class="my-3">{{ $message->admin->name }}</h5>
                                <p class="lead text-muted">@lang('Staff')</p>
                            </div>
                            <div class="col-md-9">
                                <p class="text-muted font-weight-bold my-3">
                                    @lang('Posted on') {{ $message->created_at->format('l, dS F Y @ H:i') }}</p>
                                <p>{{$message->message}}</p>@if($message->attachments()->count() > 0)
                                    <div class="mt-2">
                                        @foreach($message->attachments as $k=> $image)
                                            <a href="{{route('ticket.download',encrypt($image->id))}}"
                                            class="mr-3"><i
                                                    class="fa fa-file"></i> @lang('Attachment') {{++$k}}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('modal')

<div class="modal fade" id="DelModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('ticket.reply', $my_ticket->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"> @lang('Confirmation Alert')!</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <strong class="text-dark">@lang('Are you sure you want to Close This Support Ticket')?</strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">
                        @lang('No')
                    </button>
                    <button type="submit" class="btn btn--success" name="replayTicket"
                            value="2"></i> @lang("Yes")
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush
@endsection

@push('script')
    <script>
        'use strict';
        (function($){
            $(document).on("change", '.custom-file-input' ,function() {
                var fileName = $(this).val().split("\\").pop();
                $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
            });
            var itr = 0;
            $('.add-more-btn').on('click', function(){
                itr++
                $(".fileUploadsContainer").append(`<div class="form-group">
                                    <label>@lang('Attachments')</label>
                                    <input name="attachments[]" type="file" id="customFile" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                                </div>`);

            });

        })(jQuery)
    </script>
@endpush
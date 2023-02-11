<div role="dialog" class="modal fade" style="display: ;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                   @lang("Question.Q"): {{ $question->title }}
                </h3>
            </div>

            @if(count($answers))
            <div class="table-responsive">
                           <table class="table">
                               <thead>
                               <tr>
                                   <th>
                                       @lang("Question.attendee_details")
                                   </th>
                                   <th>
                                       @lang("ManageEvent.ticket")
                                   </th>
                                   <th>
                                       @lang("Question.answer")
                                   </th>
                               </tr>

                               </thead>
                               <tbody>
                               @foreach($answers as $answer)
                                   <tr>
                                       <td>

                                           {{ $answer->attendee->full_name }}
                                           @if($answer->attendee->is_cancelled)
                                               (<span title="@lang("ManageEvent.attendee_cancelled_help")" class="text-danger">@lang("ManageEvent.attendee_cancelled")</span>)
                                           @endif<br>
                                           <a title="@lang("ManageEvent.go_to_attendee_name", ["name"=>$answer->attendee->full_name])" href="{{route('showEventAttendees', ['event_id' => $answer->attendee->event_id, 'q' => $answer->attendee->reference])}}">{{ $answer->attendee->email }}</a><br>

                                       </td>
                                       <td>
                                           {{ $answer->attendee->ticket->title }}
                                       </td>
                                       <td>
                                        @if($question->question_type_id == config('attendize.question_file_single'))
                                            <a target="_blank" href="{!! url($answer->answer_text) !!}">@lang('ManageEvent.open_file')</a>
                                        @else
                                            {!! nl2br(e($answer->answer_text)) !!}
                                        @endif
                                       </td>
                                   </tr>
                               @endforeach
                               </tbody>
                           </table>

                       </div>
            @else
                <div class="modal-body">
                    <div class="alert alert-info">
                        @lang("Question.no_answers")
                    </div>
                </div>

            @endif

            <div class="modal-footer">
                {!! Form::button(trans("ManageEvent.close"), ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
            </div>
        </div><!-- /end modal content-->
    </div>
</div>
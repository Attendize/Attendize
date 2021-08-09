<div id="QuestionForm" role="dialog" class="modal fade" style="display: none;">
    {!!  Form::open(['url' => route('postCreateEventQuestion', ['event_id'=>$event->id]), 'id' => 'edit-question-form', 'class' => 'ajax']) !!}

    <script id="question-option-template" type="text/template">
        <tr>
            <td><input class="form-control" name="option[]" type="text"></td>
            <td width="50">
                <i class="btn btn-danger ico-remove" onclick="removeQuestionOption(this);"></i>
            </td>
        </tr>
    </script>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-question"></i>
                    @lang("Question.create_question")</h3>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="question-title" class="required">
                        @lang("Question.question")
                    </label>
                    {!! Form::text('title', '', [
                        'id' => 'question-title',
                        'class' => 'form-control',
                        'placeholder' => trans("Question.question_placeholder"),//'e.g. Please enter your full address?',
                    ]) !!}
                </div>
                <div class="form-group">
                    <label for="question-type">
                        @lang("Question.question_type")
                    </label>

                    <select id="question-type" class="form-control" name="question_type_id"
                            onchange="changeQuestionType(this);">
                        @foreach ($question_types as $question_type)
                            <option data-has-options="{{$question_type->has_options}}" value="{{$question_type->id}}">
                                {{$question_type->name}}
                            </option>
                        @endforeach
                    </select>
                </div>


                <fieldset id="question-options" class="hide">
                    <h4>@lang("Question.question_options")</h4>
                    <table class="table table-bordered table-condensed">
                        <tbody>
                        <tr>
                            <td><input class="form-control" name="option[]" type="text" value=""></td>
                            <td width="50">
                                <i class="btn btn-danger ico-remove" onclick="removeQuestionOption(this);"></i>
                            </td>
                        </tr>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="2">
                                           <span id="add-question-option" class="btn btn-success btn-xs"
                                                 onclick="addQuestionOption();">
                                               <i class="ico-plus"></i>
                                               @lang("Question.add_another_option")
                                           </span>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </fieldset>

                <div class="form-group">
                    <div class="custom-checkbox">
                        {!! Form::checkbox('is_required', 1, false, ['data-toggle' => 'toggle', 'id' => 'is_required']) !!}
                        {!! Form::label('is_required', trans('Question.make_this_a_required_question')) !!}
                    </div>
                </div>

                <h4>
                    @lang("Question.require_this_question_for_ticket(s)"):
                </h4>
                <div class="form-group">

                    @foreach ($event->tickets as $ticket)
                        <div class="custom-checkbox mb5">
                            <input id="ticket_{{ $ticket->id }}" name="tickets[]" data-toggle='toggle' type="checkbox"
                                   value="{{ $ticket->id }}">
                            <label for="ticket_{{ $ticket->id }}">&nbsp; {{ $ticket->title }}</label>
                        </div>
                    @endforeach
                </div>

            </div> <!-- /end modal body-->
            <div class="modal-footer">
                {!! Form::button(trans("basic.cancel"), ['class'=>"btn modal-close btn-danger",'data-dismiss'=>'modal']) !!}
                {!! Form::submit(trans("ManageEvent.save_question"), ['class'=>"btn btn-success"]) !!}
            </div>
        </div><!-- /end modal content-->
    </div>
    {!! Form::close() !!}
</div>



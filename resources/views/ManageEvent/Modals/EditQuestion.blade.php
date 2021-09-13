<div role="dialog" class="modal fade" style="display: none;">
    {!!  Form::model($question, ['url' => route('postEditEventQuestion', ['event_id' => $event->id, 'question_id' => $question->id]), 'id' => 'edit-question-form', 'class' => 'ajax']) !!}
    <script id="question-option-template" type="text/template">
        <tr>
            <td width="300"><input class="form-control" name="option[]" type="text" placeholder="Option" value=""></td>
            <td width="100" ><input class="form-control" name="price[]" type="number" placeholder="Price" step="0.01" value=""></td>
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
                    @lang("ManageEvent.edit_question")</h3>
                </h3>
            </div>
            <div class="modal-body">
                        <div class="form-group">
                            <label for="question-title" class="required">
                                @lang("Question.question")
                            </label>
                            {!! Form::text('title', $question->title, [
                                'id' => 'question-title',
                                'class' => 'form-control',
                                'placeholder' => trans("Question.question_placeholder"),
                            ]) !!}
                        </div>
                        <div class="form-group">
                            <label for="question-type">
                                @lang("Question.question_type")
                            </label>

                            <select id="question-type" class="form-control" name="question_type_id" onchange="changeQuestionType(this);">
                                @foreach ($question_types as $question_type)
                                    <option {{$question->question_type_id == $question_type->id ? 'selected' : ''}} data-has-options="{{$question_type->has_options}}" value="{{$question_type->id}}">
                                        {{$question_type->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <fieldset id="question-options" class="{{ $question->question_type->has_options ? '' : 'hide' }}" >
                            <h4>@lang("Question.question_options")</h4>
                            <table class="table table-condensed table-bordered">
                                <tbody>
                                @if(count($question->options))
                                    @foreach ($question->options as $question_option)
                                        <tr>
                                            <input name="id[]" type="hidden" value="{{ $question_option->id }}">
                                            <td width="300"><input class="form-control" name="option[]" type="text" placeholder="Option" value="{{ $question_option->name }}"></td>
                                            <td width="100" ><input class="form-control" name="price[]" type="number" placeholder="Price" step="0.01" value="{{ $question_option->price }}"></td>
                                            <td width="50">
                                                <i class="btn btn-danger ico-remove" onclick="removeQuestionOption(this);"></i>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td width="300"><input class="form-control" name="option[]" type="text" placeholder="Option" value=""></td>
                                        <td width="100" ><input class="form-control" name="price[]" type="number" placeholder="Price" step="0.01" value=""></td>
                                        <td width="50"></td>
                                    </tr>
                                    @endif

                                </tbody>
                                <tfoot>
                                    <tr>
                                       <td colspan="2">
                                           <span id="add-question-option" class="btn btn-success btn-xs" onclick="addQuestionOption();">
                                               <i class="ico-plus"></i>
                                               @lang("Question.add_another_option")
                                           </span>
                                       </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </fieldset>

                        <div class="form-group">
                            <div class="custom-checkbox ">
                            {!! Form::checkbox('is_required', 1, $question->is_required, ['data-toggle' => 'toggle', 'id' => 'is_required']) !!}
                            {!! Form::label('is_required', trans('Question.make_this_a_required_question')) !!}
                                </div>
                        </div>

                        <div class="form-group">
                            <label>
                                @lang("Question.require_this_question_for_ticket(s)"):
                            </label>
                            @foreach ($event->tickets as $ticket)
                                <div class="custom-checkbox mb5">
                                <input {{in_array($ticket->id, $question->tickets->pluck('id')->toArray()) ? 'checked' : ''}} id="ticket_{{ $ticket->id }}" data-toggle="toggle" name="tickets[]" type="checkbox" value="{{ $ticket->id }}">
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

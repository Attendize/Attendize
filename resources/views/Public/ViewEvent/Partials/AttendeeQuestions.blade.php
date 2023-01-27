@foreach($ticket->questions->where('is_enabled', 1)->sortBy('sort_order') as $question)
    <div class="col-md-12">
        <div class="form-group">
            {!! Form::label("ticket_holder_questions[{$ticket->id}][{$i}][$question->id]", $question->title, ['class' => $question->is_required ? 'required' : '']) !!}

            @if($question->question_type_id == config('attendize.question_textbox_single'))
                {!! Form::text("ticket_holder_questions[{$ticket->id}][{$i}][$question->id]", null, [$question->is_required ? 'required' : '' => $question->is_required ? 'required' : '', 'class' => "ticket_holder_questions.{$ticket->id}.{$i}.{$question->id}   form-control"]) !!}
            @elseif($question->question_type_id == config('attendize.question_textbox_multi'))
                {!! Form::textarea("ticket_holder_questions[{$ticket->id}][{$i}][$question->id]", null, ['rows'=>5, $question->is_required ? 'required' : '' => $question->is_required ? 'required' : '', 'class' => "ticket_holder_questions.{$ticket->id}.{$i}.{$question->id}  form-control"]) !!}
            @elseif($question->question_type_id == config('attendize.question_dropdown_single'))
                <select @if($question->is_required) required @endif class="form-control answer" name="ticket_holder_questions[{{$ticket->id}}][{{$i}}][{{$question->id}}]">
                    <option selected value="">-- Please Select --</option>
                    @foreach($question->options as $option)
                        <option data-price="{{$option->price}}" data-name="{{$option->name}}" value="{{$option->id}}">{{$option->showWithNameAndPrice($currency)}}</option>
                    @endforeach
                </select>
            @elseif($question->question_type_id == config('attendize.question_dropdown_multi'))
                <select @if($question->is_required) required @endif multiple="multiple" name="ticket_holder_questions[{{$ticket->id}}][{{$i}}][{{$question->id}}][]" class="form-control answer">
                    @foreach($question->options as $option)
                        <option data-price="{{$option->price}}" data-name="{{$option->name}}" value="{{$option->id}}">{{$option->showWithNameAndPrice($currency)}}</option>
                    @endforeach
                </select>
            @elseif($question->question_type_id == config('attendize.question_checkbox_multi'))
                <br>
                @foreach($question->options as $option)
                    <?php
                        $checkbox_id = md5($ticket->id.$i.$question->id.$option->name);
                    ?>
                    <div class="custom-checkbox">
                        <input @if($question->is_required) required @endif id="{{$checkbox_id}}" type="checkbox" class="answer" name="ticket_holder_questions[{{$ticket->id}}][{{$i}}][{{$question->id}}][]" data-price="{{$option->price}}" data-name="{{$option->name}}" value="{{$option->id}}">
                        <label for="{{ $checkbox_id }}">{{$option->showWithNameAndPrice($currency)}} </label>
                    </div>
                @endforeach
            @elseif($question->question_type_id == config('attendize.question_radio_single'))
                <br>
                @foreach($question->options as $option)
                    <?php
                    $radio_id = md5($ticket->id.$i.$question->id.$option->name);
                    ?>
                <div class="custom-radio">
                    <input @if($question->is_required) required @endif id="{{$radio_id}}" type="radio" class="answer" name="ticket_holder_questions[{{$ticket->id}}][{{$i}}][{{$question->id}}]" data-price="{{$option->price}}" data-name="{{$option->name}}" value="{{$option->id}}">
                    <label for="{{ $radio_id }}">{{$option->showWithNameAndPrice($currency)}}</label>
                </div>
                @endforeach
            @endif

        </div>
    </div>
@endforeach

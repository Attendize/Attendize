@extends('Shared.Layouts.MasterWithoutMenus')

@section('title')
    @lang("User.sign_up")
@stop

@section('content')
    <div class="row">
        <div class="col-md-7 col-md-offset-2">
            {!! Form::open(['url' => route("showSignup"), 'class' => 'panel', 'id' => 'signup-form']) !!}
            <div class="panel-body">
                <div class="logo">
                   {!! Html::image('assets/images/logo-dark.png') !!}
                </div>
                <h2>@lang("User.sign_up")</h2>

                @if(Request::input('first_run'))
                    <input type="hidden" name="first_run" id="first_run" value="yup" />
                    <div class="alert alert-info">
                        @lang("User.sign_up_first_run")
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ ($errors->has('first_name')) ? 'has-error' : '' }}">
                            {!! Form::label('first_name', trans("User.first_name"), ['class' => 'control-label required']) !!}
                            {!! Form::text('first_name', null, ['class' => 'form-control']) !!}
                            @if($errors->has('first_name'))
                                <p class="help-block">{{ $errors->first('first_name') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ ($errors->has('last_name')) ? 'has-error' : '' }}">
                            {!! Form::label('last_name', trans("User.last_name"), ['class' => 'control-label required']) !!}
                            {!! Form::text('last_name', null, ['class' => 'form-control']) !!}
                            @if($errors->has('last_name'))
                                <p class="help-block">{{ $errors->first('last_name') }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group {{ ($errors->has('email')) ? 'has-error' : '' }}">
                    {!! Form::label('email', trans("User.email"), ['class' => 'control-label required']) !!}
                    {!! Form::text('email', null, ['class' => 'form-control']) !!}
                    @if($errors->has('email'))
                        <p class="help-block">{{ $errors->first('email') }}</p>
                    @endif
                </div>
                <div class="form-group {{ ($errors->has('password')) ? 'has-error' : '' }}">
                    {!! Form::label('password', trans("User.password"), ['class' => 'control-label required']) !!}
                    {!! Form::password('password',  ['class' => 'form-control']) !!}
                    @if($errors->has('password'))
                        <p class="help-block">{{ $errors->first('password') }}</p>
                    @endif
                </div>
                <div class="form-group {{ ($errors->has('password_confirmation')) ? 'has-error' : '' }}">
                    {!! Form::label('password_confirmation', 'Password again', ['class' => 'control-label required']) !!}
                    {!! Form::password('password_confirmation',  ['class' => 'form-control']) !!}
                    @if($errors->has('password_confirmation'))
                        <p class="help-block">{{ $errors->first('password_confirmation') }}</p>
                    @endif
                </div>

                @if(Utils::isAttendizeCloud())
                <div class="form-group {{ ($errors->has('terms_agreed')) ? 'has-error' : '' }}">
                    <div class="checkbox custom-checkbox">
                        {!! Form::checkbox('terms_agreed', old('terms_agreed'), false, ['id' => 'terms_agreed']) !!}
                        {!! Form::rawLabel('terms_agreed', trans("User.terms_and_conditions", ["url"=>route('termsAndConditions')])) !!}
                        @if ($errors->has('terms_agreed'))
                            <p class="help-block">{{ $errors->first('terms_agreed') }}</p>
                        @endif
                    </div>
                </div>
                @endif

                @include('Public.LoginAndRegister.Partials.CaptchaSection')

                <div class="form-group">
                    <p><input class="btn btn-block btn-success" type="submit" value="@lang('User.sign_up')"></p>
                </div>
                <div class="signup">
                    <span>{!! @trans("User.already_have_account", ["url"=>route("login")]) !!}</span>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@stop

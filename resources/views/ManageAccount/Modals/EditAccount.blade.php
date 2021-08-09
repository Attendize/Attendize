<div role="dialog" class="modal fade" style="display: none;">
    <style>
        .account_settings .modal-body {
            border: 0;
            margin-bottom: -35px;
            border: 0;
            padding: 0;
        }
        .account_settings .panel-footer {
            margin: -15px;
            margin-top: 20px;
        }
        .account_settings .panel {
            margin-bottom: 0;
            border: 0;
        }
        .user-list .form-group {
            margin-bottom: 0;
        }
        [hidden] {
           display: none;
        }
    </style>
    <div class="modal-dialog account_settings" style="width:750px;">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3 class="modal-title">
                    <i class="ico-cogs"></i>
                    @lang("ManageAccount.account")</h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- tab -->
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#general_account" data-toggle="tab">@lang("ManageAccount.general")</a></li>
                            <li><a href="#payment_account" data-toggle="tab">@lang("ManageAccount.payment")</a></li>
                            <li><a href="#users_account" data-toggle="tab">@lang("ManageAccount.users")</a></li>
                            <li><a href="#about" data-toggle="tab">@lang("ManageAccount.about")</a></li>
                        </ul>
                        <div class="tab-content panel">
                            <div class="tab-pane active" id="general_account">
                                {!! Form::model($account, ['url' => route('postEditAccount'), 'class' => 'ajax']) !!}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('first_name', trans("ManageAccount.first_name"), ['class'=>'control-label required']) !!}
                                            {!! Form::text('first_name', old('first_name'), ['class'=>'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('last_name', trans("ManageAccount.last_name"), ['class'=>'control-label required']) !!}
                                            {!! Form::text('last_name', old('last_name'), ['class'=>'form-control']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            {!! Form::label('email', trans("ManageAccount.email"), ['class'=>'control-label required']) !!}
                                            {!! Form::text('email', old('email'), ['class'=>'form-control']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('timezone_id', trans("ManageAccount.timezone"), ['class'=>'control-label required']) !!}
                                            {!! Form::select('timezone_id', $timezones, $account->timezone_id, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('currency_id', trans("ManageAccount.default_currency"), ['class'=>'control-label required']) !!}
                                            {!! Form::select('currency_id', $currencies, $account->currency_id, ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="panel-footer">
                                            {!! Form::submit(trans("ManageAccount.save_account_details_submit"), ['class' => 'btn btn-success pull-right']) !!}
                                        </div>
                                    </div>
                                </div>
                                {!! Form::close() !!}
                            </div>
                            <div class="tab-pane " id="payment_account">
                                @include('ManageAccount.Partials.PaymentGatewayOptions')
                            </div>
                            <div class="tab-pane" id="users_account">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <td><strong>{{ trans("ManageAccount.name") }}</strong></td>
                                                <td><strong>{{ trans("ManageAccount.role") }}</strong></td>
                                                <td><strong>{{ trans("ManageAccount.email") }}</strong></td>
                                                <td><strong>{{ trans("ManageAccount.manage_events") }}</strong></td>
                                                <td>&nbsp;</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($account->users()->withTrashed()->get() as $user)
                                            <tr class="user-list">
                                                <td>{{$user->first_name}} {{$user->last_name}}</td>
                                                <td class="text-center">
                                                    <div class="form-group">
                                                        <?php
                                                        $defaultAssignedSelected = 1;
                                                        $assignedRoles = $roles->mapWithKeys(function($role) use ($user, &$defaultAssignedSelected) {
                                                            // Auto select the user role
                                                            if ($role->name === $user->roles->first()->name) {
                                                                $defaultAssignedSelected = $role->id;
                                                            }
                                                            return [$role['id'] => Str::title($role['name'])];
                                                        });
                                                        ?>
                                                        {!!
                                                        Form::select('assigned_role', $assignedRoles, $defaultAssignedSelected, [
                                                            'class' => 'form-control required',
                                                            'data-user' => $user->id,
                                                            'data-update-url' => route('postUpdateUserRole'),
                                                        ]);
                                                        !!}
                                                    </div>
                                                </td>
                                                <td>{{$user->email}}</td>
                                                <td>
                                                    <div class="form-group">
                                                        <?php
                                                        $checked = $user->can('manage events');
                                                        $disabled = ($user->roles->first()->name !== 'user');
                                                        $uniqueID = sprintf("can_manage_events_%d", $user->id);
                                                        $attributes = [
                                                            'id' => $uniqueID,
                                                            'data-user' => $user->id,
                                                            'data-update-url' => route('postToggleUserCanManageEvents'),
                                                        ];

                                                        if ($disabled) {
                                                            $attributes['disabled'] = 'disabled';
                                                        }
                                                        ?>
                                                        <div class="checkbox custom-checkbox">
                                                            {!! Form::checkbox("can_manage_events", 1, $checked, $attributes) !!}
                                                            {!! Form::label($uniqueID, ' ', [
                                                                'title' => ($disabled) ? trans("ManageAccount.additional_permission") : '',
                                                            ]) !!}
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    @if ($user->id != auth()->user()->id)
                                                        <div style="position:relative;">
                                                            <button data-id="dropdown" class="btn btn-sm btn-default">
                                                                &hellip;
                                                            </button>

                                                            <div class="dropdown-content" style="position:absolute;z-index:10;bottom:0;right:0;transform:translateY(100%)" hidden>
                                                                <div style="display:flex;flex-direction:column;padding:0.5rem;background-color:white">
                                                                    <button
                                                                        name="user_action"
                                                                        data-action="send_invitation_message"
                                                                        data-href="{!! route("sendInvitationMessage", ["id" => $user->id]) !!}"
                                                                        class="btn btn-sm btn-default"
                                                                        style="margin-bottom:0.5rem"
                                                                        {!! $user->trashed() ? 'hidden' : '' !!}
                                                                    >
                                                                        {!! trans("ManageAccount.send_invitation_message") !!}
                                                                    </button>

                                                                    <button
                                                                        name="user_action"
                                                                        data-action="restore"
                                                                        data-href="{!! route("userRestore", ["id" => $user->id]) !!}"
                                                                        aria-label="{!! trans("basic.restore") !!}"
                                                                        title="{!! trans("basic.restore") !!}"
                                                                        class="btn btn-sm btn-warning"
                                                                        style="margin-bottom:0.5rem"
                                                                        {!! !$user->trashed() ? 'hidden' : '' !!}
                                                                    >
                                                                        {!! trans("basic.restore") !!}
                                                                    </button>

                                                                    <button
                                                                        name="user_action"
                                                                        data-action="force_delete"
                                                                        data-href="{!! route("userDelete", ["id" => $user->id, "force" => true]) !!}"
                                                                        class="btn btn-sm btn-danger"
                                                                        style="margin-bottom:0.5rem"
                                                                        {!! !$user->trashed() ? 'hidden' : '' !!}
                                                                    >
                                                                        {!! trans("basic.force_delete") !!}
                                                                    </button>

                                                                    <button
                                                                        name="user_action"
                                                                        data-action="delete"
                                                                        data-href="{!! route("userDelete", ["id" => $user->id]) !!}"
                                                                        class="btn btn-sm btn-danger"
                                                                        {!! $user->trashed() ? 'hidden' : '' !!}
                                                                    >
                                                                        {!! trans("basic.delete") !!}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td colspan="5">
                                                {!! Form::open(['url' => route('postInviteUser'), 'class' => 'ajax']) !!}
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <h4>@lang("ManageAccount.invite_new_user")</h4>
                                                        <span class="help-block">@lang("ManageAccount.add_user_help_block")</span>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            {!! Form::label('first_name', trans("ManageAccount.first_name"), ['class'=>'control-label']) !!}
                                                            {!! Form::text('first_name', old('first_name'), ['class'=>'form-control']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            {!! Form::label('last_name', trans("ManageAccount.last_name"), ['class'=>'control-label']) !!}
                                                            {!! Form::text('last_name', old('last_name'), ['class'=>'form-control']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            {!! Form::label('email', trans("ManageAccount.email"), ['class'=>'control-label required', 'placeholder' => trans("ManageAccount.email_address_placeholder")]) !!}
                                                            {!! Form::text('email', old('email'), ['class'=>'form-control']) !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            {!! Form::label('organiser', trans("ManageAccount.organiser"), ['class'=>'control-label required']) !!}
                                                            <?php
                                                            $organisers = $account->organisers->mapWithKeys(function($organiser) {
                                                                return [$organiser['id'] => $organiser['name']];
                                                            });
                                                            ?>
                                                            {!! Form::select('organiser', $organisers, '', ['class'=>'form-control required']); !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            {!! Form::label('role', trans("ManageAccount.role"), ['class'=>'control-label required']) !!}
                                                            <?php
                                                            $defaultSelected = 1;
                                                            $roles = $roles->mapWithKeys(function($role) use (&$defaultSelected) {
                                                                // Auto select the user role
                                                                if ($role->name === 'user') {
                                                                    $defaultSelected = $role->id;
                                                                }
                                                                return [$role['id'] => Str::title($role['name'])];
                                                            });
                                                            ?>
                                                            {!! Form::select('role', $roles, $defaultSelected, ['class'=>'form-control required']); !!}
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="input-group-btn">{!!Form::submit(trans("ManageAccount.add_user_submit"), ['class' => 'btn btn-primary'])!!}</span>
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane " id="about">
                                <h4>@lang("ManageAccount.version_info")</h4>
                                <p>
                                    @if(is_array($version_info) && $version_info['is_outdated'])
                                        @lang("ManageAccount.version_out_of_date", ["installed" => $version_info['installed'], "latest"=> $version_info['latest'], "url"=>"https://attendize.com/documentation.php#download"]).
                                    @elseif(is_array($version_info))
                                        @lang("ManageAccount.version_up_to_date", ["installed" => $version_info['installed']])
                                    @else
                                        Error retrieving the latest Attendize version.
                                    @endif
                                </p>
                                <h4>{!! @trans("ManageAccount.licence_info") !!}</h4>
                                <p>{!! @trans("ManageAccount.licence_info_description") !!}</p>
                                <h4>{!! @trans("ManageAccount.open_source_soft") !!} Open-source Software</h4>
                                <p>{!! @trans("ManageAccount.open_source_soft_description") !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

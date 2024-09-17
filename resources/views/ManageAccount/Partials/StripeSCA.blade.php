<section class="payment_gateway_options" id="gateway_{{$payment_gateway['id']}}">
    <h4>@lang("ManageAccount.stripe_settings")</h4>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('stripe_sca[apiKey]', trans("ManageAccount.stripe_secret_key"), array('class'=>'control-label ')) !!}
                {!! Form::text('stripe_sca[apiKey]', $account->getGatewayConfigVal($payment_gateway['id'], 'apiKey'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('publishableKey', trans("ManageAccount.stripe_publishable_key"), array('class'=>'control-label ')) !!}
                {!! Form::text('stripe_sca[publishableKey]', $account->getGatewayConfigVal($payment_gateway['id'], 'publishableKey'),[ 'class'=>'form-control'])  !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('stripe_sca[application_fee_amount]', trans("ManageAccount.stripe_connect_application_fee"), array('class'=>'control-label ')) !!}
                {!! Form::text('stripe_sca[application_fee_amount]', $account->getGatewayConfigVal($payment_gateway['id'], 'application_fee_amount'),[ 'class'=>'form-control']) !!}
            </div>
        </div>
        <div class="col-md-6">
            {!! Form::label('stripe_sca[transfer_data_destination_id]', trans("ManageAccount.stripe_connect_transfer_data_destination_id"), array('class'=>'control-label ')) !!}
            {!! Form::text('stripe_sca[transfer_data_destination_id]', $account->getGatewayConfigVal($payment_gateway['id'], 'transfer_data_destination_id'),[ 'class'=>'form-control']) !!}
        </div>
    </div>
</section>

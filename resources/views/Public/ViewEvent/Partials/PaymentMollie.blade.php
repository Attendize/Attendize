<form class="online_payment ajax" action="<?php echo route('postCreateOrder', ['event_id' => $event->id]); ?>" method="post">
    <div class="online_payment">
        @lang("Public_ViewEvent.mollie_redirect")

        {!! Form::token() !!}

        <input class="btn btn-lg btn-success card-submit" style="width:100%;" type="submit" value="@lang("Public_ViewEvent.complete_payment")">
    </div>
</form>

<script>
    window.Attendize = window.Attendize || {};
    
    @if(isset($js))
        {!! $js !!}
    @endif
    
    // Ensure essential properties are always available
    window.Attendize.DateTimeFormat = window.Attendize.DateTimeFormat || '{!! config('attendize.default_date_picker_format') !!}';
    window.Attendize.DateSeparator = window.Attendize.DateSeparator || '{!! config('attendize.default_date_picker_seperator') !!}';
    window.Attendize.GenericErrorMessage = window.Attendize.GenericErrorMessage || '{!! trans('Controllers.whoops') !!}';
    
    @if(Auth::check())
        window.Attendize.User = window.Attendize.User || {
            full_name: '{!! addslashes(Auth::user()->full_name) !!}',
            email: '{!! addslashes(Auth::user()->email) !!}',
            is_confirmed: {!! Auth::user()->is_confirmed ? 'true' : 'false' !!}
        };
    @else
        window.Attendize.User = window.Attendize.User || {
            full_name: '',
            email: '',
            is_confirmed: false
        };
    @endif
</script>
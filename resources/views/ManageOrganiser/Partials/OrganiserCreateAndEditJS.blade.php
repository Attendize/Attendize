{!! Html::script('vendor/simplemde/dist/simplemde.min.js') !!}
{!! Html::style('vendor/simplemde/dist/simplemde.min.css') !!}
<script>
$(document).ready(function(){
    var charge_tax = $("input[type=radio][name='charge_tax']:checked").val();
    if (charge_tax == 1) {
        $('#tax_fields').show();
    } else {
        $('#tax_fields').hide();
    }

    $('.editable').each(function() {
        var simplemde = new SimpleMDE({
            element: this,
            spellChecker: false,
            status: false
        });
        simplemde.render();
    })


    $('input[type=radio][name=charge_tax]').change(function() {
        if (this.value == 1) {
            $('#tax_fields').show();
        }
        else {
            $('#tax_fields').hide();
        }
    });
});
</script>
<style>
    .editor-toolbar {
        border-radius: 0;
    }
    .CodeMirror {
        height: 100px;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }
</style>


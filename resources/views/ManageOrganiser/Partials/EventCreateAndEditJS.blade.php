{!! Html::script('vendor/simplemde/dist/simplemde.min.js') !!}
{!! Html::style('vendor/simplemde/dist/simplemde.min.css') !!}

<script>
    $(function() {
        try {
            $(".geocomplete").geocomplete({
                    details: "form.gf",
                    types: ["geocode", "establishment"]
                }).bind("geocode:result", function(event, result) {
                    console.log(result);
            }, 1000);

        } catch (e) {
            console.log(e);
        }

        $('.editable').each(function() {
            var simplemde = new SimpleMDE({
                element: this,
                spellChecker: false,
                status: false
            });
            simplemde.render();
        })

        $("#DatePicker").remove();
        var $div = $("<div>", {id: "DatePicker"});
        $("body").append($div);
        $div.DateTimePicker({
            dateTimeFormat: window.Attendize.DateTimeFormat,
            dateSeparator: window.Attendize.DateSeparator
        });

    });
</script>
<style>
    .editor-toolbar {
        border-radius: 0;
    }
    .CodeMirror {
        min-height: 100px;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .create_organiser, .address-manual {
        padding: 10px;
        border: 1px solid #ddd;
        margin-top: 10px;
        margin-bottom: 10px;
        background-color: #FAFAFA;
    }

    .in-form-link {
        display: block; padding: 5px;margin-bottom: 5px;padding-left: 0;
    }
</style>

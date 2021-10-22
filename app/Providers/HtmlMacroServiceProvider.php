<?php

namespace App\Providers;

use Form;
use Html;
use Illuminate\Support\ServiceProvider;

class HtmlMacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->rawLabel();
        $this->labelWithHelp();
        $this->customCheckBox();
        $this->styledFile();
        $this->sortableLink();
    }

    private function rawLabel()
    {
        Form::macro('rawLabel', function ($name, $value = null, $options = []) {
            $label = Form::label($name, '%s', $options);

            return sprintf($label, $value);
        });
    }

    private function labelWithHelp()
    {
        Form::macro('labelWithHelp', function ($name, $value, $options, $help_text) {
            $label = Form::label($name, '%s', $options);

            return sprintf($label, $value)
                .'<a style="margin-left: 4px;font-size: 11px;" href="javascript:showHelp('."'".$help_text."'".');" >'
                .'<i class="ico ico-question "></i>'
                .'</a>';
        });
    }

    private function customCheckBox()
    {
        Form::macro('customCheckbox', function ($name, $value, $checked = false, $label = false, $options = []) {

//    $checkbox = Form::checkbox($name, $value = null, $checked, $options);
//    $label    = Form::rawLabel();
//
//    $out = '<div class="checkbox custom-checkbox">
//                                <input type="checkbox" name="send_copy" id="send_copy" value="1">
//                                <label for="send_copy">&nbsp;&nbsp;Send a copy to <b>{{$attendee->event->organiser->email}}</b></label>
//            </div>';
//
//    return $out;
        });
    }

    private function styledFile()
    {
        Form::macro('styledFile', function ($name, $multiple = false) {
            $out = '<div class="styledFile" id="input-'.$name.'">
        <div class="input-group">
            <span class="input-group-btn">
                <span class="btn btn-primary btn-file ">
                    '.trans('basic.browse').'&hellip; <input name="'.$name.'" type="file" '.($multiple ? 'multiple' : '').'>
                </span>
            </span>
            <input type="text" class="form-control" readonly>
            <span style="display: none;" class="input-group-btn btn-upload-file">
                <span class="btn btn-success ">
                    '.trans('basic.upload').'
                </span>
            </span>
        </div>
    </div>';

            return $out;
        });
    }

    private function sortableLink()
    {
        Html::macro('sortable_link',
            function ($title, $active_sort, $sort_by, $sort_order, $url_params = [], $class = '', $extra = '') {
                $sort_order = $sort_order == 'asc' ? 'desc' : 'asc';

                $url_params = http_build_query([
                        'sort_by'    => $sort_by,
                        'sort_order' => $sort_order,
                    ] + $url_params);

                $html = "<a href='?$url_params' class='col-sort $class' $extra>";

                $html .= ($active_sort == $sort_by) ? "<b>$title</b>" : $title;

                $html .= ($sort_order == 'desc') ? '<i class="ico-arrow-down22"></i>' : '<i class="ico-arrow-up22"></i>';

                $html .= '</a>';

                return $html;
            });
    }
}

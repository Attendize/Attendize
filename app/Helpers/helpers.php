<?php

if (!function_exists('money')) {
    /**
     * Format a given amount to the given currency
     *
     * @param $amount
     * @param \App\Models\Currency $currency
     * @return string
     */
    function money($amount, \App\Models\Currency $currency = null)
    {
        if(!$currency){
            return number_format($amount,2,'.',',').' manat';
        }
        return $currency->symbol_left . number_format($amount, $currency->decimal_place, $currency->decimal_point,
            $currency->thousand_point) . $currency->symbol_right;
    }
}
if(!function_exists('main_categories')){

    /**
     * return main categories
     * @return mixed
     */
    function main_categories(){
        return \App\Models\Category::main()->pluck(trans('Category.category_title'),'id');
    }
}

if(!function_exists('category_menu')){
    /**
     *  make menu from categories
     */
    function category_menu(){

        return \App\Models\Category::main()->select('id','title_tm','title_ru')->get();
//        $categories = main_categories();
//        if(count($categories)>6){
//            //todo implement top category menu
//        }
    }
}

if(! function_exists('sub_categories')){
    /**
     * return sub categoreies
     */

    function sub_categories(){
        return \App\Models\Category::sub()
            ->select(trans('Category.category_title'),'id','parent_id')
            ->get();
    }
}
if(!function_exists('organisers')){

    function organisers(){
        if(Illuminate\Support\Facades\Auth::user()->is_admin)
            return \App\Models\Organiser::all();
        else
            return \Illuminate\Support\Facades\Auth::user()->account->organisers;
    }
}
if ( ! function_exists('sanitise')) {
    /**
     * @param string $input
     * @return string
     */
    function sanitise($input)
    {
        $clear = clean($input); // Package to remove code "mews/purifier"
        $clear = strip_tags($clear);
        $clear = html_entity_decode($clear);
        $clear = urldecode($clear);
        $clear = preg_replace('~[\r\n\t]+~', ' ', trim($clear));
        $clear = preg_replace('/ +/', ' ', $clear);
        return $clear;
    }

    /**
     * @param string $input
     * @return string
     */
    function clean_whitespace($input)
    {
        $clear = preg_replace('~[\r\n\t]+~', ' ', trim($input));
        $clear = preg_replace('/ +/', ' ', $clear);
        return $clear;
    }
}
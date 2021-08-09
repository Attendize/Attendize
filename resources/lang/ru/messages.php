<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute должен быть принят.',
    'active_url'           => ':attribute содержит некорректный URL.',
    'after'                => ':attribute должен быть датой после :date.',
    'alpha'                => ':attribute может содержать только буквы.',
    'alpha_dash'           => ':attribute может содержать только буквы, цифры и символ подчеркивания.',
    'alpha_num'            => ':attribute может содержать только буквы и цифры.',
    'array'                => ':attribute должен быть массивом',
    'before'               => ':attribute должен быть датой до :date.',
    'between'              => [
        'numeric' => ':attribute должен быть между :min и :max.',
        'file'    => ':attribute должен быть между :min и :max килобайт.',
        'string'  => ':attribute должен быть между :min и :max символов.',
        'array'   => ':attribute должен быть между :min и :max элементов.',
    ],
    'boolean'              => ':attribute должен быть в значении true или false.',
    'confirmed'            => ':attribute не совпадает.',
    'date'                 => ':attribute некорректная дата.',
    'date_format'          => ':attribute должен быть отформатирован как :format.',
    'different'            => ':attribute и :other должны различаться.',
    'digits'               => ':attribute должны быть :digits цифр.',
    'digits_between'       => ':attribute должен быть между :min and :max цифрами.',
    'email'                => ':attribute должен быть правильным email адресом.',
    'filled'               => ':attribute поле обязательно.',
    'exists'               => 'выбранное значение :attribute неверно.',
    'image'                => ':attribute должен быть изображением.',
    'in'                   => 'Выбранный атрибут :attribute неверен.',
    'integer'              => ':attribute должен быть целым числом.',
    'ip'                   => ':attribute должен быть правильным IP адресом.',
    'max'                  => [
        'numeric' => ':attribute не может быть больше чем :max.',
        'file'    => ':attribute не может быть больше чем :max килобайт.',
        'string'  => ':attribute не может быть больше чем :max символов.',
        'array'   => ':attribute не может быть больше чем :max элементов.',
    ],
    'mimes'                => ':attribute должен быть файлом типа: :values.',
    'min'                  => [
        'numeric' => ':attribute must be at least :min.',
        'file'    => ':attribute must be at least :min kilobytes.',
        'string'  => ':attribute must be at least :min characters.',
        'array'   => ':attribute must have at least :min items.',
    ],
    'not_in'               => 'выбранное значение :attribute неверно.',
    'numeric'              => ':attribute должен быть числом.',
    'regex'                => ':attribute формат неверен.',
    'required'             => ':attribute поле обязательно.',
    'required_if'          => ':attribute поле обязательно, когда :other содержит :value.',
    'required_with'        => ':attribute поле обязательно, когда :values заполнено.',
    'required_with_all'    => ':attribute поле обязательно, когда :values заполнено.',
    'required_without'     => ':attribute поле обязательно, когда :values не заполнено.',
    'required_without_all' => ':attribute поле обязательно, когда ни одно из :values не заполнены.',
    'same'                 => ':attribute и :other должны совпадать.',
    'size'                 => [
        'numeric' => ':attribute должен быть размера :size.',
        'file'    => ':attribute должен быть :size килобайт.',
        'string'  => ':attribute must be :size символов.',
        'array'   => ':attribute должен содержать :size элементов.',
    ],
    'unique'               => ':attribute уже был введен ранее.',
    'url'                  => ':attribute неверный формат.',
    'timezone'             => ':attribute должен содержать корректную временную зону.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'terms_agreed' => [
            'required' => 'Пожалуйста, согласитесь с нашими Условиями предоставления услуг.'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];

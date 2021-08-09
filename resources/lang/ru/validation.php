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
    'after_or_equal'       => ':attribute должен быть датой после или равной :date.',
    'alpha'                => ':attribute может содержать только буквы.',
    'alpha_dash'           => ':attribute может содержать только буквы, цифры и символ подчеркивания.',
    'alpha_num'            => ':attribute может содержать только буквы и цифры.',
    'array'                => ':attribute должен быть массивом',
    'before'               => ':attribute должен быть датой до :date.',
    'before_or_equal'      => ':attribute должен быть датой до или равной :date.',
    'between'              => [
        'numeric' => ':attribute должен быть между :min и :max.',
        'file'    => ':attribute должен быть между :min и :max килобайт.',
        'string'  => ':attribute должен быть между :min и :max символов.',
        'array'   => ':attribute должен быть между :min и :max элементов.',
    ],
    'boolean'              => ':attribute должен быть в значении true или false.',
    'confirmed'            => ':attribute не совпадает.',
    'date'                 => ':attribute некорректная дата.',
    'date_equals'          => 'The :attribute должен быть датой, равной :date.',
    'date_format'          => ':attribute неверный формат даны, нужен :format.',
    'different'            => ':attribute и :other должны различаться.',
    'digits'               => ':attribute должны быть :digits цифр.',
    'digits_between'       => ':attribute должен быть между :min and :max цифрами.',
    'dimensions'           => ':attribute неправильный размер изображения.',
    'distinct'             => ':attribute поле имеет дублирующее значение.',
    'email'                => ':attribute должен быть правильным email адресом.',
    'ends_with'            => ':attribute должен заканчиваться следующим: :values',
    'exists'               => 'выбранное значение :attribute неверно.',
    'file'                 => ':attribute должен быть файлом.',
    'filled'               => ':attribute поле обязательно.',
    'gt'                   => [
        'numeric' => ':attribute должен быть больше чем :value.',
        'file'    => ':attribute должен быть больше чем :value килобайт.',
        'string'  => ':attribute должен быть больше чем :value символов.',
        'array'   => ':attribute должен быть больше чем :value элементов.',
    ],
    'gte'                  => [
        'numeric' => 'The :attribute должен быть больше или равно :value.',
        'file'    => 'The :attribute должен быть больше или равно :value килобайт.',
        'string'  => 'The :attribute должен быть больше или равно :value символов.',
        'array'   => 'The :attribute должен быть :value элементов или больше.',
    ],
    'image'                => ':attribute должен быть изображением.',
    'in'                   => 'выбранный :attribute неверен.',
    'in_array'             => ':attribute поле не существует в :other.',
    'integer'              => ':attribute должен быть целым числом.',
    'ip'                   => ':attribute должен быть корректным IP адресом.',
    'ipv4'                 => ':attribute должен быть корректным IPv4 адресом.',
    'ipv6'                 => ':attribute должен быть корректным IPv6 адресом.',
    'json'                 => ':attribute должен быть корректной JSON строкой',
    'lt'                   => [
        'numeric' => ':attribute должен быть меньше чем :value.',
        'file'    => ':attribute должен быть меньше чем :value килобайт.',
        'string'  => ':attribute должен быть меньше чем :value символов.',
        'array'   => ':attribute  должен быть меньше чем :value элементов.',
    ],
    'lte'                  => [
        'numeric' => 'The :attribute должен быть меньше или равен :value.',
        'file'    => 'The :attribute должен быть меньше или равен :value килобайт.',
        'string'  => 'The :attribute должен быть меньше или равен :value символов.',
        'array'   => 'The :attribute должен быть меньше или равен :value элементов.',
    ],
    'max'                  => [
        'numeric' => ':attribute не может быть больше чем :max.',
        'file'    => ':attribute не может быть больше чем :max килобайт.',
        'string'  => ':attribute не может быть больше чем :max символов.',
        'array'   => ':attribute не может быть больше чем :max элементов.',
    ],
    'mimes'                => 'The :attribute должен быть файлом типа: :values.',
    'mimetypes'            => 'The :attribute должен быть файлом типа: :values.',
    'min'                  => [
        'numeric' => ':attribute должен быть как минимум :min.',
        'file'    => ':attribute должен быть как минимум :min килобайт.',
        'string'  => ':attribute должен быть как минимум :min символов.',
        'array'   => ':attribute должен быть как минимум :min элементов.',
    ],
    'not_in'               => 'Выбранное значение :attribute неверно.',
    'not_regex'            => 'Формат :attribute неверен.',
    'numeric'              => ':attribute должен быть числом.',
    'password'             => 'Пароль неправильный.',
    'present'              => ':attribute поле должно быть заполнено.',
    'regex'                => ':attribute формат неверен.',
    'required'             => ':attribute обязателен.',
    'required_if'          => ':attribute поле обязательно если :other содержит :value.',
    'required_unless'      => ':attribute поле обязательно если :other не содержит :values.',
    'required_with'        => ':attribute поле обязательно, когда :values введено.',
    'required_with_all'    => ':attribute поле обязательно, когда :values введены.',
    'required_without'     => ':attribute поле обязательно, когда :values не введены.',
    'required_without_all' => ':attribute поле обязательно, когда ни одно из :values не введены.',
    'same'                 => ':attribute и :other должны совпадать.',
    'size'                 => [
        'numeric' => ':attribute должен иметь размер :size.',
        'file'    => ':attribute должен иметь размер :size килобайт.',
        'string'  => ':attribute должен иметь размер :size символов.',
        'array'   => ':attribute должен содержать :size элементов.',
    ],
    'starts_with'          => ':attribute должен начинаться одним из следующего: :values',
    'string'               => ':attribute должен быть строкой.',
    'timezone'             => ':attribute должен быть корректной временной зоной.',
    'unique'               => ':attribute неуникален.',
    'uploaded'             => ':attribute ошибка закачки.',
    'url'                  => ':attribute форман неправильный.',
    'uuid'                 => ':attribute формат UUID неправильный.',

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
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];

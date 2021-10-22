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

    'accepted'             => ':attribute doit être accepté.',
    'active_url'           => ':attribute n\'est pas une URL valide.',
    'after'                => ':attribute doit être une date après :date.',
    'alpha'                => ':attribute ne doit contenir que des lettres.',
    'alpha_dash'           => ':attribute ne peut contenir que des lettres, des chiffres et des tirets.',
    'alpha_num'            => ':attribute ne peut contenir que des lettres et des chiffres.',
    'array'                => ':attribute doit être un tableau.',
    'before'               => ':attribute doit être une date antérieure à :date.',
    'between'              => [
        'numeric' => ':attribute doit être entre :min et :max.',
        'file'    => ':attribute doit être entre :min et :max kilobytes.',
        'string'  => ':attribute doit être entre :min et :max caractères.',
        'array'   => ':attribute doit avoir entre :min et :max éléments.',
    ],
    'boolean'              => 'Le champ :attribute doit être vrai ou faux.',
    'confirmed'            => 'La confirmation :attribute ne convient pas.',
    'date'                 => ':attribute n\'est pas une date valide.',
    'date_format'          => ':attribute ne correspond pas au format :format.',
    'different'            => ':attribute et :other ne doivent pas être identiques.',
    'digits'               => ':attribute doit contenir :digits chiffres.',
    'digits_between'       => ':attribute doit compter entre :min et :max chiffres.',
    'email'                => ':attribute doit être une adresse de courriel valide.',
    'filled'               => 'Le champ :attribute est requis.',
    'exists'               => 'Le choix :attribute est invalide.',
    'image'                => ':attribute doit être une image.',
    'in'                   => 'Le choix :attribute est invalide.',
    'integer'              => ':attribute doit être un nombre entier.',
    'ip'                   => ':attribute doit être une adresse IP valide.',
    'max'                  => [
        'numeric' => ':attribute ne doit pas être plus grand que :max.',
        'file'    => ':attribute ne doit pas être plus grand que :max kilobytes.',
        'string'  => ':attribute ne doit pas contenir plus de :max caractères.',
        'array'   => ':attribute ne doit pas avoir plus de :max éléments.',
    ],
    'mimes'                => ':attribute doit être du type : :values.',
    'min'                  => [
        'numeric' => ':attribute doit être au moins :min.',
        'file'    => ':attribute doit faire au moins :min kilobytes.',
        'string'  => ':attribute doit contenir au moins :min caractères.',
        'array'   => ':attribute doit contenir au moins least :min éléments.',
    ],
    'not_in'               => 'Le choix :attribute est invalide.',
    'numeric'              => ':attribute doit être un nombre.',
    'regex'                => 'Le format de :attribute n\'est pas valide.',
    'required'             => 'Le champ :attribute est requis.',
    'required_if'          => 'Le champ :attribute est requis quand :other est :value.',
    'required_with'        => 'Le champ :attribute est requis quand :values est présent.',
    'required_with_all'    => 'Le champ :attribute est requis quand :values est présent.',
    'required_without'     => 'Le champ :attribute est requis quand :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est requis quand aucun :values n\'est présent.',
    'same'                 => 'Le champ :attribute et :other doivent correspondre.',
    'size'                 => [
        'numeric' => ':attribute doit faire :size.',
        'file'    => ':attribute doit faire :size kilobytes.',
        'string'  => ':attribute doit faire :size caractères.',
        'array'   => ':attribute doit contenir :size éléments.',
    ],
    'unique'               => ':attribute a déjà été pris.',
    'url'                  => 'Le format de :attribute n\'est pas valide.',
    'timezone'             => ':attribute doit être un fuseau valide.',

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
            'required' => 'Merci d\'accepter nos conditions générales d\'utilisation.',
        ],
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

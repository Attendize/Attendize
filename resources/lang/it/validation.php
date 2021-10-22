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

    'accepted' => ':attribute deve essere accettata .',
    'active_url' => ':attribute non è una URL valido.',
    'after' => ':attribute deve essere una data dopo :date.',
    'alpha' => ':attribute può contenere solo lettere.',
    'alpha_dash' => ':attribute può contenere solo lettere, numeri e trattini.',
    'alpha_num' => ':attribute può contenere solo lettere e numeri.',
    'array' => ':attribute deve essere una matrice.',
    'before' => ':attribute deve essere una data anteriore :date.',
    'between' => [
        'numeric' => ':attribute deve essere compreso tra :min e :max.',
        'file' => ':attribute deve essere compreso tra :min e :max kilobyte.',
        'string' => ':attribute deve essere compreso tra :min e :max caratteri.',
        'array' => ':attribute deve avere tra :min e :max oggetti.',
    ],
    'boolean' => ':attribute attributo deve essere vero o falso.',
    'confirmed' => ':attribute attributo non corrisponde.',
    'date' => ':attribute non è una data valida.',
    'date_format' => ':attribute non corrisponde al formato :format.',
    'different' => ':attribute e :other devono essere differenti.',
    'digits' => ':attribute deve essere di :digits cifre.',
    'digits_between' => ':attribute deve essere compreso tra :min e :max cifre.',
    'email' => ':attribute deve essere un indirizzo email valido.',
    'filled' => ':attribute è obbligatorio.',
    'exists' => ':attribute selezionato non è valido.',
    'image' => ':attribute deve essere un\'immagine.',
    'in' => ':attribute selezionato non è valido.',
    'integer' => ':attribute deve essere un numero intero.',
    'ip' => ':attribute deve essere un indirizzo IP valido.',
    'max' => [
        'numeric' => ':attribute può non essere superiore a :max.',
        'file' => ':attribute può non essere superiore a :max kilobyte.',
        'string' => ':attribute può non essere superiore a :max caratteri.',
        'array' => ':attribute non può avere più di :max oggetti.',
    ],
    'mimes' => ':attribute deve essere un file di tipo :values',
    'min' => [
        'numeric' => ':attribute deve essere almeno :min.',
        'file' => ':attribute deve essere almeno :min kilobyte.',
        'string' => ':attribute deve essere almeno :min caratteri.',
        'array' => ':attribute deve avere almeno :min oggetti.',
    ],
    'not_in' => ':attribute selezionato non è valido.',
    'numeric' => ':attribute deve essere un numero.',
    'regex' => 'Il formato :format dell\'attributo non è valido.',
    'required' => ':attribute è obbligatorio.',
    'required_if' => ':attribute attributo è richiesto quando :other è :value.',
    'required_with' => ':attribute attributo è richiesto quando :values è presente.',
    'required_with_all' => ':attribute attributo è richiesto quando :values è presente.',
    'required_without' => ':attribute attributo è richiesto quando :values non è presente.',
    'required_without_all' => ':attribute attributo è richiesto quando nessuno di: valori è presente.',
    'same' => ':attribute e :other devono combaciare.',
    'size' => [
        'numeric' => ':attribute deve essere :size.',
        'file' => ':attribute deve essere :size kilobyte di dimensione.',
        'string' => ':attribute deve essere :size caratteri di dimensioni.',
        'array' => ':attribute deve contenere :items oggetti.',
    ],
    'unique' => ':attribute è già stato preso.',
    'url' => 'Il formato di :attribute non è valido.',
    'timezone' => ':attribute deve essere un orario valido.',

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
            'required' => 'Per favore accetta i nostri Termini e Condizioni d\'uso.',
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

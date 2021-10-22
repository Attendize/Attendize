<?php
/****************************************************************************************
*    Title: Validation Language Lines (Dutch)
*    Author: Johnhout
*    Date: Feb 3, 2015
*    Availability: https://gist.github.com/johnhout/055d2c039cfb7574d657
*
***************************************************************************************/

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

'accepted'             => ':attribute dient te worden geaccepteerd.',
    'active_url'           => ':attribute is geen geldige URL.',
    'after'                => ':attribute dient een datum te zijn na :date.',
    'alpha'                => ':attribute mag alleen letters bevatten.',
    'alpha_dash'           => ':attribute mag alleen letters, nummers, and strepen bevatten.',
    'alpha_num'            => ':attribute mag alleen letters en nummers bevatten.',
    'array'                => ':attribute moet een array zijn.',
    'before'               => ':attribute moet een datum zijn eerder dan :date.',
    'between'              => [
        'numeric' => ':attribute moet tussen :min en :max liggen.',
        'file'    => ':attribute moet tussen :min en :max kilobytes zijn.',
        'string'  => ':attribute moet tussen :min en :max karakters lang zijn.',
        'array'   => ':attribute moet tussen :min en :max items bevatten.',
    ],
    'boolean'              => ':attribute kan enkel true of false zijn.',
    'confirmed'            => ':attribute bevestiging komt niet overeen.',
    'date'                 => ':attribute is geen geldige datum.',
    'date_format'          => ':attribute komt niet overeen met het formaat :format.',
    'different'            => ':attribute en :other dienen verschillend te zijn.',
    'digits'               => ':attribute moet :digits cijfers zijn.',
    'digits_between'       => ':attribute moet tussen :min en :max cijfers zijn.',
    'email'                => ':attribute dient een geldig emailadres te zijn.',
    'filled'               => ':attribute veld is verplicht.',
    'exists'               => 'Het geselecteerde :attribute is ongeldig.',
    'image'                => ':attribute dient een afbeelding te zijn.',
    'in'                   => 'Het geselecteerde :attribute is ongeldig.',
    'integer'              => ':attribute dient een geheel getal te zijn.',
    'ip'                   => ':attribute dient een geldig IP adres te zijn.',
    'max'                  => [
        'numeric' => ':attribute mag niet groter zijn dan :max.',
        'file'    => ':attribute mag niet groter zijn dan :max kilobytes.',
        'string'  => ':attribute mag niet groter zijn dan :max karakters.',
        'array'   => ':attribute mag niet meer dan :max items bevatten.',
    ],
    'mimes'                => ':attribute dient een bestand te zijn van het type: :values.',
    'min'                  => [
        'numeric' => ':attribute dient minimaal :min te zijn.',
        'file'    => ':attribute dient minimaal :min kilobytes te zijn.',
        'string'  => ':attribute dient minimaal :min karakters te bevatten.',
        'array'   => ':attribute dient minimaal :min items te bevatten.',
    ],
    'not_in'               => 'Het geselecteerde :attribute is ongeldig.',
    'numeric'              => 'Het :attribute dient een nummer te zijn.',
    'regex'                => 'Het :attribute formaat is ongeldig.',
    'required'             => 'Het :attribute veld is verplicht.',
    'required_if'          => 'Het :attribute veld is verplicht wanneer :other is :value.',
    'required_with'        => 'Het :attribute veld is verplicht wanneer :values aanwezig is.',
    'required_with_all'    => 'Het :attribute veld is verplicht wanneer :values aanwezig is.',
    'required_without'     => 'Het :attribute veld is verplicht wanneer :values niet aanwezig is.',
    'required_without_all' => 'Het :attribute veld is verplicht wanneer geen van :values aanwezig is.',
    'same'                 => 'Het :attribute en :other moeten hetzelfde zijn.',
    'size'                 => [
        'numeric' => ':attribute moet :size zijn.',
        'file'    => ':attribute moet :size kilobytes groot zijn.',
        'string'  => ':attribute moet :size karakters lang zijn.',
        'array'   => ':attribute moet :size items bevatten.',
    ],
    'unique'               => ':attribute is al bezet.',
    'url'                  => ':attribute formaat is ongeldig.',
    'timezone'             => ':attribute moet een geldige tijdszone zijn.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention 'attribute.rule' to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'terms_agreed' => [
            'required' => 'Accepteren van de algemene voorwaarde verplicht.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of 'email'. This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];

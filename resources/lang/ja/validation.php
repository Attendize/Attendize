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

    'accepted'             => ':attributeは受け入れられなければなりません。',
    'active_url'           => ':attributeは有効なURLではありません。',
    'after'                => ':attributeは:dateより後の日付でなければなりません。',
    'alpha'                => ':attributeは文字のみを含むことができます。',
    'alpha_dash'           => ':attributeには文字、数字、およびハイフンだけを含めることができます。',
    'alpha_num'            => ':attributeは文字と数字だけを含むことができます。',
    'array'                => ':attributeは配列でなければなりません。',
    'before'               => ':attributeは:dateより前の日付でなければなりません。',
    'between'              => [
        'numeric' => ':attributeは:minと:maxの間になければなりません。',
        'file'    => ':attributeは:minから:maxキロバイトまででなければなりません。',
        'string'  => ':attributeは:min文字と:max文字の間になければなりません。',
        'array'   => ':attributeは:minから:maxまでの間になければなりません。',
    ],
    'boolean'              => ':attributeフィールドはtrueまたはfalseでなければなりません。',
    'confirmed'            => ':attributeの確認が一致しません。',
    'date'                 => ':attributeは有効な日付ではありません。',
    'date_format'          => ':attributeがフォーマット:formatと一致しません。',
    'different'            => ':attributeと:otherは異なる必要があります。',
    'digits'               => ':attributeは数字でなければなりません。',
    'digits_between'       => ':attributeは:minと:maxの間になければなりません。',
    'email'                => ':attributeは有効なメールアドレスでなければなりません。',
    'filled'               => ':attributeフィールドは必須です。',
    'exists'               => '選択した:attributeが無効です。',
    'image'                => ':attributeは画像でなければなりません。',
    'in'                   => '選択した:attributeが無効です。',
    'integer'              => ':attributeは整数でなければなりません。',
    'ip'                   => ':attributeは有効なIPアドレスでなければなりません。',
    'max'                  => [
        'numeric' => ':attributeは:maxより大きくてはいけません',
        'file'    => ':attributeは:maxキロバイトより大きくてはいけません。',
        'string'  => ':attributeは:max文字より大きくてはいけません。',
        'array'   => ':attributeは:max個以上の項目を持つことはできません。',
    ],
    'mimes'                => ':attributeは:values.型のファイルでなければなりません。',
    'min'                  => [
        'numeric' => ':attributeは少なくとも:minでなければなりません。',
        'file'    => ':attributeは少なくとも:minキロバイトでなければなりません。',
        'string'  => ':attributeは少なくとも:min文字でなければなりません。',
        'array'   => ':attributeには少なくとも:min個の項目が必要です。',
    ],
    'not_in'               => 'selected:attributeが無効です。',
    'numeric'              => ':attributeは数字でなければなりません。',
    'regex'                => ':attributeのフォーマットが無効です。',
    'required'             => ':attributeフィールドは必須です。',
    'required_if'          => ':otherフィールドが:valueの場合、:attributeフィールドは必須です。',
    'required_with'        => ':valuesが存在する場合、:attributeフィールドは必須です。',
    'required_with_all'    => ':valuesが存在する場合、:attributeフィールドは必須です。',
    'required_without'     => ':valuesが存在しない場合は:attributeフィールドが必須です。',
    'required_without_all' => ':attributeフィールドは:valuesがない場合は必須です。',
    'same'                 => ':attributeと:otherは一致しなければなりません。',
    'size'                 => [
        'numeric' => ':attributeは:sizeでなければなりません。',
        'file'    => ':attributeは:sizeキロバイトなければなりません。',
        'string'  => ':attributeは:size文字でなければなりません。',
        'array'   => ':attributeは:sizeつの項目を含まなければなりません。',
    ],
    'unique'               => ':attributeは既に使われています。',
    'url'                  => ':attributeの形式が無効です。',
    'timezone'             => ':attributeは有効なゾーンでなければなりません。',

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
            'required' => '利用規約に同意してください。',
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

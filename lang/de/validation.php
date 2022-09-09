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

    'accepted' => ':attribute muss akzeptiert werden.',
    'accepted_if' => 'attribute muss akzeptiert werden, falls :other :value ist.',
    'active_url' => ':attribute ist keine korrekte URL.',
    'after' => ':attribute muss ein Datum nach dem :date sein.',
    'after_or_equal' => ':attribute muss ein Datum nach oder am :date sein.',
    'alpha' => ':attribute darf nur Buchstaben enthalten.',
    'alpha_dash' => ':attribute darf nur Buchstaben, Zahlen, Binde- und Unterstriche enthalten.',
    'alpha_num' => ':attribute darf nur Buchstaben und Zahlen enthalten.',
    'array' => ':attribute muss eine Liste sein.',
    'before' => ':attribute muss ein Datum vor dem :date sein.',
    'before_or_equal' => ':attribute muss ein Datum vor dem oder am :date sein.',
    'between' => [
        'array' => ':attribute muss zwischen :min und :max Einträge haben.',
        'file' => ':attribute muss zwischen :min und :max Kilobytes gross sein.',
        'numeric' => ':attribute muss zwischen :min und :max liegen.',
        'string' => ':attribute darf :min bis :max Zeichen haben.',
    ],
    'boolean' => ':attribute muss wahr oder falsch sein.',
    'confirmed' => 'Die :attribute Bestätigung stimmt nicht überein.',
    'current_password' => 'Das Passwort ist falsch.',
    'date' => ':attribute ist kein gültiges Datum.',
    'date_equals' => ':attribute muss gleich :date sein.',
    'date_format' => ':attribute entspricht nicht dem Format :format.',
    'declined' => ':attribute muss ablehnend sein.',
    'declined_if' => ':attribute muss ablehnend sein, falls :other :value ist.',
    'different' => ':attribute und :other müssen unterschiedlich sein.',
    'digits' => ':attribute muss :digits Zeichen haben.',
    'digits_between' => ':attribute muss zwischen :min und :max Zeichen lang sein.',
    'dimensions' => ':attribute hat falsches Bild Format.',
    'distinct' => ':attribute, den Wert gibt es schon.',
    'doesnt_end_with' => ':attribute darf nicht mit :values enden.',
    'doesnt_start_with' => ':attribute darf nicht mit :values beginnen.',
    'email' => ':attribute ist keine gültige Email Adresse.',
    'ends_with' => ':attribute muss mit :values enden.',
    'enum' => 'Das ausgewählte :attribute ist ungültig.',
    'exists' => 'Das ausgewählte :attribute ist ungültig.',
    'file' => ':attribute muss eine Datei sein.',
    'filled' => ':attribute darf nicht leer bleiben.',
    'gt' => [
        'array' => ':attribute muss mehr als :value Einträge haben.',
        'file' => ':attribute muss größer als :value Kilobytes sein.',
        'numeric' => ':attribute muss größer als :value sein.',
        'string' => ':attribute muss länger als :value Zeichen sein.',
    ],
    'gte' => [
        'array' => ':attribute muss mindestens :value Einträge haben.',
        'file' => ':attribute muss mindestens :value Kilobytes groß sein.',
        'numeric' => ':attribute muss mindestens :value sein.',
        'string' => ':attribute muss mindestens :value Zeichen lang sein.',
    ],
    'image' => ':attribute muss ein Bild sein.',
    'in' => 'Ausgewähltes :attribute ist ungültig.',
    'in_array' => ':attribute gibt es nicht in :other.',
    'integer' => ':attribute muss eine Ganzzahl sein.',
    'ip' => ':attribute muss eine gültige IP-Adresse sein.',
    'ipv4' => ':attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6' => ':attribute muss eine gültige IPv6-Adresse sein.',
    'json' => ':attribute muss ein gültiger JSON-String sein.',
    'lt' => [
        'array' => ':attribute muss weniger als :value Einträge haben.',
        'file' => ':attribute muss kleiner als :value Kilobytes groß sein.',
        'numeric' => ':attribute muss kleiner als :value sein.',
        'string' => 'attribute muss weniger als :value Zeichen lang sein.',
    ],
    'lte' => [
        'array' => ':attribute darf nicht mehr als :value Einträge haben.',
        'file' => ':attribute darf nicht größer als :value Kilobytes groß sein.',
        'numeric' => ':attribute darf nicht größer als :value sein.',
        'string' => 'The :attribute must be less than or equal to :value characters.',
    ],
    'mac_address' => ':attribute muss eine gültige MAC-Adresse sein.',
    'max' => [
        'array' => ':attribute darf nicht mehr als :max Einträge haben.',
        'file' => ':attribute darf nicht größer als :max Kilobytes sein.',
        'numeric' => ':attribute darf nicht größer als :max sein.',
        'string' => ':attribute darf höchstens :max Zeichen lang sein.',
    ],
    'mimes' => ':attribute muss eine Datei mit dem Typ :values sein.',
    'mimetypes' => ':attribute muss eine Datei mit dem Typ :values sein.',
    'min' => [
        'array' => ':attribute muss mindestens :min Einträge haben.',
        'file' => ':attribute muss mindestens :min Kilobytes groß sein.',
        'numeric' => ':attribute muss mindestens :min sein.',
        'string' => ':attribute muss mindestens :min Zeichen lang sein.',
    ],
    'multiple_of' => ':attribute muss ein vielfaches von :value sein.',
    'not_in' => 'Das ausgewählte :attribute ist ungültig.',
    'not_regex' => ':attribute Format ist ungültig.',
    'numeric' => ':attribute muss eine Zahl sein.',
    'password' => [
        'letters' => ':attribute muss mindestens einen Buchstaben enthalten.',
        'mixed' => ':attribute muss mindestens ein klein- und ein großgeschriebenen Buchstaben enthalten.',
        'numbers' => ':attribute muss mindestens eine Ziffer enthalten.',
        'symbols' => ':attribute muss mindestens ein Sonderzeichen enthalten.',
        'uncompromised' => ':attribute wurde in einem Datenleck gefunden. Bitte wählen Sie ein anderes :attribute.',
    ],
    'present' => ':attribute muss vorhanden sein.',
    'prohibited' => ':attribute ist verboten.',
    'prohibited_if' => ':attribute ist verboten, falls :other :value ist.',
    'prohibited_unless' => ':attribute ist verboten es sei denn :other hat einen der Werte :values.',
    'prohibits' => ':attribute untersagt das Vorhandensein von :other.',
    'regex' => ':attribute Format ist ungültig.',
    'required' => ':attribute ist erforderlich.',
    'required_array_keys' => ':attribute muss Einträge für: :values enthalten.',
    'required_if' => ':attribute ist erforderlich, falls :other :value ist.',
    'required_unless' => ':attribute ist erforderlich, falls :other nicht in :values ist.',
    'required_with' => ':attribute ist erfordlich, falls :values vorhanden ist.',
    'required_with_all' => ':attribute ist erforderlich, falls :values vorhanden sind.',
    'required_without' => ':attribute ist erforderlich, falls :values nicht vorhanden ist.',
    'required_without_all' => ':attribute ist erforderlich, falls keines von :values vorhanden ist.',
    'same' => ':attribute und :other müssen übereinstimmen.',
    'size' => [
        'array' => ':attribute muss :size Einträge haben.',
        'file' => ':attribute muss :size Kilobytes groß sein.',
        'numeric' => ':attribute muss :size sein.',
        'string' => ':attribute muss :size Zeichen lang sein.',
    ],
    'starts_with' => ':attribute muss mit einem der folgenden beginnen: :values.',
    'string' => ':attribute muss eine Zeichenkette sein.',
    'timezone' => ':attribute muss eine gültige Zeitzone sein.',
    'unique' => ':attribute wird schon verwendet.',
    'uploaded' => 'Das hochladen von :attribute schlug fehl.',
    'url' => ':attribute muss eine gültige URL seine.',
    'uuid' => ':attribute muss eine gültige UUID sein.',

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
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
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

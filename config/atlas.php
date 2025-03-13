<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | If you want to give a custom name to the database tables, you must create
    | environment variables with the following names and overwrite the desired
    | table name
    |--------------------------------------------------------------------------
    */
    'countries_tablename'              => env('ATLAS_COUNTRIES', 'countries'),
    'states_tablename'                 => env('ATLAS_STATES', 'states'),
    'cities_tablename'                 => env('ATLAS_CITIES', 'cities'),
    'currencies_tablename'             => env('ATLAS_CURRENCIES', 'currencies'),
    'languages_tablename'              => env('ATLAS_LANGUAGES', 'languages'),
    'timezones_tablename'              => env('ATLAS_TIMEZONES', 'timezones'),
    'country_timezone_pivot_tablename' => env('ATLAS_COUNTRY_TIMEZONE', 'country_timezone'),

    /*
    |--------------------------------------------------------------------------
    | Enable or disable the entities you want to have in your database.
    |
    | Notice: the cities depend of the states
    |--------------------------------------------------------------------------
    */
    'entities'                         => [
        'states'     => true,
        'cities'     => true,
        'timezones'  => true,
        'currencies' => true,
        'languages'  => true,
    ],
];

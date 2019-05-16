<?php

return [
    'locales'   => [
        'en' => null,
        'ar' => null,
    ],

    'source'    => null,

    'slugField' => 'slug',

    'route_binding' => true,

    /**
     * Generate a new slug from the 'source' attribute on creating a new model.
     *
     */
    'onCreate'  => true,

    /**
     * Generate a new slug from the 'source' attribute on updating an exists model.
     */
    'onUpdate'  => false,

    'slugger'   => [
        'unique'     => true,
        'separator'  => '-',
        'lowercase'  => true,
        'regex'      => null,
    ],
];

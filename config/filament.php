<?php

return [
    'default_filesystem_disk' => 'public',

    'layout' => [
        'sidebar' => [
            'is_collapsible_on_desktop' => true,
        ],
        'footer' => [
            'should_show_logo' => false,
        ],
    ],

    'theme' => [
        'colors' => [
            'primary' => [
                50 => '238, 242, 255',
                100 => '224, 231, 255',
                200 => '199, 210, 254',
                300 => '165, 180, 252',
                400 => '129, 140, 248',
                500 => '#001C4D',
                600 => '#001233',
                700 => '#001233',
                800 => '#001233',
                900 => '#001233',
                950 => '#001233',
            ],
        ],
    ],

    'auth' => [
        'session' => [
            'expiry' => null,
        ],
    ],

    'profile' => [
        'auth' => [
            'logout' => [
                'label' => 'Cerrar Sesión',
            ],
        ],
    ],
]; 
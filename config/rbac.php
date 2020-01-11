<?php

return [
    'models' => [
        'user' => 'App\User',
        'role' => 'App\Role',
        'permission' => 'App\Permission',
    ],
    'cache' => [
        'expires' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'rbac.cache',
    ],
];

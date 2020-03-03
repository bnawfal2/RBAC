<?php

return [
    'models' => [
        'user' => 'App\Models\User',
        'role' => 'App\Models\Role',
        'permission' => 'App\Models\Permission',
    ],
    'cache' => [
        'expires' => \DateInterval::createFromDateString('24 hours'),
        'key' => 'rbac.cache',
    ],
];

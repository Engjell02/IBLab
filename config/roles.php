<?php
// Define role hierarchy and permissions
return [
    'roles' => [
        'super_admin' => [
            'name' => 'Super Administrator',
            'level' => 100,
            'permissions' => ['*']
        ],
        'admin' => [
            'name' => 'Administrator',
            'level' => 80,
            'permissions' => [
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
                'notes.view_all',
                'notes.delete_any',
                'roles.assign',
                'access.grant_temporary'
            ]
        ],
        'manager' => [
            'name' => 'Manager',
            'level' => 60,
            'permissions' => [
                'team.view',
                'team.notes.view',
                'notes.share',
                'users.view_team',
                'access.request'
            ]
        ],
        'user' => [
            'name' => 'User',
            'level' => 20,
            'permissions' => [
                'notes.create',
                'notes.view_own',
                'notes.edit_own',
                'notes.delete_own',
                'profile.edit'
            ]
        ],
        'guest' => [
            'name' => 'Guest',
            'level' => 10,
            'permissions' => [
                'notes.view_shared'
            ]
        ]
    ],

    'resource_permissions' => [
        'owner' => ['read', 'write', 'delete', 'share'],
        'editor' => ['read', 'write'],
        'viewer' => ['read'],
        'commenter' => ['read', 'comment']
    ]
];
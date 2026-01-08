<?php

namespace App\DataFixtures;

use App\Enums\RolesEnum;

class UserFixtureData
{
    public const array USERS = [
        [
            'login' => 'admin',
            'phone' => '12345678',
            'password' => 'admin123',
            'roles' => [RolesEnum::ROOT->value],
        ],
        [
            'login' => 'user1',
            'phone' => '87654321',
            'password' => 'user1234',
        ],
        [
            'login' => 'user2',
            'phone' => '11223344',
            'password' => 'user5678',
        ],
    ];
}

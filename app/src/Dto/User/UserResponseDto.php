<?php

namespace App\Dto\User;

use Symfony\Component\Serializer\Attribute\Groups;

class UserResponseDto
{
    #[Groups(['response:id', 'response:full'])]
    public int $id;

    #[Groups(['response:full'])]
    public string $login;

    #[Groups(['response:full'])]
    public string $phone;

    public function __construct(int $id, string $login, string $phone)
    {
        $this->id = $id;
        $this->login = $login;
        $this->phone = $phone;
    }
}

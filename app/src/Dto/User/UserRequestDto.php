<?php

namespace App\Dto\User;

use App\Validator\UniqueUserLoginPass;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueUserLoginPass(groups: ['create', 'update'])]
class UserRequestDto
{
    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 8, groups: ['create', 'update'])]
    #[Groups(['create', 'update'])]
    public ?string $login = null;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 8, groups: ['create', 'update'])]
    #[Groups(['create', 'update'])]
    public ?string $phone = null;

    #[Assert\NotBlank(groups: ['create', 'update'])]
    #[Assert\Length(max: 8, groups: ['create', 'update'])]
    #[Groups(['create', 'update'])]
    public ?string $pass = null;
}
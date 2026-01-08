<?php

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

class LoginRequestDto
{
    #[Assert\NotBlank]
    public string $login;

    #[Assert\NotBlank]
    public string $pass;
}

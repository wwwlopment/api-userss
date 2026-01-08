<?php

namespace App\Service\User;

use App\Dto\User\UserRequestDto;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class UserService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function create(UserRequestDto $dto): User
    {
        $user = new User();
        $user->setLogin($dto->login);
        $user->setPhone($dto->phone);
        $user->setPass(
            $this->passwordHasher->hashPassword($user, $dto->pass)
        );

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function update(User $user, UserRequestDto $dto): User
    {
        $user->setLogin($dto->login);
        $user->setPhone($dto->phone);
        $user->setPass(
            $this->passwordHasher->hashPassword($user, $dto->pass)
        );

        $this->em->flush();

        return $user;
    }
}

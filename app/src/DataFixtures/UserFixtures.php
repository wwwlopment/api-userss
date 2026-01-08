<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Service\Auth\AuthService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly AuthService $authService
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        echo "\n=== Creating users ===\n\n";

        foreach (UserFixtureData::USERS as $userData) {
            $user = new User();
            $user->setLogin($userData['login']);
            $user->setPhone($userData['phone']);

            if (isset($userData['roles'])) {
                foreach ($userData['roles'] as $role) {
                    $user->addRole($role);
                }
            }

            $hashedPassword = $this->passwordHasher->hashPassword($user, $userData['password']);
            $user->setPass($hashedPassword);

            $manager->persist($user);

            $token = $this->authService->createToken($user);

            echo "Created User: {$userData['login']}\n";
            echo "  Password: {$userData['password']}\n";
            echo "  Token: {$token->getToken()}\n\n";
        }

        $manager->flush();
    }
}

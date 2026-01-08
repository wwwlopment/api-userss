<?php

namespace App\Service\Auth;

use App\Entity\AccessToken;
use App\Entity\User;
use App\Repository\AccessTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;

readonly class AuthService
{
    public function __construct(
        private EntityManagerInterface $em,
        private AccessTokenRepository $accessTokenRepository
    ) {
    }

    /**
     * @throws \DateMalformedStringException
     * @throws RandomException
     */
    public function createToken(User $user, int $ttl = 3600): AccessToken
    {
        $this->accessTokenRepository->deleteExpiredTokens();

        $tokenString = bin2hex(random_bytes(32));
        $accessToken = new AccessToken($tokenString, $user, $ttl);

        $this->em->persist($accessToken);
        $this->em->flush();

        return $accessToken;
    }
}

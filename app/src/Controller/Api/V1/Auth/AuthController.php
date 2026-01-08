<?php

namespace App\Controller\Api\V1\Auth;

use App\Dto\Auth\LoginRequestDto;
use App\Repository\UserRepository;
use App\Service\Auth\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/v1/api/auth')]
class AuthController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly AuthService $authService
    ) {
    }

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload]
        LoginRequestDto $loginRequestDto
    ): JsonResponse {
        $user = $this->userRepository->findOneBy(['login' => $loginRequestDto->login]);

        if (!$user || !$this->passwordHasher->isPasswordValid($user, $loginRequestDto->pass)) {
            return $this->json(['error' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $accessToken = $this->authService->createToken($user);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Could not create token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json([
            'token' => $accessToken->getToken(),
            'expires_at' => $accessToken->getExpiresAt()->format(\DateTimeInterface::ATOM),
        ]);
    }
}

<?php

namespace App\Security;

use App\Repository\AccessTokenRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class BearerAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly AccessTokenRepository $accessTokenRepository
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new AuthenticationException('Bearer token not exists or invalid format');
        }

        $token = substr($authHeader, 7);

        if (empty($token)) {
            throw new AuthenticationException('Token is empty');
        }

        return new SelfValidatingPassport(
            new UserBadge($token, function ($tokenValue) {
                $accessToken = $this->accessTokenRepository->findOneBy(['token' => $tokenValue]);

                if (null === $accessToken) {
                    throw new AuthenticationException('Token not found or invalid');
                }

                if ($accessToken->getExpiresAt() < new \DateTimeImmutable()) {
                    throw new AuthenticationException('Token expired');
                }

                return $accessToken->getUser();
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['error' => $exception->getMessage()],
            Response::HTTP_UNAUTHORIZED
        );
    }
}

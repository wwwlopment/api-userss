<?php

namespace App\Controller\Api\V1;

use App\Entity\User;
use App\Enums\RolesEnum;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use App\Dto\User\UserResponseDto;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BaseApiV1Controller extends AbstractController
{

    public function __construct(
        private readonly UserRepository $repository,
        private readonly SerializerInterface $serializer
    ) {
    }

    protected function canAccessUser(User $user): bool
    {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User) {
            return false;
        }
        return in_array(RolesEnum::ROOT->value, $currentUser->getRoles()) || $currentUser->getId() === $user->getId();
    }

    protected function jsonResponse(User $user, int $status = Response::HTTP_OK, array $context = []): JsonResponse
    {
        $dto = new UserResponseDto(
            $user->getId(),
            $user->getLogin(),
            $user->getPhone()
        );

        $data = $this->serializer->serialize($dto, 'json', $context);

        return new JsonResponse($data, $status, [], true);
    }

    /**
     * @throws HttpException
     */
    protected function findUserOrFail(int $id): ?User
    {
        $user = $this->repository->find($id);

        if (null === $user) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'User not found');
        }

        if (!$this->canAccessUser($user)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Access denied');
        }

        return $user;
    }
}

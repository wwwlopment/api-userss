<?php

namespace App\Controller\Api\V1\User;

use App\Controller\Api\V1\BaseApiV1Controller;
use App\Repository\UserRepository;
use App\Service\User\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use App\Dto\User\UserRequestDto;

#[Route('/v1/api/users')]
class UserController extends BaseApiV1Controller
{
    public function __construct(SerializerInterface $serializer, readonly private UserRepository $repository, readonly private EntityManagerInterface $em, private readonly UserService $userService)
    {
        parent::__construct($repository, $serializer);
    }

    #[Route('/{id}', name: 'user_get', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function get(int $id): JsonResponse
    {
        try {
            $user = $this->findUserOrFail($id);

            return $this->jsonResponse(user: $user, context: ['groups' => ['response:full']]);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Error getting user: ' . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('', name: 'user_post', methods: ['POST'])]
    #[IsGranted('ROLE_ROOT')]
    public function post(
        #[MapRequestPayload(
            serializationContext: ['groups' => ['create']],
            validationGroups: ['create']
        )]
        UserRequestDto $userRequestDto
    ): JsonResponse {
        try {
            $user = $this->userService->create($userRequestDto);

            return $this->jsonResponse(user: $user, status: Response::HTTP_CREATED, context: ['groups' => ['response:full']]);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Error creating user: ' . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: 'user_put', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function put(
        int $id,
        #[MapRequestPayload(
            serializationContext: ['groups' => ['update']],
            validationGroups: ['update']
        )]
        UserRequestDto $userRequestDto
    ): JsonResponse {
        try {
            $user = $this->findUserOrFail($id);
            $user = $this->userService->update($user, $userRequestDto);

            return $this->jsonResponse(user: $user, context: ['groups' => ['response:id']]);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Error updating user: ' . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    #[Route('/{id}', name: 'user_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ROOT')]
    public function delete(
        int $id
    ): JsonResponse {
        try {
            $user = $this->repository->find($id);

            if (null === $user) {
                return $this->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            $this->em->remove($user);
            $this->em->flush();

            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->json(
                ['error' => 'Error deleting user: ' . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}

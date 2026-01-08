<?php

namespace App\Validator;

use App\Dto\User\UserRequestDto;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueUserLoginPassValidator extends ConstraintValidator
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RequestStack $requestStack
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueUserLoginPass) {
            throw new UnexpectedTypeException($constraint, UniqueUserLoginPass::class);
        }

        if (!$value instanceof UserRequestDto) {
            return;
        }

        if (null === $value->login || null === $value->pass) {
            return;
        }

        $existingUser = $this->userRepository->findOneBy([
            'login' => $value->login,
            'pass' => $value->pass,
        ]);

        if (null === $existingUser) {
            return;
        }

        $currentRequest = $this->requestStack->getCurrentRequest();
        $id = $currentRequest?->attributes->get('id');

        if ($id && (int) $id === $existingUser->getId()) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->atPath('login')
            ->addViolation();
    }
}

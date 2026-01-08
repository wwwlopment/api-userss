<?php

namespace App\EventListener;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // only for API v1 requests
        if (!str_starts_with($request->getPathInfo(), '/v1/api')) {
            return;
        }

        $statusCode = $this->getStatusCode($exception);
        $errorMessage = $this->getErrorMessage($exception);

        $responseData = [
            'error' => $errorMessage
        ];

        $response = new JsonResponse($responseData, $statusCode);
        $event->setResponse($response);
    }

    private function getStatusCode(\Throwable $exception): int
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getStatusCode();
        }

        if ($exception instanceof ValidationFailedException) {
            return Response::HTTP_BAD_REQUEST;
        }

        if ($exception instanceof UniqueConstraintViolationException) {
            return Response::HTTP_CONFLICT;
        }

        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    private function getErrorMessage(\Throwable $exception): string
    {
        if ($exception instanceof ValidationFailedException) {
            $violations = $exception->getViolations();
            $messages = [];
            foreach ($violations as $violation) {
                $messages[] = $violation->getMessage();
            }
            return implode(', ', $messages);
        }

        if ($exception instanceof UniqueConstraintViolationException) {
            return 'Violation of data uniqueness';
        }

        if ($exception instanceof HttpExceptionInterface) {
            return $exception->getMessage();
        }

        return 'Internal server error';
    }
}

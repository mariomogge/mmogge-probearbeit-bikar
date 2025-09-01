<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

// Converts exceptions to consistent JSON responses
// DomainException -> 422, else 400 from HttpException
#[AsEventListener(event: 'kernel.exception')]
final class ExceptionSubscriber
{
    public function __invoke(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();
        $status = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 400;
        if ($e instanceof \DomainException) {
            $status = 422;
        }
        $event->setResponse(new JsonResponse([
            'error' => $e->getMessage(),
        ], $status));
    }
}

<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        if ($exception instanceof HttpException) {
            $data = [
            'status' => $exception->getStatusCode(),
            'message' => $exception->getMessage()
            ];
            $event->setResponse(new JsonResponse($data, $exception->getStatusCode()));
        } else {
            $data = [
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage()
            ];
            $event->setResponse(new JsonResponse($data, Response::HTTP_INTERNAL_SERVER_ERROR));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onKernelException',
        ];
    }
}

<?php


namespace App\EventListener;


use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

//        dd($exception);

        // HttpExceptionInterface is a special type of exception that
        // holds status code and header details
        if ($exception instanceof HttpExceptionInterface) {
            $response = new JsonResponse([
                'status' => $exception->getStatusCode(),
                'message' => $exception->getMessage(),
            ], $exception->getStatusCode());
            $response->headers->replace($exception->getHeaders());
        } else {
            $response = new JsonResponse([
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                'message' => $exception->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->headers->set('Content-Type', 'application/json');
        }

        // sends the modified response object to the event
        $event->setResponse($response);
    }
}
<?php


namespace Drupal\voteapp\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;


class ExceptionToJsonSubscriber implements EventSubscriberInterface
{


  public function onException(ExceptionEvent $event)
  {

    $request = $event->getRequest();
    $route_name = (string)$request->attributes->get('_route');

    if (strpos($route_name, 'voteapp.api.') !== 0) {
      return;
    }

    $exception = $event->getThrowable();
    $status_code = 500;

    if ($exception instanceof HttpExceptionInterface) {
      $status_code = $exception->getStatusCode();
    }

    $reflect = new \ReflectionClass($exception);

    $response = new JsonResponse([
      'status' => 'error',
      'exception' =>  $reflect->getShortName(),
      'message' => $exception->getMessage(),
    ], $status_code);

    $event->setResponse($response);
  }


  public static function getSubscribedEvents(): array
  {
    return [
      KernelEvents::EXCEPTION => ['onException', 0],
    ];
  }
}

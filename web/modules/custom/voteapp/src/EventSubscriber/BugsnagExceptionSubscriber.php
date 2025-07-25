<?php

namespace Drupal\voteapp\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Bugsnag\Client;

class BugsnagExceptionSubscriber implements EventSubscriberInterface
{

  protected $bugsnag;

  public function __construct(Client $bugsnag)
  {
    $this->bugsnag = Client::make(getenv('BUGSNAG_API_KEY'));
  }

  public function onException(ExceptionEvent $event)
  {
    $exception = $event->getThrowable();
    $this->bugsnag->notifyException($exception);
  }

  public static function getSubscribedEvents()
  {
    return [
      KernelEvents::EXCEPTION => ['onException', 0],
    ];
  }
}

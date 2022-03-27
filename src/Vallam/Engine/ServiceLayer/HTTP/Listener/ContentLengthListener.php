<?php


namespace Vallam\Engine\ServiceLayer\HTTP\Listener;


use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vallam\Engine\ServiceLayer\HTTP\Event\ResponseEvent;

class ContentLengthListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return ['response' => ['onResponse', -255]];
    }

    public function onResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();
        $headers = $response->headers;

        if (!$headers->has('Content-Length') && !$headers->has('Transfer-Encoding')) {
            $headers->set('Content-Length', strlen($response->getContent()));
        }
    }
}
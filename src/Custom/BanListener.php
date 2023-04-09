<?php

namespace App\Custom;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class BanListener
{
    private $authorizationChecker;
    private $router;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, RouterInterface $router)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->router = $router;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $controller = $event->getController();

        // Check if the controller is a closure or an invokable object
        if (!is_array($controller)) {
            return;
        }

        $object = $controller[0];
        $method = $controller[1];

        // Check if the controller method is a string
        if (!is_string($method)) {
            return;
        }

        // Check if the user is banned
        if ($this->authorizationChecker->isGranted('ROLE_BANNED')) {
            // Check if the controller is the PostController and the method is index
            if ($object instanceof \App\Controller\PostController && $method === 'index') {
                return;
            }

            $url = $this->router->generate('app_post');
            //add flash message
            
            $request = $event->getRequest();
            $session = $request->getSession();
            $response = new RedirectResponse($url);
            $event->setController(function () use ($response) {
                return $response;
            });
        }
    }
}
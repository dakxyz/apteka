<?php

namespace Xyz\Akulov\Apteka\Symfony\Listener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\Router;
use Xyz\Akulov\Apteka\Symfony\Controller\UserController;
use Xyz\Akulov\Service\UserService\Entity\Role;
use Xyz\Akulov\Service\UserService\UserServiceInterface;
use Xyz\Akulov\Symfony\Service\UserService\Security\UserAuthenticator;

class UserControllerRedirect
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var Router
     */
    private $router;

    public function __construct(UserServiceInterface $userService, Router $router)
    {
        $this->userService = $userService;
        $this->router = $router;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        $controller = $controller[0];

        if (!$controller instanceof UserController) {
            return;
        }

        $authKey = $event->getRequest()->cookies->get(UserAuthenticator::AUTH_COOKIE_KEY);
        $result = $this->userService->getUserByAuthKey($authKey);
        if (!$result->isSuccess()) {
            return;
        }

        $user = $result->getValue();

        $response = new RedirectResponse($this->router->generate('search'), 302);

        if ($user->checkAccess(Role::CUSTOMER)) {
            $response = new RedirectResponse($this->router->generate('customer_personal_area_index'), 302);
        }

        if ($user->checkAccess(Role::PHARMACY)) {
            $response = new RedirectResponse($this->router->generate('pharmacy_personal_area_index'), 302);
        }

        $event->setController(function () use ($response) {
            return $response;
        });
    }
}

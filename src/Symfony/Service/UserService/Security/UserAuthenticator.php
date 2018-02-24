<?php

namespace Xyz\Akulov\Symfony\Service\UserService\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class UserAuthenticator extends AbstractGuardAuthenticator
{
    const AUTH_COOKIE_KEY = 'authKey';

    /**
     * @var Router
     */
    private $router;

    /**
     * @var string
     */
    private $loginRoute;

    public function __construct(Router $router, string $loginRoute)
    {
        $this->router = $router;
        $this->loginRoute = $loginRoute;
    }

    /**
     * @inheritdoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return $this->getRedirectToLoginForm($request);
    }

    /**
     * @inheritdoc
     */
    public function supports(Request $request)
    {
        return $request->cookies->has(self::AUTH_COOKIE_KEY);
    }

    /**
     * @inheritdoc
     */
    public function getCredentials(Request $request)
    {
        return $request->cookies->get(self::AUTH_COOKIE_KEY);
    }

    /**
     * @inheritdoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials);
    }

    /**
     * @inheritdoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return $this->getRedirectToLoginForm($request);
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    private function getRedirectToLoginForm(Request $request): RedirectResponse
    {
        $response = new RedirectResponse(
            $this->router->generate(
                $this->loginRoute,
                ['back_url' => $request->getRequestUri()]
            ),
            302
        );

        $response->headers->clearCookie(self::AUTH_COOKIE_KEY);
        return $response;
    }
}

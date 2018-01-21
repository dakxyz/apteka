<?php

namespace Xyz\Akulov\Apteka\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Xyz\Akulov\Common\Service\UserService\UserServiceInterface;

class LuckyController extends Controller
{
    public function number()
    {
        $userService = $this
            ->container
            ->get(UserServiceInterface::class);

//        $userService->registry('denis@akulov.xyz', 'efsef', ['root']);
//
//        $userService->authorization('denis@akulov.xyz', 'efsef');

        $user = $userService->getUserByAuthKey('85c4da10-c5c8-48cd-9a8e-aedd851676a3-51757233');

        return new Response(
            '<html><body>Lucky number: ' . $user->getEmail() . '</body></html>'
        );
    }

//    public function
}

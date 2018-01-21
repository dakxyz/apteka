<?php

namespace Xyz\Akulov\Apteka\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Xyz\Akulov\Common\Service\UserService\UserServiceInterface;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        $user = $this
            ->container
            ->get(UserServiceInterface::class)
            ->getUserByAuthKey($request->cookies->get('authKey') ?? '');

        return $this->render('index.html.twig', ['user' => $user]);
    }
}

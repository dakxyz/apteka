<?php

namespace Xyz\Akulov\Apteka\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Xyz\Akulov\Common\Service\UserService\Exception\InternalException;
use Xyz\Akulov\Common\Service\UserService\Exception\ValidationException;
use Xyz\Akulov\Common\Service\UserService\UserServiceInterface;

class UserController extends Controller
{
    public function authorization(Request $request)
    {
    }

    public function registry(Request $request)
    {
        $session = new Session();
        $session->start();

        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('registry', SubmitType::class, array('label' => 'Registry'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                $this
                    ->container
                    ->get(UserServiceInterface::class)
                    ->registry(
                        $form->getData()['email'] ?? '',
                        $form->getData()['password'] ?? ''
                    );
            } catch (ValidationException $exception) {

            } catch (InternalException $exception) {

            }
        }

        return $this->render(
            'user/registry.html.twig',
            [
                'form' => $form->createView(),
                'message' => $message ?? ''
            ]
        );
    }
}

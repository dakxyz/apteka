<?php

namespace Xyz\Akulov\Apteka\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Xyz\Akulov\Common\Service\UserService\Exception\InternalException;
use Xyz\Akulov\Common\Service\UserService\Exception\ValidationException;
use Xyz\Akulov\Common\Service\UserService\UserServiceInterface;

class UserController extends Controller
{
    public function authorization(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('authorize', SubmitType::class, array('label' => 'Sign in'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $translator = $this->get('translator');
            try {
                $authKey = $this
                    ->container
                    ->get(UserServiceInterface::class)
                    ->authorization(
                        $form->getData()['email'] ?? '',
                        $form->getData()['password'] ?? ''
                    );

                if ($authKey) {
                    $response = $this->redirectToRoute('main_page');
                    $response->headers->setCookie(new Cookie('authKey', $authKey));
                    return $response;
                }

                $errors['common'][] = 'Incorrect login or password';
            } catch (InternalException $exception) {
                $errors['common'][] = $translator->trans('Service unavailable, please try again later');
            }
        }

        return $this->render(
            'user/authorization.html.twig',
            [
                'form'   => $form->createView(),
                'errors' => $errors ?? []
            ]
        );
    }

    public function registry(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('registry', SubmitType::class, array('label' => 'Registry'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $translator = $this->get('translator');
            try {
                $this
                    ->container
                    ->get(UserServiceInterface::class)
                    ->registry(
                        $form->getData()['email'] ?? '',
                        $form->getData()['password'] ?? '',
                        ['customer']
                    );

                return $this->redirectToRoute('main_page');
            } catch (ValidationException $exception) {
                foreach ($exception->getViolations() as $item) {
                    $errors[$item['property']][] = $translator->trans(
                        $item['message'],
                        $item['parameters']
                    );
                }
            } catch (InternalException $exception) {
                $errors['common'][] = $translator->trans('Service unavailable, please try again later');
            }
        }

        return $this->render(
            'user/registry.html.twig',
            [
                'form'    => $form->createView(),
                'message' => $message ?? '',
                'errors'  => $errors ?? []
            ]
        );
    }
}

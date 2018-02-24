<?php

namespace Xyz\Akulov\Apteka\Symfony\Controller;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Xyz\Akulov\Service\UserService\UserServiceInterface;
use Xyz\Akulov\Symfony\Service\UserService\Security\UserAuthenticator;

class UserController extends AbstractController
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    public function authorization(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('back_url', HiddenType::class, ['data' => $request->get('back_url')])
            ->add('sign_in', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $result = $this->userService->authorization(
                $form->getData()['email'] ?? '',
                $form->getData()['password'] ?? ''
            );

            $backUrl = $request->get('back_url');

            if (!$backUrl) {
                $backUrl = $this->generateUrl('pharmacy_personal_area_index');
            }

            if ($result->isSuccess()) {
                $response = $this->redirect($backUrl);
                $response->headers->setCookie(new Cookie(UserAuthenticator::AUTH_COOKIE_KEY, $result->getValue()));
                return $response;
            }
        }

        return $this->render(
            'authorization.html.twig',
            [
                'form'  => $form->createView(),
                'error' => isset($result) ? $result->getError() : null
            ]
        );
    }

    public function customerRegistration(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('sign_up', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $result = $this->userService->registry(
                $form->getData()['email'] ?? '',
                $form->getData()['password'] ?? '',
                ['customer']
            );

            if (!$result->isSuccess()) {
                return $this->render('customer_registration.html.twig', [
                    'form'  => $form->createView(),
                    'error' => $result->getError()
                ]);
            }

            $result = $this->userService->authorization(
                $form->getData()['email'] ?? '',
                $form->getData()['password'] ?? ''
            );

            if (!$result->isSuccess()) {
                return $this->redirectToRoute('authorization');
            }

            $response = $this->redirectToRoute('customer_personal_area_index');
            $response->headers->setCookie(new Cookie(UserAuthenticator::AUTH_COOKIE_KEY, $result->getValue()));
            return $response;
        }

        return $this->render('customer_registration.html.twig', ['form' => $form->createView()]);
    }

    public function pharmacyRegistration(Request $request)
    {
        return $this->render(
            'pharmacy_registration.html.twig'
        );
    }
}

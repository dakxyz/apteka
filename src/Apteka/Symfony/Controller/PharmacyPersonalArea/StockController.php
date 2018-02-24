<?php

namespace Xyz\Akulov\Apteka\Symfony\Controller\PharmacyPersonalArea;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Xyz\Akulov\Apteka\Service\PharmacyStockService\PharmacyStockServiceInterface;
use Xyz\Akulov\Apteka\Symfony\Controller\AbstractController;
use Xyz\Akulov\Symfony\Service\UserService\Security\UserAuthenticator;

class StockController extends AbstractController
{
    /**
     * @var PharmacyStockServiceInterface
     */
    private $pharmacyStockService;

    public function __construct(PharmacyStockServiceInterface $pharmacyStockService)
    {
        $this->pharmacyStockService = $pharmacyStockService;
    }

    public function upload(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('stock_file', FileType::class)
            ->add('upload', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        $file = $form->getData()['stock_file'] ?? null;
        if ($form->isSubmitted() && $file instanceof UploadedFile) {
            $result = $this->pharmacyStockService->uploadStockFile(
                $request->cookies->get(UserAuthenticator::AUTH_COOKIE_KEY),
                $file->getPathname(),
                $file->getClientOriginalName(),
                $file->getClientSize(),
                $file->getClientMimeType()
            );

            if ($result->isSuccess()) {
                return $this->redirectToRoute('pharmacy_personal_area_stock_list');
            }
        }

        return $this->render('pharmacy_personal_area/stock_upload.html.twig', [
            'error' => isset($result) ? $result->getError() : null,
            'form'  => $form->createView()
        ]);
    }

    public function stocksUploadsList(Request $request)
    {
        $result = $this->pharmacyStockService->getTasks($request->cookies->get(UserAuthenticator::AUTH_COOKIE_KEY));

        $parameters = [];
        if ($result->isSuccess()) {
            $parameters['tasks'] = $result->getValue();
        } else {
            $parameters['error'] = $result->getError();
        }

        return $this->render('pharmacy_personal_area/stocks_uploads_list.html.twig', $parameters);
    }
}

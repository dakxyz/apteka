<?php

namespace Xyz\Akulov\Apteka\Symfony\Controller\PharmacyPersonalArea;

use Xyz\Akulov\Apteka\Symfony\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function index()
    {
        return $this->render(
            'pharmacy_personal_area/index.html.twig'
        );
    }
}

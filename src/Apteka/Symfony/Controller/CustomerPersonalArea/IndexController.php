<?php

namespace Xyz\Akulov\Apteka\Symfony\Controller\CustomerPersonalArea;

use Xyz\Akulov\Apteka\Symfony\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function index()
    {
        return $this->render('customer_personal_area/index.html.twig');
    }
}

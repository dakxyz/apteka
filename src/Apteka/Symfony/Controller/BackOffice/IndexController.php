<?php

namespace Xyz\Akulov\Apteka\Symfony\Controller\BackOffice;

use Xyz\Akulov\Apteka\Symfony\Controller\AbstractController;

class IndexController extends AbstractController
{
    public function index()
    {
        $this->render('back_office/index.html.twig');
    }
}

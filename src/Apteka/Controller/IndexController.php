<?php

namespace Xyz\Akulov\Apteka\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return $this->render('index.html.twig');
    }
}

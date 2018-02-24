<?php

namespace Xyz\Akulov\Apteka\Symfony\Controller;

class IndexController extends AbstractController
{
    public function search()
    {
        return $this->render('search.html.twig');
    }

    public function catalog()
    {
        return $this->render('catalog.html.twig');
    }
}

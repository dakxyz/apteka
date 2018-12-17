<?php

namespace Xyz\Akulov\Apteka\Symfony\Controller;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class IndexController extends AbstractController
{
    public function search()
    {
        $expLang = new ExpressionLanguage();

        dump($expLang->parse('x + 1', ['x' => 1]));
        return $this->render('search.html.twig');
    }

    public function catalog()
    {
        return $this->render('catalog.html.twig');
    }
}

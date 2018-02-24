<?php

namespace Xyz\Akulov\Symfony\TwigExtension;

class UnitsOfInformationExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('units_of_information', array($this, 'toHumanReadable')),
        );
    }

    public function toHumanReadable($size)
    {
        $mod = 1024;

        $units = explode(' ', 'B KB MB GB TB PB');
        for ($i = 0; $size > $mod; $i++) {
            $size /= $mod;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    public function getName()
    {
        return 'units_of_information_extension';
    }
}

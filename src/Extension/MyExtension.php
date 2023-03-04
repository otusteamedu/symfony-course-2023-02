<?php

namespace App\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class MyExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('my_filter', [$this, 'myFilter']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('my_function', [$this, 'myFunction']),
        ];
    }

    public function myFilter()
    {
        return 'myFilter myFilter';
    }

    public function myFunction()
    {
        return 'myFunction myFunction';
    }
}

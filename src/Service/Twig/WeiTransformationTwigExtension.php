<?php

namespace App\Service\Twig;

use App\Service\WeiTransformationService;
use Twig_Extension;
use Twig_SimpleFilter;

class WeiTransformationTwigExtension extends Twig_Extension
{
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('weiTransform', [$this, 'transformWei']),
        ];
    }

    public function transformWei($wei, string $transformTo = WeiTransformationService::WEI)
    {
        $value = WeiTransformationService::transformWei($wei, $transformTo);

        return number_format($value, 2, ',', '.');
    }
}

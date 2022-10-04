<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class IconCleaner extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('cleanSvg', [$this, 'cleanSvg']),
            new TwigFilter('svgClass', [$this, 'svgClass']),
        ];
    }

    public function cleanSvg(string $svgContent): array|string|null
    {
        $svgContentCleanedColor = preg_replace('/fill="#\w+"/', '', $svgContent);
        $svgContentCleanedStroke = preg_replace('/stroke="#\w+"/', '', $svgContentCleanedColor);
        return preg_replace('/(<svg (width="\w+"\ height="\w+"))/', '<svg ', $svgContentCleanedStroke);
    }

    public function svgClass($categories): string
    {
        $categoriesArray = [];

        foreach ($categories as $category)
        {
            $categoriesArray[] = $category->getName();
        }

        $outlineClass = "outlinesvg";

        if (!in_array('Outline', $categoriesArray)) {
            return '';
        }

        return $outlineClass;
    }
}
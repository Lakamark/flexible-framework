<?php

namespace FlexibleFramework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ago', $this->ago(...), ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param \DateTime $dateTime
     * @param string $format
     * @return string
     */
    public function ago(\DateTime $dateTime, string $format = 'd/m/Y H:i'): string
    {
        return '<span class="timeago" datetime="' . $dateTime->format(\DateTime::ATOM) . '">' . $dateTime->format(
            $format
        ) . '</span>';
    }
}

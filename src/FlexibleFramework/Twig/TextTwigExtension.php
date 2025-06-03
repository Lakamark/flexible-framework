<?php

namespace FlexibleFramework\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TextTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('excerpt', $this->excerpt(...)),
        ];
    }

    /**
     * Generate an excerpt text.
     *
     * @param string|null $content
     * @param int $maxLength
     * @return string
     */
    public function excerpt(?string $content, int $maxLength = 100): string
    {
        if (is_null($content)) {
            return '';
        }
        if (mb_strlen($content) > $maxLength) {
            $excerpt = mb_substr($content, 0, $maxLength);
            $lastSpace = mb_strrpos($excerpt, ' ');
            return mb_substr($excerpt, 0, $lastSpace) . '...';
        }

        return $content;
    }
}

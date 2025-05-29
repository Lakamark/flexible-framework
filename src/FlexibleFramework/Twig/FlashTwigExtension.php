<?php

namespace FlexibleFramework\Twig;

use FlexibleFramework\Session\FlashService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FlashTwigExtension extends AbstractExtension
{
    public function __construct(
        private readonly FlashService $flashService,
    ) {}

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('flash', $this->getFlash(...)),
        ];
    }

    /**
     * Get the flash message
     *
     * @param string $type
     * @return string|null
     */
    public function getFlash(string $type): ?string
    {
        return $this->flashService->get($type);
    }
}

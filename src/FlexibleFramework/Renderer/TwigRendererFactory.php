<?php

namespace App\FlexibleFramework\Renderer;

use FlexibleFramework\Renderer\TwigRenderer;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRendererFactory
{
    public function __invoke(ContainerInterface $container): TwigRenderer
    {
        $viewPath = $container->get('templates.path');
        $loader = new FilesystemLoader($viewPath);
        $twig = new Environment($loader, []);

        // Load twig extension from the container
        if ($container->has('twig.extensions')) {
            foreach ($container->get('twig.extensions') as $extension) {
                $twig->addExtension($extension);
            }
        }

        return new TwigRenderer($twig);
    }
}

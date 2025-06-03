<?php

namespace FlexibleFramework;

use App\FlexibleFramework\Middleware\KernelMiddleware\CombinedMiddleware;
use App\FlexibleFramework\Middleware\KernelMiddleware\RoutePrefixedMiddleware;
use DI\ContainerBuilder;
use Exception;
use FlexibleFramework\Exception\KernelException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Kernel implements RequestHandlerInterface
{
    /**
     * The module list load in the kernel
     * @var string[]
     */
    private array $modules = [];

    /**
     * @var array
     */
    private array $definitions;

    private $container;

    /**
     * @var array
     */
    private array $middlewares = [];

    /**
     * @var int
     */
    private int $index = 0;

    /**
     * Kernel constructor.
     * @param array|string|null $definitions
     */
    public function __construct(array|string|null $definitions = [])
    {
        if (is_string($definitions)) {
            $definitions = [$definitions];
        }
        if (!$this->isSequential($definitions)) {
            $definitions = [$definitions];
        }
        return $this->definitions = $definitions;
    }

    /**
     * To register a module in the kernel
     *
     * @param string $module
     * @return $this
     */
    public function addModule(string $module): self
    {
        $this->modules[] = $module;
        return $this;
    }

    /**
     * Add middleware between the request to the response
     *
     * @param $routePrefix
     * @param $middleware
     * @return self
     * @throws Exception
     */
    public function pipe($routePrefix, $middleware = null): self
    {
        if ($middleware === null) {
            $this->middlewares[] = $routePrefix;
        } else {
            $this->middlewares[] = new RoutePrefixedMiddleware($this->getContainer(), $routePrefix, $middleware);
        }
        return $this;
    }

    /**
     * @throws KernelException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->index++;
        if ($this->index > 1) {
            throw new KernelException();
        }
        $middleware = new CombinedMiddleware($this->getContainer(), $this->middlewares);
        return $middleware->process($request, $this);
    }

    public function run(ServerRequestInterface $request): ResponseInterface
    {
        foreach ($this->modules as $module) {
            $this->getContainer()->get($module);
        }
        return $this->handle($request);
    }

    /**
     * Return all initialized modules from the kernel
     * @return string[]
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @return ContainerInterface
     * @throws Exception
     */
    public function getContainer(): ContainerInterface
    {
        if ($this->container === null) {
            $builder = new ContainerBuilder();

            /*
             * Add the definitions in the kernel
             * app.php
             * database.php
             * template.php
             * etc.
             */
            foreach ($this->definitions as $definition) {
                $builder->addDefinitions($definition);
            }

            // Load all the module with their definitions in the kernel
            foreach ($this->modules as $module) {
                if ($module::DEFINITIONS) {
                    $builder->addDefinitions($module::DEFINITIONS);
                }
            }

            // Load the kernel class himself
            $builder->addDefinitions([
                Kernel::class => $this,
            ]);
            $this->container = $builder->build();
        }
        return $this->container;
    }

    /**
     * @param array $array
     * @return bool
     */
    private function isSequential(array $array): bool
    {
        if (empty($array)) {
            return true;
        }
        return array_keys($array) === range(0, count($array) - 1);
    }
}

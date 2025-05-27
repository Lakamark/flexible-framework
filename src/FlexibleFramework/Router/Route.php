<?php

namespace FlexibleFramework\Router;

/**
 * Represent a matched route
 */
class Route
{
    public function __construct(
        private readonly string $name,
        private $callback,
        private readonly array $params,
    ) {}

    public function getName(): string
    {
        return $this->name;
    }


    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * @return string[]
     */
    public function getParams(): array
    {
        return $this->params;
    }
}

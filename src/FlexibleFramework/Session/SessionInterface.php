<?php

namespace FlexibleFramework\Session;

interface SessionInterface
{
    /**
     * Get a key in the session
     *
     * @param string $key
     * @param mixed $default
     */
    public function get(string $key, mixed $default);

    /**
     * Add a key in the session
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void;

    /**
     * Remove a key in the session
     *
     * @param string $key
     * @return void
     */
    public function delete(string $key): void;
}

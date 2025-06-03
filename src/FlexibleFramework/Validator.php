<?php

namespace FlexibleFramework;

use DateTime;
use FlexibleFramework\Database\Table;
use FlexibleFramework\Validator\ValidatorError;

class Validator
{
    /**
     * @var string[]
     */
    private array $errors = [];

    public function __construct(
        private readonly array $params
    ) {}

    /**
     * Check if the keys are in the params array
     *
     * @param string ...$keys
     * @return $this
     */
    public function required(string ...$keys): self
    {
        if (is_array($keys[0])) {
            $keys = $keys[0];
        }

        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }

    /**
     * check if the value keys are not empty
     *
     * @param string ...$keys
     * @return $this
     */
    public function notEmpty(string ...$keys): self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (empty($value)) {
                $this->addError($key, 'notEmpty');
            }
        }
        return $this;
    }

    /**
     * Check if the content keys respect limit length
     *
     * @param string $key
     * @param int|null $min
     * @param int|null $max
     * @return $this
     */
    public function length(string $key, ?int $min, ?int $max = null): self
    {
        $value = $this->getValue($key);
        $length = mb_strlen($value);
        if (!is_null($min) &&
            !is_null($max) &&
            ($length < $min || $length > $max)
        ) {
            $this->addError($key, 'betweenLength', [$min, $max]);
            return $this;
        }
        if (!is_null($min) &&
            $length < $min
        ) {
            $this->addError($key, 'minLength', [$min]);
            return $this;
        }
        if (!is_null($max) &&
            $length > $max
        ) {
            $this->addError($key, 'maxLength', [$max]);
        }
        return $this;
    }

    /**
     * Check if the key params is a valid slug
     *
     * @param string $key
     * @return $this
     */
    public function slug(string $key): self
    {
        $value = $this->getValue($key);
        $pattern = '/^[a-z0-9]+(-[a-z0-9]+)*$/';
        if (!is_null($value) && !preg_match($pattern, $value)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }

    /**
     * Check if the value key is datetime valid
     * On PHP 8.3x the \DateTime::getLastErrors() method returns false rather than an array
     * with warning_count and error_count
     * If \DateTime::getLastErrors() method return true you should to very the error count and the wearings cont
     * @link https://github.com/php/php-src/issues/9431
     *
     * @param string $key
     * @param string $format
     * @return $this
     */
    public function dateTime(string $key, string $format = "Y-m-d H:i:s"): self
    {
        $value = $this->getValue($key);
        DateTime::createFromFormat($format, $value);
        $errors = DateTime::getLastErrors();
        if ($errors !== false) {
            if ($errors['error_count'] > 0 || $errors['warning_count'] > 0) {
                $this->addError($key, 'datetime', [$format]);
            }
        }
        return $this;
    }

    /**
     * Check if the record exists in the database
     *
     * @param string $key
     * @param string $table
     * @param \PDO $pdo
     * @return $this
     */
    public function exists(string $key, string $table, \PDO $pdo): self
    {
        $value = $this->getValue($key);
        $statement = $pdo->prepare("SELECT id FROM $table WHERE id = ?");
        $statement->execute([$value]);
        if ($statement->fetchColumn() === false) {
            $this->addError($key, 'exists', [$table]);
        }
        return $this;
    }

    /**
     * Check if the key is unique in the database
     *
     * @param string $key
     * @param string $table
     * @param \PDO $pdo
     * @param int|null $exclude
     * @return $this
     */
    public function unique(string $key, string $table, \PDO $pdo, ?int $exclude = null): self
    {
        $value = $this->getValue($key);
        $query = "SELECT id FROM $table WHERE $key = ?";
        $params = [$value];
        if ($exclude !== null) {
            $query .= " AND id != ?";
            $params[] = $exclude;
        }
        $statement = $pdo->prepare($query);
        $statement->execute($params);
        if ($statement->fetchColumn() !== false) {
            $this->addError($key, 'unique', [$value]);
        }
        return $this;
    }

    /**
     * Return errors array
     *
     * @return ValidatorError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    private function getValue(string $key): mixed
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }

    /**
     * To add a validator error
     *
     * @param string $key
     * @param string $rule
     * @param array $attributes
     * @return void
     */
    private function addError(string $key, string $rule, array $attributes = []): void
    {
        $this->errors[$key] = new ValidatorError($key, $rule, $attributes);
    }
}

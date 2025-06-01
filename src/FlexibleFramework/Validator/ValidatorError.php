<?php

namespace FlexibleFramework\Validator;

class ValidatorError
{
    private array $messages = [
        'required' => 'The %s field is required.',
        'empty' => 'The %s field is empty.',
        'slug' => 'The %s field must be a valid slug.',
        'minLength' => 'The %s field must be at least %d characters.',
        'maxLength' => 'The %s field must be at most %d characters.',
        'between' => 'The %s field must be between %d and %d characters.',
        'datetime' => 'The %s field must be a valid datetime (%s).',
    ];

    public function __construct(
        private readonly string $key,
        private readonly string $rule,
        private readonly array $attributes = [],
    ) {}

    public function __toString()
    {
        if (!array_key_exists($this->rule, $this->messages)) {
            return "The {$this->key} does not match the {$this->rule}";
        } else {
            $params = array_merge([$this->messages[$this->rule], $this->key], $this->attributes);
            return (string) call_user_func_array('sprintf', $params);
        }
    }
}

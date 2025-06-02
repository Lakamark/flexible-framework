<?php

namespace FlexibleFramework\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FormTwigExtension extends AbstractExtension
{
    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('field', $this->field(...), [
                'is_safe' => ['html'],
                'needs_context' => true,
            ]),
        ];
    }

    /**
     * Generate form html
     *
     * @param array $context
     * @param string $key
     * @param $value
     * @param string|null $label
     * @param array $options
     * @return string
     */
    public function field(array $context, string $key, $value, ?string $label = null, array $options = []): string
    {
        $type = $options['type'] ?? 'text';
        $errors = $this->getErrorHtml($context, $key);
        $class = 'form-group';
        $value = $this->convertValue($value);
        $attributes = [
            'class' => trim('form-control ' . ($options['class'] ?? '')),
            'name' => $key,
            'id' => $key,
        ];

        if ($errors) {
            $class .= ' form-check';
            $attributes['class'] .= ' is-invalid';
        }

        if ($type === 'textarea') {
            $input = $this->textarea($value, $attributes);
        } else {
            $input = $this->input($value, $attributes);
        }

        return "<div class=\"$class\">
              <label for=\"$key\">$label</label>
              {$input}
              {$errors}
            </div>";
    }

    /**
     * Get error validation from the Twig context
     *
     * @param $context
     * @param $key
     * @return string
     */
    private function getErrorHtml($context, $key): string
    {
        $errors = $context['errors'][$key] ?? false;
        if ($errors) {
            return "<div class=\"invalid-feedback\">$errors</div>";
        }
        return "";
    }

    /**
     * Generate an input field
     *
     * @param string|null $value
     * @param array $attributes
     * @return string
     */
    private function input(?string $value, array $attributes): string
    {
        return "<input type=\"text\" " . $this->getHtmlFromArray($attributes) . " value=\"$value\">";
    }

    /**
     * Generate a textarea field
     *
     * @param string|null $value
     * @param array $attributes
     * @return string
     */
    private function textarea(?string $value, array $attributes): string
    {
        return "<textarea " . $this->getHtmlFromArray($attributes) . ">$value</textarea>";
    }

    /**
     * Convert an attribute array to HTML attributes
     *
     * @param array $attributes
     * @return string
     */
    private function getHtmlFromArray(array $attributes): string
    {
        return implode(' ', array_map(function ($key, $value) {
            return "$key=\"$value\"";
        }, array_keys($attributes), $attributes));
    }

    /**
     * To convert a value other type to a string type
     *
     * @param $value
     * @return string|null
     */
    private function convertValue($value): ?string
    {
        if ($value instanceof DateTime) {
            return $value->format('Y-m-d H:i:s');
        }
        return (string) $value;
    }

}

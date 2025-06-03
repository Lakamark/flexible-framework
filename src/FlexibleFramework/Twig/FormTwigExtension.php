<?php

namespace FlexibleFramework\Twig;

use DateTime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

use function DI\string;

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
        } elseif (array_key_exists('options', $options)) {
            $input = $this->select($value, $options['options'], $attributes);
        } else {
            $attributes['type'] = $options['type'] ?? 'text';
            $input = $this->input($value, $attributes);
        }

        return "<div class=\"" . $class . "\">
           <label for=\"name\">{$label}</label>
              {$input}{$errors}
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
        return "<input " . $this->getHtmlFromArray($attributes) . " value=\"$value\">";
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
     *
     * @param string|null $value
     * @param array $options
     * @param array $attributes
     * @return string
     */
    private function select(?string $value, array $options, array $attributes): string
    {
        $htmlOptions = array_reduce(array_keys($options), function (string $html, string $key) use ($options, $value) {
            $params = ['value' => $key, 'selected' => $key === $value];
            return $html . '<option ' . $this->getHtmlFromArray($params) . '>' . $options[$key] . '</option>';
        }, "");
        return "<select " . $this->getHtmlFromArray($attributes) . ">$htmlOptions</select>";
    }

    /**
     * Convert an attribute array to HTML attributes
     *
     * @param array $attributes
     * @return string
     */
    private function getHtmlFromArray(array $attributes): string
    {
        $htmlParts = [];
        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $htmlParts[] = (string) $key;
            } elseif ($value !== false) {
                $htmlParts[] = "$key=\"$value\"";
            }
        }
        return implode(' ', $htmlParts);
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

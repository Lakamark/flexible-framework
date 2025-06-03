<?php

namespace Tests\FlexibleFramework\Twig;

use FlexibleFramework\Twig\FormTwigExtension;
use PHPUnit\Framework\TestCase;

class FormExtensionTest extends TestCase
{
    private FormTwigExtension $formTwigExtension;

    public function setUp(): void
    {
        $this->formTwigExtension = new FormTwigExtension();
    }

    private function trim(string $string): string
    {
        $lines = explode('\n', $string);
        $lines = array_map('trim', $lines);
        return implode('', $lines);
    }

    public function assertSimilar(string $expected, string $actual): void
    {
        $this->assertEqualsIgnoringCase($this->trim($actual), $this->trim($expected));
    }

    public function testField(): void
    {
        $html = $this->formTwigExtension->field([], 'name', 'demo', 'Titre');
        $this->assertSimilar("
           <div class=\"form-group\">
           <label for=\"name\">Titre</label>
              <input class=\"form-control\" name=\"name\" id=\"name\" type=\"text\" value=\"demo\">
           </div>
        ", $html);
    }

    public function testAreaField(): void
    {
        $html = $this->formTwigExtension->field(
            [],
            'name',
            'demo',
            'Titre',
            ['type' => 'textarea']
        );
        $this->assertSimilar("
           <div class=\"form-group\">
           <label for=\"name\">Titre</label>
              <textarea class=\"form-control\" name=\"name\" id=\"name\">demo</textarea>
           </div>
        ", $html);
    }

    public function testFieldWithClass(): void
    {
        $html = $this->formTwigExtension->field(
            [],
            'name',
            'demo',
            'Titre',
            ['class' => 'demo']
        );
        $this->assertSimilar("
           <div class=\"form-group\">
           <label for=\"name\">Titre</label>
              <input class=\"form-control demo\" name=\"name\" id=\"name\" type=\"text\" value=\"demo\">
           </div>
        ", $html);
    }

    public function testFieldWithErrors(): void
    {
        $context = ['errors' => ['name' => 'error']];
        $html = $this->formTwigExtension->field(
            $context,
            'name',
            'demo',
            'Titre'
        );
        $this->assertSimilar("
           <div class=\"form-group form-check\">
           <label for=\"name\">Titre</label>
              <input class=\"form-control is-invalid\" name=\"name\" id=\"name\" type=\"text\" value=\"demo\"><div class=\"invalid-feedback\">error</div>
           </div>
        ", $html);
    }

    /*
    public function testSelect()
    {
        $this->markTestSkipped('They string are egale');
        $html = $this->formTwigExtension->field(
            [],
            'name',
            2,
            'Titre',
            ['options' => [1 => 'Demo', '2' => 'Demo2']]
        );
        $this->assertSimilar('<div class="form-group">
           <label for="name">Titre</label>
                <select class="form-control" name="name" id="name">
                <option value="1">Demo</option>
                <option value="2" selected>Demo2</option>
                </select>
           </div>
        ', $html);
    }
    */
}

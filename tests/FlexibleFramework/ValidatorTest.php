<?php

namespace Tests\FlexibleFramework;

use FlexibleFramework\Validator;
use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase
{
    public function testRequiredIsFailed(): void
    {
        $errors = $this->makeValidator([
            'name' => 'john',
        ])
            ->required('name', 'content')
            ->getErrors();
        ;
        $this->assertCount(1, $errors);
    }

    public function testRequiredIsSuccess(): void
    {
        $errors = $this->makeValidator([
            'name' => 'john',
            'content' => 'my content',
        ])
            ->required('name', 'content')
            ->getErrors();
        ;
        $this->assertCount(0, $errors);
    }

    public function testSlugIsSuccess(): void
    {
        $errors = $this->makeValidator([
            'slug' => 'my-slug42',
        ])
            ->slug('slug')
            ->getErrors();
        ;
        $this->assertCount(0, $errors);
    }

    public function testNotEmpty(): void
    {
        $errors = $this->makeValidator([
            'name' => 'john',
            'content' => '',
        ])
            ->notEmpty('content')
            ->getErrors();
        ;
        $this->assertCount(1, $errors);
    }

    public function testSlugIsFailed(): void
    {
        $errors = $this->makeValidator([
            'slug'  => 'aze-aze-azeAze34',
            'slug2' => 'aze-aze_azeAze34',
            'slug4' => 'aze-azeaze-',
            'slug3' => 'aze--aze-aze',
        ])
            ->slug('slug')
            ->slug('slug2')
            ->slug('slug3')
            ->getErrors();
        ;
        $this->assertCount(3, $errors);
    }

    public function testLength()
    {
        $params = ['slug' => '123456789'];
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3)->getErrors());
        $errors = $this->makeValidator($params)->length('slug', 12)->getErrors();
        $this->assertCount(1, $errors);
        $this->assertCount(1, $this->makeValidator($params)->length('slug', 3, 4)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', 3, 20)->getErrors());
        $this->assertCount(0, $this->makeValidator($params)->length('slug', null, 20)->getErrors());
        $this->assertCount(1, $this->makeValidator($params)->length('slug', null, 8)->getErrors());
    }

    public function testDateTime()
    {
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 11:12:13'])->dateTime('date')->getErrors());
        $this->assertCount(0, $this->makeValidator(['date' => '2012-12-12 00:00:00'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2012-21-12'])->dateTime('date')->getErrors());
        $this->assertCount(1, $this->makeValidator(['date' => '2013-02-29 11:12:13'])->dateTime('date')->getErrors());
    }

    private function makeValidator(array $params = []): Validator
    {
        return new Validator($params);
    }
}

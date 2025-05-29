<?php

namespace FlexibleFramework\Twig;

use PHPUnit\Framework\TestCase;

class TimeTwigExtensionTest extends TestCase
{
    private TimeTwigExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new TimeTwigExtension();

    }

    public function testDateFormat(): void
    {
        $dateTime = new \DateTime();
        $format = 'd/m/Y H:i';
        $result = '<span class="timeago" datetime="' . $dateTime->format(\DateTime::ATOM) . '">' . $dateTime->format($format) . '</span>';
        $this->assertEquals($result, $this->extension->ago($dateTime));
    }
}

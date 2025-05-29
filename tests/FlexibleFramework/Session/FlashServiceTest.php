<?php

namespace Tests\FlexibleFramework\Session;

use FlexibleFramework\Session\ArraySession;
use FlexibleFramework\Session\FlashService;
use PHPUnit\Framework\TestCase;

class FlashServiceTest extends TestCase
{
    /**
     * @var ArraySession
     */
    private ArraySession $session;

    /**
     * @var FlashService
     */
    private FlashService $flashService;

    protected function setUp(): void
    {
        $this->session = new ArraySession();
        $this->flashService = new FlashService($this->session);
    }

    public function testDeleteFlashAfterOneShowed(): void
    {
        $this->flashService->success('Bravo');
        $this->assertEquals('Bravo', $this->flashService->get('success'));
        $this->assertNull($this->session->get('flash'));
        $this->assertEquals('Bravo', $this->flashService->get('success'));
        $this->assertEquals('Bravo', $this->flashService->get('success'));
    }
}

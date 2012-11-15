<?php

namespace Ace\SenderBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testEmpty()
    {
        $this->assertLessThanOrEqual(1,1);
    }
}

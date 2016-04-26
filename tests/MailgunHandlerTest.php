<?php

namespace Tylercd100\Monolog\Tests;

use Tylercd100\Monolog\Handler\MailgunHandler;

class MailgunHandlerTest extends TestCase
{
    public function testItCanBeInstantiated()
    {
        $handler = new MailgunHandler("to@test.com", "Test subject", "from@test.com", "Token", "test.com");
    }
}

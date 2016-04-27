<?php

namespace Tylercd100\Monolog\Tests;

use Exception;
use Monolog\Logger;
use Tylercd100\Monolog\Handler\MailgunHandler;

class MailgunHandlerTest extends TestCase
{
    public function testItCanBeInstantiated()
    {
        $handler = $this->createHandler("to@test.com", "Test subject", "from@test.com", "Token", "test.com");
    }

    public function testItThrowsExceptionWithUnsupportedVersion()
    {
        $this->setExpectedException(Exception::class);
        $handler = $this->createHandler("to@test.com", "Test subject", "from@test.com", "Token", "test.com", 100,  true, 'api.mailgun.net', 'v0');
    }

    private function createHandler($to = "to@test.com",$subject = "Test subject",$from = "from@test.com",$token = "Token",$domain = "test.com", $level = Logger::CRITICAL, $bubble = true, $host = 'api.mailgun.net', $version = 'v3')
    {
        $constructorArgs = array($to, $subject, $from, $token, $domain, $level, $bubble, $host, $version);
        $this->handler = $this->getMock(
            '\Tylercd100\Monolog\Handler\MailgunHandler',
            array(),
            $constructorArgs
        );
    }
}

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
        $handler = $this->createHandler("to@test.com", "Test subject", "from@test.com", "Token", "test.com", 100, true, true, 'api.mailgun.net', 'v0');
    }

    public function testWriteHeader()
    {
        $this->createHandler();
        $this->handler->handle($this->getRecord(Logger::CRITICAL, 'test1'));
        fseek($this->res, 0);
        $content = fread($this->res, 1024);

        $this->assertRegexp('/POST \/v3\/test.com\/messages HTTP\/1.1\\r\\nHost: api.mailgun.net\\r\\nAuthorization: Basic YXBpOlRva2Vu\\r\\nContent-Type: application\/x-www-form-urlencoded\\r\\nCache-Control: no-cache\\r\\nContent-Length: \d{2,4}\\r\\n\\r\\n/', $content);

        return $content;
    }

    public function testWriteCustomHostHeader()
    {
        $this->createHandler("to@test.com", "Test subject", "from@test.com", "Token", "test.com");
        $this->handler->handle($this->getRecord(Logger::CRITICAL, 'test1'));
        fseek($this->res, 0);
        $content = fread($this->res, 1024);

        $this->assertRegexp('/POST \/v3\/test.com\/messages HTTP\/1.1\\r\\nHost: api.mailgun.net\\r\\nAuthorization: Basic YXBpOlRva2Vu\\r\\nContent-Type: application\/x-www-form-urlencoded\\r\\nCache-Control: no-cache\\r\\nContent-Length: \d{2,4}\\r\\n\\r\\n/', $content);

        return $content;
    }

    /**
     * @depends testWriteHeader
     */
    public function testWriteContent($content)
    {
        $this->assertRegexp('/from=from%40test.com&to=to%40test.com&subject=Test\+subject&text=test1/', $content);
    }

    /**
     * @depends testWriteCustomHostHeader
     */
    public function testWriteContentNotify($content)
    {
        $this->assertRegexp('/from=from%40test.com&to=to%40test.com&subject=Test\+subject&text=test1/', $content);
    }

    public function testWriteWithComplexMessage()
    {
        $this->createHandler();
        $this->handler->handle($this->getRecord(Logger::CRITICAL, 'Backup of database example finished in 16 minutes.'));
        fseek($this->res, 0);
        $content = fread($this->res, 1024);

        $this->assertRegexp('/from=from%40test.com&to=to%40test.com&subject=Test\+subject&text=Backup\+of\+database\+example\+finished\+in\+16\+minutes\./', $content);
    }

    private function createHandler($to = "to@test.com",$subject = "Test subject",$from = "from@test.com",$token = "Token",$domain = "test.com", $level = Logger::CRITICAL, $bubble = true, $useSSL = true, $host = 'api.mailgun.net', $version = 'v3')
    {
        $constructorArgs = array($to, $subject, $from, $token, $domain, $level, $bubble, $useSSL, $host, $version);
        $this->res = fopen('php://memory', 'a');
        $this->handler = $this->getMock(
            '\Tylercd100\Monolog\Handler\MailgunHandler',
            array('fsockopen', 'streamSetTimeout', 'closeSocket'),
            $constructorArgs
        );

        $reflectionProperty = new \ReflectionProperty('\Monolog\Handler\SocketHandler', 'connectionString');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->handler, 'localhost:1234');

        $this->handler->expects($this->any())
            ->method('fsockopen')
            ->will($this->returnValue($this->res));
        $this->handler->expects($this->any())
            ->method('streamSetTimeout')
            ->will($this->returnValue(true));
        $this->handler->expects($this->any())
            ->method('closeSocket')
            ->will($this->returnValue(true));

        $this->handler->setFormatter($this->getIdentityFormatter());
    }
}

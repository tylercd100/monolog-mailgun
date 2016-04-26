<?php

namespace Tylercd100\Monolog\Handler;

use Exception;
use Monolog\Logger;
use Monolog\Handler\SocketHandler;

/**
 * Mailgun - Monolog Handler
 */
class MailgunHandler extends SocketHandler
{
    private $host;
    private $version;
    private $domain;
    private $token;
    private $to;
    private $subject;
    private $from;

    public function __construct($to, $subject, $from, $token, $domain, $level = Logger::CRITICAL, $bubble = true, $useSSL = true, $host = 'api.mailgun.net', $version = 'v3')
    {
        if($version !== 'v3'){
            throw new Exception("Version '{$version}' is not supported");
        }

        $connectionString = $useSSL ? 'ssl://'.$host.':443' : $host.':80';
        parent::__construct($connectionString, $level, $bubble);

        $this->to = $to;
        $this->subject = $subject;
        $this->from = $from;
        $this->host = $host;
        $this->version = $version;
        $this->domain = $domain;
        $this->token = $token;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array  $record
     * @return string
     */
    protected function generateDataStream($record)
    {
        $content = $this->buildContent($record);
        return $this->buildHeader($content) . $content;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array  $record
     * @return string
     */
    protected function buildContent($record)
    {
        $dataArray = array(
            'from'    => $this->from,
            'to'      => $this->to,
            'subject' => $this->subject,
            'text'    => $record['formatted']
        );

        return http_build_query($dataArray);
    }

    /**
     * Builds the URL for the API call
     * 
     * @return string
     */
    protected function buildRequestUrl()
    {
        return "POST /{$this->version}/{$this->domain}/messages HTTP/1.1\r\n";
    }

    /**
     * Builds the header of the API call
     *
     * @param  string $content
     * @return string
     */
    private function buildHeader($content)
    {
        $auth = base64_encode("api:".$this->token);

        $header = $this->buildRequestUrl();

        $header .= "Host: {$this->host}\r\n";
        $header .= "Authorization: Basic ".$auth."\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Cache-Control: no-cache\r\n";
        $header .= "Content-Length: " . strlen($content) . "\r\n";
        $header .= "\r\n";

        return $header;
    }
}

<?php

namespace Hoptoad\HoptoadBundle\Client;

use Hoptoad\HoptoadBundle\Client\ClientInterface;

class Curl extends BaseClient implements ClientInterface
{
    public function __construct()
    {
        $this->client = curl_init();
    }
    
    public function setUrl($url)
    {
        curl_setopt($this->client, CURLOPT_URL, $url);
    }
    
    public function setHeaders(array $headers)
    {
        curl_setopt($this->client, CURLOPT_HTTPHEADER, $this->formatHeaders($headers));
    }
    
    public function setBody($body)
    {
        curl_setopt($this->client, CURLOPT_POSTFIELDS, $body);
    }
    
    public function send()
    {
        curl_setopt($this->client, CURLOPT_POST, 1);
        curl_setopt($this->client, CURLOPT_HEADER, 0);
        curl_setopt($this->client, CURLOPT_TIMEOUT, 2);
        curl_setopt($this->client, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($this->client);
        $response = curl_getinfo($this->client, CURLINFO_HTTP_CODE);
        curl_close($this->client);
        return $response;
    }
}
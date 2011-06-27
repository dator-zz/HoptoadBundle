<?php

namespace Hoptoad\HoptoadBundle\Client;

use Hoptoad\HoptoadBundle\Client\ClientInterface;
use Hoptoad\HoptoadBundle\Client\BaseClient;

use Zend\Http\Client;

class Zend extends BaseClient implements ClientInterface
{
    public function __construct()
    {
        $this->client = new Client();
    }
    
    public function setUrl($url)
    {
        $this->client->setUri($url);
    }
    
    public function setHeaders(array $headers)
    {
        $this->client->setHeaders($this->formatHeaders($headers));
    }
    
    public function setBody($body)
    {
        $this->client->setRawData($body);
    }
    
    public function send()
    {
        $response = $this->client->request('POST');
        return $response->getStatus();
    }
}
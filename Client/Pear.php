<?php

namespace Hoptoad\HoptoadBundle\Client;

use Hoptoad\HoptoadBundle\Client\ClientInterface;

class Pear extends BaseClient implements ClientInterface
{
    public function __construct()
    {
        if (!class_exists('HTTP_Request2') || !class_exists('HTTP_Request2_Adapter_Socket')){
            throw new \Exception('HTTP_Request2 or HTTP_Request2_Adapter_Socket not found');
        }
        
        $this->client = new \HTTP_Request2();
        $this->client->setMethod(\HTTP_Request2::METHOD_POST);
        $this->client->setAdapter(new \HTTP_Request2_Adapter_Socket());
    }
    
    public function setUrl($url)
    {
        $this->client->setUrl($url);
    }
    
    public function setHeaders(array $headers)
    {
        $this->client->setHeader($headers);
    }
    
    public function setBody($body)
    {
        $this->client->setBody($body);
    }
    
    public function send()
    {
        $response = $this->client->send()->getStatus();
        return $response;
    }
}
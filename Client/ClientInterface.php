<?php

namespace Hoptoad\HoptoadBundle\Client;

interface ClientInterface 
{
    public function setUrl($url);
    public function setHeaders(array $headers);
    public function setBody($body);
    public function send();
}
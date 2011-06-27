<?php

namespace Hoptoad\HoptoadBundle\Client;

abstract class BaseClient 
{
    protected $client;
    
    public function formatHeaders(array $headers)
    {
        $formattedHeaders = array();
        foreach ($headers as $key => $val) {
            $formattedHeaders[] = "{$key}: {$val}";
        }
        
        return $formattedHeaders;
    }
}
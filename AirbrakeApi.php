<?php

namespace Airbrake\AirbrakeBundle;

use Symfony\Component\EventDispatcher\Event;
/**
 * Airbrake API 
 *
 *
 * @author Clément JOBEILI (clement.jobeili@gmail.com)
 * @author Rich Cavanaugh (no@email)
 * @author Till Klampaeckel (till@php.net)
 * 
 * @see Zend\Http\Client
 */
 
class AirbrakeApi
{
    const NOTIFIER_NAME         = 'AirbrakeBundle';
    const NOTIFIER_VERSION      = '2.0';
    const NOTIFIER_URL          = 'http://github.com/danielknell/AirbrakeBundle';
    const NOTIFIER_API_VERSION  = '0.1';
    
    const EXCEPTION_SSL         = '403';
    const EXCEPTION_NOTICE      = '422';
    const EXCEPTION_ERROR       = '500';
    
    /**
     * @var Symfony\Component\EventDispatcher\Event $event the event dispatched by Symfony
     */
    public $event;
    
    /**
     * @var array $options the options array (key, env)
     */
    public $options;
    
    protected $clients = array('curl', 'zend', 'pear');
    
    protected $defaults = array(
        'client'    => 'curl',
        'env'       => 'dev'
    );
    
    public function __construct(array $parameters)
    {
        $this->options = array_merge($this->defaults, $parameters);
    }
    
    public function setEvent(Event $event)
    {
        $this->event = $event;
    }
    
    public function getEvent()
    {
        return $this->event;
    }
    
    public function getClient()
    {
        $client = $this->options['client'];
        if(in_array(strtolower($client), $this->clients)){
            $class = 'Airbrake\\AirbrakeBundle\\Client\\'. ucfirst($client);
            return new $class;
        }else{
            throw new \Exception(sprintf('The client %s is not supported by AirbrakeBundle ', $client));
        }
    }
    
    /**
     * Notify the error to airbrake
     * 
     * Raise 4 exceptions:
     *  - if Zend2.0 is not in the autoload
     *  - if the response status is 403
     *  - if the response status is 422
     *  - if the response status is 500
     * 
     */ 
    public function notify()
    {
        $url = "http://airbrakeapp.com/notifier_api/v2/notices";
        $headers = array(
            'Accept'        => 'text/xml, application/xml',
            'Content-Type'  => 'text/xml'
        );
        $body = $this->build();
        
        $client = $this->getClient();
        $client->setUrl($url);
        $client->setHeaders($headers);
        $client->setBody($body);
        
        $response = $client->send();
        
        if($response == self::EXCEPTION_SSL){
            throw new \Exception('The requested project does not support SSL');
        }else if($response == self::EXCEPTION_NOTICE){
            throw new \Exception('The submitted notice was invalid - check the xml and ensure the API key is correct');
        }else if($response == self::EXCEPTION_ERROR){
            throw new \Exception('Unexpected errors - submit a bug report at http://help.airbrakeapp.com');
        }
        
        return $response;
    }
    
    
    /**
     * Build the xml notice
     */ 
    protected function build()
    {
        $event      = $this->getEvent();
        $exception  = $this->getEvent()->getException();
        $request    = $this->getEvent()->getRequest();
        $parameters = $request->attributes;

        $doc = new \SimpleXMLElement('<notice />');
        $doc->addAttribute('version', self::NOTIFIER_API_VERSION);
        $doc->addChild('api-key', htmlspecialchars($this->options['key'], ENT_NOQUOTES));
        
        $notifier = $doc->addChild('notifier');
        $notifier->addChild('name', self::NOTIFIER_NAME);
        $notifier->addChild('version', self::NOTIFIER_VERSION);
        $notifier->addChild('url', self::NOTIFIER_URL);
        
        $error = $doc->addChild('error');
        $error->addChild('class', get_class($exception));
        $error->addChild('message', htmlspecialchars($exception->getMessage(), ENT_NOQUOTES));
        $this->addXmlBacktrace($error, $exception);
        
        $env = $doc->addChild('server-environment');
        $env->addChild('project-root', htmlspecialchars($request->server->get('DOCUMENT_ROOT'), ENT_NOQUOTES));
        $env->addChild('environment-name', htmlspecialchars($this->options['env'], ENT_NOQUOTES));

        $rq = $doc->addChild('request');
        $rq->addChild('url', htmlspecialchars($request->getRequestUri(), ENT_NOQUOTES));
        $rq->addChild('component', '');
        $rq->addChild('action', htmlspecialchars($request->attributes->get('_controller'), ENT_NOQUOTES));
        
        $this->addXmlVars($rq, 'params', $request->query->all());  
        $this->addXmlVars($rq, 'session', $request->getSession()->getAttributes());
        $this->addXmlVars($rq, 'cgi-data', $request->server->all());   
        
        return $doc->asXML();
    }
    
    /**
     * Format an array in XML
     */
    protected function addXmlVars($parent, $key, $source)
    {
        if (empty($source)) return;

        $node = $parent->addChild($key);
        foreach ($source as $key => $val) {
            if (is_array($val)) {
                $val = str_replace("\n", ' ', print_r($val, true));
            }
            if (is_array($key)) {
                $key = str_replace("\n", ' ', print_r($key, true));
            }

            $var_node = $node->addChild('var', htmlspecialchars($val, ENT_NOQUOTES));
            $var_node->addAttribute('key', $key);
        }
    }
    
    /**
     * Format the exception stacktrace in XML
     */ 
    protected function addXmlBacktrace($parent, $exception)
    {
        $backtrace = $parent->addChild('backtrace');

        foreach ($exception->getTrace() as $entry) {
            $line_node = $backtrace->addChild('line');
            if (!isset($entry['file'])) {
                $entry['file'] = $entry['class'];
            }
            if (!isset($entry['line'])) {
              $entry['line'] = '?';
            }
            if (!isset($entry['function'])) {
              $entry['function'] = '?';
            }
            $line_node->addAttribute('file', $entry['file']);
            $line_node->addAttribute('number', $entry['line']);
            $line_node->addAttribute('method', $entry['function']);
        }
    }
}

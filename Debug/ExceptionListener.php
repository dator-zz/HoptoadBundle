<?php

namespace Airbrake\AirbrakeBundle\Debug;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Events;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

use Airbrake\AirbrakeBundle\AirbrakeApi;

/*
 * This file is part of the Symfony framework.
 *
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

/**
 * ExceptionListener.
 *
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 */
class ExceptionListener
{
    protected $airbrake;
    
    public function __construct(AirbrakeApi $airbrake)
    {
        $this->airbrake = $airbrake;
    } 

    /**
     * Registers an onCoreException listener.
     *
     * @param EventDispatcher $dispatcher An EventDispatcher instance
     * @param integer         $priority   The priority
     */
    public function register(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addListener(Events::onCoreException, $this);
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->airbrake->setEvent($event);
        try{
            $this->airbrake->notify();
        }catch(\Exception $e){
           throw new \RuntimeException("AirbrakeBundle failed: ".$e->getMessage());
        }
        
        return false;
    }
}

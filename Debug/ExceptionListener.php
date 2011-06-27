<?php

namespace Hoptoad\HoptoadBundle\Debug;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Events;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

use Hoptoad\HoptoadBundle\HoptoadApi;

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
    protected $hoptoad;
    
    public function __construct(HoptoadApi $hoptoad)
    {
        $this->hoptoad = $hoptoad;
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

    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $this->hoptoad->setEvent($event);
        try{
            $this->hoptoad->notify();
        }catch(\Exception $e){
           throw new \RuntimeException("HoptoadBundle failed: ".$e->getMessage());
        }
        
        return false;
    }
}

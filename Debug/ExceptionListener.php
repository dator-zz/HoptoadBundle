<?php

namespace Bundle\HoptoadBundle\Debug;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;
use Bundle\HoptoadBundle\HoptoadApi;

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
     * Registers a core.exception listener.
     *
     * @param EventDispatcher $dispatcher An EventDispatcher instance
     * @param integer         $priority   The priority
     */
    public function register(EventDispatcher $dispatcher, $priority = 0)
    {
        $dispatcher->connect('core.exception', array($this, 'handle'), $priority);
    }

    public function handle(Event $event)
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

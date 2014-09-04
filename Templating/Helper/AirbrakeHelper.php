<?php

namespace Airbrake\AirbrakeBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;

class AirbrakeHelper extends Helper
{
    const FORMAT = <<<HTML
    <script src="%s" type="text/javascript">
      
    </script>
    <script type="text/javascript">
    Airbrake.setKey('%s');
    Airbrake.setEnvironment(%s)
    </script>
HTML;

    protected $options;

    public function __construct(array $parameters)
    {
        $this->options = $parameters;
    }
    public function initialize()
    {
        return sprintf(static::FORMAT, 
            '//airbrakeapp.com/javascripts/notifier.js',
            $this->options['key'],
            $this->options['env']
            );
    }

    public function getName()
    {
        return 'airbrake';
    }
}

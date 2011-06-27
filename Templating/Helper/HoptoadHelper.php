<?php

namespace Hoptoad\HoptoadBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;

class HoptoadHelper extends Helper
{
    const FORMAT = <<<HTML
    <script src="%s" type="text/javascript">
      
    </script>
    <script type="text/javascript">
    Hoptoad.setKey('%s');
    Hoptoad.setEnvironment(%s)
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
            'http://hoptoadapp.com/javascripts/notifier.js',
            $this->options['key'],
            $this->options['env']
            );
    }

    public function getName()
    {
        return 'hoptoad';
    }
}

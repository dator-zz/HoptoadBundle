<?php

namespace Hoptoad\HoptoadBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;


class HoptoadExtension extends Extension
{

    /**
     * Loads the web configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function load(array $config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('hoptoad.config')) {
            $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('services.xml');
        }
       
        foreach ($config as $temp)
        { 
          foreach ($temp as $key => $value)
          {
              if (in_array($key, array('key', 'client'))) {
                  $parameters[$key] = $value;
              }
          }
        }

        $parameters['env'] = $container->getParameterBag()->get('kernel.environment');

        $container->getDefinition('hoptoad.api')->addArgument($parameters);
        $container->getDefinition('hoptoad.helper')->addArgument($parameters);
    }

    /**
     * Returns the base path for the XSD files.
     *
     * @return string The XSD base path
     */
    public function getXsdValidationBasePath()
    {
        return null;
    }

    public function getNamespace()
    {
        return 'hoptoad.config';
    }

    public function getAlias()
    {
        return 'hoptoad';
    }

}

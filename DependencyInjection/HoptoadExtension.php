<?php

namespace Bundle\HoptoadBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HoptoadExtension extends Extension
{

    /**
     * Loads the web configuration.
     *
     * @param array            $config    An array of configuration settings
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function configLoad($config, ContainerBuilder $container)
    {
        if (!$container->hasDefinition('hoptoad.config')) {
            $loader = new XmlFileLoader($container, __DIR__.'/../Resources/config');
            $loader->load('config.xml');
        }
        
        foreach (array('key', 'client') as $key) {
            if (isset($config[$key])) {
                $parameters[$key] = $config[$key];
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
        return 'http://www.symfony-project.org/schema/dic/symfony';
    }

    public function getAlias()
    {
        return 'hoptoad';
    }

}

<?php

namespace ContainerNHe9sZt;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class get_ServiceLocator_WcPH6MXService extends App_KernelDevDebugContainer
{
    /**
     * Gets the private '.service_locator.WcPH6MX' shared service.
     *
     * @return \Symfony\Component\DependencyInjection\ServiceLocator
     */
    public static function do($container, $lazyLoad = true)
    {
        return $container->privates['.service_locator.WcPH6MX'] = new \Symfony\Component\DependencyInjection\Argument\ServiceLocator($container->getService, [
            'ticket' => ['privates', '.errored..service_locator.WcPH6MX.App\\Entity\\Ticket', NULL, 'Cannot autowire service ".service_locator.WcPH6MX": it references class "App\\Entity\\Ticket" but no such service exists.'],
        ], [
            'ticket' => 'App\\Entity\\Ticket',
        ]);
    }
}

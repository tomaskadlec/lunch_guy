<?php
namespace Net\TomasKadlec\LunchGuy\BaseBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class OutputCompilerPass
 *
 * Pass is used to register services as Outputs
 *
 * @package Net\TomasKadlec\LunchGuy\BaseBundle\DependencyInjection\Compiler
 */
class OutputCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('net_tomas_kadlec_lunch_guy_base.service.output')) {
            return;
        }

        $definition = $container->findDefinition(
            'net_tomas_kadlec_lunch_guy_base.service.output'
        );

        $serviceIds = $container->findTaggedServiceIds(
            'net_tomas_kadlec_lunch_guy_base.service.output'
        );

        foreach ($serviceIds as $serviceId => $tags) {
            $definition->addMethodCall(
                'add',
                array(new Reference($serviceId))
            );
        }
    }


}
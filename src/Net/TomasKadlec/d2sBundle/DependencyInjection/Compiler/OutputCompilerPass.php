<?php
namespace Net\TomasKadlec\d2sBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class OutputCompilerPass
 *
 * Pass is used to register services as Outputs
 *
 * @package Net\TomasKadlec\d2sBundle\DependencyInjection\Compiler
 */
class OutputCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('net_tomas_kadlec_d2s.service.output')) {
            return;
        }

        $definition = $container->findDefinition(
            'net_tomas_kadlec_d2s.service.output'
        );

        $serviceIds = $container->findTaggedServiceIds(
            'net_tomas_kadlec_d2s.service.output'
        );

        foreach ($serviceIds as $serviceId => $tags) {
            $definition->addMethodCall(
                'add',
                array(new Reference($serviceId))
            );
        }
    }


}
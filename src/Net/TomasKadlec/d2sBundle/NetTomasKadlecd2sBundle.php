<?php
namespace Net\TomasKadlec\d2sBundle;

use Net\TomasKadlec\d2sBundle\DependencyInjection\Compiler\OutputCompilerPass;
use Net\TomasKadlec\d2sBundle\DependencyInjection\Compiler\ParserCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class NetTomasKadlecd2sBundle
 * @package Net\TomasKadlec\d2sBundle
 */
class NetTomasKadlecd2sBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ParserCompilerPass());
        $container->addCompilerPass(new OutputCompilerPass());
    }

}

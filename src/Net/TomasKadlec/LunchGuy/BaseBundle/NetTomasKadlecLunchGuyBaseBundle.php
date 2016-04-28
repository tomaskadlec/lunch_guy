<?php

namespace Net\TomasKadlec\LunchGuy\BaseBundle;

use Net\TomasKadlec\LunchGuy\BaseBundle\DependencyInjection\Compiler\OutputCompilerPass;
use Net\TomasKadlec\LunchGuy\BaseBundle\DependencyInjection\Compiler\ParserCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetTomasKadlecLunchGuyBaseBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ParserCompilerPass());
        $container->addCompilerPass(new OutputCompilerPass());
    }


}

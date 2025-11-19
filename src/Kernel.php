<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

final class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass($this, PassConfig::TYPE_AFTER_REMOVING, -10000);
    }

    public function process(ContainerBuilder $container): void
    {
        static $booted;

        if (!$booted) {
            $booted = true;
            return;
        }

        echo "Building container\n";
        $f = $this->getCacheDir().'/local_services';
        $sourceFile = $f.'.dot';
        \file_put_contents($f.'.dot', (new LocalServicesDumper($container))->dump());
        system('echo "Dumping to dot..." && dot -Tpng '.$sourceFile.' > '.$f.'_dot.png');
        system('echo "Dumping to fdp..." && fdp -Tpng '.$sourceFile.' > '.$f.'_fdp.png');
        system('echo "Dumping to neato..." && neato -Tpng '.$sourceFile.' > '.$f.'_neato.png');
//        system('echo "Dumping to circo..." && circo -Tpng '.$sourceFile.' > '.$f.'_circo.png');

        $booted = false;
    }
}

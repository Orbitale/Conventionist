<?php

namespace App;

use Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use Symfony\Component\DependencyInjection\Argument\BoundArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use \Symfony\Component\DependencyInjection\Dumper\Dumper;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;

class LocalServicesDumperBackup extends Dumper
{
    private array $classSourceChecks = [];

    private array $nodes;

    private array $edges;

    // All values should be strings
    private array $options = [
        'graph' => ['ratio' => 'compress'],
        'node' => ['fontsize' => '11', 'fontname' => 'Arial', 'shape' => 'record'],
        'edge' => ['fontsize' => '9', 'fontname' => 'Arial', 'color' => 'grey', 'arrowhead' => 'open', 'arrowsize' => '0.5'],
        'node.instance' => ['fillcolor' => '#9999ff', 'style' => 'filled'],
        'node.definition' => ['fillcolor' => '#eeeeee'],
        'node.missing' => ['fillcolor' => '#ff9999', 'style' => 'filled'],
    ];

    private string $srcDir;

    public function __construct(
        protected ContainerBuilder $container,
    ) {
        parent::__construct($this->container);

        $this->srcDir = $this->container->getParameter('kernel.project_dir').'/src';
    }

    public function dump(array $options = []): string
    {
        $data = $this->doDump($options);

        return $data;
    }

    private function doDump(array $options)
    {
        foreach (['graph', 'node', 'edge', 'node.instance', 'node.definition', 'node.missing'] as $key) {
            if (isset($options[$key])) {
                $this->options[$key] = array_merge($this->options[$key], $options[$key]);
            }
        }

        $smallerContainer = new ContainerBuilder($this->container->getParameterBag());

        $ignoredServices = [
            'kernel',
            'service_container',
        ];

        $noClass = [];
        $notSources = [];
        $sourceServices = [];
        $sourceDependencies = [];

        // Retrieving source classes
        foreach ($this->container->getDefinitions() as $id => $definition) {
            if (\in_array($id, $ignoredServices, true)) {
                continue;
            }

            $class = $definition->getClass();
            if (!$class) {
                // No class = service name that's probably synthetic.
                // Usually applies to kernel, service_container, and some others.
                $noClass[$id] = $definition;
                continue;
            }
            if (
                $class &&
                !$this->isSourceClass($class)) {
                $notSources[$id] = $definition;
                continue;
            }
            if (\str_starts_with($id, '.abstract')) {
                // Abstract instances registered automatically by Symfony. Should be removed at the end.
                continue;
            }

            foreach ($definition->getArguments() as $argName => $argument) {
                dd($id, $argName, $argument);
            }
            foreach ($definition->getBindings() as $bindingName => $binding) {
                $bindingValues = $binding->getValues();
                $used = $bindingValues[2] ?? false;
                if (!$used) {
                    continue;
                }
                $type = $bindingValues[3] ?? null;
                if ($type === BoundArgument::SERVICE_BINDING) {
                    $bindingValue = $bindingValues[0] ?? null;
                    $sourceDependencies[$bindingName] = $bindingValue;
                } else {
                    dd('binding', $bindingName, $binding);
                }
            }

            foreach ($definition->getProperties() as $propName => $property) {
                dd('prop', $propName, $property);
            }

            foreach ($definition->getMethodCalls() as [$method, $calls]) {
                foreach ($calls as $args) {
                    if ($args instanceof Reference) {
                        $sourceDependencies[] = $args;
                        continue;
                    }

                    if (is_iterable($args)) {
                        foreach ($args as $arg) {
                            if ($arg instanceof Reference) {
                                $sourceDependencies[] = $arg;
                            }
                        }
                        continue;
                    }
                }
            }

            $smallerContainer->setDefinition($id, $definition);
            $sourceServices[$id] = $definition;
        }

//        // Retrieving source classes dependencies
//        foreach ($this->container->getDefinitions() as $id => $definition) {
//            //
//        }

        dd($sourceDependencies);

        return '';
    }


    private function isSourceClass(string $class): bool
    {
        if (!isset($this->classSourceChecks[$class])) {
            try {
                $refl = new \ReflectionClass($class);
            } catch (\ReflectionException) {
                // Non-existent class
                return false;
            }
            $classPath = $refl->getFileName();

            $this->classSourceChecks[$class] = \str_starts_with($classPath, $this->srcDir);
        }

        return $this->classSourceChecks[$class];
    }
}

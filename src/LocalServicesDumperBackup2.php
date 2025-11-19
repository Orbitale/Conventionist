<?php

namespace App;

use Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use \Symfony\Component\DependencyInjection\Dumper\Dumper;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;

class LocalServicesDumperBackup2 extends Dumper
{
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

    private array $classSourceChecks = [];

    public function __construct(
        protected ContainerBuilder $container,
    ) {
        parent::__construct($this->container);

        $this->srcDir = $this->container->getParameter('kernel.project_dir').'/src';
    }

    /**
     * Dumps the service container as a graphviz graph.
     *
     * Available options:
     *
     *  * graph: The default options for the whole graph
     *  * node: The default options for nodes
     *  * edge: The default options for edges
     *  * node.instance: The default options for services that are defined directly by object instances
     *  * node.definition: The default options for services that are defined via service definition instances
     *  * node.missing: The default options for missing services
     */
    public function dump(array $options = []): string
    {
        $this->classSourceChecks = [];
        $this->nodes = [];
        $this->edges = [];

        foreach (['graph', 'node', 'edge', 'node.instance', 'node.definition', 'node.missing'] as $key) {
            if (isset($options[$key])) {
                $this->options[$key] = array_merge($this->options[$key], $options[$key]);
            }
        }

        $this->nodes = $this->findNodes();

        $this->edges = [];
        foreach ($this->container->getDefinitions() as $id => $definition) {
            $this->edges[$id] = array_merge(
                $this->findEdges($id, $definition->getArguments(), true, ''),
                $this->findEdges($id, $definition->getProperties(), false, '')
            );

            foreach ($definition->getMethodCalls() as $call) {
                $this->edges[$id] = array_merge(
                    $this->edges[$id],
                    $this->findEdges($id, $call[1], false, $call[0].'()')
                );
            }
        }

        $startDot = $this->startDot();
        $addNodes = $this->addNodes();
        $addEdges = $this->addEdges();
        $endDot = $this->endDot();
        return $this->container->resolveEnvPlaceholders($startDot.$addNodes.$addEdges.$endDot, '__ENV_%s__');
    }

    private function addNodes(): string
    {
        $code = '';
        foreach ($this->nodes as $id => $node) {
            $code .= \sprintf("  %s ;\n",
                $this->dotize($id),
            );
        }

        return $code;
    }

    private function addEdges(): string
    {
        $code = '';
        foreach ($this->edges as $id => $edges) {
            if (isset($edges['to'])) {
                $edges = [$edges];
            }
            foreach ($edges as $edge) {
                if (!$edge['name']) {
                    continue;
                }
                $code .= \sprintf("  %s -> %s ;\n",
                    $this->dotize($id),
                    $edge['name'] ?: ' Standalone ',
                );
            }
        }

        return $code;
    }

    /**
     * Finds all edges belonging to a specific service id.
     */
    private function findEdges(string $id, array $arguments, bool $required, string $name, bool $lazy = false): array
    {
        $edges = [];
        foreach ($arguments as $argument) {
            $newEdges = [];

            if ($argument instanceof Parameter) {
                $argument = $this->container->hasParameter($argument) ? $this->container->getParameter($argument) : null;
            } elseif (\is_string($argument) && preg_match('/^%([^%]+)%$/', $argument, $match)) {
                $argument = $this->container->hasParameter($match[1]) ? $this->container->getParameter($match[1]) : null;
            }

            if ($argument instanceof Reference) {
                $lazyEdge = $lazy;

                if (!$this->container->has((string) $argument)) {
                    $this->nodes[(string) $argument] = ['name' => $name, 'required' => $required, 'class' => '', 'attributes' => $this->options['node.missing']];
                } elseif ('service_container' !== (string) $argument) {
                    try {
                        $lazyEdge = $lazy || $this->container->getDefinition((string)$argument)->isLazy();
                    } catch (ServiceNotFoundException) {
                    }
                }

                $newEdges[] = [['name' => $name, 'required' => $required, 'to' => $argument, 'lazy' => $lazyEdge]];
            } elseif ($argument instanceof ArgumentInterface) {
                $newEdges[] = $this->findEdges($id, $argument->getValues(), $required, $name, true);
            } elseif ($argument instanceof Definition) {
                $newEdges[] = $this->findEdges($id, $argument->getArguments(), $required, '');
                $newEdges[] = $this->findEdges($id, $argument->getProperties(), false, '');

                foreach ($argument->getMethodCalls() as $call) {
                    $newEdges[] = $this->findEdges($id, $call[1], false, $call[0].'()');
                }
            } elseif (\is_array($argument)) {
                $newEdges[] = $this->findEdges($id, $argument, $required, $name, $lazy);
            }

            $edgesToAdd = [];
            foreach ($newEdges as $e) {
                if (isset($e['to'])) {
                    $e = [$e];
                }
                foreach ($e as $edge) {
                    $to = $edge['to'];
                    if (!$to) {
                        dd('edge does not have to:',$edge);
                    }
                    if ($to instanceof Reference) {
                        $id = (string) $to;
                        if (!$this->container->hasDefinition((string) $to)) {
                            continue;
                        }
                        $service = $this->container->getDefinition($id);
                        if (($class = $service->getClass()) && $this->isSourceClass($class)) {
                            $edgesToAdd[] = $edge;
                        }
                    } else {
                        dd('to:', $edge);
                    }
                }
            }
            if (count($edgesToAdd)) {
//                dd('$edgesToAdd', $edgesToAdd);
            }
            foreach ($edgesToAdd as $edge) {
                $edges[] = $edge;
            }
        }

        return array_merge([], ...$edges);
    }

    private function findNodes(): array
    {
        $nodes = [];

        $container = $this->cloneContainer();

        foreach ($container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();

            if (str_starts_with($class, '\\')) {
                $class = substr($class, 1);
            }

            try {
                $class = $this->container->getParameterBag()->resolveValue($class);
            } catch (ParameterNotFoundException) {
            }

            if ($class && !$this->isSourceClass($class)) {
                continue;
            }

            $nodes[$id] = ['class' => str_replace('\\', '\\\\', $class), 'attributes' => array_merge($this->options['node.definition'], ['style' => $definition->isShared() ? 'filled' : 'dotted'])];
            $container->setDefinition($id, new Definition('stdClass'));
        }

        foreach ($container->getServiceIds() as $id) {
            if (\array_key_exists($id, $container->getAliases())) {
                continue;
            }

            if (!$container->hasDefinition($id)) {
                $nodes[$id] = ['class' => str_replace('\\', '\\\\', $container->get($id)::class), 'attributes' => $this->options['node.instance']];
            }
        }

        return $nodes;
    }

    private function cloneContainer(): ContainerBuilder
    {
        $parameterBag = new ParameterBag($this->container->getParameterBag()->all());

        $container = new ContainerBuilder($parameterBag);
        $container->setDefinitions($this->container->getDefinitions());
        $container->setAliases($this->container->getAliases());
        $container->setResources($this->container->getResources());
        foreach ($this->container->getExtensions() as $extension) {
            $container->registerExtension($extension);
        }

        return $container;
    }

    private function startDot(): string
    {
        return \sprintf("digraph sc {\n  %s\n  node [%s];\n  edge [%s];\n\n",
            $this->addOptions($this->options['graph']),
            $this->addOptions($this->options['node']),
            $this->addOptions($this->options['edge'])
        );
    }

    private function endDot(): string
    {
        return "}\n";
    }

    private function addAttributes(array $attributes): string
    {
        $code = [];
        foreach ($attributes as $k => $v) {
            $code[] = \sprintf('%s="%s"', $k, $v);
        }

        return $code ? ', '.implode(', ', $code) : '';
    }

    private function addOptions(array $options): string
    {
        $code = [];
        foreach ($options as $k => $v) {
            $code[] = \sprintf('%s="%s"', $k, $v);
        }

        return implode(' ', $code);
    }

    private function dotize(string $id): string
    {
        $id = preg_replace('~(\\n|\s).*$~isUu', '', $id);
        $id = preg_replace('~\W~i', '_', $id);
        return trim($id);
    }

    private function getAliases(string $id): array
    {
        $aliases = [];
        foreach ($this->container->getAliases() as $alias => $origin) {
            if ($id == $origin) {
                $aliases[] = $alias;
            }
        }

        return $aliases;
    }

    private function isSourceClass(string $class): bool
    {
        if ($class === Kernel::class) {
            return false;
        }

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

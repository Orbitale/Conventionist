<?php

namespace App;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use \Symfony\Component\DependencyInjection\Dumper\Dumper;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\TypedReference;

class LocalServicesDumper extends Dumper
{
    private array $appSourceServices = [];
    private array $edges = [];

    private array $controllers = [];
    private array $repositories = [];
    private array $commands = [];
    private array $externalServices = [];

    private string $srcDir;

    private array $classSourceChecks = [];
    private int $level = 0;

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
        $this->edges = [];
        $this->appSourceServices = [];

        $this->getSourceServices();
        $this->getControllers();
        $this->getRepositories();
        $this->getCommands();
        $this->getExternalServices();

        $content = $this->getDotContent();

        $this->appSourceServices =
        $this->edges =
        $this->controllers =
        $this->repositories =
        $this->commands =
        $this->externalServices =
            [];

        return $content;
    }

    private function getSourceServices(): void
    {
        \HaydenPierce\ClassFinder\ClassFinder::disablePSR4Vendors();
        $classes = \HaydenPierce\ClassFinder\ClassFinder::getClassesInNamespace(
            'App',
            \HaydenPierce\ClassFinder\ClassFinder::RECURSIVE_MODE,
        );

        $definitions = [];
        foreach ($classes as $class) {
            if (!$this->container->hasDefinition($class)) {
                dump('no definition for class '.$class);
                continue;
            }
            $definition = $this->container->getDefinition($class);
            $definitions[$class] = $definition;
            $this->appSourceServices[$class] = $class;

            $dependencies = $this->getDependenciesNodes($definition);
            foreach ($dependencies as $dependency) {
                $this->edges[$class][] = $dependency;
            }
        }

        // TODO: double-check with container what's already sets

        foreach ($this->container->getDefinitions() as $id => $definition) {
            $class = $definition->getClass();

            if (!$class || !$this->isSourceClass($class)) {
                continue;
            }
            if (isset($definitions[$class])) {
                continue;
            }
            if (\str_starts_with($id, '.abstract')) {
                continue;
            }
            $dependencies = $this->getDependenciesNodes($definition);
            foreach ($dependencies as $dependency) {
                $this->edges[$class][] = $dependency;
            }

            $this->appSourceServices[$class] = $class;
        }
    }

    private function isSourceClass(string $class): bool
    {
        if ($class === Kernel::class) {
            return false;
        }

        try {
            $refl = new \ReflectionClass($class);
        } catch (\ReflectionException) {
            // Non-existent class
            return false;
        }
        $classPath = $refl->getFileName();

        $isSourcePath = \str_starts_with($classPath, $this->srcDir);

        if (!$isSourcePath) {
            echo $class."\n";
        }

        return $isSourcePath;
    }

    private function getDependenciesNodes(Definition $definition): array
    {
        $class = $definition->getClass();

        if (!$class) {
            return [];
        }

        return [
            ...$this->getDependenciesFromArguments($definition),
            ...$this->getDependenciesFromBindings($definition)
        ];
    }

    /**
     * @param Definition $definition
     * @return array
     */
    public function getDependenciesFromArguments(Definition $definition): array
    {
        $dependencies = [];

        foreach ($definition->getArguments() as $argId => $arg) {
            if (\is_string($arg)) {
                // Raw strings, or kernel parameters.
                continue;
            }
            if (\str_starts_with($argId, '.service_locator')) {
                // TODO: add all services in this locator as dependency
                //dump('service locator::'.__LINE__);
                continue;
            }

            if ($arg instanceof Reference) {
                $dependencyId = (string) $arg;
                if (\str_starts_with($dependencyId, '.service_locator')) {
                    // TODO: add all services in this locator as dependency
                    //dump('service locator::'.__LINE__);
                    continue;
                }
                if ($arg instanceof TypedReference) {
                    $dependencyId = $arg->getType();
                }
                $dependencies[] = $dependencyId;

            } elseif ($arg instanceof Definition) {
                $class = $arg->getClass();

                if (!$class || !$this->isSourceClass($class)) {
                    continue;
                }

                $dependencies[] = $arg->getClass() ?: 'Unidentified service class';

            } else {
                dd('Error: arg cannot be handled, got "' . get_debug_type($arg) . '":', $argId, $arg);
            }
        }

        return $dependencies;
    }

    public function getDependenciesFromBindings(Definition $definition): array
    {
        $dependencies = [];

        foreach ($definition->getBindings() as $binding) {
            [$value, $identifier, $used, $type, $file] = $binding->getValues();

            if (!$used) {
                continue;
            }

            if ($type !== $binding::SERVICE_BINDING) {
                continue;
            }

            if ($value instanceof Reference) {
                $refId = (string)$value;
                if (\str_starts_with($refId, '.service_locator')) {
                    // TODO: add all services in this locator as dependency
                    //dump('service locator::'.__LINE__);
                    continue;
                }
                if ($value instanceof TypedReference) {
                    $refId = $value->getType();
                }
                $dependencies[] = $refId;
            }
        }

        return $dependencies;
    }

    private function dotize(string $id): string
    {
        $id = preg_replace('~(\\n|\s).*$~isUu', '', $id);
        $id = preg_replace('~\W~i', '_', $id);
        return trim($id);
    }

    private function s(): string
    {
        return $this->level ? str_repeat('  ', $this->level) : '';
    }

    private function getDotContent(): string
    {
        $this->level = 0;

        $content = $this->line("graph Services {");

        ++$this->level;

        $content .= $this->line('fontname="Helvetica,Arial,sans-serif"');
        $content .= $this->line('node [fontname="Helvetica,Arial,sans-serif"]');
        $content .= $this->line('edge [fontname="Helvetica,Arial,sans-serif"]');

        $content .= $this->getDotSubgraph('externalservices', $this->externalServices, '#ff9999', 'white');
        $content .= $this->getDotSubgraph('controllers', $this->controllers, '#99ff99', 'white');
        $content .= $this->getDotSubgraph('repositories', $this->repositories, '#9999ff', 'white');
        $content .= $this->getDotSubgraph('commands', $this->commands, '#ff99ff', 'white');

        foreach ($this->edges as $from => $edges) {
            if (
                isset(
                    $this->controllers[$from],
                    $this->repositories[$from],
                    $this->commands[$from],
                    $this->externalServices[$from],
                )
                || \in_array($from, $this->controllers, true)
                || \in_array($from, $this->repositories, true)
                || \in_array($from, $this->commands, true)
                || \in_array($from, $this->externalServices, true)
            ) {
                continue;
            }

            $from = $this->dotize($from);
            $to = count($edges) > 1 ? sprintf('{ %s }', implode(' ', \array_map($this->dotize(...), $edges))) : $this->dotize($edges[0]);
            $content .= $this->line("%s -- %s ;", $from, $to);
        }

        $content .= "\n";

        foreach ($this->appSourceServices as $class) {
            if (
                isset(
                    $this->controllers[$class],
                    $this->repositories[$class],
                    $this->commands[$class],
                    $this->externalServices[$class],
                    $this->edges[$class],
                )
                || \in_array($class, $this->controllers, true)
                || \in_array($class, $this->repositories, true)
                || \in_array($class, $this->commands, true)
                || \in_array($class, $this->externalServices, true)
                || \array_any($this->edges, fn (array $i) => \in_array($class, $i, true) || isset($i[$class]))
            ) {
                continue;
            }

            $content .= $this->line("%s;", $this->dotize($class));
        }

        $content .= "\n";

        --$this->level;
        $content .= $this->line("}");

        return $content;
    }

    private function line(string $line, ...$params): string
    {
        return \sprintf("%s %s\n", $this->s(), \trim(\sprintf($line, ...$params)));
    }

    private function getDotSubgraph(string $label, array $data, string $fillColor, string $nodeColor, bool $cluster = true): string
    {
        if (!$data) {
            return '';
        }

        $content = $this->line("subgraph %s {", $label);
        ++$this->level;

        if ($cluster) {
            $content .= $this->line("cluster=true;");
        }
        $content .= $this->line("style=filled;");
        $content .= $this->line('color="%s";', $fillColor);
        $content .= $this->line('node [style=filled,color=%s];', $nodeColor);
        $content .= $this->line('label = "%s";', $label);
        $content .= "\n";

        foreach ($data as $itemId => $itemClass) {
            if (isset($this->edges[$itemId])) {
                $dependencies = $this->edges[$itemId];

                $content .= $this->line("%s -- %s ;",
                    $this->dotize($itemId),
                    \count($dependencies) > 1 ? \sprintf('{ %s }', \implode(' ', \array_map($this->dotize(...), $dependencies))) : $this->dotize($dependencies[0]),
                );
            } else {
                // No dependencies, usually just a class string
                $content .= $this->line("%s;", $this->dotize($itemClass));
            }
        }

        --$this->level;
        $content .= $this->line("}");
        $content .= "\n";

        return $content;
    }

    private function getControllers(): void
    {
        $this->controllers = [];

        foreach ($this->appSourceServices as $id => $class) {
            if (\str_ends_with($class, 'Controller')) {
                $this->controllers[$id] = $class;
            }
        }
    }

    private function getRepositories(): void
    {
        $this->repositories = [];

        foreach ($this->appSourceServices as $id => $class) {
            if (\str_ends_with($class, 'Repository')) {
                $this->repositories[$id] = $class;
            }
        }
    }

    private function getCommands(): void
    {
        $this->commands = [];

        foreach ($this->appSourceServices as $id => $class) {
            if (\str_ends_with($class, 'Command')) {
                $this->commands[$id] = $class;
            }
        }
    }

    private function getExternalServices(): void
    {
        $this->externalServices = [];

        foreach ($this->edges as $dependencies) {
            foreach ($dependencies as $class) {
                if (\str_starts_with($class, 'App\\')) {
                    continue;
                }
                if (isset($this->edges[$class])) {
                    continue;
                }
                $this->externalServices[$class] = $class;
            }
        }
    }
}

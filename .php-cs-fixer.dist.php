<?php
/*
 * This document has been generated with
 * https://mlocati.github.io/php-cs-fixer-configurator/#version:3.68.5|configurator
 * you can change this configuration by importing this file.
 */
$config = new PhpCsFixer\Config();
return $config
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'yoda_style' => false,
    ])
    ->setFinder(PhpCsFixer\Finder::create()
        ->in([
            'src',
            'config',
            'migrations',
        ])
        ->exclude(['vendor'])
    )
;

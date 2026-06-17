<?php

$finder = (new PhpCsFixer\Finder())->in([__DIR__ . '/lib', __DIR__ . '/tests']);
return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true
    ])
    ->setFinder($finder);

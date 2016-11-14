<?php

$finder = Symfony\CS\Finder::create()->in(__DIR__ . '/src');
return Symfony\CS\Config::create()->finder($finder);
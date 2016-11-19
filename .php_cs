<?php

$finder = Symfony\CS\Finder::create()->in([__DIR__ . '/src', __DIR__ . '/tests']);
return Symfony\CS\Config::create()->finder($finder);
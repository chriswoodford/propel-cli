<?php

set_include_path(implode(PATH_SEPARATOR, array(
	'/workspace/Doctrine 2.1.6',
	'/workspace/Propel',
	'/workspace/Propel Schema Diff CLI Tool/lib',
	get_include_path(),
)));

require_once 'propel/Propel.php';
require_once 'Doctrine/Common/ClassLoader.php';

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Symfony', 'Doctrine');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Cli');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Propel');
$classLoader->register();

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(

));

\Cli\Console\ConsoleRunner::run($helperSet);

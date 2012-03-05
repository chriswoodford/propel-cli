<?php

require_once 'propel/Propel.php';
require_once 'Doctrine/Common/ClassLoader.php';

$classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('Symfony', 'Doctrine');
$classLoader->register();

$classLoader = new \Doctrine\Common\ClassLoader('PropelCli');
$classLoader->register();

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(

));

\PropelCli\Console\ConsoleRunner::run($helperSet);

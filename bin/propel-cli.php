<?php

require_once 'vendor/autoload.php';

$helperSet = new \Symfony\Component\Console\Helper\HelperSet(array(

));

\PropelCli\Console\ConsoleRunner::run($helperSet);

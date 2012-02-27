<?php

namespace Cli\Console;

use Symfony\Component\Console\Application,
	Symfony\Component\Console\Helper\HelperSet;

/**
 * @category   Cli
 * @package    Cli
 * @copyright  Copyright (c) 2012 ideaPHP (http://www.ideaphp.com)
 */
class ConsoleRunner
{

    /**
     * Run console with the given helperset.
     *
     * @param \Symfony\Component\Console\Helper\HelperSet $helperSet
     * @return void
     */
    public static function run(HelperSet $helperSet)
    {

        $cli = new Application('Propel Command Line Tool', \Propel\Version::VERSION);
        $cli->setCatchExceptions(true);
        $cli->setHelperSet($helperSet);

        self::addCommands($cli);

        $cli->run();

    }

    /**
     * @param Application $cli
     */
    public static function addCommands(Application $cli)
    {

        $cli->addCommands(array(
            new \Cli\Console\Command\SchemaTool\UpdateCommand(),
        ));

    }

}
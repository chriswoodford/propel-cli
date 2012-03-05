<?php

namespace PropelCli;

/**
 * @category   PropelCli
 * @package    PropelCli
 * @copyright  Copyright (c) 2012 ideaPHP (http://www.ideaphp.com)
 */
class Version
{

    /**
     * Current Version
     */
    const VERSION = '1.0.0';

    public static function compare($version)
    {
        $currentVersion = str_replace(' ', '', strtolower(self::VERSION));
        $version = str_replace(' ', '', $version);

        return version_compare($version, $currentVersion);
    }

}

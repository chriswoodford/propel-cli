<?php

namespace PropelCli\Console\Command\SchemaTool;

use Symfony\Component\Console\Command,
	Symfony\Component\Console\Input,
	Symfony\Component\Console\Output,
	Doctrine\DBAL;

/**
 * @category   PropelCli
 * @package    PropelCli\Console\Command\SchemaTool
 * @copyright  Copyright (c) 2012 ideaPHP (http://www.ideaphp.com)
 */
class UpdateCommand extends Command\Command
{

    protected function configure()
    {

        $this
        ->setName('schema-tool:update')
        ->setDescription('Executes (or dumps) the SQL needed to update the database schema to match the current schema.')
        ->setDefinition(array(
        	new Input\InputArgument(
        		'schema-file', Input\InputArgument::REQUIRED,
        		'Path to the propel schema file (schema.xml)', null
        	),
        	new Input\InputArgument(
        		'config-file', Input\InputArgument::REQUIRED,
        		'Path to the propel configuration file (*-conf.php)', null
        	),
            new Input\InputOption(
                'datasource', '-d', Input\InputOption::VALUE_REQUIRED,
                'Index to the datasource array in the propel configuration file.'
            ),
            new Input\InputOption(
                'dump-sql', null, Input\InputOption::VALUE_NONE,
                'Dumps the generated SQL statements to the screen (does not execute them).'
            ),
            new Input\InputOption(
                'force', null, Input\InputOption::VALUE_NONE,
                'Causes the generated SQL statements to be physically executed against your database.'
            ),
            new Input\InputOption(
                'ignore-constraints', null, Input\InputOption::VALUE_NONE,
                'Suppress foreign key constraints from sql.'
            ),
        ))
        ->setHelp(<<<EOT
Generate a set of SQL commands to migrate an existing database to
the state of the supplied Propel schema

EOT
        );

    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Input\InputInterface $input, Output\OutputInterface $output)
    {

    	$schemaFile = $input->getArgument('schema-file');
    	$configFile = $input->getArgument('config-file');
    	$dataSource = $input->getOption('datasource');
    	$ignoreConstraints = $input->getOption('ignore-constraints');

    	// initialize propel
		\Propel::init($configFile);

		// get xml representation of schema file
		$schemaXml = simplexml_load_file($schemaFile);

		// intitialize doctrine DBAL with data from propel
		$config = \PropelCli\Configuration::getDataSourceConfiguration($dataSource, \Propel::getConfiguration());
		$conn = DBAL\DriverManager::getConnection($config->toArray(), new DBAL\Configuration());
		$sm = $conn->getSchemaManager();

		// create a schema of the existing db
		$fromSchema = $sm->createSchema();

		// initialize a schema for the updated db
		$toSchema = new \Doctrine\DBAL\Schema\Schema();

		// generate the schema object
		$generator = new \PropelCli\Schema\Generator($schemaXml);
		$generator->generate($toSchema);

		// generate the sql to migrate from fromSchema to toSchema
		$sql = $fromSchema->getMigrateToSql($toSchema, $conn->getDatabasePlatform());

		if ($input->getOption('dump-sql')) {

			foreach ($sql as $stmt) {

				if ($ignoreConstraints
					&& preg_match('/CONSTRAINT/', $stmt)
				) {
					continue;
				}

				$output->write($stmt . ';' . PHP_EOL);

			}

		}

    }

}

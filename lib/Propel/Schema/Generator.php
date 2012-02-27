<?php

namespace Propel\Schema;

/**
 * @category   Propel
 * @package    Propel\Schema
 * @copyright  Copyright (c) 2012 ideaPHP (http://www.ideaphp.com)
 */
class Generator
{

	/** @var SimpleXmlElement */
	protected $data;

	/** @var array */
	protected $tables;

	/**
	 *
	 * initialize the generator with propel schema data
	 * @param SimpleXmlElement $data
	 */
	public function __construct(\SimpleXmlElement $data)
	{

		$this->data = $data;
		$this->tables = array();

	}

	/**
	 *
	 * convert the propel schema data into a Schema object
	 * @param \Doctrine\DBAL\Schema\Schema $schema
	 */
	public function generate(\Doctrine\DBAL\Schema\Schema $schema)
	{

		foreach ($this->data->table as $tableData) {

			$table = $schema->createTable((string)$tableData['name']);

			$columns = $tableData->column;
			$indexes = $tableData->index;
			$uniqueIndexes = $tableData->unique;

			foreach ($columns as $columnData) {

				$table->addColumn(
					(string)$columnData['name'],
					$this->mapDataType((string)$columnData['type']),
					$this->getColumnOptions($columnData)
				);

				if ((string)$columnData['primaryKey'] == 'true') {
					$table->setPrimaryKey(array((string)$columnData['name']));
				}

			}

			foreach ($indexes as $key => $indexData) {

				$index = $this->processIndex($indexData->{"index-column"});

				if (!empty($index)) {
					$table->addIndex($index);
				}

			}

			foreach ($uniqueIndexes as $indexData) {

				$index = $this->processIndex($indexData->{"unique-column"});

				if (!empty($index)) {
					$table->addUniqueIndex($index);
				}

			}

			$this->tables[(string)$tableData['name']] = $table;

		}

		foreach ($this->data->table as $tableData) {

			$foreignKeys = $tableData->{"foreign-key"};
			$localTable = $this->tables[(string)$tableData['name']];

			foreach ($foreignKeys as $foreignKeyData) {

			    $foreignTableName = (string)$foreignKeyData['foreignTable'];
			    $localColumn = (string)$foreignKeyData->reference['local'];
			    $foreignColumn = (string)$foreignKeyData->reference['foreign'];

			    $foreignTable = $this->tables[$foreignTableName];
			    $localTable->addForeignKeyConstraint(
			        $foreignTable,
			        array($localColumn),
			        array($foreignColumn)
			    );

			}

		}

	}

	/**
	 *
	 * process an index or unique constraint
	 * @param Iteratable $indexColumns
	 * @return array
	 */
	protected function processIndex($indexColumns)
	{

		$index = array();

		foreach ($indexColumns as $indexColumnData) {

			if ((string)$indexColumnData['name']) {
				$index[] = (string)$indexColumnData['name'];
			}

		}

		return $index;

	}

	/**
	 *
	 * get the remaining options from the columns
	 * @param SimpleXmlElement $data
	 * @return array
	 */
	protected function getColumnOptions(\SimpleXmlElement $data)
	{

		$options = array();

		if ((string)$data['scale']) {

			$options['scale'] = (string)$data['scale'];

			if ((string)$data['size']) {
				$options['size'] = (string)$data['size'];
			}

		} elseif ((string)$data['size']) {
			$options['length'] = (string)$data['size'];
		}

		if ((string)$data['size']) {
			$options['length'] = (string)$data['size'];
		}

		if ((string)$data['default']) {
			$options['default'] = (string)$data['default'];
		}

		if ((string)$data['required'] == 'true') {
			$options['notnull'] = true;
		}

		if ((string)$data['autoIncrement'] == 'true') {
			$options['autoincrement'] = true;
		}

		return $options;

	}

	/**
	 *
	 * map from propel data type to doctrine DBAL data type
	 * @param string $dataType
	 * @return string
	 */
	protected function mapDataType($dataType)
	{

		switch (strtolower($dataType)) {

			case 'integer':
				return \Doctrine\DBAL\Types\Type::INTEGER;

			case 'varchar':
			case 'char':
				return \Doctrine\DBAL\Types\Type::STRING;

			case 'timestamp':
				return \Doctrine\DBAL\Types\Type::DATETIME;

			case 'tinyint':
				return \Doctrine\DBAL\Types\Type::BOOLEAN;

			case 'clob':
			case 'longvarchar':
				return \Doctrine\DBAL\Types\Type::TEXT;

			case 'numeric':
			case 'decimal':
				return \Doctrine\DBAL\Types\Type::DECIMAL;

			case 'date':
				return \Doctrine\DBAL\Types\Type::DATE;

			case 'time':
				return \Doctrine\DBAL\Types\Type::TIME;

			default:
				return $dataType;

		}

	}

}

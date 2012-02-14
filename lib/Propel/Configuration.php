<?php

namespace Propel;

use Doctrine\Common\Annotations\AnnotationException;

class Configuration
{

	/** @var string */
	protected $adapter;

	/** @var string */
	protected $databaseName;

	/** @var string */
	protected $host;

	/** @var int */
	protected $port;

	/** @var string */
	protected $user;

	/** @var string */
	protected $password;

	/**
	 * initialize the configuration
	 * @param array $options
	 */
	public function __construct(array $options = array())
	{

		if (array_key_exists('adapter', $options)) {
			$this->adapter = $options['adapter'];
		}

		if (array_key_exists('connection', $options)
			&& is_array($options['connection'])
		) {

			$connection = $options['connection'];

			if (array_key_exists('dsn', $connection)) {

				$dsn = $connection['dsn'];
				$parts = $this->parseDsn($dsn);

				$this->databaseName = $parts['dbname'];
				$this->host = $parts['host'];

			}

			if (array_key_exists('user', $connection)) {
				$this->user = $connection['user'];
			}

			if (array_key_exists('password', $connection)) {
				$this->password = $connection['password'];
			}

		}

	}

	/**
	 * get the data as an array
	 * @return array
	 */
	public function toArray()
	{

		$params = array(
		    'dbname' => $this->getDatabaseName(),
		    'user' => $this->getUser(),
		    'password' => $this->getPassword(),
		    'host' => $this->getHost(),
		    'driver' => $this->getPdoAdapter(),
		);

		if ($this->getPort()) {
			$params['port'] = $this->getPort();
		}

		return $params;

	}

	/**
	 *
	 * get the db user
	 * @return string
	 */
	public function getUser()
	{

		return $this->user;

	}

	/**
	 *
	 * get the db password
	 * @return string
	 */
	public function getPassword()
	{

		return $this->password;

	}

	/**
	 *
	 * get the database name
	 * @return string
	 */
	public function getDatabaseName()
	{

		return $this->databaseName;

	}

	/**
	 *
	 * get the server host
	 * @return string
	 */
	public function getHost()
	{

		return $this->host;

	}

	/**
	 *
	 * get the host port
	 * @return int
	 */
	public function getPort()
	{

		return $this->port;

	}

	/**
	 *
	 * get the associated pdo adapter
	 * @return string
	 */
	public function getPdoAdapter()
	{

		switch ($this->adapter) {


			default:
				return 'pdo_mysql';

		}

	}

	/**
	 *
	 * parse a dsn into its variable parts
	 * @param string $dsn
	 * @return array
	 */
	protected function parseDsn($dsn)
	{

		$parts = array(
			'adapter' => '',
			'host' => '',
			'dbname' => '',
		);

		$data = explode(':', $dsn);
		$parts['adapter'] = $data[0];

		$data = explode(';', $data[1]);
		$host = explode('=', $data[0]);
		$dbname = explode('=', $data[1]);

		$parts['host'] = $host[1];
		$parts['dbname'] = $dbname[1];

		if (array_key_exists(2, $data)) {
			$port = explode('=', $data[2]);
			$parts['port'] = $port[1];
		}

		return $parts;

	}

	/**
	 *
	 * get the configuration for a data source
	 * @param string $name
	 * @param array $options
	 * @throws \DomainException
	 */
	public static function getDataSourceConfiguration($name, array $options)
	{

		if (!array_key_exists('datasources', $options)) {
			throw new \DomainException('No [datasources] key in config options');
		}

		$dataSources = $options['datasources'];

		if (!is_array($dataSources) || !array_key_exists($name, $dataSources)) {
			throw new \DomainException('Invalid data source: ' . $name);
		}

		if (is_array($dataSources[$name])) {
			return new self($dataSources[$name]);
		}

		throw new \DomainException('Unable to get configuration for data source: ' . $name);

	}


}
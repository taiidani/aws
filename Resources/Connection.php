<?php
namespace Rnd\Aws\Resources;

use Aws\S3\S3Client;

/**
 * Represents a connection to S3
 */
class Connection implements \Rnd\Aws\Interfaces\Resource {

	/**
	 * The region that this connection is connected to
	 * @var string
	 */
	private $region;

	/**
	 * An array of connections, one for each region
	 * @var array
	 */
	private static $conn = [];

	/**
	 * Instantiates an S3 connection
	 * @param string $region The S3 server region to connect to
	 */
	public function __construct($region) {
		$this->region = $region;
	}

	/**
	 * Returns the AWS connection object for working with the API
	 * @return Aws\S3\S3Client An S3 client
	 */
	public function getClient() {
		if(!isset(static::$conn[$this->region])) {
			self::$conn[$this->region] = S3Client::factory([
				"region" => $this->region,
				"version" => "latest"
			]);
		}

		return static::$conn[$this->region];
	}
}
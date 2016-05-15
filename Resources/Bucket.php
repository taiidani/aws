<?php
namespace Rnd\Aws\Resources;

/**
 * Represents an S3 bucket
 */
class Bucket implements \Rnd\Aws\Interfaces\Resource {

	/**
	 * An S3 bucket name
	 * @var string
	 */
	private $name;

	/**
	 * An S3 connection
	 * @var Aws\S3\S3Client
	 */
	private $conn;

	/**
	 * Instantiates an S3 key
	 * @param string     $name The bucket name
	 * @param Connection $conn   A configured connection object
	 */
	public function __construct($name, Connection $conn) {
		$this->name = $name;
		$this->conn = $conn;
	}

	/**
	 * Returns the name of the S3 bucket
	 * @return string An S3 bucket name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Returns the AWS connection object for working with the API
	 * @return Aws\S3\S3Client An S3 client
	 */
	public function getClient() {
		return $this->conn->getClient();
	}
}
<?php
namespace Rnd\Aws\Resources;

/**
 * Represents an S3 key
 */
class Key implements \Rnd\Aws\Interfaces\Resource {

	/**
	 * An S3 bucket name
	 * @var string
	 */
	private $bucket;

	/**
	 * An S3 key
	 * @var string
	 */
	private $key;

	/**
	 * An S3 connection
	 * @var Aws\S3\S3Client
	 */
	private $conn;

	/**
	 * Instantiates an S3 key
	 * @param string     $bucket The bucket name
	 * @param string     $key    The key to represent
	 * @param Connection $conn   A configured connection object
	 */
	public function __construct($bucket, $key, Connection $conn) {
		$this->bucket = $bucket;
		$this->key = $key;
		$this->conn = $conn;
	}

	/**
	 * Returns the name of the S3 bucket
	 * @return string An S3 bucket name
	 */
	public function getBucket() {
		return $this->bucket;
	}

	/**
	 * Returns the S3 key
	 * @return string An S3 key
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * Returns the AWS connection object for working with the API
	 * @return Aws\S3\S3Client An S3 client
	 */
	public function getClient() {
		return $this->conn->getClient();
	}
}
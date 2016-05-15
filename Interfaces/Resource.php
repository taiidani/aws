<?php
namespace Rnd\Aws\Interfaces;

/**
 * Represents an AWS resource
 */
interface Resource {

	/**
	 * Returns the AWS connection object for working with the API
	 * @return Aws\S3\S3Client An S3 client
	 */
	function getClient();
}

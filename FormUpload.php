<?php
namespace Rnd\Aws;

use DateTime;
use Aws\S3\S3Client;
use Aws\S3\PostObjectV4;
use Exception;

/**
 * A builder for an AWS POST file upload form
 *
 * @see http://docs.aws.amazon.com/AmazonS3/latest/API/sigv4-HTTPPOSTForms.html
 */
class FormUpload {

	/**
	 * Used when a key prefix is in use. Tells AWS to use the name of the uploaded file in the key
	 */
	const FILENAME_PLACEHOLDER = '${filename}';

	/**
	 * Separates out S3 key folders
	 */
	const KEY_SEPARATOR = "/";

	/**
	 * An S3 client for communicating with the AWS API
	 * @var Aws\S3\S3Client
	 */
	private $client;

	/**
	 * The S3 bucket to upload the file to
	 * @var string
	 */
	private $bucket;

	/**
	 * All form inputs that will appear in the generated HTML
	 * @var array
	 */
	private $fields = [];

	/**
	 * All AWS options for generating the upload policy document
	 * @var array
	 */
	private $options = [];

	/**
	 * Displays the form as HTML
	 *
	 * You may also use the `getAction`, `getMethod`, `getEncType` and `getInput` methods to generate the
	 * form manually. Please remember to include an input of type 'file' with name 'file' in your form to
	 * correctly enable file uploads.
	 * @return string Generated HTML representing the form
	 */
	public function __toString() {
		try {
			$obj = $this->getAWSObject();

			$attr = $obj->getFormAttributes();
			$ret = "<form action='{$attr["action"]}' method='{$attr["method"]}' enctype='{$attr["enctype"]}'>\n";

			foreach($obj->getFormInputs() as $key => $value) {
				$ret .= "\t<input type='hidden' name='{$key}' value='{$value}' />\n";
			}

			$ret .= "\t<input type='file' name='file' />\n";
			$ret .= "\t<input type='submit' />\n";
			$ret .= '</form>';

			return $ret;
		} catch(\Exception $ex) {
			return strval($ex);
		}
	}

	/**
	 * Returns the form action to be used (e.g. <form action="">)
	 * @return string A form action
	 */
	public function getAction() {
		$attr = $this->getAWSObject()->getFormAttributes();
		return $attr["action"];
	}

	/**
	 * Returns the form method to be used (e.g. <form method="">)
	 * @return string A form method
	 */
	public function getMethod() {
		$attr = $this->getAWSObject()->getFormAttributes();
		return $attr["method"];
	}

	/**
	 * Returns the form encoding type to be used (e.g. <form enctype="">)
	 * @return string A form encoding type
	 */
	public function getEncType() {
		$attr = $this->getAWSObject()->getFormAttributes();
		return $attr["enctype"];
	}

	/**
	 * Returns all inputs that need to be present on the form
	 * @return array An dictionary of required form inputs in `name => value` format
	 */
	public function getInputs() {
		return $this->getAWSObject()->getFormInputs();
	}

	/**
	 * Assigns an S3 access control list to the upload
	 * @param Enum\Acl|string $acl An access control list value
	 */
	public function setAcl($acl) {
		if(!$acl instanceof Enum\Acl) {
			$acl = new Enum\Acl(strval($acl));
		}

		$this->removeExistingOption("acl");
		$this->fields["acl"] = strval($acl);
		$this->options[] = [ "acl" => strval($acl) ];
	}

	/**
	 * Assigns an S3 bucket to the upload
	 * @param Resources\Bucket|string $bucket An S3 bucket
	 */
	public function setBucket($bucket) {
		if($bucket instanceof Resources\Bucket) {
			$this->client = $bucket->getClient();
			$bucket = $bucket->getName();
		}

		$this->removeExistingOption("bucket");
		$this->bucket = $bucket;
		$this->options[] = [ "bucket" => $this->bucket ];
	}

	/**
	 * Assigns the S3 connection to the uploader
	 * @param Resources\Connection $conn A configured S3 connection
	 */
	public function setConnection(Resources\Connection $conn) {
		$this->client = $conn->getClient();
	}

	/**
	 * Assigns the exact key to be used for the upload
	 *
	 * The key is mutually exlusive with the key prefix. Please use one or the other.
	 * @param string $key An S3 key
	 */
	public function setKey($key) {
		if($key instanceof Resources\Key) {
			$this->client = $key->getClient();
			$this->setBucket($key->getBucket());
			$key = $key->getKey();
		}

		$this->removeExistingOption("key");
		$this->fields["key"] = $key;
		$this->options[] = [ 'key' => $key ];
	}

	/**
	 * Assigns the folder prefix for a dynamic key to the uploader
	 *
	 * The prefix designates the part of the key that will appear before the filename
	 * is entered. For example, if the prefix is "pending" and the uploaded filename is
	 * "test.txt", the final S3 key will be "pending/test.txt".
	 * 
	 * @param string $prefix An S3 folder prefix for the key
	 */
	public function setKeyPrefix($prefix) {
		$this->removeExistingOption("key");
		$this->fields["key"] = trim($prefix, self::KEY_SEPARATOR) . self::KEY_SEPARATOR . self::FILENAME_PLACEHOLDER;
		$this->options[] = [ "starts-with", '$key', str_ireplace(self::FILENAME_PLACEHOLDER, "", $this->fields["key"]) ];
	}

	/**
	 * Assigns the callback URL that the browser will be redirected to after a successful upload
	 * @param string $url A valid absolute URL
	 */
	public function setSuccessUrl($url) {
		$this->removeExistingOption("success_action_redirect");
		$this->fields["success_action_redirect"] = $url;
		$this->options[] = [ "success_action_redirect" => $url ];
	}

	/**
	 * Assigns metadata to be added to the key after it uploads
	 * @param string $key   The meta key
	 * @param mixed  $value The meta value
	 */
	public function setMeta($key, $value) {
		if(is_array($value)) {
			$value = implode(",", $value);
		}

		$key = "x-amz-meta-" . mb_strtolower($key);
		$this->removeExistingOption($key);
		$this->fields[$key] = $value;
		$this->options[] = [ $key => $value ];
	}

	/**
	 * Returns the AWS form uploader object which generates the required inputs and attributes
	 * @return Aws\S3\PostObjectV4 An S3 form uploader object
	 */
	private function getAWSObject() {
		if(empty($this->client) || empty($this->bucket)) {
			throw new \Exception("Required fields are missing");
		}

		return new PostObjectV4($this->client, $this->bucket, $this->fields, $this->options);
	}

	/**
	 * Scans through the option list for the uploader and removes rules for the specified key
	 * @param  string $key The key to be removed
	 * @return void
	 */
	private function removeExistingOption($key) {
		$this->options = array_filter($this->options, function(array $value) use ($key) {
			return !isset($value[$key]) && (!isset($value[1]) || $value[1] !== '$' . $key);
		});
	}
}
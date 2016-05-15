<?php
namespace Rnd\Aws\Resources;

use Rnd\Aws\Enum\Acl;
use DateTime;

/**
 * @see http://docs.aws.amazon.com/AmazonS3/latest/API/sigv4-HTTPPOSTForms.html
 */
abstract class Request implements \Rnd\Aws\Interfaces\Resource {

	private $conn;
	public $region;
	public $date;

	public function __construct(Connection $conn) {
		$this->conn = $conn;
		$this->date = new DateTime();
	}

	public function getClient() {
		return $this->conn->getClient();
	}

	public function getCredential() {
		return implode("/", [
			$this->getAccessKey(),
			$this->date->format("Ymd"),
			$this->getRegion(),
			$this->getService(),
			Signature::REQUEST_TYPE
		]);
	}

	public function getScope() {
		return implode("/", [
			$this->date->format("Ymd"),
			$this->getRegion(),
			$this->getService(),
			Signature::REQUEST_TYPE
		]);
	}

	public function getPolicy() {
		$policy = json_encode([
			"expiration" => date(DATE_ATOM, strtotime("+1 hour")),
			"conditions" => $this->getFields()
		]);

		return base64_encode(trim($policy));
	}

	public function getRegion() {
		return $this->region;
	}

	public abstract function getService();

	public function getUrl() {
		return "https://{$this->bucket}.s3-" . $this->getRegion() . ".amazonaws.com";
	}

	protected function getAccessKey() {
		return getenv("AWS_ACCESS_KEY_ID");
	}

	protected abstract function getFields();
	
}
<?php
namespace Rnd\Aws\Resources;

use Rnd\Aws\Enum\Acl;
use DateTime;

/**
 * @see http://docs.aws.amazon.com/AmazonS3/latest/API/sigv4-HTTPPOSTForms.html
 */
class Signature implements \Rnd\Aws\Interfaces\Resource {

	const TYPE = "AWS4-HMAC-SHA256";

	const REQUEST_TYPE = "aws4_request";

	public $request;

	public function __construct(Request $request) {
		$this->request = $request;
	}

	public function getClient() {
		return $this->request->getClient();
	}

	public function __toString() {
		$ret = hash_hmac("sha256", $this->request->date->format("Ymd"), "AWS4" . $this->getAccessSecret(), true);
		$ret = hash_hmac("sha256", $this->request->getRegion(), $ret, true);
		$ret = hash_hmac("sha256", $this->request->getService(), $ret, true);
		$ret = hash_hmac("sha256", self::REQUEST_TYPE, $ret, true);
		return hash_hmac("sha256", $this->getPolicy(), $ret);
	}

	protected function getAccessSecret() {
		return getenv("AWS_SECRET_ACCESS_KEY");
	}

	protected function getPolicy() {
		return implode("\n", [
			self::TYPE,
			$this->request->date->format("Ymd\\THis\\Z"),
			$this->request->getScope(),
			$this->request->getPolicy()
		]);
	}
}
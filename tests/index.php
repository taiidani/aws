<?php
namespace RND\AWS;

date_default_timezone_set("UTC");
require_once(__DIR__ . "/../autoload.php");

$upload = new FormUpload();
$upload->setBucket(new Resources\Bucket("test.ryannixon.com", new Resources\Connection("us-west-2")));
$upload->setSuccessUrl("http://localhost:8080/success.php");
$upload->setAcl(Enum\Acl::ACL_PRIVATE);
$upload->setKeyPrefix('pending');
print $upload;
<?php
namespace Rnd\Aws\Enum;

/**
 * An AWS Access Control List, suitable for applying to an object
 */
class Acl extends BaseEnum {

	const __default = self::ACL_PRIVATE;

	/**
	 * Owner gets FULL_CONTROL. No one else has access rights (default).
	 */
	const ACL_PRIVATE = "private";

	/**
	 * Owner gets FULL_CONTROL. The AllUsers group gets READ access.
	 */
    const ACL_PUBLICREAD = "public-read";

    /**
     * Owner gets FULL_CONTROL. The AllUsers group gets READ and WRITE access. Granting this on a bucket is generally not recommended.
     */
    const ACL_PUBLIC_READ_WRITE = "public-read-write";

    /**
     * Owner gets FULL_CONTROL. Amazon EC2 gets READ access to GET an Amazon Machine Image (AMI) bundle from Amazon S3.
     */
    const ACL_AWS_EXEC_READ = "aws-exec-read";

    /**
     * Owner gets FULL_CONTROL. The AuthenticatedUsers group gets READ access.
     */
    const ACL_AUTHENTICATED_READ = "authenticated-read";

    /**
     * Object owner gets FULL_CONTROL. Bucket owner gets READ access. If you specify this canned ACL when creating a bucket, Amazon S3 ignores it.
     */
    const ACL_BUCKET_OWNER_READ = "bucket-owner-read";

    /**
     * Both the object owner and the bucket owner get FULL_CONTROL over the object. If you specify this canned ACL when creating a bucket, Amazon S3 ignores it.
     */
    const ACL_BUCKET_OWNER_FULL_CONTROL = "bucket-owner-full-control";
}
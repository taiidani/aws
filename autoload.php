<?php
namespace RND\AWS;

require_once(__DIR__ . "/vendor/autoload.php");

//Register the namespaced autoloader
spl_autoload_register(function($className)
{
    if(mb_strpos($className, __NAMESPACE__) === 0) {
        //Strip off the vendor prefix
        $namespace = array_filter(explode("\\", mb_substr($className, strlen(__NAMESPACE__))));

        //Include the file if found
        $path = __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $namespace) . ".php";
        if(file_exists($path)) {
            include($path);
        }
    }
});

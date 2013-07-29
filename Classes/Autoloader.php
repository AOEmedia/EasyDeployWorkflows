<?php

namespace EasyDeployWorkflows;

/* Bug https://bugs.php.net/bug.php?id=43200 requires PHP to be >= 5.3.9 */
if (version_compare(PHP_VERSION, '5.3.9') < 0) die('EasyDeployWorkflow neede PHP v5.3.9 or above because of bug https://bugs.php.net/bug.php?id=43200');

spl_autoload_register(__NAMESPACE__ .'\Autoloader::autoload');

/**
 * spl autoloader for EasyDeployWorkflows classes
 */
class Autoloader {
	/**
	 * spl autoloader
	 * @param $name classname
	 */
	static public function autoload($name) {
		#echo $name.' - '.PHP_EOL;
		if (strpos($name,'EasyDeployWorkflows') === 0) {
			$classPath = substr($name,strlen(__NAMESPACE__));
			$classPath = str_replace('\\',DIRECTORY_SEPARATOR,$classPath).'.php';
			require_once dirname(__FILE__).$classPath;
		}
	}
}

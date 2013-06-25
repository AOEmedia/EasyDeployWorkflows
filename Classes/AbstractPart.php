<?php

namespace EasyDeployWorkflows;

use EasyDeployWorkflows\Workflows;

abstract class AbstractPart {

	/**
	 * @var Logger\Logger
	 */
	protected $logger;

	/**
	 * constructor
	 */
	public function __construct() {
		$this->injectLogger(\EasyDeployWorkflows\Logger\Logger::getInstance());
	}

	/**
	 * @param Logger\Logger $logger
	 */
	public function injectLogger(\EasyDeployWorkflows\Logger\Logger $logger) {
		$this->logger = $logger;
	}

	/**
	 * @param string $serverName
	 * @return \EasyDeploy_LocalServer|\EasyDeploy_RemoteServer
	 */
	protected function getServer($serverName, $serverKey = NULL) {
		if ($serverName == 'localhost') {
			$server =  new \EasyDeploy_LocalServer($serverName);
		}
		else {
			$server = new \EasyDeploy_RemoteServer($serverName);
		}
		$server->setLogCommandsToScreen(false);
		$server->setInternalTitle($serverName);
		if (!empty($serverKey)) {
			$server->setInternalTitle($serverKey);
		}
		return $server;
	}

	/**
	 * @param $string
	 * @return mixed
	 */
	public function replaceConfigurationMarkers($string, \EasyDeployWorkflows\Workflows\AbstractConfiguration $workflowConfiguration, \EasyDeployWorkflows\Workflows\InstanceConfiguration $instanceConfiguration) {
		$string = str_replace('###releaseversion###',$workflowConfiguration->getReleaseVersion(),$string);
		$string = str_replace('###environment###',$instanceConfiguration->getEnvironmentName(),$string);
		$string = str_replace('###environmentname###',$instanceConfiguration->getEnvironmentName(),$string);
		$string = str_replace('###projectname###',$instanceConfiguration->getProjectName(),$string);
		return $this->replaceWithEnvironmentVariables($string);
	}

	/**
	 * Replaces this pattern ###ENV:TEST### with the environment variable
	 * @param $string
	 * @return string
	 * @throws \Exception
	 */
	protected function replaceWithEnvironmentVariables($string) {
		$matches=array();
		preg_match_all('/###ENV:([^#]*)###/',$string,$matches,PREG_PATTERN_ORDER);
		if (!is_array($matches) || !is_array($matches[0])) {
			return $string;
		}
		foreach ($matches[0] as $index=>$completeMatch) {
			if (getenv($matches[1][$index]) == FALSE) {
				throw new \Exception('Expect an environmentvariable '.$matches[1][$index]);
			}
			$string = str_replace($completeMatch,getenv($matches[1][$index]),$string);
		}
		return $string;
	}


	protected function getFilenameFromPath($path) {
		$dir = dirname($path).DIRECTORY_SEPARATOR;
		return str_replace($dir,'',$path);
	}

	protected function getFileBaseName($filename) {
		return substr($filename,0,strpos($filename,'.'));
	}

}
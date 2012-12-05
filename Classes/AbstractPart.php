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
	protected function getServer($serverName) {
		if ($serverName == 'localhost') {
			$server =  new \EasyDeploy_LocalServer($serverName);
		}
		else {
			$server = new \EasyDeploy_RemoteServer($serverName);
		}
		$server->setLogCommandsToScreen(false);
		return $server;
	}

	/**
	 * @param $string
	 * @return mixed
	 */
	protected function replaceConfigurationMarkers($string, \EasyDeployWorkflows\Workflows\AbstractConfiguration $workflowConfiguration, \EasyDeployWorkflows\Workflows\InstanceConfiguration $instanceConfiguration) {
		$string = str_replace('###releaseversion###',$workflowConfiguration->getReleaseVersion(),$string);
		$string = str_replace('###environment###',$instanceConfiguration->getEnvironmentName(),$string);
		$string = str_replace('###environmentname###',$instanceConfiguration->getEnvironmentName(),$string);
		$string = str_replace('###projectname###',$instanceConfiguration->getProjectName(),$string);
		return $string;
	}


	protected function getFilenameFromPath($path) {
		$dir = dirname($path).DIRECTORY_SEPARATOR;
		return str_replace($dir,'',$path);
	}

}
<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeploy_AbstractServer;
use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Tasks;


class WriteVersionFile extends Tasks\AbstractServerTask {

	/**
	 * @var string
	 */
	protected $version;

	/**
	 * @var string
	 */
	protected $targetPath;

	/**
	 * @param string $version
	 * @return $this
	 */
	public function setVersion($version) {
		$this->version = $version;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}

	/**
	 * @param string $targetPath
	 * @return $this
	 */
	public function setTargetPath($targetPath) {
		$this->targetPath = rtrim($targetPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTargetPath() {
		return $this->targetPath;
	}

	/**
	 * @param Tasks\TaskRunInformation $taskRunInformation
	 * @param EasyDeploy_AbstractServer $server
	 * @return mixed
	 */
	protected function runOnServer(Tasks\TaskRunInformation $taskRunInformation, EasyDeploy_AbstractServer $server) {
		$this->executeAndLog($server, 'echo "' . $this->version . '" > ' . $this->targetPath . 'version.txt');
		$this->executeAndLog($server, 'echo "' . gmdate('r') . '" > ' . $this->targetPath . 'deploytime.txt');
	}

	/**
	 * @return boolean
	 * @throws InvalidConfigurationException
	 */
	public function validate() {
		if (empty($this->targetPath)) {
			throw new InvalidConfigurationException('targetPath not set');
		}
		if (empty($this->version)) {
			throw new InvalidConfigurationException('version not set');
		}
	}
}

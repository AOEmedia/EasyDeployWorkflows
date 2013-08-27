<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Tasks;


class RunScript extends Tasks\AbstractServerTask {

	/**
	 * @var string
	 */
	protected $script;

	/**
	 * @var bool
	 */
	protected $isOptional = false;

	/**
	 * @param bool $isOptional
	 */
	public function setIsOptional($isOptional) {
		$this->isOptional = $isOptional;
	}

	/**
	 * @param string $script
	 * @return $this
	 */
	public function setScript($script) {
		$this->script = $script;

		return $this;
	}

	/**
	 * @param Tasks\TaskRunInformation $taskRunInformation
	 * @param \EasyDeploy_AbstractServer $server
	 * @throws \EasyDeployWorkflows\Exception\FileNotFoundException
	 * @return mixed
	 */
	protected function runOnServer(Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {

		if (!$server->isFile($this->script) && !$this->isOptional) {
			$message = 'Try to run script that not exists '.htmlspecialchars($this->script);
			throw new \EasyDeployWorkflows\Exception\FileNotFoundException($message);
		}

		if ($server->isFile($this->script)) {
			$this->logger->log('Run Script: "'.$this->script.'"');
			$this->executeAndLog($server,$this->script, FALSE, FALSE, $this->logger->getLogFile());
		}
	}

	/**
	 * @return bool
	 * @throws InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->script)) {
			throw new InvalidConfigurationException('Script not set');
		}

		return true;
	}
}

<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeploy_AbstractServer;
use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Tasks;


class WriteToFile extends Tasks\AbstractServerTask {

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var string
	 */
	protected $fileName;

	/**
	 * @param string $content
	 * @return $this
	 */
	public function setContent($content) {
		$this->content = $content;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * @param string $fileName
	 * @return $this
	 */
	public function setFileName($fileName) {
		$this->fileName = $fileName;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getFileName() {
		return $this->fileName;
	}

	/**
	 * @param Tasks\TaskRunInformation $taskRunInformation
	 * @param EasyDeploy_AbstractServer $server
	 * @return mixed
	 */
	protected function runOnServer(Tasks\TaskRunInformation $taskRunInformation, EasyDeploy_AbstractServer $server) {
		$this->executeAndLog($server,
			'echo "' . escapeshellarg($this->content) . '" > ' . escapeshellarg($this->fileName)
		);
	}

	/**
	 * @return boolean
	 * @throws InvalidConfigurationException
	 */
	public function validate() {
		if (empty($this->fileName)) {
			throw new InvalidConfigurationException('fileName not set');
		}
		if (empty($this->content)) {
			throw new InvalidConfigurationException('content not set');
		}
	}
}

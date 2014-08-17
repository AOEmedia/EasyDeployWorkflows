<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeploy_AbstractServer;
use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Logger\Logger;
use EasyDeployWorkflows\Tasks;


class WriteToFile extends Tasks\Common\RunCommand {

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
	 * Run command on server
	 *
	 * @param Tasks\TaskRunInformation $taskRunInformation
	 * @param \EasyDeploy_AbstractServer $server
	 * @return mixed
	 */
	protected function runOnServer(Tasks\TaskRunInformation $taskRunInformation, \EasyDeploy_AbstractServer $server) {
		$commandWorkingDirectory = $server->getCwd();
		if ($this->getChangeToDirectory()) {
			$commandWorkingDirectory = $this->getChangeToDirectory();
		}

		$message = sprintf('Writing content %s to file %s in directory %s',
			$this->getContent(), $this->getFileName(), $commandWorkingDirectory
		);
		$this->logger->log($message, Logger::MESSAGE_TYPE_INFO);

		$command = sprintf('touch %s', $this->getFileName());
		$this->executeAndLog($server, $this->_prependWithCd($command, $taskRunInformation));
		file_put_contents($this->getFileName(), $this->getContent());
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

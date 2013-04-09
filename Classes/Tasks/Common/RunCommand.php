<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class RunCommand extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var string
	 */
	protected $command;



	/**
	 * @param string $folder
	 */
	public function setCommand($script) {
		$this->command = $script;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {


		$server->run($this->command, FALSE, FALSE, $this->logger->getLogFile());

	}

	/**
	 * @return boolean
	 * @throws \EasyDeployWorkflows\Exception\InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->command)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Command not set');
		}

		return true;
	}
}
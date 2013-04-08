<?php

namespace EasyDeployWorkflows\Tasks;

use EasyDeployWorkflows\Workflows;


/**
 * A task that is executed on one or many servers
 */
abstract class AbstractServerTask extends AbstractTask {

	/**
	 * @var array
	 */
	protected $servers = array();

	/**
	 * Adds a server on which this task should be executed
	 */
	public function addServer(\EasyDeploy_AbstractServer $server) {
		$this->servers[] = $server;
	}

	/**
	 * @return array
	 */
	public function getServers() {
		return $this->servers;
	}

	/**
	 * @return array
	 */
	public function hasServers() {
		return count($this->servers) > 0;
	}

	/**
	 * Adds a server on which this task should be executed
	 */
	public function addServerByName($server) {
		if (!is_string($server)) {
			throw new \InvalidArgumentException('no string given: '.gettype($server));
		}
		$this->addServer($this->getServer($server));
	}

	/**
	 * Adds servers on which this task should be executed
	 */
	public function addServersByName(array $servers) {
		foreach ($servers as $server) {
			$this->addServerByName($server);
		}
	}


	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	public function run(TaskRunInformation $taskRunInformation) {
		$this->validate();
		if (!$this->hasServers()) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('no server set for server based task: '.get_class($this));
		}
		foreach ($this->getServers() as  $server) {
			$this->logger->log('Run on Server '.$server->getInternalTitle());
			$this->logger->addLogIndentLevel();
			$this->runOnServer($taskRunInformation, $server);
			$this->logger->removeLogIndentLevel();
		}
	}

	/**
	 * Use this function as Wrapper to $server->run($command) to ensure that the output is logged properly
	 * Also this function takes care of tryRun
	 * @param \EasyDeploy_AbstractServer $server
	 * @param $command
	 */
	protected function executeAndLog(\EasyDeploy_AbstractServer $server, $command) {
		$this->logger->log($command,\EasyDeployWorkflows\Logger\Logger::MESSAGE_TYPE_COMMAND);
		if (isset($GLOBALS['tryRun'])) {
			return;
		}
		$this->logger->log('',\EasyDeployWorkflows\Logger\Logger::MESSAGE_TYPE_COMMANDOUTPUT);
		return $server->run($command,FALSE,FALSE,$this->logger->getLogFile());
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	abstract protected function runOnServer(TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server);
}
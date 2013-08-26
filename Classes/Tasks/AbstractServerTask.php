<?php

namespace EasyDeployWorkflows\Tasks;

use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Logger\Logger;
use EasyDeployWorkflows\Workflows;


/**
 * A task that is executed on one or many servers
 */
abstract class AbstractServerTask extends AbstractTask {

	/**
	 * @var \EasyDeploy_AbstractServer[]
	 */
	protected $servers = array();

	/**
	 * Adds a server on which this task should be executed
	 *
	 * @param \EasyDeploy_AbstractServer $server
	 * @return $this
	 */
	public function addServer(\EasyDeploy_AbstractServer $server) {
		$this->servers[] = $server;

		return $this;
	}

	/**
	 * @return \EasyDeploy_AbstractServer[]
	 */
	public function getServers() {
		return $this->servers;
	}

	/**
	 * @return bool
	 */
	public function hasServers() {
		return count($this->servers) > 0;
	}

	/**
	 * Adds a server on which this task should be executed
	 *
	 * @param string $server
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function addServerByName($server) {
		if (!is_string($server)) {
			throw new \InvalidArgumentException('no string given: ' . gettype($server));
		}
		$this->addServer($this->getServer($server));

		return $this;
	}

	/**
	 * Adds servers on which this task should be executed
	 *
	 * @param string[] $servers
	 * @return $this
	 */
	public function addServersByName(array $servers) {
		foreach ($servers as $server) {
			$this->addServerByName($server);
		}

		return $this;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed|void
	 * @throws InvalidConfigurationException
	 */
	public function run(TaskRunInformation $taskRunInformation) {
		$this->validate();
		if (!$this->hasServers()) {
			throw new InvalidConfigurationException('no server set for server based task: ' . get_class($this));
		}
		foreach ($this->getServers() as $server) {
			$this->logger->log('Run on Server ' . $server->getInternalTitle());
			$this->logger->addLogIndentLevel();
			$this->runOnServer($taskRunInformation, $server);
			$this->logger->removeLogIndentLevel();
		}
	}

	/**
	 * Use this function as Wrapper to $server->run($command) to ensure that the output is logged properly
	 * Also this function takes care of tryRun
	 *
	 * @param \EasyDeploy_AbstractServer $server
	 * @param string $command
	 * @return null
	 */
	protected function executeAndLog(\EasyDeploy_AbstractServer $server, $command) {
		$this->logger->log($command, Logger::MESSAGE_TYPE_COMMAND);
		if (isset($GLOBALS['tryRun'])) {
			return null;
		}
		$this->logger->log('', Logger::MESSAGE_TYPE_COMMANDOUTPUT);

		return $server->run($command, false, false, $this->logger->getLogFile());
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @param \EasyDeploy_AbstractServer $server
	 * @return mixed
	 */
	abstract protected function runOnServer(TaskRunInformation $taskRunInformation, \EasyDeploy_AbstractServer $server);
}

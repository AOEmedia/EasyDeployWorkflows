<?php
/**
 * @author Dmytro Zavalkin <dmytro.zavalkin@aoe.com>
 */

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeploy_AbstractServer;
use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Logger\Logger;
use EasyDeployWorkflows\Tasks;


class CreateCronJob extends Tasks\AbstractServerTask {

	const CURRENT_USER = 'current';

	const COMMAND_TEMPLATE = 'cat <(crontab -l %s | grep -v "%s") <(echo "%s") | crontab -';

	/**
	 * @var string
	 */
	protected $job;

	/**
	 * @var string
	 */
	protected $user = self::CURRENT_USER;

	/**
	 * @param string $job
	 * @return $this
	 */
	public function setJob($job) {
		$this->job = $job;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getJob() {
		return $this->job;
	}

	/**
	 * @param string $user
	 * @return $this
	 */
	public function setUser($user) {
		$this->user = $user;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * Run command on server
	 *
	 * @param Tasks\TaskRunInformation $taskRunInformation
	 * @param \EasyDeploy_AbstractServer $server
	 * @return mixed
	 */
	protected function runOnServer(Tasks\TaskRunInformation $taskRunInformation, \EasyDeploy_AbstractServer $server) {
		$message = sprintf('Creating new job "%s" for "%s" user', $this->getJob(), $this->getUser());
		$this->logger->log($message, Logger::MESSAGE_TYPE_INFO);

		$u = '';
		if ($this->getUser() != self::CURRENT_USER) {
			$u = sprintf(' -u %s ', $this->getUser());
		}

		$command = sprintf(self::COMMAND_TEMPLATE, $u, $this->getJob(), $this->getJob());
		$command = $this->replaceConfigurationMarkersWithTaskRunInformation($command, $taskRunInformation);
		$this->executeAndLog($server, $command);
	}

	/**
	 * @return boolean
	 * @throws InvalidConfigurationException
	 */
	public function validate() {
		if (empty($this->user)) {
			throw new InvalidConfigurationException('user not set');
		}
		if (empty($this->job)) {
			throw new InvalidConfigurationException('job not set');
		}
	}
}

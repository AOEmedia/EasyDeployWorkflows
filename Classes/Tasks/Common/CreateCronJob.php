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

	const COMMAND_TEMPLATE = '(crontab %s -l | grep -v "%s" ; echo "%s") | crontab %s -';

	/**
	 * @var string
	 */
	protected $schedule;

	/**
	 * @var string
	 */
	protected $command;

	/**
	 * @var string
	 */
	protected $user = self::CURRENT_USER;

	/**
	 * @return string
	 */
	public function getSchedule() {
		return $this->schedule;
	}

	/**
	 * @param string $schedule
	 * @return $this
	 */
	public function setSchedule($schedule) {
		$this->schedule = $schedule;

		return $this;
	}

	/**
	 * @param string $job
	 * @return $this
	 */
	public function setCommand($job) {
		$this->command = $job;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getCommand() {
		return $this->command;
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
		$message = sprintf('Creating new job "%s" for "%s" user', $this->getCommand(), $this->getUser());
		$this->logger->log($message, Logger::MESSAGE_TYPE_INFO);

		$u = '';
		if ($this->getUser() != self::CURRENT_USER) {
			$u = sprintf(' -u %s ', $this->getUser());
		}

		$cronTabLine = $this->getSchedule() . ' ' . $this->getCommand();
		$command = sprintf(self::COMMAND_TEMPLATE, $u, $this->getCommand(), $cronTabLine, $u);
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
		if (empty($this->command)) {
			throw new InvalidConfigurationException('command not set');
		}
		if (empty($this->schedule)) {
			throw new InvalidConfigurationException('schedule not set');
		}
	}
}

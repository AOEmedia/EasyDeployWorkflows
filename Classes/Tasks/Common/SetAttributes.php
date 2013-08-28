<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Logger\Logger;
use EasyDeployWorkflows\Tasks;


class SetAttributes extends Tasks\Common\RunCommand {

	/**
	 * @var array
	 */
	protected $target = array();

	/**
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Set target (file, folder, unix file pattern)
	 *
	 * @param string $target
	 * @return $this
	 */
	public function setTarget($target) {
		$this->target = $target;

		return $this;
	}

	/**
	 * Set target attributes
	 *
	 * @param string $target file, folder, unix file pattern
	 * @param int $permissions
	 * @param string $owner
	 * @param string $group
	 * @param bool $recursive
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setAttributes($target, $permissions = null, $owner = null, $group = null, $recursive = false) {
		if (empty($target)) {
			throw new \InvalidArgumentException("Target can't be empty");
		}
		if ($permissions) {
			if (!is_int($permissions) || $permissions > 0777) {
				throw new \InvalidArgumentException('Target permissions must be set as integer value <= 0777');
			}
		}

		$this->target     = $target;
		$this->attributes = array(
			'permissions' => $permissions,
			'owner'       => $owner,
			'group'       => $group,
			'recursive'   => $recursive,
		);

		return $this;
	}

	/**
	 * Run command on server
	 *
	 * @param Tasks\TaskRunInformation $taskRunInformation
	 * @param \EasyDeploy_AbstractServer $server
	 * @return mixed
	 * @throws \Exception
	 */
	protected function runOnServer(Tasks\TaskRunInformation $taskRunInformation, \EasyDeploy_AbstractServer $server) {
		$commandWorkingDirectory = $server->getCwd();
		if ($this->getChangeToDirectory()) {
			$commandWorkingDirectory = $this->getChangeToDirectory();
		}

		$message = sprintf('Changing attributes of target %s in directory %s', $this->target, $commandWorkingDirectory);
		$this->logger->log($message, Logger::MESSAGE_TYPE_INFO);

		if ($this->attributes['owner'] || $this->attributes['group']) {
			$command = sprintf("chown %s %s:%s '%s'", $this->attributes['recursive'] ? '-r' : '',
				$this->attributes['owner'], $this->attributes['group'], $this->target
			);
			$this->executeAndLog($server, $this->_prependWithCd($command));
		}

		if ($this->attributes['permissions']) {
			$command = sprintf("chmod %s %o '%s'", $this->attributes['recursive'] ? '-r' : '',
				$this->attributes['permissions'], $this->target
			);
			$this->executeAndLog($server, $this->_prependWithCd($command));
		}
	}

	/**
	 * @return boolean
	 * @throws InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->target)) {
			throw new InvalidConfigurationException('No targets are set');
		}

		return true;
	}
}

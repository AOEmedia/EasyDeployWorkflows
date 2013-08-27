<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Logger\Logger;
use EasyDeployWorkflows\Tasks;


class ChangePermissions extends Tasks\Common\RunCommand {

	/**
	 * @var array
	 */
	protected $targets = array();

	/**
	 * Set file or folder permissions
	 *
	 * @param string $target
	 * @param int $mode
	 * @param bool $recursive
	 * @return $this
	 * @throws \InvalidArgumentException
	 */
	public function setPermissions($target, $mode, $recursive = false) {
		if (!is_int($mode)) {
			throw new \InvalidArgumentException('Target permissions must be set as integer value');
		}
		$this->targets[$target] = array(
			'mode'      => $mode,
			'recursive' => $recursive,
		);

		return $this;
	}

	/**
	 * @param Tasks\TaskRunInformation $taskRunInformation
	 * @param \EasyDeploy_AbstractServer $server
	 * @return mixed
	 * @throws \Exception
	 */
	protected function runOnServer(Tasks\TaskRunInformation $taskRunInformation, \EasyDeploy_AbstractServer $server) {
		foreach ($this->targets as $target => $options) {
			$directory = $this->getChangeToDirectory();
			$path = $target;
			if ($this->getChangeToDirectory()) {
				$path = $directory . $target;
			}
			if (!$server->targetExists($path)) {
				$message = "Target to change permissions is not present! Try to create '" . $path . "' first";
				$this->logger->log($message, Logger::MESSAGE_TYPE_WARNING);
			} else {
				$command = 'chmod ';
				if ($options['recursive']) {
					$command .= ' -R';
				}
				$command .= sprintf(" %o '%s'", $options['mode'], $target);
				$this->executeAndLog($server, $this->_prependWithCd($command));

				$result = $this->executeAndLog($server, $this->_prependWithCd("stat -c %a '" . $target . "'"));
				$newTargetPermissions = sscanf($result, "%o");

				if ($newTargetPermissions !== $options['mode']) {
					$message = sprintf("Can't change target '%s' permissions to %o. Current target permissions: %o",
						$target, $options['mode'], $newTargetPermissions
					);
					$this->logger->log($message, Logger::MESSAGE_TYPE_ERROR);
					throw new \Exception($message);
				}
			}
		}
	}

	/**
	 * @return boolean
	 * @throws InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->targets)) {
			throw new InvalidConfigurationException('No targets are set');
		}

		return true;
	}
}

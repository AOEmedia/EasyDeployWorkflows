<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Tasks;


class Rename extends Tasks\AbstractServerTask {

	/**
	 * @var string
	 */
	protected $source;

	/**
	 * @var string
	 */
	protected $target;

	/**
	 * @param string $source
	 * @return $this
	 */
	public function setSource($source)
	{
		$this->source = $source;

		return $this;
	}

	/**
	 * @param string $target
	 * @return $this
	 */
	public function setTarget($target)
	{
		$this->target = $target;

		return $this;
	}

	/**
	 * @param Tasks\TaskRunInformation $taskRunInformation
	 * @param \EasyDeploy_AbstractServer $server
	 * @return mixed
	 */
	protected function runOnServer(Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {
		$this->executeAndLog($server,'mv '.$this->source.' '.$this->target);
	}

	/**
	 * @return boolean
	 * @throws InvalidConfigurationException
	 */
	public function validate() {
		if (empty($this->source)) {
			throw new InvalidConfigurationException('source not set');
		}
		if (empty($this->target)) {
			throw new InvalidConfigurationException('target not set');
		}
	}
}

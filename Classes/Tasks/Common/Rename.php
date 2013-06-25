<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class Rename extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var string
	 */
	protected $source;

	/**
	 * @var string
	 */
	protected $target;

	/**
	 * @param string $file
	 */
	public function setSource($sourceFile)
	{
		$this->source = $sourceFile;
	}

	/**
	 * @param string $file
	 */
	public function setTarget($targetFile)
	{
		$this->target = $targetFile;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {
			$this->executeAndLog($server,'mv '.$this->source.' '.$this->target);
	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	public function validate() {
		if (empty($this->source)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('source not set');
		}
		if (empty($this->target)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('target not set');
		}
	}
}
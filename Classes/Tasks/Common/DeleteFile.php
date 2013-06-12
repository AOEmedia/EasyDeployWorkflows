<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class DeleteFile extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var string
	 */
	protected $file;

	/**
	 * @param string $file
	 */
	public function setFile($file)
	{
		$this->file = $file;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {
			$server->run('rm '.$this->file);
	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	public function validate() {
		return true;
	}
}
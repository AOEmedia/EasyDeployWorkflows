<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class DeleteFolder extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var string
	 */
	protected $folder;

	/**
	 * @param string $folder
	 */
	public function setFolder($folder)
	{
		$this->folder = $folder;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {
		if (!$server->isDir($this->folder)) {
			throw new \Exception('Folder "'.$this->folder.'" on Node "'.$server->getHostname().'" is not present!');
		}
		$this->executeAndLog($server,'rm -rf '.$this->folder);
	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->folder)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Folder not set');
		}
		return true;
	}
}
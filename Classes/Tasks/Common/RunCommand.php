<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class RunCommand extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var string
	 */
	protected $command;

	/**
	 * @var string
	 */
	protected $changeToDirectory;



	/**
	 * @param string $folder
	 */
	public function setCommand($script) {
		$this->command = $script;
	}

	/**
	 * @param string $changeToDirectory
	 */
	public function setChangeToDirectory($changeToDirectory) {
		$this->changeToDirectory = $changeToDirectory;
	}


	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {
		$command = $this->command;
		if (isset($this->changeToDirectory)) {
			$command = 'cd '.$this->changeToDirectory.'; '.$command;
		}
		$environmentVariables = 'export ENVIRONMENT="'.$taskRunInformation->getInstanceConfiguration()->getEnvironmentName() .'"';
		$environmentVariables .= ' && export PROJECTNAME="'.$taskRunInformation->getInstanceConfiguration()->getProjectName() .'"';
		$environmentVariables .= ' && export RELEASEVERSION="'.$taskRunInformation->getWorkflowConfiguration()->getReleaseVersion() .'"';
		$environmentVariables .= ' && export RELEASEVERSION_ESCAPED="'.PREG_REPLACE("/[^0-9a-zA-Z]/i", '', $taskRunInformation->getWorkflowConfiguration()->getReleaseVersion()) .'" && ';
		$this->executeAndLog($server,$environmentVariables.$command);
	}

	/**
	 * @return boolean
	 * @throws \EasyDeployWorkflows\Exception\InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->command)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Command not set');
		}

		return true;
	}
}
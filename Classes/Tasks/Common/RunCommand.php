<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Tasks;
use EasyDeployWorkflows\Tasks\AbstractServerTask;
use EasyDeployWorkflows\Tasks\TaskRunInformation;


class RunCommand extends AbstractServerTask  {

	/**
	 * @var string
	 */
	protected $command;

	/**
	 * @var string
	 */
	protected $changeToDirectory;

	/**
	 * @var bool
	 */
	protected $runInBackground = FALSE;

	/**
	 * @param string $script
	 * @return $this
	 */
	public function setCommand($script) {
		$this->command = $script;

		return $this;
	}

	/**
	 * @param string $changeToDirectory
	 * @return $this
	 */
	public function setChangeToDirectory($changeToDirectory) {
		$this->changeToDirectory = $changeToDirectory;

		return $this;
	}

	/**
	 * @param boolean $runInBackground
	 * @return $this
	 */
	public function setRunInBackground($runInBackground) {
		$this->runInBackground = $runInBackground;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getRunInBackground() {
		return $this->runInBackground;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @param \EasyDeploy_AbstractServer $server
	 * @return mixed
	 */
	protected function runOnServer(TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {
		$command = $this->command;
		if ($this->runInBackground) {
			$command .= ' >/dev/null &';
		}
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
	 * @throws InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->command)) {
			throw new InvalidConfigurationException('Command not set');
		}

		return true;
	}
}

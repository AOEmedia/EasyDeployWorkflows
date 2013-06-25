<?php

namespace EasyDeployWorkflows\Workflows\Application;

use EasyDeployWorkflows\Workflows as Workflows;
use EasyDeployWorkflows\Workflows\Exception as Exception;


/**
 * Configuration for the Basic Application Workflow
 *
 */
class ReleaseFolderApplicationConfiguration extends Workflows\AbstractWorkflowConfiguration {

	/**
	 * @var the command for configuring the application
	 */
	protected $setupCommand;

	/**
	 * @var array
	 */
	protected $preSetupTasks = array();

	/**
	 * @var array
	 */
	protected $postSetupTasks = array();

	/**
	 * @param \EasyDeployWorkflows\Workflows\Application\the $configurationCommand
	 */
	public function setSetupCommand($configurationCommand) {
		$this->setupCommand = $configurationCommand;
	}

	/**
	 * @return \EasyDeployWorkflows\Workflows\Application\the
	 */
	public function getSetupCommand() {
		return $this->setupCommand;
	}

	/**
	 * @param $name
	 * @param \EasyDeployWorkflows\Tasks\AbstractTask $step
	 * @throws \EasyDeployWorkflows\Workflows\Exception\DuplicateStepAssignmentException
	 */
	public function addPreSetupTask($name, \EasyDeployWorkflows\Tasks\AbstractTask $step) {
		if (isset($this->preSetupTasks[$name])) {
			throw new \EasyDeployWorkflows\Workflows\Exception\DuplicateStepAssignmentException($name.' already existend!');
		}
		$step->validate();
		$this->preSetupTasks[$name] = $step;
	}

	/**
	 * @param $name
	 * @param \EasyDeployWorkflows\Tasks\AbstractTask $step
	 * @throws \EasyDeployWorkflows\Workflows\Exception\DuplicateStepAssignmentException
	 */
	public function addPostSetupTask($name, \EasyDeployWorkflows\Tasks\AbstractTask $step) {
		if (isset($this->postSetupTasks[$name])) {
			throw new \EasyDeployWorkflows\Workflows\Exception\DuplicateStepAssignmentException($name.' already existend!');
		}
		$step->validate();
		$this->postSetupTasks[$name] = $step;
	}

	public function getPreSetupTasks() {
		return $this->preSetupTasks;
	}

	public function getPostSetupTasks() {
		return $this->postSetupTasks;
	}


	/**
	 * @param string $webRoot
	 * @return self
	 */
	public function setReleaseBaseFolder($folder) {
		$this->setFolder($folder,'ReleaseBaseFolder',0);
		return $this;
	}

	/**
	 * @return string
	 *
	 */
	public function getReleaseBaseFolder() {
		return $this->getFolder('ReleaseBaseFolder',0);
	}


	/**
	 * @param string $webRoot
	 * @return self
	 */
	public function setSharedFolder($folder) {
		$this->setFolder($folder,'SharedFolder',0);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSharedFolder() {
		return $this->getFolder('SharedFolder',0);
	}

	/**
	 * @return boolean
	 */
	public function hasSharedFolder() {
		return $this->getSharedFolder() !='';
	}


	/**
	 * @return array
	 */
	public function getInstallServers() {
		return $this->getServers('installserver');
	}

	/**
	 * @return bool
	 */
	public function hasInstallServers() {
		return count($this->getInstallServers()) > 0;
	}

	/**
	 * @param string $hostName
	 * @return self
	 */
	public function addInstallServer($hostName) {
		$this->addServer($hostName,'installserver');
		return $this;
	}


	/**
	 * @return string
	 */
	public function getWorkflowClassName() {
		return 'EasyDeployWorkflows\Workflows\Application\ReleaseFolderApplicationWorkflow';
	}

	/**
	 * @return bool
	 */
	public function validate() {
		if(!$this->getReleaseBaseFolder() !='') {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException("Please configure ReleaseBaseFolder: ".get_class($this));
		}

		if (!$this->hasSource()) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException("No download Source given: ".get_class($this));
		}

		return true;
	}

}
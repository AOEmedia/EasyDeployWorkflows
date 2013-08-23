<?php

namespace EasyDeployWorkflows\Workflows\Application;

use EasyDeployWorkflows\Workflows as Workflows;
use EasyDeployWorkflows\Workflows\Exception as Exception;


/**
 * Configuration for the Basic Application Workflow
 *
 */
abstract class AbstractBaseApplicationConfiguration extends Workflows\AbstractWorkflowConfiguration {

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
     * @var array
     */
    protected $smokeTestTasks = array();

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

    /**
     * @param $name
     * @param \EasyDeployWorkflows\Tasks\AbstractTask $task
     */
    public function addSmokeTestTask($name, \EasyDeployWorkflows\Tasks\AbstractTask $task) {
        if (isset($this->smokeTestTasks[$name])) {
            throw new \EasyDeployWorkflows\Workflows\Exception\DuplicateStepAssignmentException($name.' already existend!');
        }
        $task->validate();
        $this->smokeTestTasks[$name] = $task;
    }

	/**
	 * @return array
	 */
	public function getPreSetupTasks() {
		return $this->preSetupTasks;
	}

	/**
	 * @return array
	 */
	public function getPostSetupTasks() {
		return $this->postSetupTasks;
	}

    /**
     * @return array
     */
    public function getSmokeTestTasks() {
        return $this->smokeTestTasks;
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

}
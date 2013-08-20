<?php

namespace EasyDeployWorkflows\Workflows\Application;

use EasyDeployWorkflows\Workflows as Workflows;

class BaseApplicationWorkflow extends Workflows\TaskBasedWorkflow {

	/**
	 * @var AbstractWorkflowConfiguration
	 */
	protected $workflowConfiguration;


	/**
	 * Possibility to add some tasks
	 *
	 * @return void
	 */
	protected function addPreSetupTasks() {
		foreach ($this->workflowConfiguration->getPreSetupTasks() as $name => $task) {
			$this->addTask($name,$task);
		}
	}

	/**
	 * @return \EasyDeployWorkflows\Tasks\Common\WriteVersionFile
	 */
	protected function getWriteVersionFileTask($targetPathForVersionFile) {
		$task = new \EasyDeployWorkflows\Tasks\Common\WriteVersionFile();
		$task->setTargetPath($targetPathForVersionFile);
		$task->setVersion($this->workflowConfiguration->getReleaseVersion());
		return $task;
	}

	/**
	 * Installation of the application
	 *
	 * @return \EasyDeployWorkflows\Tasks\Common\RunCommand
	 */
	protected function getSetupTask($applicationRootFolder)
	{
		$task = new \EasyDeployWorkflows\Tasks\Common\RunCommand();
		$task->setChangeToDirectory($applicationRootFolder);
		$command = $this->replaceMarkers($this->workflowConfiguration->getSetupCommand());
		$task->setCommand($command);
		return $task;
	}


	/**
	 * Possibility to add some tasks
	 *
	 * @return void
	 */
	protected function addPostSetupTasks() {
		foreach ($this->workflowConfiguration->getPostSetupTasks() as $name => $task) {
			$this->addTask($name,$task);
		}
	}

	/**
	 * @TODO
	 */
	protected function addPostSwitchTasks() {

	}



}
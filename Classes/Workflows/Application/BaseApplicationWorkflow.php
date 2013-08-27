<?php

namespace EasyDeployWorkflows\Workflows\Application;

use EasyDeployWorkflows\Tasks\Common\RunCommand;
use EasyDeployWorkflows\Tasks\Common\WriteVersionFile;
use EasyDeployWorkflows\Workflows as Workflows;

class BaseApplicationWorkflow extends Workflows\TaskBasedWorkflow {

	/**
	 * @var AbstractBaseApplicationConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * Possibility to add some tasks
	 *
	 * @return void
	 */
	protected function addPreSetupTasks() {
		foreach ($this->workflowConfiguration->getPreSetupTasks() as $name => $task) {
			$this->addTask($name, $task);
		}
	}

	/**
	 * @param string $targetPathForVersionFile
	 * @return WriteVersionFile
	 */
	protected function getWriteVersionFileTask($targetPathForVersionFile) {
		$task = new WriteVersionFile();
		$task->setTargetPath($targetPathForVersionFile);
		$task->setVersion($this->workflowConfiguration->getReleaseVersion());

		return $task;
	}

	/**
	 * Installation of the application
	 *
	 * @param string $applicationRootFolder
	 * @return RunCommand
	 */
	protected function getSetupTask($applicationRootFolder) {
		$task = new RunCommand();
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
			$this->addTask($name, $task);
		}
	}

	/**
	 * @TODO
	 */
	protected function addPostSwitchTasks() {

	}

}

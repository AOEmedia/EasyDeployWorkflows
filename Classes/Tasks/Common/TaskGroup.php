<?php

namespace EasyDeployWorkflows\Tasks\Common;


class TaskGroup extends \EasyDeployWorkflows\Tasks\AbstractTask  {

	/**
	 * @var AbstractTask[]
	 */
	protected $tasks = array();

	/**
	 * @var string
	 */
	protected $headline = '';

	/**
	 * @param string $headline
	 */
	public function setHeadline($headline) {
		$this->headline = $headline;
	}

	/**
	 * @return string
	 */
	public function getHeadline() {
		return $this->headline;
	}

	/**
	 * @param string $name
	 * @param AbstractTask $task
	 * @throws \EasyDeployWorkflows\Tasks\DuplicateStepAssignmentException
	 */
	public function addTask($name, \EasyDeployWorkflows\Tasks\AbstractTask $task) {
		if (isset($this->tasks[$name])) {
			throw new \EasyDeployWorkflows\Tasks\DuplicateStepAssignmentException($name . ' already exists!');
		}
		$task->validate();
		$this->tasks[$name] = $task;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	public function run(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation) {
		if (empty($this->tasks)) {
			$this->logger->log('No Tasks');
		}
		foreach ($this->tasks as $taskName => $task) {
			$this->logger->log('[Task] ' . $taskName);
			$this->logger->addLogIndentLevel();
			$task->run($taskRunInformation);
			$this->logger->log('[Task Successful]', \EasyDeployWorkflows\Logger\Logger::MESSAGE_TYPE_SUCCESS);
			$this->logger->removeLogIndentLevel();
		}
	}

	public function validate() {

	}
}
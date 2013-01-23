<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

require_once dirname(__FILE__) . '/Exception/DuplicateStepAssignmentException.php';

class TaskBasedWorkflow extends AbstractWorkflow {

	/**
	 * @var
	 */
	protected $tasks = array();

	/**
	 * @param $name
	 * @param \EasyDeployWorkflows\Tasks\AbstractTask $step
	 * @throws DuplicateStepAssignmentException
	 */
	public function addTask($name, \EasyDeployWorkflows\Tasks\AbstractTask $step) {
		if (isset($this->tasks[$name])) {
			throw new \EasyDeployWorkflows\Workflows\Exception\DuplicateStepAssignmentException($name.' already existend!');
		}
		$step->validate();
		$this->tasks[$name] = $step;
	}

	public function deploy() {
		$taskRunInformation = $this->createTaskRunInformation();
		$this->logger->log('[Workflow] '.$this->getWorkflowConfiguration()->getTitle().' ('.get_class($this).')');
		$this->logger->addLogIndentLevel();
		foreach ($this->tasks as $taskName => $task) {
			$this->logger->log('[Task] '.$taskName);
			$this->logger->addLogIndentLevel();
			try {
				$task->run($taskRunInformation);
				$this->logger->log('[Task Successful]',\EasyDeployWorkflows\Logger\Logger::MESSAGE_TYPE_SUCCESS);
			}
			catch (\Exception $e) {
				$this->logger->log('[TASK EXCEPTION] '.$e->getMessage(),\EasyDeployWorkflows\Logger\Logger::MESSAGE_TYPE_ERROR);
				$this->logger->printLogFileInfoMessage();
				throw new \EasyDeployWorkflows\Exception\HaltAndRollback($taskName.' failed with message: "'.$e->getMessage().'"');
			}
			$this->logger->removeLogIndentLevel();
		}
		$this->logger->removeLogIndentLevel();
	}

	/**
	 * @param $classname
	 * @return mixed
	 */
	public function getTaskByName($name) {
		if (!isset( $this->tasks[$name])) {
			throw new \Exception('Task with name '.$name.' no added');
		}
		return $this->tasks[$name];
	}

	/**
	 * @return array
	 */
	public function getTasks() {
		return $this->tasks;
	}
}
<?php

namespace EasyDeployWorkflows\Tasks;

use EasyDeployWorkflows\Workflows;


/**
 * A task is something that encapsulates a certain part of todo
 */
abstract class AbstractTask extends \EasyDeployWorkflows\AbstractPart implements \EasyDeployWorkflows\ValidateableInterface {


	/**
	 * @return boolean
	 */
	public function isValid() {
		try {
			$this->validate();
		}catch(\EasyDeployWorkflows\Exception\InvalidConfigurationException $e) {
			return false;
		}
		return true;
	}

	/**
	 * @param $string
	 * @param TaskRunInformation $taskRunInformation
	 * @return string
	 */
	protected function replaceConfigurationMarkersWithTaskRunInformation($string,\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation) {
		return $this->replaceConfigurationMarkers($string,$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration());
	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	public function validate() {
		return false;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	abstract public function run(TaskRunInformation $taskRunInformation);

}
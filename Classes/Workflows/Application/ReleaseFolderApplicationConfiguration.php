<?php

namespace EasyDeployWorkflows\Workflows\Application;

use EasyDeployWorkflows\Workflows as Workflows;
use EasyDeployWorkflows\Workflows\Exception as Exception;


/**
 * Configuration for the Basic Application Workflow
 *
 */
class ReleaseFolderApplicationConfiguration extends AbstractBaseApplicationConfiguration {




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
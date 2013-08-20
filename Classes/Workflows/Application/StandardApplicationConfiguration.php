<?php

namespace EasyDeployWorkflows\Workflows\Application;

use EasyDeployWorkflows\Workflows as Workflows;
use EasyDeployWorkflows\Workflows\Exception as Exception;


/**
 * Configuration for the Basic Application Workflow
 *
 */
class StandardApplicationConfiguration extends AbstractBaseApplicationConfiguration {

	/**
	 * @var string
	 */
	protected $setupCommand = 'rsync -az . ###targetfolder###';


	/**
	 * @param string $webRoot
	 */
	public function setInstallationTargetFolder($installationTargetFolder) {
		$this->setFolder($installationTargetFolder,'InstallationTargetFolder',0);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getInstallationTargetFolder() {
		return $this->getFolder('InstallationTargetFolder',0);
	}

	/**
	 * @return bool
	 */
	public function hasInstallationTargetFolder() {
		return $this->getInstallationTargetFolder() != '';
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
	 * @return SimpleApplicationConfiguration
	 */
	public function addInstallServer($hostName) {
		$this->addServer($hostName,'installserver');
		return $this;
	}


	/**
	 * @return string
	 */
	public function getWorkflowClassName() {
		return 'EasyDeployWorkflows\Workflows\Application\StandardApplicationWorkflow';
	}

	/**
	 * @return bool
	 */
	public function validate() {
		if(!$this->hasInstallServers()) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException("Please configure at least one server for workflow: ".get_class($this));
		}

		if(!$this->hasInstallationTargetFolder()) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException("Please configure the target folder for workflow: ".get_class($this));
		}

		if (!$this->hasSource()) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException("No download Source given: ".get_class($this));
		}

		return true;
	}

}
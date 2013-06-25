<?php

namespace EasyDeployWorkflows\Workflows\Application;

use EasyDeployWorkflows\Workflows as Workflows;
use EasyDeployWorkflows\Workflows\Exception as Exception;


/**
 * Configuration for the Magento Application Workflow
 *
 */
class MagentoApplicationConfiguration extends ReleaseFolderApplicationConfiguration {

	/**
	 * @var string
	 */
	protected $setupCommand = './Setup/Setup.sh';

	/**
	 * @return bool
	 */
	public function validate() {
		parent::validate();

		/*if(!$this->hasSharedFolder()) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException("Please configure SharedFolder: ".get_class($this));
		}
		*/
		return true;
	}

	/**
	 * @return string
	 */
	public function getWorkflowClassName() {
		return 'EasyDeployWorkflows\Workflows\Application\MagentoApplicationWorkflow';
	}



}
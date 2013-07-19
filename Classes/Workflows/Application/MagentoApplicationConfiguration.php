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

	protected $reindexAllMode;

	const REINDEX_MODE_NONE = 0;
	const REINDEX_MODE_FOREGROUND = 1;
	const REINDEX_MODE_BACKGROUND = 2;

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
	 * @param mixed $reindexAllMode
	 * @return self
	 */
	public function setReindexAllMode($reindexAllMode) {
		$this->reindexAllMode = $reindexAllMode;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getReindexAllMode() {
		if (!isset($this->reindexAllMode) || !in_array($this->reindexAllMode,array(0,1,2))) {
			return self::REINDEX_MODE_NONE;
		}
		return $this->reindexAllMode;
	}



	/**
	 * @return string
	 */
	public function getWorkflowClassName() {
		return 'EasyDeployWorkflows\Workflows\Application\MagentoApplicationWorkflow';
	}



}
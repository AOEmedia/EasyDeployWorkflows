<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

abstract class AbstractWorkflowConfiguration extends AbstractConfiguration {

	/**
	 * @var string
	 */
	protected $deploymentSource = '';

	/**
	 * @var boolean
	 */
	protected $installSilent = false;

	/**
	 * Flag to enable/disable verbose mode.
	 *
	 * @var boolean
	 */
	protected $verbose = FALSE;

	/**
	 * @param $packageSource
	 */
	public function setDeploymentSource($packageSource) {
		$this->deploymentSource = $packageSource;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getDeploymentSource() {
		return $this->deploymentSource;
	}

	/**
	 * @param boolean $installSilent
	 */
	public function setInstallSilent($installSilent) {
		$this->installSilent = $installSilent;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getInstallSilent() {
		return $this->installSilent;
	}

	/**
	 * @param  boolean $verboseMode
	 * @return $this
	 */
	public function setVerboseMode($verboseMode) {
		$this->verbose = filter_var($verboseMode, FILTER_VALIDATE_BOOLEAN);
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getVerboseMode() {
		return $this->verbose;
	}

	/**
	 * @return string
	 */
	abstract function getWorkflowClassName();
}

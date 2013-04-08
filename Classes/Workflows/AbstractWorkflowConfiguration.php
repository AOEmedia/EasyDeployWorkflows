<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

abstract class AbstractWorkflowConfiguration extends AbstractConfiguration {

	/**
	 * @var string
	 */
	protected $downloadSource;

	/**
	 * @var boolean
	 */
	protected $installSilent = false;

	/**
	 * @param \EasyDeployWorkflows\Source\DownloadSourceInterface $packageSource
	 */
	public function setDownloadSource(\EasyDeployWorkflows\Source\DownloadSourceInterface $packageSource) {
		$this->downloadSource = $packageSource;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function hasDownloadSource() {
		return isset($this->downloadSource);
	}

	/**
	 * @return \EasyDeployWorkflows\Source\DownloadSourceInterface
	 */
	public function getDownloadSource() {
		return $this->downloadSource;
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
	 * @return string
	 */
	abstract function getWorkflowClassName();
}

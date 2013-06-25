<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

abstract class AbstractWorkflowConfiguration extends AbstractConfiguration {

	/**
	 * @var \EasyDeployWorkflows\Source\SourceInterface
	 */
	protected $source;

	/**
	 * @var boolean
	 */
	protected $installSilent = false;

	/**
	 * @var string
	 */
	protected $releaseVersion;

	/**
	 * @var string
	 */
	protected $deliveryFolder;

	/**
	 * @param string $deliveryFolder
	 * @return \EasyDeployWorkflows\Workflows\InstanceConfiguration
	 */
	public function setDeliveryFolder($deliveryFolder) {
		$this->deliveryFolder = rtrim($deliveryFolder,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasDeliveryFolder() {
		return $this->deliveryFolder != '';
	}

	/**
	 * The delivery folder
	 * Always ending with "/"
	 * @return string
	 */
	public function getDeliveryFolder() {
		return $this->deliveryFolder;
	}

	/**
	 * @param string $releaseVersion
	 * @return self
	 */
	public function setReleaseVersion($releaseVersion)
	{
		$this->releaseVersion = $releaseVersion;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getReleaseVersion()
	{
		return $this->releaseVersion;
	}

	/**
	 * @param \EasyDeployWorkflows\Source\SourceInterface $packageSource
	 * @return self
	 */
	public function setSource(\EasyDeployWorkflows\Source\SourceInterface $packageSource) {
		$this->source = $packageSource;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function hasSource() {
		return isset($this->source);
	}

	/**
	 * @return \EasyDeployWorkflows\Source\SourceInterface
	 */
	public function getSource() {
		return $this->source;
	}

	/**
	 * @param boolean $installSilent
	 * @return self
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

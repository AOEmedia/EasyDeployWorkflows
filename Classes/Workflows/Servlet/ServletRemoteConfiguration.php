<?php
namespace EasyDeployWorkflows\Workflows\Servlet;

use EasyDeployWorkflows\Workflows as Workflows;
use EasyDeployWorkflows\Workflows\Exception as Exception;

class ServletRemoteConfiguration extends ServletConfiguration {

	/**
	 * @var string
	 */
	protected $tomcatHostname = 'localhost';

	/**
	 * @param string $tomcatHostname
	 */
	public function setTomcatHostname($tomcatHostname) {
		$this->tomcatHostname = $tomcatHostname;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTomcatHostname() {
		return $this->tomcatHostname;
	}

	/**
	 * @return bool
	 */
	public function getIsLocalTomcat() {
		return $this->tomcatHostname == 'localhost';
	}

	/**
	 * @return bool
	 */
	public function getIsRemoteTomcat() {
		return !$this->getIsLocalTomcat();
	}

	/**
	 * @return string
	 */
	public function getWorkflowClassName() {
		return 'EasyDeployWorkflows\Workflows\Servlet\ServletRemoteWorkflow';
	}


	/**
	 * @throws Exception\InvalidConfigurationException
	 * @return boolean
	 */
	public function validate() {
		if($this->getTomcatVersion() == '') {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('Invalid tomcat version '.$this->getTomcatVersion());
		}

		if(trim($this->tomcatHostname) == '') {
			$message = 'Please configure a tomcat host name';
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException($message);
		}

		if(trim($this->tomcatPassword) == '') {
			$message = 'Please configured a tomcat password for workflow: '.get_class($this);
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException($message);
		}

		if(trim($this->tomcatUsername) == '') {
			$message = 'Please configure a tomcat username for workflow: '.get_class($this);
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException($message);
		}

		return true;
	}
}

<?php

use EasyDeployWorkflows\Workflows\Servlet;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';

class ServletRemoteConfigurationTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var EasyDeployWorkflows\Workflows\Servlet\ServletRemoteConfiguration
	 */
	protected $configuration;

	/**
	 * @return void
	 */
	public function setUp() {
		$this->configuration = new EasyDeployWorkflows\Workflows\Servlet\ServletRemoteConfiguration();
	}

	/**
	 * @test
	 */
	public function setTomcatHostname() {
		$this->assertEquals(array(), $this->configuration->getServletServers());
		$this->assertEquals('tomcat.tld',$this->configuration->setTomcatHostname('tomcat.tld')->getTomcatHostname());
	}
}
<?php

use EasyDeployWorkflows\Workflows\Servlet as Servlet;
use EasyDeployWorkflows\Workflows as Workflows;


class ServletRemoteWorkflowTest extends AbstractMockedTest {

	/**
	 *
	 * @test
	 * @return void
	 */
	public function canDeployToTwoTomcatServers() {
		$this->requireEasyDeployClassesOrSkip();

		$workflowConfiguration = new Servlet\ServletRemoteConfiguration();
		$instanceConfiguration = new Workflows\InstanceConfiguration();

		$workflowConfiguration
				->setTomcatHostname('tomcat.local')
				->setTomcatPort(8080)
				->setTomcatUsername('foo')
				->setTomcatPassword('bar')
				->setTomcatVersion('6')
				->setDeploymentSource('/home/homer.simpson/###releaseversion###/somedownloadpackage.tar.gz')
				->setInstallSilent(false)
				->setReleaseVersion('4711');

		$instanceConfiguration
				->setProjectName('nasa')
				->addAllowedDeployServer('allowedserver')
				->setEnvironmentName('deploy')
				->setDeliveryFolder('/home/download/###projectname###/###releaseversion###');

			/** @var $workflow  EasyDeployWorkflows\Workflows\Servlet\ServletRemoteWorkflow */
		$workflow = new EasyDeployWorkflows\Workflows\Servlet\ServletRemoteWorkflow($instanceConfiguration,$workflowConfiguration);
		$this->assertEquals(count($workflow->getTasks()),4,'expected 4 tasks in the workflow');


		$this->assertTrue($workflowConfiguration->getIsRemoteTomcat());
		//First task downloads from correct url
		$dowloadFromCiServerTask = $workflow->getTaskByName('Download tracker war to local delivery folder');
		$this->assertEquals(1,count($dowloadFromCiServerTask->getServers()));
		$this->assertInstanceOf('EasyDeployWorkflows\Tasks\Common\Download',$dowloadFromCiServerTask);
		$this->assertEquals('/home/homer.simpson/4711/somedownloadpackage.tar.gz', $dowloadFromCiServerTask->getDownloadSource());
		$this->assertEquals('/tmp/tracker_nasa_deploy_4711/', $dowloadFromCiServerTask->getTargetFolder());


		// last step deploys war local on 2 servers
		$servletTask = $workflow->getTaskByName('deploy the war file to the tomcat servers');
		$this->assertInstanceOf('EasyDeployWorkflows\Tasks\Servlet\DeployWarInTomcat', $servletTask);
		$this->assertEquals(1,count($servletTask->getServers()));
		$this->assertEquals(8080,$servletTask->getTomcatPort());
		$this->assertEquals('foo',$servletTask->getTomcatUser());
		$this->assertEquals('/tmp/tracker_nasa_deploy_4711/somedownloadpackage.tar.gz',$servletTask->getWarFileSourcePath());
	}
}
<?php

use EasyDeployWorkflows\Workflows\Servlet as Servlet;
use EasyDeployWorkflows\Workflows as Workflows;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';
require_once EASYDEPLOY_WORKFLOW_ROOT . 'Tests/Mocked/AbstractMockedTest.php';

class ServletWorkflowTest extends AbstractMockedTest {

	/**
	 *
	 * @test
	 * @return void
	 */
	public function canDeployToTwoTomcatServers() {
		$this->requireEasyDeployClassesOrSkip();

		$workflowConfiguration = new Servlet\ServletConfiguration();
		$instanceConfiguration = new Workflows\InstanceConfiguration();

		$workflowConfiguration
				->addServletServer('solr1.company.com')
				->addServletServer('solr2.company.com')
				->setTomcatPort(8080)
				->setTomcatUsername('foo')
				->setTomcatPassword('bar')
				->setTomcatVersion('6')
				->setDownloadSource(new EasyDeployWorkflows\Source\DownloadSource('/home/homer.simpson/###releaseversion###/somedownloadpackage.tar.gz'))
				->setInstallSilent(false)
				->setReleaseVersion('4711');


		$instanceConfiguration
				->setProjectName('nasa')
				->addAllowedDeployServer('allowedserver')
				->setEnvironmentName('deploy')
				->setDeliveryFolder('/home/download/###projectname###/###releaseversion###');

			/** @var $workflow  EasyDeployWorkflows\Workflows\Servlet\ServletWorkflow */
		$workflow = new EasyDeployWorkflows\Workflows\Servlet\ServletWorkflow($instanceConfiguration,$workflowConfiguration);
		$this->assertEquals(count($workflow->getTasks()),4,'expected 4 tasks in the workflow');


		//First task downloads from correct url
		$dowloadFromCiServerTask = $workflow->getTaskByName('Download tracker war to local delivery folder');
		$this->assertEquals(1,count($dowloadFromCiServerTask->getServers()));
		$this->assertInstanceOf('EasyDeployWorkflows\Tasks\Common\Download',$dowloadFromCiServerTask);
		$this->assertEquals('/home/homer.simpson/###releaseversion###/somedownloadpackage.tar.gz', $dowloadFromCiServerTask->getDownloadSource()->getSourceSpecification());
		$this->assertEquals('/home/download/nasa/4711/', $dowloadFromCiServerTask->getTargetFolder());

		//second uploads to servlet servers
		$uploadToServletServersTask = $workflow->getTaskByName('Load tracker war to tmp folder on servlet servers');
		$this->assertEquals(2,count($uploadToServletServersTask->getServers()));
		$this->assertInstanceOf('EasyDeployWorkflows\Tasks\Common\Download',$uploadToServletServersTask);
		$this->assertEquals('/home/download/nasa/4711/somedownloadpackage.tar.gz', $uploadToServletServersTask->getDownloadSource()->getSourceSpecification());
		$this->assertEquals('/tmp/', $uploadToServletServersTask->getTargetFolder());

		// last step deploys war local on 2 servers
		$servletTask = $workflow->getTaskByName('deploy the war file to the tomcat servers');
		$this->assertInstanceOf('EasyDeployWorkflows\Tasks\Servlet\DeployWarInTomcat', $servletTask);
		$this->assertEquals(2,count($servletTask->getServers()));
		$this->assertEquals(8080,$servletTask->getTomcatPort());
		$this->assertEquals('foo',$servletTask->getTomcatUser());
		$this->assertEquals('/tmp/somedownloadpackage.tar.gz',$servletTask->getWarFileSourcePath());
	}

}
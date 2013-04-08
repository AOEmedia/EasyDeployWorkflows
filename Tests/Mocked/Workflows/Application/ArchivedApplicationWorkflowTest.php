<?php


use EasyDeployWorkflows\Workflows as Workflows;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';
require_once EASYDEPLOY_WORKFLOW_ROOT . 'Tests/Mocked/AbstractMockedTest.php';


class ArchivedApplicationWorkflowTest extends AbstractMockedTest {


	/**
	 * @test
	 */
	public function canDeploy() {
		$this->requireEasyDeployClassesOrSkip();
		$workflowConfiguration = new \EasyDeployWorkflows\Workflows\Application\ArchivedApplicationConfiguration();
		$workflowConfiguration->addInstallServer('www.mywebserver.de');
		$workflowConfiguration->setInstallationTargetFolder('/webroot');
		$workflowConfiguration->setDownloadSource(new EasyDeployWorkflows\Source\DownloadSource('http://www.jenkins.my/artifacts/my.tar.gz'));

		$instanceConfiguration = new Workflows\InstanceConfiguration();
		$instanceConfiguration->setProjectName('project');
		$instanceConfiguration->setEnvironmentName('production');
		$instanceConfiguration->addAllowedDeployServer('localhost');
		$instanceConfiguration->setDeliveryFolder('/here');

		$workflow = new \EasyDeployWorkflows\Workflows\Application\ArchivedApplicationWorkflow($instanceConfiguration,$workflowConfiguration);
		$tasks = $workflow->getTasks();
	}


}
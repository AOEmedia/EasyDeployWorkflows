<?php

namespace EasyDeployWorkflows\Tests\Acceptance\Workflows;

use EasyDeployWorkflows\Workflows as Workflows;

require_once EASYDEPLOY_WORKFLOW_ROOT . 'Classes/Autoloader.php';
require_once EASYDEPLOY_WORKFLOW_ROOT . 'Tests/Mocked/AbstractMockedTest.php';


class ArchivedApplicationWithNFSServerWorkflowTest extends \EasyDeployWorkflows\Tests\Acceptance\AbstractAcceptanceTest {



	/**
	 * @test
	 */
	public function canDeployBasicApplicationToTargetFolder() {
		$localServer = new \EasyDeploy_LocalServer();

		$workflowConfiguration = new \EasyDeployWorkflows\Workflows\Application\ArchivedApplicationWithNFSServerConfiguration();
		$workflowConfiguration->setInstallationTargetFolder($this->targetFolder);
		$workflowConfiguration->setDownloadSource(new \EasyDeployWorkflows\Source\DownloadSource(EASYDEPLOY_WORKFLOW_ROOT.'Tests/Acceptance/Fixtures/Source/BasicApplication.tar.gz'));
		$workflowConfiguration->setNFSServer('localhost');

		$instanceConfiguration = $this->getInitialisedInstanceConfiguration($localServer->getHostname());

		$workflow = new \EasyDeployWorkflows\Workflows\Application\ArchivedApplicationWithNFSServerWorkflow($instanceConfiguration,$workflowConfiguration);
		$workflow->deploy();

		$this->assertTrue(is_file($this->targetFolder.'/version.txt'),'Expected to have the version.txt file from archive in the target folder:'.$this->targetFolder.'/version.txt');

	}




}
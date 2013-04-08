<?php

namespace EasyDeployWorkflows\Workflows\AOE;

use EasyDeployWorkflows\Workflows as Workflows;

class ArchivedApplicationWithNFSServerWorkflow extends ArchivedApplicationWorkflow {

	/**
	 * @var BasicApplicationWithNFSServerConfiguration
	 */
	protected $workflowConfiguration;

	protected function getRunNFSSyncScriptTask()
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\RunScript();
		$step->addServersByName($this->workflowConfiguration->getWebServers());
		$projectName = $this->instanceConfiguration->getProjectName();
		$environmentName = $this->instanceConfiguration->getEnvironmentName();
		$script = '/usr/local/bin/deployment_' . $projectName . '_' . $environmentName . '_nfs_sync';
		$step->setScript($script);
		$step->setIsOptional(true);
		return $step;
	}




}
<?php

namespace EasyDeployWorkflows\Workflows\Solr;

use EasyDeployWorkflows\Workflows as Workflows;

class SolrWorkflow extends Workflows\AbstractWorkflow {

	/**
	 * @var \EasyDeployWorkflows\Workflows\Solr\SolrConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * @param string $releaseVersion
	 * @return mixed|void
	 */
	public function deploy() {
		$task = new \EasyDeployWorkflows\Tasks\Common\CheckCorrectDeployNode();
		$task->run($this->createTaskRunInformation());

		$masterServers = $this->workflowConfiguration->getMasterServers();
		$deployService =  new \EasyDeploy_DeployService($this->getInstallStrategy());
		$this->initDeployService($deployService);
		$deploymentPackage = $this->replaceMarkers($this->workflowConfiguration->getDeploymentSource());

		foreach ($masterServers as $server) {
			$solrServer = $this->getServer($server);
			$this->out('Start deploying SolrConf Package: "'.$deploymentPackage.'"', self::MESSAGE_TYPE_INFO);
			$deployService->deploy( $solrServer, $this->workflowConfiguration->getReleaseVersion(), $deploymentPackage);
			$this->reloadSolr($solrServer);
		}


	}

	protected function reloadSolr(\EasyDeploy_AbstractServer $server) {
		$restartCommand = $this->workflowConfiguration->getRestartCommand();
		if (empty($restartCommand) == '') {
			$this->out('No restart Command is Set for the deployment!',self::MESSAGE_TYPE_WARNING);
			return;
		}
		$server->run($restartCommand);
	}

	protected function getInstallStrategy() {
		$strategy = new \EasyDeploy_InstallStrategy_PHPInstaller();
		$strategy->setSilentMode($this->workflowConfiguration->getInstallSilent());
		return $strategy;
	}

	/**
	 * @param EasyDeploy_DeployService $deployService
	 */
	protected function initDeployService(\EasyDeploy_DeployService $deployService ) {
		$deployService->setEnvironmentName($this->instanceConfiguration->getEnvironmentName());
		$deployService->setDeliveryFolder($this->replaceMarkers($this->instanceConfiguration->getDeliveryFolder()));
		$deployService->setSystemPath($this->replaceMarkers($this->workflowConfiguration->getInstancePath()));
	}
}
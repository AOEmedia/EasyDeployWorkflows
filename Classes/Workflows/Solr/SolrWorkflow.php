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
		$this->logger->log('[Workflow] SolrWorkflow');
		$this->logger->addLogIndentLevel();

		$deliveryFolder = $this->replaceMarkers($this->instanceConfiguration->getDeliveryFolder());

		$this->logger->log('[Task] CheckCorrectDeployNode');
		$task = new \EasyDeployWorkflows\Tasks\Common\CheckCorrectDeployNode();
		$task->run($this->createTaskRunInformation());

		$this->logger->log('[Task] Download Package');
		$task = new \EasyDeployWorkflows\Tasks\Common\Download();
		$task->addServersByName($this->workflowConfiguration->getMasterServers());
		$task->setDownloadSource($this->replaceMarkers($this->workflowConfiguration->getDeploymentSource()));
		$task->setTargetFolder($deliveryFolder);
		$task->run($this->createTaskRunInformation());

		$packageFileName = $this->getFilenameFromPath($this->replaceMarkers($this->workflowConfiguration->getDeploymentSource()));
		$this->logger->log('[Task] Unzip Solr Package');
		$task = new \EasyDeployWorkflows\Tasks\Common\Untar();
		$task->addServersByName($this->workflowConfiguration->getMasterServers());
		$task->autoInitByPackagePath($deliveryFolder . '/' . $packageFileName);
		$task->setMode(\EasyDeployWorkflows\Tasks\Common\Untar::MODE_SKIP_IF_EXTRACTEDFOLDER_EXISTS);
		$task->run($this->createTaskRunInformation());

		$this->logger->log('[Task] Deploy Solr Package');
		$task = new \EasyDeployWorkflows\Tasks\Web\RunPackageInstallBinaries();
		$task->addServersByName($this->workflowConfiguration->getMasterServers());
		$task->setTargetSystemPath($this->replaceMarkers($this->workflowConfiguration->getInstancePath()));
		$task->setSilentMode($this->workflowConfiguration->getInstallSilent());
		$task->setPackageFolder($deliveryFolder . '/' . $this->getFileBaseName($packageFileName));
		$task->setNeedBackupToInstall(FALSE);
		$task->run($this->createTaskRunInformation());


		$restartCommand = $this->workflowConfiguration->getRestartCommand();
		if (empty($restartCommand) == '') {
			$this->logger->log('No restart Command is Set for the deployment!',\EasyDeployWorkflows\Logger\Logger::MESSAGE_TYPE_WARNING);
		}
		else {
			$this->logger->log('[Task] Reload Solrs');
			$task = new \EasyDeployWorkflows\Tasks\Common\RunScript();
			$task->setScript($restartCommand);
			$task->run($this->createTaskRunInformation());
		}


		$this->logger->log('[Workflow Successful]',\EasyDeployWorkflows\Logger\Logger::MESSAGE_TYPE_SUCCESS);
		$this->logger->removeLogIndentLevel();

	}

}
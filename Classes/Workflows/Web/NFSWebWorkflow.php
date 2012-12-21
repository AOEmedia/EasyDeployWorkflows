<?php

namespace EasyDeployWorkflows\Workflows\Web;

use EasyDeployWorkflows\Workflows as Workflows;

class NFSWebWorkflow extends Workflows\TaskBasedWorkflow {

	/**
	 * Can be used to do individual workflow initialisation and/or checks
	 */
	protected function workflowInitialisation() {
		$packageFileName = $this->getFilenameFromPath($this->workflowConfiguration->getDeploymentSource());
		$packageExtractedFolderName = $this->getFileBaseName($packageFileName);


		$this->addTask('check correct deploy node',
						new \EasyDeployWorkflows\Tasks\Common\CheckCorrectDeployNode());

		foreach ($this->workflowConfiguration->getIndexerDataFolders() as $folder) {
			$folder = $this->replaceMarkers($folder);
			$this->addTask('Create indexer folders "'.$folder.'"',
			$this->getIndexerFolderTask($folder));
		}


		$this->addTask('Download Package',
						$this->getDownloadPackageTask($packageExtractedFolderName));


		$this->addTask('Untar Package',
						$this->getUnzipPackageTask($packageFileName));


		$this->addTask('Install Package',
			$this->getInstallPackageTask($packageExtractedFolderName));


		$this->addTask('Run NFS Sync',
			$this->getRunNFSSyncScriptTask());

	}

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

	protected function getInstallPackageTask($packageExtractedFolderName)
	{
		$step = new \EasyDeployWorkflows\Tasks\Web\RunPackageInstallBinaries();
		$step->addServerByName($this->workflowConfiguration->getNFSServer());
		$step->setCreateBackupBeforeInstalling(false);
		$step->setPackageFolder($this->getFinalDeliveryFolder() . $packageExtractedFolderName);
		$step->setTargetSystemPath($this->replaceMarkers($this->workflowConfiguration->getWebRootFolder()));
		$step->setSilentMode($this->workflowConfiguration->getInstallSilent());
		return $step;
	}

	protected function getUnzipPackageTask($packageFileName)
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\Untar();
		$step->addServerByName($this->workflowConfiguration->getNFSServer());
		$step->autoInitByPackagePath($this->getFinalDeliveryFolder() . '/' . $packageFileName);
		$step->setMode(\EasyDeployWorkflows\Tasks\Common\Untar::MODE_SKIP_IF_EXTRACTEDFOLDER_EXISTS);
		return $step;
	}

	protected function getDownloadPackageTask($packageExtractedFolderName)
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\Download();
		$step->addServerByName($this->workflowConfiguration->getNFSServer());
		$step->setDownloadSource($this->workflowConfiguration->getDeploymentSource());
		$step->setTargetFolder($this->getFinalDeliveryFolder());
		$step->setNotIfPathExists($this->getFinalDeliveryFolder() . $packageExtractedFolderName);
		return $step;
	}

	protected function getIndexerFolderTask($folder)
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\CreateMissingFolder();
		$step->addServersByName($this->workflowConfiguration->getIndexerServers());
		$step->setFolder($folder);
		return $step;
	}

}
<?php

namespace EasyDeployWorkflows\Workflows\Application;

use EasyDeployWorkflows\Workflows as Workflows;

class MagentoApplicationWorkflow extends Workflows\TaskBasedWorkflow {

	/**
	 * @var MagentoApplicationConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * Can be used to do individual workflow initialisation and/or checks
	 */
	protected function workflowInitialisation() {

		$this->addTask('check correct deploy node',
			new \EasyDeployWorkflows\Tasks\Common\CheckCorrectDeployNode());

		$this->addTasksToDownloadFromSourceToReleaseFolder();
		$this->addMagentoInstallTasks();
		$this->addCleanupTasks();

	}

	protected function addTasksToDownloadFromSourceToReleaseFolder() {
		if ($this->workflowConfiguration->getSource() instanceof \EasyDeployWorkflows\Source\File\FileSourceInterface) {
			//we expect this to be an archive - so we download it to deliveryfolder first
			$task = new \EasyDeployWorkflows\Tasks\Common\SourceEvaluator();
			$task->setSource($this->workflowConfiguration->getSource());
			$task->setParentFolder($this->getFinalDeliveryFolder());
			$task->setNotIfPathExists($this->getFinalDeliveryFolder() . $this->workflowConfiguration->getSource()->getFileName());
			$task->addServersByName($this->workflowConfiguration->getInstallServers());
			$this->addTask('Download Filesource to Deliveryfolder',$task);

			$task = $this->getUnzipPackageTask();
			$task->addServersByName($this->workflowConfiguration->getInstallServers());
			$task->setChangeToDirectory($this->workflowConfiguration->getReleaseBaseFolder());
			$this->addTask('Untar Package', $task);

			$task = new \EasyDeployWorkflows\Tasks\Common\Rename();
			$task->addServersByName($this->workflowConfiguration->getInstallServers());
			$task->setSource($this->workflowConfiguration->getReleaseBaseFolder().$this->workflowConfiguration->getSource()->getFileName());
			$task->setTarget($this->workflowConfiguration->getReleaseBaseFolder().$this->workflowConfiguration->getReleaseVersion());
			$this->addTask('Rename Unzipped Package to Release', $task);


		}
		else {
			//we expect this to be an archive - so we download it to deliveryfolder first
			$source = $this->workflowConfiguration->getSource();
			$source->setIndividualTargetFolderName($this->workflowConfiguration->getReleaseVersion());
			$task = new \EasyDeployWorkflows\Tasks\Common\SourceEvaluator();
			$task->setSource($source);
			$task->setParentFolder($this->getFinalReleaseBaseFolder());
			$task->setNotIfPathExists($this->getFinalReleaseBaseFolder() . $this->workflowConfiguration->getReleaseVersion());
			$task->addServersByName($this->workflowConfiguration->getInstallServers());
			$this->addTask('Download Foldersource to Releasefolder',$task);
		}
	}

	/**
	 * Installation of Magento
	 *
	 * @return \EasyDeployWorkflows\Tasks\Web\RunPackageInstallBinaries
	 */
	protected function addMagentoInstallTasks()
	{
		$task = new \EasyDeployWorkflows\Tasks\Common\RunCommand();
		$task->setChangeToDirectory($this->getFinalReleaseBaseFolder());
		$task->setCommand($this->workflowConfiguration->getRelativeModmanPath().' deploy-all');
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Modman Deployment',$task);
	}

	/**
	 * Installation is simple copy
	 *
	 * @return \EasyDeployWorkflows\Tasks\Common\DeleteFolder
	 */
	protected function addCleanupTasks($extractedFolder)
	{
		$task = new \EasyDeployWorkflows\Tasks\Release\CleanupReleases();
		$task->setReleasesBaseFolder($this->getFinalReleaseBaseFolder());
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Cleanup old Releases',$task);

	}


	protected function getUnzipPackageTask()
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\Untar();
		$step->autoInitByPackagePath($this->getFinalDeliveryFolder() . '/' . $this->workflowConfiguration->getDownloadSource()->getFileName());
		$step->setMode(\EasyDeployWorkflows\Tasks\Common\Untar::MODE_SKIP_IF_EXTRACTEDFOLDER_EXISTS);
		return $step;
	}

	/**
	 * @return string
	 */
	protected function getFinalReleaseBaseFolder() {
		return $this->replaceMarkers($this->workflowConfiguration->getReleaseBaseFolder());
	}



}
<?php

namespace EasyDeployWorkflows\Workflows\Application;

use EasyDeployWorkflows\Workflows as Workflows;

class ReleaseFolderApplicationWorkflow extends BaseApplicationWorkflow {

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
		$this->addUpdateNextSymlinkTask();
		$this->addPreSetupTasks();
		$this->addWriteVersionFileTask();
		$this->addSetupTasks();
		$this->addSymlinkSharedFoldersTasks();
		$this->addPostSetupTaskGroup();
		$this->addSmokeTestTaskGroup();
		$this->addSwitchTask();
		$this->addPostSwitchTasks();
		$this->addCleanupTasks();

	}

	protected function addTasksToDownloadFromSourceToReleaseFolder() {
		if ($this->workflowConfiguration->getSource() instanceof \EasyDeployWorkflows\Source\File\FileSourceInterface) {
			//we expect this to be an archive - so we download it to deliveryfolder first
			if (!$this->workflowConfiguration->hasDeliveryFolder()) {
				throw new \Exception('Cannot proceed in the Workflow: A file source needs a deliveryfolder configured for storing the archive first! Please specify one in the workflowConfiguration first!');
			}

			$task = new \EasyDeployWorkflows\Tasks\Common\SourceEvaluator();
			$task->setSource($this->workflowConfiguration->getSource());
			$task->setParentFolder($this->getFinalDeliveryFolder());
			$task->setNotIfPathExists($this->getFinalDeliveryFolder() . $this->workflowConfiguration->getSource()->getFileName());
			$task->addServersByName($this->workflowConfiguration->getInstallServers());
			$this->addTask('Download Filesource to Deliveryfolder',$task);

			$task = $this->getUnzipPackageTask();
			$task->addServersByName($this->workflowConfiguration->getInstallServers());
			$this->addTask('Untar Package', $task);

			$task = new \EasyDeployWorkflows\Tasks\Common\Rename();
			$task->setMode(\EasyDeployWorkflows\Tasks\Common\Rename::MODE_SKIP_IF_TARGET_EXISTS);
			$task->addServersByName($this->workflowConfiguration->getInstallServers());
			$task->setSource($this->workflowConfiguration->getReleaseBaseFolder().$this->workflowConfiguration->getSource()->getFileNameWithOutExtension());
			$task->setTarget($this->workflowConfiguration->getReleaseBaseFolder().$this->workflowConfiguration->getReleaseVersion());
			$this->addTask('Rename Unzipped Package to Release', $task);

			$task = new \EasyDeployWorkflows\Tasks\Common\DeleteFile();
			$task->setFile($this->getFinalDeliveryFolder() . $this->workflowConfiguration->getSource()->getFileName());
			$task->addServersByName($this->workflowConfiguration->getInstallServers());
			$this->addTask('Delete downloaded package', $task);
		}
		else {
			/** @var \EasyDeployWorkflows\Source\Folder\FolderSourceInterface $source */
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

	protected function addUpdateNextSymlinkTask() {
		$task = new \EasyDeployWorkflows\Tasks\Release\UpdateNext();
		$task->setReleasesBaseFolder($this->getFinalReleaseBaseFolder());
		$task->setNextRelease($this->workflowConfiguration->getReleaseVersion());
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Update next symlink',$task);
	}



	/**
	 * add version file write
	 */
	protected function addWriteVersionFileTask() {
		$task = $this->getWriteVersionFileTask($this->getFinalReleaseBaseFolder().'next');
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Write Version File',$task);
	}

	/**
	 * Installation of Magento
	 *
	 * @return void
	 */
	protected function addSetupTasks()
	{
		$task = $this->getSetupTask($this->getFinalReleaseBaseFolder().'next');
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Setup Script',$task);
	}

	/**
	 * Symlinks media folder
	 */
	protected function addSymlinkSharedFoldersTasks() {

	}


	/**
	 */
	protected function addSmokeTestTaskGroup($additionalTasks = array())
	{
		$taskGroup = $this->getTaskGroup('Smoke Tests:',$this->workflowConfiguration->getSmokeTestTasks());
		foreach ($additionalTasks as $name => $task) {
			$taskGroup->addTask($name, $task);
		}
		$this->addTask('Smoke Tests',$taskGroup);
	}


	protected function addSwitchTask() {
		$task = new \EasyDeployWorkflows\Tasks\Release\UpdateCurrentAndPrevious();
		$task->setReleasesBaseFolder($this->getFinalReleaseBaseFolder());
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Switch current symlink',$task);
	}


	/**
	 * clean up old releases
	 */
	protected function addCleanupTasks()
	{
		$task = new \EasyDeployWorkflows\Tasks\Release\CleanupReleases();
		$task->setReleasesBaseFolder($this->getFinalReleaseBaseFolder());
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Cleanup old Releases',$task);

	}


	protected function getUnzipPackageTask()
	{
		$archivePath = $this->replaceMarkers($this->getFinalDeliveryFolder() . $this->workflowConfiguration->getSource()->getFileName());
		$step = new \EasyDeployWorkflows\Tasks\Common\Untar();
		$step->setPackagePath($archivePath);
		$step->setFolder($this->workflowConfiguration->getReleaseBaseFolder());
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
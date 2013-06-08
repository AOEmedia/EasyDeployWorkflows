<?php

namespace EasyDeployWorkflows\Workflows\Application;

use EasyDeployWorkflows\Workflows as Workflows;

class ArchivedApplicationWorkflow extends Workflows\TaskBasedWorkflow {

	/**
	 * @var BasicApplicationConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * Can be used to do individual workflow initialisation and/or checks
	 */
	protected function workflowInitialisation() {

		$this->addTask('check correct deploy node',
			new \EasyDeployWorkflows\Tasks\Common\CheckCorrectDeployNode());

		$task = $this->getDownloadPackageTask();
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Download Package',$task	);

		$task = $this->getUnzipPackageTask();
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Untar Package', $task);

		$extractedFolder = $this->replaceMarkers($this->getFinalDeliveryFolder()) . $this->getFileBaseName($this->workflowConfiguration->getSource()->getFileName());

		$task = $this->getInstallPackageTask($extractedFolder);
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Install Package', $task);

		$task = $this->getCleanupTask($extractedFolder);
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Cleanup Archive',$task);

	}

	/**
	 * Installation is simple copy
	 *
	 * @return \EasyDeployWorkflows\Tasks\Web\RunPackageInstallBinaries
	 */
	protected function getInstallPackageTask($extractedFolder)
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\Rsync();
		$step->setSourceFolder($extractedFolder);
		$step->setTargetFolder($this->replaceMarkers($this->workflowConfiguration->getInstallationTargetFolder()));
		return $step;
	}

	/**
	 * Installation is simple copy
	 *
	 * @return \EasyDeployWorkflows\Tasks\Common\DeleteFolder
	 */
	protected function getCleanupTask($extractedFolder)
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\DeleteFolder();
		$step->setFolder($extractedFolder);
		return $step;
	}



	protected function getDownloadPackageTask()
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\SourceEvaluator();
		$step->setSource($this->workflowConfiguration->getSource());
		$step->setParentFolder($this->getFinalDeliveryFolder());
		$step->setNotIfPathExists($this->getFinalDeliveryFolder() . $this->workflowConfiguration->getSource()->getFileName());
		return $step;
	}


	protected function getUnzipPackageTask()
	{
		$step = new \EasyDeployWorkflows\Tasks\Common\Untar();
		$step->autoInitByPackagePath($this->getFinalDeliveryFolder() . '/' . $this->workflowConfiguration->getSource()->getFileName());
		$step->setMode(\EasyDeployWorkflows\Tasks\Common\Untar::MODE_SKIP_IF_EXTRACTEDFOLDER_EXISTS);
		return $step;
	}


}
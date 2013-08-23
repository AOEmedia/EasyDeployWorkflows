<?php

namespace EasyDeployWorkflows\Workflows\Application;

use EasyDeployWorkflows\Workflows as Workflows;

class MagentoApplicationWorkflow extends ReleaseFolderApplicationWorkflow {

	/**
	 * @var MagentoApplicationConfiguration
	 */
	protected $workflowConfiguration;

    /**
     * Contains list of \EasyDeployWorkflows\Tasks\ elements
     * which then are added in addSmokeTestTasks method
     *
     * @var array
     */
    protected $smokeTestTasks = array();

	/**
	 * add version file write
	 */
	protected function addWriteVersionFileTask() {
		$task = new \EasyDeployWorkflows\Tasks\Common\WriteVersionFile();
		$task->setTargetPath($this->getFinalReleaseBaseFolder().'next/htdocs');
		$task->setVersion($this->workflowConfiguration->getReleaseVersion());
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Write Version File',$task);
	}

	/**
	 * Symlinks media folder
	 */
	protected function addSymlinkSharedFoldersTasks() {
		$sharedFolder = $this->replaceMarkers($this->workflowConfiguration->getSharedFolder());
		if (!empty($sharedFolder)) {
			$task = new \EasyDeployWorkflows\Tasks\Common\RunCommand();
			$task->setChangeToDirectory($this->getFinalReleaseBaseFolder().'next/htdocs');
			$task->setCommand('rm -rf media && ln -s '.$sharedFolder.'media media');
			$task->addServersByName($this->workflowConfiguration->getInstallServers());
			$this->addTask('Link shared folder',$task);
		}
	}

	/**
	 * See if commandline indexer can return a status
	 */
	protected function addSmokeTestTasks() {
        // add default smoke test
		$task = new \EasyDeployWorkflows\Tasks\Common\RunCommand();
		$task->setChangeToDirectory($this->getFinalReleaseBaseFolder().'next');
		$task->setCommand('php htdocs/shell/indexer.php status');
		$task->addServersByName($this->workflowConfiguration->getInstallServers());
		$this->addTask('Smoke Test - call status',$task);

        // add defined tasks
        foreach ($this->smokeTestTasks as $taskData) {
            $this->addTask($taskData['description'], $taskData['task']);
        }
	}

	/**
	 * Possibility to add some tasks
	 *
	 * @return void
	 */
	protected function addPostSetupTasks() {
		parent::addPostSetupTasks();

		$task = new \EasyDeployWorkflows\Tasks\Common\RunCommand();
		$task->setChangeToDirectory($this->getFinalReleaseBaseFolder().'next');
		$task->setCommand('php htdocs/shell/indexer.php --reindexall');
		$task->addServersByName($this->workflowConfiguration->getInstallServers());

		switch ($this->workflowConfiguration->getReindexAllMode()) {
			case MagentoApplicationConfiguration::REINDEX_MODE_NONE:
				return;
			break;
			case MagentoApplicationConfiguration::REINDEX_MODE_FOREGROUND:
				$this->addTask('Reindex all in foreground',$task);
				return;
			break;
			case MagentoApplicationConfiguration::REINDEX_MODE_BACKGROUND:
				$task->setRunInBackground(TRUE);
				$this->addTask('Reindex all in background',$task);
				return;
			break;
		}
	}

    /**
     * Save additional smoke test tasks internally for later execution
     *
     * @param $description
     * @param $task
     */
    public function addAdditionalSmokeTestTasks($description, $task) {
        $this->smokeTestTasks[] = array(
            'description' => $description,
            'task'        => $task,
        );
    }


}
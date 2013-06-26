<?php

namespace EasyDeployWorkflows\Tasks\Magento;

use EasyDeployWorkflows\Tasks;


/**
 * Depends on the Magento Backup Structure
 *
 */
class SetupDatabaseFromBackupStorage extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var string
	 */
	protected $backupSourceFolder;

	/**
	 * @var string - e.g. 'Setup/GetDbSettings.sh'
	 */
	protected $detectDbSettingsScriptPath;

	protected $databaseImportScript = 'mgdeployscripts/import_dump_diffable.sh -u "###DB_USER###" -p "###DB_PASSWORD###" -h "###DB_HOST###" -d "###DB_NAME###" -s "###BACKUPSOURCEFOLDER###/db/latest"';

	protected $changeToFolder;

	protected $dbName;
	protected $dbHost;
	protected $dbUser;
	protected $dbPassword;



	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	public function validate() {

		if (empty($this->backupSourceFolder)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('backupSource Folder not set');
		}

		return true;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation, \EasyDeploy_AbstractServer $server) {
		if (isset($this->detectDbSettingsScriptPath)) {
			$command ='source '.$this->detectDbSettingsScriptPath;
			$this->executeAndLog($server,$this->prependCommandWithChangeToFolder($command,$taskRunInformation));
			$this->dbName=getenv('DB_NAME');
			if ($this->dbName === false) {
				throw new \Exception('The script did not set the DB_HOST environment variable!');
			}
			$this->dbHost=getenv('DB_HOST');
			$this->dbUser=getenv('DB_USER');
			$this->dbPassword=getenv('DB_PASSWORD');
		}

		if (empty($this->dbName)) {
			throw new \Exception('No database host given - cannot setup from backupstorage');
		}


		$importCommand = $this->replaceConfigurationMarkersWithTaskRunInformation($this->databaseImportScript,$taskRunInformation);
		$importCommand = str_replace('###DB_HOST###',$this->replaceConfigurationMarkersWithTaskRunInformation($this->dbHost,$taskRunInformation),$importCommand);
		$importCommand = str_replace('###DB_NAME###',$this->replaceConfigurationMarkersWithTaskRunInformation($this->dbName,$taskRunInformation),$importCommand);
		$importCommand = str_replace('###DB_USER###',$this->replaceConfigurationMarkersWithTaskRunInformation($this->dbUser,$taskRunInformation),$importCommand);
		$importCommand = str_replace('###DB_PASSWORD###',$this->replaceConfigurationMarkersWithTaskRunInformation($this->dbPassword,$taskRunInformation),$importCommand);
		$importCommand = str_replace('###BACKUPSOURCEFOLDER###',$this->replaceConfigurationMarkersWithTaskRunInformation($this->backupSourceFolder,$taskRunInformation),$importCommand);
		$this->executeAndLog($server,$this->prependCommandWithChangeToFolder($importCommand,$taskRunInformation));
	}

	/**
	 * @param $command
	 * @return string
	 */
	protected function prependCommandWithChangeToFolder($command,$taskRunInformation) {
		if (isset($this->changeToFolder)) {
			$command ='cd '.$this->replaceConfigurationMarkersWithTaskRunInformation($this->changeToFolder,$taskRunInformation).'; '.$command;
		}
		return $command;
	}

	/**
	 * @param string $backupSourceFolder
	 * @return self
	 */
	public function setBackupSourceFolder($backupSourceFolder) {
		$this->backupSourceFolder = $backupSourceFolder;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getBackupSourceFolder() {
		return $this->backupSourceFolder;
	}

	/**
	 * @param $changeToFolder
	 * @return SetupDatabaseFromBackupStorage
	 */
	public function setChangeToFolder($changeToFolder) {
		$this->changeToFolder = $changeToFolder;
		return $this;
	}

	public function getChangeToFolder() {
		return $this->changeToFolder;
	}

	/**
	 * @param $databaseImportScript
	 * @return SetupDatabaseFromBackupStorage
	 */
	public function setDatabaseImportScript($databaseImportScript) {
		$this->databaseImportScript = $databaseImportScript;
		return $this;
	}

	public function getDatabaseImportScript() {
		return $this->databaseImportScript;
	}

	/**
	 * @param $dbHost
	 * @return SetupDatabaseFromBackupStorage
	 */
	public function setDbHost($dbHost) {
		$this->dbHost = $dbHost;
		return $this;
	}

	public function getDbHost() {
		return $this->dbHost;
	}

	/**
	 * @param $dbName
	 * @return SetupDatabaseFromBackupStorage
	 */
	public function setDbName($dbName) {
		$this->dbName = $dbName;
		return $this;
	}

	public function getDbName() {
		return $this->dbName;
	}

	/**
	 * @param $dbPassword
	 * @return self
	 */
	public function setDbPassword($dbPassword) {
		$this->dbPassword = $dbPassword;
		return $this;
	}

	public function getDbPassword() {
		return $this->dbPassword;
	}

	/**
	 * @param $dbUser
	 * @return SetupDatabaseFromBackupStorage
	 */
	public function setDbUser($dbUser) {
		$this->dbUser = $dbUser;
		return $this;
	}

	public function getDbUser() {
		return $this->dbUser;
	}

	public function setDetectDbSettingsScriptPath($detectDbSettingsScriptPath) {
		$this->detectDbSettingsScriptPath = $detectDbSettingsScriptPath;
	}

	public function getDetectDbSettingsScriptPath() {
		return $this->detectDbSettingsScriptPath;
	}


}
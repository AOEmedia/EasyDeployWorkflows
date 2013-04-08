<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class Untar extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {


	protected $folder;

	protected $packageFileName;

	protected $expectedExtractedFolder;

	protected $mode;

	protected $changeToDirectory;


	const MODE_SKIP_IF_EXTRACTEDFOLDER_EXISTS=1;
	const MODE_DELETE_IF_EXTRACTEDFOLDER_EXISTS=2;

	public function setExpectedExtractedFolder($expectedExtractedFolder)
	{
		$this->expectedExtractedFolder = $expectedExtractedFolder;
	}

	public function setFolder($folder)
	{
		$this->folder = $folder;
	}

	public function setMode($mode)
	{
		$this->mode = $mode;
	}

	public function setPackageFileName($packageFileName)
	{
		$this->packageFileName = $packageFileName;
	}

	/**
	 * @param $path
	 */
	public function autoInitByPackagePath($path) {

		$infos = pathinfo($path);
		$this->setFolder($infos['dirname']);
		//fix .tar.gz
		$extractedFolder = str_replace('.tar','',$infos['filename']);
		$this->setExpectedExtractedFolder($extractedFolder);

		$this->setPackageFileName($infos['filename'].'.'.$infos['extension']);
	}

	/**
	 * Set a directory that should be changed to before extracting the archive.
	 * If not set the directory of the archive is used
	 *
	 * @param $changeToDirectory
	 */
	public function setChangeToDirectory($changeToDirectory) {
		$this->changeToDirectory = rtrim($changeToDirectory,DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
	}


	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {

		$packageFileName = $this->replaceConfigurationMarkersWithTaskRunInformation($this->packageFileName,$taskRunInformation);
		$expectedExtractedFolder = $this->replaceConfigurationMarkersWithTaskRunInformation($this->expectedExtractedFolder,$taskRunInformation);

		if (isset($this->changeToDirectory)) {
			$targetDirectory = $this->replaceConfigurationMarkersWithTaskRunInformation($this->changeToDirectory,$taskRunInformation);
		}
		else {
			$targetDirectory = rtrim($this->replaceConfigurationMarkersWithTaskRunInformation($this->folder,$taskRunInformation),DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
		}

		if ($server->isDir($targetDirectory.$expectedExtractedFolder)) {
			if ($this->mode == self::MODE_SKIP_IF_EXTRACTEDFOLDER_EXISTS) {
				$this->logger->log('Extracted folder "'.$targetDirectory.$expectedExtractedFolder.'" already exists! I am skipping the extraction.',\EasyDeployWorkflows\Logger\Logger::MESSAGE_TYPE_WARNING);
				return;
			}
			else {
				$this->executeAndLog($server,'rm -rf '.$targetDirectory.$expectedExtractedFolder);
			}
		}
		if (!$server->isFile($targetDirectory.$packageFileName)) {
			throw new \Exception('The given file "'.$targetDirectory.$packageFileName.'" is not existend.');
		}
		//extract
		$args = 'x';
		if (strpos($packageFileName,'.zip') !== false || strpos($packageFileName,'.gz') !== false) {
			$args = 'xz';
		}
		$this->executeAndLog($server,'cd ' . $targetDirectory . '; tar -'.$args.'f ' . $packageFileName);
	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	public function validate() {
		if (empty($this->folder)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('source not set');
		}
		if (empty($this->packageFileName)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('packageFileName not set');
		}
		if (empty($this->expectedExtractedFolder)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('expectedExtractedFolder not set');
		}
		return true;
	}
}
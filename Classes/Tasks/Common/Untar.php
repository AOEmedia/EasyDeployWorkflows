<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Exception\InvalidConfigurationException;
use EasyDeployWorkflows\Logger\Logger;
use EasyDeployWorkflows\Tasks;


class Untar extends Tasks\AbstractServerTask {

	const MODE_SKIP_IF_EXTRACTEDFOLDER_EXISTS   = 1;
	const MODE_DELETE_IF_EXTRACTEDFOLDER_EXISTS = 2;

	protected $folder;

	protected $packageFileName;

	protected $expectedExtractedFolder;

	protected $mode;

	public function setExpectedExtractedFolder($expectedExtractedFolder) {
		$this->expectedExtractedFolder = $expectedExtractedFolder;

		return $this;
	}

	public function setFolder($folder) {
		$this->folder = $folder;

		return $this;
	}

	public function setMode($mode) {
		$this->mode = $mode;

		return $this;
	}

	public function setPackageFileName($packageFileName) {
		$this->packageFileName = $packageFileName;

		return $this;
	}

	/**
	 * @param string $path
	 */
	public function autoInitByPackagePath($path) {

		$info = pathinfo($path);
		$this->setFolder($info['dirname']);
		//fix .tar.gz
		$extractedFolder = str_replace('.tar', '', $info['filename']);
		$this->setExpectedExtractedFolder($extractedFolder);

		$this->setPackageFileName($info['filename'] . '.' . $info['extension']);

		return $this;
	}

	/**
	 * @param Tasks\TaskRunInformation $taskRunInformation
	 * @param \EasyDeploy_AbstractServer $server
	 * @return mixed
	 * @throws \Exception
	 */
	protected function runOnServer(Tasks\TaskRunInformation $taskRunInformation, \EasyDeploy_AbstractServer $server) {

		$packageFileName         = $this->replaceConfigurationMarkersWithTaskRunInformation($this->packageFileName, $taskRunInformation);
		$expectedExtractedFolder = $this->replaceConfigurationMarkersWithTaskRunInformation($this->expectedExtractedFolder, $taskRunInformation);

		if (isset($this->changeToDirectory)) {
			$targetDirectory = $this->replaceConfigurationMarkersWithTaskRunInformation($this->changeToDirectory, $taskRunInformation);
		} else {
			$targetDirectory = rtrim($this->replaceConfigurationMarkersWithTaskRunInformation($this->folder, $taskRunInformation), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
		}

		if ($server->isDir($targetDirectory . $expectedExtractedFolder)) {
			if ($this->mode == self::MODE_SKIP_IF_EXTRACTEDFOLDER_EXISTS) {
				$this->logger->log('Extracted folder "' . $targetDirectory . $expectedExtractedFolder . '" already exists! I am skipping the extraction.', Logger::MESSAGE_TYPE_WARNING);

				return;
			} else {
				$this->executeAndLog($server, 'rm -rf ' . $targetDirectory . $expectedExtractedFolder);
			}
		}
		if (!$server->isFile($targetDirectory . $packageFileName)) {
			throw new \Exception("The given file '" . $targetDirectory . $packageFileName . "' doesn't exist.");
		}
		//extract
		$args = 'x';
		if (strpos($packageFileName, '.zip') !== false || strpos($packageFileName, '.gz') !== false) {
			$args = 'xz';
		}
		$this->executeAndLog($server, 'cd ' . $targetDirectory . '; tar -' . $args . 'f ' . $packageFileName);
	}

	/**
	 * @return bool
	 * @throws InvalidConfigurationException
	 */
	public function validate() {
		if (empty($this->folder)) {
			throw new InvalidConfigurationException('source not set');
		}
		if (empty($this->packageFileName)) {
			throw new InvalidConfigurationException('packageFileName not set');
		}
		if (empty($this->expectedExtractedFolder)) {
			throw new InvalidConfigurationException('expectedExtractedFolder not set');
		}

		return true;
	}
}

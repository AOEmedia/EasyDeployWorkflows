<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class Download extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var string
	 */
	protected $source;

	/**
	 * @var bool
	 */
	protected $deleteBeforeDownload = false;

	/**
	 * @var string
	 */
	protected $target;

	/**
	 * @var \EasyDeploy_Helper_Downloader
	 */
	protected $downloader;

	public function __construct() {
		parent::__construct();
		$this->injectDownloader(new \EasyDeploy_Helper_Downloader());
	}

	/**
	 * @param \EasyDeploy_Helper_Downloader $downloader
	 */
	public function injectDownloader(\EasyDeploy_Helper_Downloader $downloader) {
		$this->downloader = $downloader;
	}

	/**
	 * @param boolean $deleteBeforeDownload
	 */
	public function setDeleteBeforeDownload($deleteBeforeDownload)
	{
		$this->deleteBeforeDownload = $deleteBeforeDownload;
	}

	/**
	 * @return boolean
	 */
	public function getDeleteBeforeDownload()
	{
		return $this->deleteBeforeDownload;
	}

	/**
	 * @return string
	 */
	public function getDownloadSource()
	{
		return $this->source;
	}

	/**
	 * @param string $source
	 */
	public function setDownloadSource($source)
	{
		$this->source = $source;
	}

	/**
	 * @return string
	 */
	public function getTargetFolder()
	{
		return $this->target;
	}

	/**
	 * @param string $target
	 */
	public function setTargetFolder($target)
	{
		$this->target = $target;
	}

	/**
	 * @param TaskRunInformation $taskRunInformation
	 * @return mixed
	 */
	protected function runOnServer(\EasyDeployWorkflows\Tasks\TaskRunInformation $taskRunInformation,\EasyDeploy_AbstractServer $server) {

		$sourceFile = $this->replaceConfigurationMarkers($this->source,$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration());
		$targetFolder = rtrim($this->replaceConfigurationMarkers($this->target,$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration()),DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;



		if ($server->isFile($targetFolder.$this->getFilenameFromPath($sourceFile))) {
			if ($this->deleteBeforeDownload) {
				$server->run('rm '.$targetFolder.$this->getFilenameFromPath($sourceFile));
			}
			else {
				$this->logger->log('Target File "'.$targetFolder.$this->getFilenameFromPath($sourceFile).'" already exists! I am skipping the download!',\EasyDeployWorkflows\Logger\Logger::MESSAGE_TYPE_WARNING);
				return;
			}
		}
		if ($sourceFile == $targetFolder) {
			$this->logger->log('Source and Target are the same. I am skipping the download!');
			return;
		}
		$this->downloader->download($server,$sourceFile,$targetFolder);
		$this->logger->log('Download ready');
	}

	/**
	 * @return boolean
	 * throws Exception\InvalidConfigurationException
	 */
	public function validate() {
		if (!isset($this->source)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('source not set');
		}
		if (!isset($this->target)) {
			throw new \EasyDeployWorkflows\Exception\InvalidConfigurationException('target not set');
		}
		return true;
	}
}
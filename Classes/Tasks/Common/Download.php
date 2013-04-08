<?php

namespace EasyDeployWorkflows\Tasks\Common;

use EasyDeployWorkflows\Tasks;



class Download extends \EasyDeployWorkflows\Tasks\AbstractServerTask  {

	/**
	 * @var \EasyDeployWorkflows\Source\DownloadSourceInterface
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


	/**
	 * @var string if file is not existend
	 */
	protected $notIfPathExists = '';

	/**
	 * @param string $notIfFileExists
	 */
	public function setNotIfPathExists($notIfPathExists)
	{
		$this->notIfPathExists = $notIfPathExists;
		return $this;
	}



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
	 * @return \EasyDeployWorkflows\Source\DownloadSourceInterface
	 */
	public function getDownloadSource()
	{
		return $this->source;
	}

	/**
	 * @param \EasyDeployWorkflows\Source\DownloadSourceInterface $source
	 */
	public function setDownloadSource(\EasyDeployWorkflows\Source\DownloadSourceInterface $source)
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

		$targetFolder = rtrim($this->replaceConfigurationMarkers($this->target,$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration()),DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

		if (!empty($this->notIfPathExists) && ( $server->isFile($this->notIfPathExists) || $server->isDir($this->notIfPathExists) )) {
			$this->logger->log('Skipping because Skip Path is present: "'.$this->notIfPathExists.'"',\EasyDeployWorkflows\Logger\Logger::MESSAGE_TYPE_WARNING);
			return;
		}

		if ($server->isFile($targetFolder.$this->source->getFileName())) {
			if ($this->deleteBeforeDownload) {
				$this->executeAndLog($server,'rm '.$targetFolder.$this->source->getFileName());
			}
			else {
				$this->logger->log('Target File "'.$targetFolder.$this->source->getFileName().'" already exists! I am skipping the download!',\EasyDeployWorkflows\Logger\Logger::MESSAGE_TYPE_WARNING);
				return;
			}
		}

		$this->logger->log('Download starting from '.$this->source->getShortExplain().' to '.$targetFolder.' on server '.$server->getHostname());
		$source = $this->replaceConfigurationMarkers($this->source->getSourceSpecification(),$taskRunInformation->getWorkflowConfiguration(),$taskRunInformation->getInstanceConfiguration());
		$this->downloader->download($server, $source,$targetFolder);
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
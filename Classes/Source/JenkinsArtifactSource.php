<?php

namespace EasyDeployWorkflows\Source;



/**
 * Download Source that abstracts from Jenkins and builds urls like:
 *
 * http://jenkins.aoemedia.de/job/aoemedia_build/lastSuccessfulBuild/artifact/
 *
 */
class JenkinsArtifactSource extends DownloadSource  {

	/**
	 * @var string
	 */
	protected $jenkinsBaseUrl;

	/**
	 * @var string
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var string
	 */
	protected $jobName;

	/**
	 * @var string
	 */
	protected $buildNr='lastSuccessfulBuild';

	/**
	 * @var string
	 */
	protected $artifactFileName;

	/**
	 * @var bool
	 */
	protected $downloadAllArtifactsZipped = false;

	/**
	 * @var \EasyDeploy_Helper_Downloader
	 */
	protected $downloader;

	public function __construct() {
		$this->injectDownloader(new \EasyDeploy_Helper_Downloader());
	}

	/**
	 * @param \EasyDeploy_Helper_Downloader $downloader
	 */
	public function injectDownloader(\EasyDeploy_Helper_Downloader $downloader) {
		$this->downloader = $downloader;
	}

	/**
	 * @param string $artifactName
	 */
	public function setArtifactFileName($artifactName) {
		$this->artifactFileName = $artifactName;
	}

	/**
	 * @return string
	 */
	public function getArtifactFileName() {
		return $this->artifactFileName;
	}

	/**
	 * @param string $buildNr
	 */
	public function setBuildNr($buildNr) {
		$this->buildNr = $buildNr;
	}

	/**
	 * @return string
	 */
	public function getBuildNr() {
		return $this->buildNr;
	}

	/**
	 * @param boolean $downloadAllArtifactsZipped
	 */
	public function setDownloadAllArtifactsZipped($downloadAllArtifactsZipped) {
		$this->downloadAllArtifactsZipped = $downloadAllArtifactsZipped;
	}

	/**
	 * @return boolean
	 */
	public function getDownloadAllArtifactsZipped() {
		return $this->downloadAllArtifactsZipped;
	}

	/**
	 * @param string $jobName
	 */
	public function setJobName($jobName) {
		$this->jobName = $jobName;
	}

	/**
	 * @return string
	 */
	public function getJobName() {
		return $this->jobName;
	}

	/**
	 * @param string $password
	 */
	public function setPassword($password) {
		$this->password = $password;
	}

	/**
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * @param string $user
	 */
	public function setUser($user) {
		$this->user = $user;
	}

	/**
	 * @return string
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * @param string $jenkinsBaseUrl
	 */
	public function setJenkinsBaseUrl($jenkinsBaseUrl) {
		$this->jenkinsBaseUrl = rtrim($jenkinsBaseUrl,'/').'/';
	}

	/**
	 * @return string
	 */
	public function getJenkinsBaseUrl() {
		return $this->jenkinsBaseUrl;
	}


	/**
	 * @param EasyDeploy_AbstractServer $server
	 * @param $to
	 */
	public function download(EasyDeploy_AbstractServer $server, $targetFolder) {
		$this->downloader->download($server,$this->buildSource(),$targetFolder);
	}

	public function getShortExplain() {
		return 'Download from:'.$this->buildSource();
	}

	/**
	 * @return string
	 */
	public function getFileName() {
		return $this->getFilenameFromPath($this->buildSource());
	}

	protected function buildSource() {
		$source = $this->jenkinsBaseUrl.'job/'.$this->jobName.'/'.$this->buildNr.'/';
		if ($this->downloadAllArtifactsZipped) {
			$source.='*zip*/archive.zip';
		}
		else {
			$source.=$this->artifactFileName;
		}
		return $source;
	}
}

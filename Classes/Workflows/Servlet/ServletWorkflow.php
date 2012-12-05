<?php

namespace EasyDeployWorkflows\Workflows\Servlet;

use EasyDeployWorkflows\Workflows as Workflows;

class ServletWorkflow extends Workflows\TaskBasedWorkflow {

	/**
	 * @var \EasyDeployWorkflows\Workflows\Servlet\ServletConfiguration
	 */
	protected $workflowConfiguration;

	/**
	 * @var string
	 */
	const CURL_DEPLOY_COMMAND = 'curl --upload-file %s -u %s "http://localhost:%s/manager/deploy?path=%s&update=true"';


	/**
	 * Can be used to do individual workflow initialisation and/or checks
	 */
	protected function workflowInitialisation() {
		$deploymentSource = $this->replaceMarkers( $this->workflowConfiguration->getDeploymentSource() );
		$localDownloadTargetFolder = rtrim($this->replaceMarkers( $this->instanceConfiguration->getDeliveryFolder() ),'/').'/';

		$this->addTask('check that we are on correct deploy node',new \EasyDeployWorkflows\Tasks\Common\CheckCorrectDeployNode());


		$downloadTask = new \EasyDeployWorkflows\Tasks\Common\Download();
		$downloadTask->addServerByName('localhost');
		$downloadTask->setDownloadSource( $deploymentSource );
		$downloadTask->setTargetFolder( $localDownloadTargetFolder );
		$this->addTask('Download tracker war to local delivery folder', $downloadTask);



		$copyTask = new \EasyDeployWorkflows\Tasks\Common\Download();
		$copyTask->addServersByName($this->workflowConfiguration->getServletServers());
		$copyTask->setDownloadSource( $localDownloadTargetFolder.$this->getFilenameFromPath($deploymentSource) );
		$copyTask->setTargetFolder( '/tmp/' );
		$copyTask->setDeleteBeforeDownload(true);
		$this->addTask('Load tracker war to tmp folder on servlet servers',	$copyTask);

		$tmpWarLocation 			= '/tmp/'.$this->getFilenameFromPath($deploymentSource);
		$deployWarTask = new \EasyDeployWorkflows\Tasks\Servlet\DeployWarInTomcat();
		$deployWarTask->addServersByName($this->workflowConfiguration->getServletServers());
		$deployWarTask->setWarFileSourcePath( $tmpWarLocation );
		$deployWarTask->setTomcatPassword( $this->workflowConfiguration->getTomcatPassword() );
		$deployWarTask->setTomcatUser( $this->workflowConfiguration->getTomcatUsername() );
		$deployWarTask->setTomcatPath( $this->workflowConfiguration->getTargetPath() );
		$deployWarTask->setTomcatPort( $this->workflowConfiguration->getTomcatPort() );
		$deployWarTask->setTomcatVersion( $this->workflowConfiguration->getTomcatVersion() );

		$this->addTask('deploy the war file to the tomcat servers',$deployWarTask);

	}
}
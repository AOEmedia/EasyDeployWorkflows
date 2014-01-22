<?php

namespace EasyDeployWorkflows\Workflows\Servlet;

use EasyDeployWorkflows\Workflows as Workflows;

class ServletRemoteWorkflow extends Workflows\TaskBasedWorkflow {

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

		$deployWarTask = new \EasyDeployWorkflows\Tasks\Servlet\DeployWarInTomcat();
			//the deployment is running on localhost
		$deployWarTask->addServersByName(array('localhost'));
			//and deployment to the tomcat host
		$deployWarTask->setTomcatHostname( $this->workflowConfiguration->getTomcatHostname() );

		$deployWarTask->setWarFileSourcePath( $localDownloadTargetFolder.$this->getFilenameFromPath($deploymentSource) );

		$deployWarTask->setTomcatPassword( $this->workflowConfiguration->getTomcatPassword() );
		$deployWarTask->setTomcatUser( $this->workflowConfiguration->getTomcatUsername() );
		$deployWarTask->setTomcatPath( $this->workflowConfiguration->getTargetPath() );
		$deployWarTask->setTomcatPort( $this->workflowConfiguration->getTomcatPort() );
		$deployWarTask->setTomcatVersion( $this->workflowConfiguration->getTomcatVersion() );

		$this->addTask('deploy the war file to the tomcat servers',$deployWarTask);
	}
}
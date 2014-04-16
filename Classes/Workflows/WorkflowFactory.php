<?php

namespace EasyDeployWorkflows\Workflows;

use EasyDeployWorkflows\Workflows;

class WorkflowFactory {

	/**
	 * @var string
	 */
	protected $configurationFolder;

	/**
	 * sets the folder by convention
	 */
	public function __construct() {
		$this->autoSetConfigurationFolder();
	}

	/**
	 * @param string $configurationFolder
	 */
	public function setConfigurationFolder($configurationFolder)
	{
		$this->configurationFolder = $configurationFolder;
	}

	/**
	 * Creates the workflow depending on the passed configuration.
	 *
	 * @param InstanceConfiguration $instanceConfiguration
	 * @param AbstractWorkflowConfiguration $workflowConfiguration
	 * @return AbstractWorkflow
	 */
	public function create(	InstanceConfiguration $instanceConfiguration,
							AbstractWorkflowConfiguration $workflowConfiguration
							) {

		if (!class_exists($workflowConfiguration->getWorkflowClassName())) {
			throw new UnknownWorkflowException('Workflow "'.$workflowConfiguration->getWorkflowClassName().'" not existend or not loaded',2212);
		}
		$this->initLoggerLogFile($instanceConfiguration, $workflowConfiguration);
		$workflowClass = $workflowConfiguration->getWorkflowClassName();

		$workflow = $this->getWorkflow($workflowClass, $instanceConfiguration, $workflowConfiguration);
		$workflow->injectDownloader(new \EasyDeploy_Helper_Downloader());

		return $workflow;
	}

	protected function initLoggerLogFile(InstanceConfiguration $instanceConfiguration,$workflowConfiguration) {
		$currentLogFile = \EasyDeployWorkflows\Logger\Logger::getInstance()->getLogFile();
		if (!empty($currentLogFile)) {
			return;
		}

		if ($instanceConfiguration->hasValidDeployLogFolder()) {
			$logDir = $instanceConfiguration->getDeployLogFolder();
		}
		else {
			if (!empty($_SERVER['SCRIPT_NAME'])) {
				if (substr($_SERVER['SCRIPT_NAME'], 0, 1) === '/') {
					$deployScript = $_SERVER['SCRIPT_NAME'];
				}
				else {
					$deployScript = $_SERVER['PWD'].'/'.$_SERVER['SCRIPT_NAME'];
				}
				$logDir = dirname($deployScript);
			}
			else {
				$logDir = dirname(__FILE__).'/../../../';
			}
		}
		\EasyDeployWorkflows\Logger\Logger::getInstance()->setLogFile( rtrim($logDir,'/') . '/deploy-'.$workflowConfiguration->getReleaseVersion().'-'.date('d.m.Y').'.log');
	}

	/**
	 * @param $projectName
	 * @param $environmentName
	 * @param $releaseVersion
	 * @param $workFlowConfigurationVariableName
	 * @param string $instanceConfigurationVariableName
	 * @return AbstractWorkflow
	 * @throws \Exception
	 */
	public function createByConfigurationVariable($projectName,$environmentName,$releaseVersion,$workFlowConfigurationVariableName, $instanceConfigurationVariableName='instanceConfiguration') {

		if (!is_dir($this->configurationFolder)) {
			throw new \Exception('Configurationfolder "'.$this->configurationFolder.'" not existend. Please check if you followed the convention - or set your Configurationfolder explicit');
		}
		$configurationFile = $this->configurationFolder.$projectName.DIRECTORY_SEPARATOR.$environmentName.'.php';
		if (!is_file($configurationFile)) {
			throw new \Exception('No configuration file found for project and environment. Looking in: '.$configurationFile);
		}
		include( $configurationFile );

		if (!isset($$instanceConfigurationVariableName)) {
			\EasyDeployWorkflows\Logger\Logger::getInstance()->log('No Instance Configuration found! Expect a variable $'.$instanceConfigurationVariableName.'. I am creating a default one now...');
			$instanceConfiguration = new \EasyDeployWorkflows\Workflows\InstanceConfiguration();
			$instanceConfiguration->addAllowedDeployServer('*')
					->setEnvironmentName($environmentName)
					->setProjectName($projectName)
					->setTitle('Default Instance Configuration');
		}

		if (!$$instanceConfigurationVariableName instanceof InstanceConfiguration) {
			throw new \Exception('No Instance Configuration found! Expect  $'.$instanceConfigurationVariableName.'  is instance of "InstanceConfiguration".');
		}

		$instanceConfiguration = $$instanceConfigurationVariableName;


		if (  $instanceConfiguration->getEnvironmentName() != $environmentName
			|| $instanceConfiguration->getProjectName() != $projectName) {
			throw new \Exception('Instance Environment Data invalid! Check that project and environment is set and valid! Current:'.$instanceConfiguration->getProjectName().' / '.$instanceConfiguration->getEnvironmentName());
		}

		if (!isset($$workFlowConfigurationVariableName) || !$$workFlowConfigurationVariableName instanceof AbstractWorkflowConfiguration
			) {
			throw new \EasyDeployWorkflows\Workflows\Exception\WorkflowConfigurationNotExistendException('No Workflow Configuration found or it is invalid! Expected a Variable with the name $'.$workFlowConfigurationVariableName);
		}
		$$workFlowConfigurationVariableName->setReleaseVersion($releaseVersion);
		return $this->create($instanceConfiguration, $$workFlowConfigurationVariableName);
	}

	/**
	 * @param $name
	 * @param InstanceConfiguration $instanceConfiguration
	 * @param AbstractWorkflowConfiguration $workflowConfiguration
	 * @return AbstractWorkflow
	 */
	protected function getWorkflow($name,InstanceConfiguration $instanceConfiguration, AbstractWorkflowConfiguration $workflowConfiguration) {
		return new $name($instanceConfiguration,$workflowConfiguration);
	}

	/**
	 * from _SERVER env
	 */
	private function autoSetConfigurationFolder() {
		$scriptDir = dirname ( $_SERVER['PWD'].DIRECTORY_SEPARATOR. $_SERVER['SCRIPT_NAME']);
		if (is_dir($scriptDir.DIRECTORY_SEPARATOR.'Configuration')) {
			$this->setConfigurationFolder($scriptDir.DIRECTORY_SEPARATOR.'Configuration'.DIRECTORY_SEPARATOR);
		}
		else {
			$this->setConfigurationFolder(dirname(__FILE__).'/../../../Configuration/');
		}
	}
}
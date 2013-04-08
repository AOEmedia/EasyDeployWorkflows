What is EasyDeployWorkflows?
=====================

EasyDeployWorkflows offers a concept to develop Deployment Workflows on Top of EasyDeploy

Build status: |buildStatusIcon|

Concept
-------------

Each component of your Application is deployed using a Workflow.
A workflow needs 3 Objects to run:
 * A Workflow Configuration (Specific Configuration Object for that Workflow.)
 * A InstanceConfiguration (Holding common Data about the name of the environment and the project.)
 * A releaseVersion

The idea ist, that the Workflows can be reused for deploying different projects to different environments.
Managing the differences between the environments comes down to manage the WorkflowConfiguration.

The typical Responsibility for a Workflow is:
 * Download the Artifact that should be installed
 * Unzip the Artifact
 * Install the Artifact (on the infrastructure - that means there might be multiple servers involved)


Workflow and Task
-----------------
A Workflow can use several Tasks to do what its supposed to do.
There is the "AbstractTaskBasedWorkflow" Class as a Basis for Task based Workflows.

To build our own Workflow you probably want to extend one of the existing task based Workflows and modify or extend the Tasks it uses.

WorkflowConfiguration and InfrastructureConfiguration
------------------------
 *  The Workflow Configuration represents the data that is required by the Deployment Workflow.
 *  It describes the target server Infrastructure of the deployment. Therefore its the part of your Deployment that is environment specific.
 *  Normally you have versions of the Configuration for each Environment (devbox, staging, production). See below for the suggested folder structure.


Sources
----------------
Most of the Workflows start with a Download Task.
The Download Tasks supports different Sources:
 * a DownloadSource can Download from different Location (using Wget or SCP for example)
 * the Jenkins Source is very useful when you want to transfer certain Build Artifacts from your Jenkins CI Server (see below for an example)


Deployment Scripts Example
------------------------------

We recommend this structure:
 * deploy.php (your central deployment script)
 * EasyDeploy (EasyDeploy Submodule)
 * EasyDeployWorkflows (EasyDeployWorkflows submodule)
 * Configuration
 * * [Projectname]
 * * * [Instancename].php


The deploy.php triggers your deployment:
::
    <?php
    require_once dirname(__FILE__) . '/EasyDeployWorkflows/Classes/Autoloader.php';
    require_once dirname(__FILE__) . '/EasyDeploy/Classes/Utils.php';
    EasyDeploy_Utils::includeAll();
    $project = 'myprojectname';
    $environment = \EasyDeploy_Utils::getParameterOrUserSelectionInput('environment','Which environment do you want to install?',array('staging','production'));

    try {
        $WebDeploymentWorkflow = $workflowFactory->createByConfigurationVariable($project,$environment,$releaseVersion, 'webWorkflowConfiguration');
        $WebDeploymentWorkflow->deploy($releaseVersion);
    }
    catch (\EasyDeployWorkflows\Exception\HaltAndRollback $e) {
        exit -1;
    }



Configuration Example
------------------------------

Sample deploy configuration
::
    <?php
    $instanceConfiguration = new \EasyDeployWorkflows\Workflows\InstanceConfiguration();
    $instanceConfiguration
    	->setDeliveryFolder('/home/systemstorage/###projectname###/deliveries/###releaseversion###/')
    	->setProjectName('saascluster');
    $webWorkflowConfiguration = new \EasyDeployWorkflows\Workflows\Web\NFSWebConfiguration();
    $webWorkflowConfiguration
    	->setWebRootFolder('/var/www/###projectname###/###environment###')
    	->setBackupMasterEnvironment('production')
    	->setBackupStorageRootFolder('/home/systemstorage/systemstorage/saascluster/backup/')
    	->setDeploymentSource('https://username:password@yourContinuousDeploymentServer/artifacts/ProjectsArtifactRepository/preparedReleases/###releaseversion###/application.tar.gz')
    	->setInstallSilent(true);

Logging:
-------------------------

There is a simple Logger singleton that is used to log to the screen and to a file.



The default file that is used for logging is "deploy-<releaseversion>-<date>.log".
The Logfiles are stored in the Instances LogFolder (defaults to the same folder like your deployment script) and can be set with:
::
   $instanceConfiguration->setDeployLogFolder('/var/log/');


You can also set a custom log file by:

::
    \EasyDeployWorkflows\Logger\Logger::getInstance()->setLogFile();


.. |buildStatusIcon| image:: https://travis-ci.org/AOEmedia/EasyDeployWorkflows.png?branch=master
   :alt: Build Status
   :target: http://travis-ci.org/AOEmedia/EasyDeployWorkflows





Workflow: ArchivedApplicationWorkflow
----------------------------------
This is a simple Workflow that deploys a common Application based on a available archive.
It deploys the Application to multiple Servers and uses the following steps:

 1 Downloads the Artifact from the configured Source to all configured servers (to the delivery folder). The Artifact should be an archive (like MyApp.tar.gz)
 2 Extract the Artifact on all configured servers (within the delivery folder)
 3 Install: Rsyncs the Artifact on all configured servers to the configured install target folder
 4 Cleanup the extracted Folder

Workflow: ArchivedApplicationWithNFSServerWorkflow
----------------------------------
Like ArchivedApplicationWorkflow, but it expects, that there is a central NFS server that has the filesystem shared with potential frontend servers.
It deploys the Application to your infrastructure using the following steps:

  1 Downloads the Artifact from the configured Source to NFS servers (to the delivery folder). The Artifact should be an archive (like MyApp.tar.gz)
  2 Extract the Artifact on NFS  server (within the delivery folder)
  3 Install: Rsyncs the Artifact on all configured servers to the configured install target folder
  4 Optional: Runs a Sync Script on all the configured Installservers
  4 Cleanup the extracted Folder

Workflow: ArchivedApplicationReleaseFolderWorkflow
----------------------------------
This is a simple Workflow that deploys a common Application based on a available archive.
It used the commonly used Releasefolder Pattern:

<TargetReleaseFolder>
   -  <ReleaseVersion1>
   -  <ReleaseVersion2>
   -  <ReleaseVersion3>
   -  current (Symlink to <ReleaseVersion2>)
   -  previous (Symlink to <ReleaseVersion1>)
   -  next (Symlink to <ReleaseVersion3> during deployment)

Your htdocs folder typically points to something like this:
- htdocs to <TargetReleaseFolder>/current/Public
- htdocsNext to <TargetReleaseFolder>/next/Public

It deploys the Application to multiple Servers and uses the following steps:

 1 Downloads the Artifact from the configured Source to all configured servers (to the delivery folder). The Artifact should be an archive (like MyApp.tar.gz)
 2 Extract the Artifact on all configured servers directly to <Releasefolder>/ExtractedFolder
 3 Renames <Releasefolder>/ExtractedFolder  to <Releasefolder>/<Releaseversion>
 4 Sets the "next" symlink to new Release
 5 Executes optional SmokeTests
 6 Updates current and previous symlink

Workflows: AOEInstaller\* Variants
----------------------------
Like the Workflows above but the installation uses the Installbinaries that are included in the archive.
This step also takes care of ensuring that a Backup of the Master System is available.


Try Run
--------------------------

Most of the tasks are not executed if you set the global tryRun flag:
::
    $GLOBALS['tryRun'] = true


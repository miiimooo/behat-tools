<?php

namespace miiimooo\BehatTools\Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\StepScope;
use Behat\Mink\Driver\Selenium2Driver;
use Sabre\DAV\Client;

class DavScreenshotFailureContext implements Context {

  /**
   * @var \Drupal\DrupalExtension\Context\MinkContext
   */
  protected $minkContext;
  protected $failurePath;

  /**
   * @BeforeScenario
   */
  public function prepare(BeforeScenarioScope $scope) {
    $this->minkContext = $scope->getEnvironment()->getContext('Drupal\DrupalExtension\Context\MinkContext');
    $this->failurePath = $scope->getEnvironment()->getSuite()->getSetting('failure_path');
  }

  /**
   * @AfterStep
   */
  public function takeScreenshotAfterFailedStep(AfterStepScope $scope) {
    if ($scope->getTestResult()->getResultCode() === \Behat\Testwork\Tester\Result\TestResult::FAILED) {
      $this->dumpInfo($scope);
    }
  }

  /**
   * @Then take a screenshot
   */
  public function takeScreenshot() {
    $this->dumpInfo();
  }

  /**
   * Dump markup/screenshot if available and save to webdav.
   *
   * @param null $scope
   */
  protected function dumpInfo(StepScope $scope = NULL) {
    $filename = $this->fileName($scope);
    $this->dumpMarkup($filename);
    $this->screenShot($filename);
//    $this->storeDumps($filename, $scope);
    if (getenv('CI') === 'drone') {
      $this->storeDumps($filename, $scope);
    }
  }

  public function screenShot($filename) {
    $filename .= '.png';
    $driver = $this->minkContext->getSession()->getDriver();
    if ($driver instanceof Selenium2Driver) {
      file_put_contents($filename, $this->minkContext->getSession()->getDriver()->getScreenshot());
      sprintf("Screenshot placed in: %s\n", $filename);
    }
  }

  /**
   * Compute a file name for the output.
   */
  protected function fileName(StepScope $scope = NULL) {
    if ($scope) {
      $basename = pathinfo($scope->getFeature()->getFile());
      $basename = substr($basename['basename'], 0 , strlen($basename['basename']) - strlen($basename['extension']) - 1);
      $basename .= '-' . $scope->getStep()->getLine();
    }
    else {
      $basename = 'screenshot';
    }
    $basename .= '-' . date('Ymd_Hi');
    if (!function_exists('file_prepare_directory')) {
      printf('*** not running inside a Drupal environment is not fully supported. Ensure the local screenshot target folder exists yourself ***');
    }
    else {
      file_prepare_directory($this->failurePath, FILE_CREATE_DIRECTORY);
    }
    $basename = $this->failurePath . '/' . $basename;
    return $basename;
  }

  /**
   * Save the markup from the failed step.
   */
  protected function dumpMarkup($filename) {
    $filename .= '.html';
    $html = $this->minkContext->getSession()->getPage()->getHtml();
    file_put_contents($filename, $html);
    sprintf("HTML dump available at: %s\n", $filename);
  }

  /**
   * Stores screenshots on a webdav server
   * Requires environment variables:
   * WEBDAV_HOST, WEBDAV_USERNAME, WEBDAV_PASSWORD.
   *
   * @param $path
   * @param \Behat\Behat\Hook\Scope\StepScope $scope
   *
   * @throws \Sabre\DAV\ClientException
   */
  public function storeDumps($path, StepScope $scope) {
    $settings = [
      'baseUri' => getenv('WEBDAV_HOST') . '/screenshots/',
      'userName' => getenv('WEBDAV_USERNAME'),
      'password' => getenv('WEBDAV_PASSWORD'),
    ];
    $prefix = array_filter(['DRONE_REPO_OWNER', 'DRONE_REPO_NAME', 'DRONE_BUILD_NUMBER'], function ($v) {
      return getenv($v);
    });
    $prefix = implode('/', array_map('getenv', $prefix));

    $client = new Client($settings);

    // Create subfolders.
    $createrepo_owner = $client->request('MKCOL', '/screenshots/' . getenv('DRONE_REPO_OWNER'));
    $create_repo_name = $client->request('MKCOL', '/screenshots/' . getenv('DRONE_REPO_OWNER') . '/' . getenv('DRONE_REPO_NAME'));
    $create_repo_build = $client->request('MKCOL', '/screenshots/' . getenv('DRONE_REPO_OWNER') . '/' . getenv('DRONE_REPO_NAME') . '/' . getenv('DRONE_BUILD_NUMBER' ));


    foreach (['.png', '.html'] as $extension) {
      if (file_exists($path . $extension)) {
        $filename = "$prefix/" . basename($path . $extension);
        $response = $client->request('PUT', $filename, file_get_contents($path . $extension));
        if ($response['statusCode'] != 201) {
          var_dump($response);
        }
        else {
          printf("Copied screenshot %s to %s\n", $filename, $settings['baseUri']);
        }
      }
    }

  }
}

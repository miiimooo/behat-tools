<?php

namespace miiimooo\BehatTools\Context;

/**
 * Behat Context for collecting and displaying Javascript error messages
 */
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\BeforeStepScope;
use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Hook\Scope\BeforeNodeCreateScope;

class JavascriptOnErrorContext implements Context {
  /**
   * @var \Drupal\DrupalExtension\Context\MinkContext
   */
  protected $minkContext;

  /**
   * @var \Behat\Gherkin\Node\ScenarioInterface
   */
  protected $currentScenario;

  /**
   * @BeforeScenario @javascript
   */
  public function prepare(BeforeScenarioScope $scope) {
    $this->minkContext = $scope->getEnvironment()->getContext('Drupal\DrupalExtension\Context\MinkContext');
    $this->currentScenario = $scope->getScenario();

  }

  /**
   * @AfterScenario @javascript
   */
  public function remove(AfterScenarioScope $scope) {
    $this->currentScenario = NULL;
  }

  /**
   * @AfterStep
   */
  public function showJSErrorsAfterFailedStep(AfterStepScope $scope) {
    if ($this->currentScenario &&
        $scope->getTestResult()->getResultCode() === \Behat\Testwork\Tester\Result\TestResult::FAILED) {
      $result = FALSE;
      try {
        $result = $this->minkContext->getSession()
          ->getDriver()
          ->getWebDriverSession()
          ->log('browser');
      } catch (\Exception $e) {
        throw $e;
      }

      if (!is_array($result) || count($result) == 0) {
        return;
      }
      $file = sprintf("%s:%d", $scope->getFeature()->getFile(), $scope->getStep()->getLine());
      $message = sprintf("Found %d javascript error%s", count($result), count($result) > 0 ? 's' : '');
      echo '-------------------------------------------------------------' . PHP_EOL;
      echo $file . PHP_EOL;
      echo $message . PHP_EOL;
      echo '-------------------------------------------------------------' . PHP_EOL;
      foreach ($result as $index => $error) {
        echo sprintf("   #%d: %s", $index, print_r($error, 1)) . PHP_EOL;
      }
      throw new \Exception($message);

    }
  }
}


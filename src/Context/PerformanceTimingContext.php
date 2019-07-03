<?php

namespace miiimooo\BehatTools\Context;

/**
 * Behat Context for collecting and displaying Javascript error messages
 */
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class PerformanceTimingContext implements Context {
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
  public function report(AfterScenarioScope $scope) {
    $timing = $this->minkContext->getSession()->evaluateScript("return window.performance.timing");
    if (!isset($timing['responseStart'], $timing['navigationStart'], $timing['domComplete'])) {
      return;
    }
    $backend = $timing['responseStart'] - $timing['navigationStart'];
    $frontend = $timing['domComplete'] - $timing['responseStart'];
    $message = sprintf("Backend: %d / Frontend: %d", $backend, $frontend);
    echo $message . PHP_EOL;
    $this->currentScenario = NULL;
  }
}

# behat-tools

```yaml
composer require miiimooo/behat-tools
```

## ParagraphsContext
Support for creating paragraph content in Drupal 8 (only)
### Enabling
**behat.yml**

```yaml
default:
  suites:
    default:
      contexts:
        - miiimooo\BehatTools\Context\ParagraphsContext

```

### Usage
In your feature first define the paragraph and name it, then use the name to reference it in a field that references paragraphs:

```gherkin
Feature: Content
  @api
  Scenario: Paragraph creation through Drupal 8 API

    Given a "my_paragraph_type" paragraph named "my_arbitrary_name":
      | title             | Lorem ipsum|

    Given I am viewing a "page" content:
      | title            | Dolor sed         |
      | field_paragraphs | my_arbitrary_name |

```


## DavScreenshotFailureContext

The DavScreenshotFailureContext triggers on a failed stop in a Behat scenario and creates a screenshot in the configured screenshots folder.

Often in CI systems all created containers and artifacts are destroyed at the end of a CI run. 

The DavScreenshotFailureContext uploads the screenshots to a WebDAV server.


**behat.yml**

```yaml
default:
  suites:
    default:
      contexts:
        - miiimooo\BehatTools\Context\DavScreenshotFailureContext
...
      failure_path: %paths.base%/screenshots
```

### Environment
* WEBDAV_HOST
* WEBDAV_FOLDER
* WEBDAV_USERNAME
* WEBDAV_PASSWORD
* DRONE_REPO_NAME
* DRONE_BUILD_NUMBER
* CI

*CI* has to be set to "drone" for the web dav upload to happen

The URI where the screenshot will be saved is constructed as:
```
WEBDAV_HOST/WEBDAV_FOLDER/DRONE_REPO_NAME-DRONE_BUILD_NUMBER-FEATURE-LINENUMBER-TIMESTAMP.(png/html)
``` 

## JavascriptOnErrorContext

The JavascriptOnErrorContext triggers on a failed stop in a Behat scenario and prints out any Javascript errors.

**behat.yml**

```yaml
default:
  suites:
    default:
      contexts:
        - miiimooo\BehatTools\Context\JavascriptOnErrorContext
```

## PerformanceTimingContext

The PerformanceTimingContext reports the result from [Performance Timing API](https://developer.mozilla.org/en-US/docs/Web/API/Navigation_timing_API) after each scenario
```shell
    Then I should see the link "Home"
  │
  │  Backend: 3199 / Frontend: 500
  │  
  │
  └─ @AfterScenario @javascript # PerformanceTimingContext::report()

```
 

**behat.yml**

```yaml
default:
  suites:
    default:
      contexts:
        - miiimooo\BehatTools\Context\PerformanceTimingContext
```

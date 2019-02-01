# behat-tools

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

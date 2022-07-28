### Webserver

Unzip the package
Host machine must have PHP8.1 with ext-curl and ext-dom installed
```
$ sudo apt-get update && sudo apt-get install php && sudo apt-get install php-dom && sudo apt-get install php-curl
```

For Webserver instructions see [WEBSERVERS.md](WEBSERVERS.md)

# IGNORE
### Github:
Create [new Github App](https://github.com/settings/apps/new) and fille the following fields:
* GitHub App name
* Homepage URL
    * Pull requests - read-only

Copy the following values to .env
* Owned by: @jamjanek
* App ID: 223480
* Client ID: Iv1.2bbddd970bed7f59

Create milestones:
https://github.com/{{GH_ACCOUNT}}/{{REPOSITORY}}/milestones

Create issues:
https://github.com/{{GH_ACCOUNT}}/{{REPOSITORY}}/issues/new


Click on Generate a private key. copy the downloaded file to `./var/pem/`


* create repo
* 


ext-dom ext-curl
### Webserver

Unzip the package
Host machine must have PHP8.1 with ext-curl, ext-memcached and ext-dom installed
```shell
$ sudo apt-get update && sudo apt-get install -y php && sudo apt-get install -y php-dom && sudo apt-get install -y php-curl
```

For Webserver instructions see [WEBSERVERS.md](WEBSERVERS.md)

### PHPUNIT Tests
There are very basic unit Tests present. In order to run those run the following command
```shell
$ ./vendor/bin/phpunit tests
```

### Use Memcached
Install and start Memcached
```shell
$ sudo apt-get update && sudo apt-get install -y memcached && sudo apt-get install -y libmemcached-tools && sudo systemctl start memcached
```
Install PHP extension
```shell
$ sudo apt-get install -y php-memcached
```
More about configuring Memcached at [Digital Ocean](https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-memcached-on-ubuntu-20-04)

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
*  Create oauth Application
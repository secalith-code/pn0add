# Simple Kanban Board Application

Requirements:
  * PHP8.1 with ext-dom and ext-curl
  * Optionally supports Memcached with ext-memcached

Unzip the package

Copy `.env.dist` as `.env.` and change the values.
Change the values depending on Authentication way.

For Webserver instructions see [WEBSERVERS.md](WEBSERVERS.md)

## Authentication
There are two ways of authentication. GH_TOKEN and oAuth.

You will need to create github app at [https://github.com/settings/apps](https://github.com/settings/apps)

In order to use oAuth change the `GH_AUTH_METHOD` value in the `.env.` file to anything but `oauth` (case-insensitive).

### Personal Access token (GH_TOKEN)
Token should be generated at [Github](https://github.com/settings/tokens) and placed in .env file as `GH_TOKEN`.

Read more at [https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token](https://docs.github.com/en/authentication/keeping-your-account-and-data-secure/creating-a-personal-access-token)

and place it in `.`env` file as `GH_TOKEN`

### oAuth Access
You will need to create `oauth app` on github at [https://github.com/settings/developers](https://github.com/settings/developers)
More information about github oauth apps [https://docs.github.com/en/developers/apps/building-oauth-apps/creating-an-oauth-app](https://docs.github.com/en/developers/apps/building-oauth-apps/creating-an-oauth-app)

Copy value of 'Client Id' to `.env` file as value of `GH_CLIENT_ID`

Create new `Client Secret` and copy it to `.env` file as value of `GH_CLIENT_SECRET`

====

In both cases you should place your github username in .env as `GH_ACCOUNT`

## .env file
* GH_ACCOUNT your github username

---

### PHPUNIT Tests
There are very basic unit Tests present. In order to run those run the following command
```shell
$ ./vendor/bin/phpunit tests
```

---

## Webservers

Host machine must have `PHP8.1` with `ext-curl`, `ext-dom` installed. If using Memcached also `ext-memcached`
```shell
$ sudo apt-get update && sudo apt-get install -y php && sudo apt-get install -y php-dom && sudo apt-get install -y php-curl
```

###Install on Apache2
Example vhost for apache2 is located at `./var/apache2-vhost-centra.conf`
Copy the file to the `/etc/apache2/sites-avaliable` location and modify the following directives:
* ServerName
* ServerAlias
* DocumentRoot
* Directory
* run `sudo a2ensite apache2-vhost-centra.conf`
* `sudo service apache2 restart`
*
### Install on Nginx
Example vhost for apache2 is located at `./var/nginx-vhost-centra`

Copy and then edit Nginx vhost declaration
```shell
sudo cp ./var/nginx-vhost-centra /etc/nginx/sites-available
```
If all good then enable
* `sudo cp /etc/nginx/sites-available/nginx-vhost-centra /etc/nginx/sites-enabled/`
* `sudo systemctl restart nginx`

### Run with PHP build-in server:
```shell
    php -S 127.0.0.1:8080 -t ./public/
```
or with `composer`:
```shell
    composer run start
```

Open the App in your browser [127.0.0.1:8080](127.0.0.1:8080)

---

## Memcached

You may activate Memcahced support by changing `MCACHED_ENABLED` to `1` in `.env.` file

Install and start Memcached
```shell
$ sudo apt-get update && sudo apt-get install -y memcached && sudo apt-get install -y libmemcached-tools && sudo systemctl start memcached
```
Install PHP extension
```shell
$ sudo apt-get install -y php-memcached
```
More about configuring Memcached at [Digital Ocean](https://www.digitalocean.com/community/tutorials/how-to-install-and-secure-memcached-on-ubuntu-20-04)

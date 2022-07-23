### Install on Apache2
Example vhost for apache2 is located at `./var/apache2-vhost-centra.conf`
Copy the file to the `/etc/apache2/sites-avaliable` location and modify the following directives: 
* ServerName
* ServerAlias
* DocumentRoot
* Directory
* run `sudo a2ensite apache2-vhost-centra.conf`
* `sudo service apache2 restart` 
### Install on Nginx
Example vhost for apache2 is located at `./var/nginx-vhost-centra`
* `sudo cp ./var/nginx-vhost-centra /etc/nginx/sites-available`
* `sudo cp /etc/nginx/sites-available/nginx-vhost-centra /etc/nginx/sites-enabled/`
* `sudo systemctl restart nginx`
#Install:


##Postgres

Postgtres version is 10.23, but it's probably not important.

##PHP

php7.1 (we might need to upgrade to something more recent)

```sudo apt-get install php7.1
sudo apt-get install php7.1-pgsql
sudo apg-get install php7.2-curl
```

Edit: /etc/php/7.1/apache2/php.ini

```short_open_tag = On
post_max_size = 4000M
upload_max_filesize = 4000M
max_execution_time = 120
```

##Apache

Might be needed to check max LimitRequestBody


##Authentication

###Email configuration

User.php add email user and password to the config (TODO: read from a configuration file).



###Google authentication


###ORCID authentication

Again the key should be read from a config file.

https://orcid.org/oauth/authorize?client_id=APP-<Orcidkeyhere>&response_type=code&scope=/authenticate&redirect_uri=https://visual.ariadne-infrastructure.eu/login?state=orcid

Other orcid links:

* https://orcid.org/developer-tools

* https://members.orcid.org/api/integrate/orcid-sign-in

* https://github.com/thephpleague/oauth2-client

* https://github.com/SocialiteProviders/Providers/blob/master/src/Orcid/Provider.php


##Data folder

'vms' is the user running the python script.

```sudo chmod g+s ariadne_upload
sudo chmod g+w ariadne_upload
sudo chown -R www-data:vms ariadne_upload


sudo chmod g+s data
sudo chmod g+w data
sudo chown -R www-data:vms data

sudo usermod -a -G www-data vcg
```

#Processing:

libvips-tools is used for deepzoom (in deepzoom.sh)
We need the executables relight, nxsbuilder, nxsedit.

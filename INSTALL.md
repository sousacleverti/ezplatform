# Installation instructions

## Terms for future reference:
  * `/<root-dir>/`: The filesystem path where eZ Platform is installed in.
    Examples: `/home/myuser/www/` or `/var/sites/<project-name>/`
  * cli: command line interface. For *Linux/BSD/OS X* specif commands, use of `bash` or similar is assumed.

## Prerequisite

  These instructions assume you have technical knowledge and have already installed PHP, web server &
  *a database server* needed for this software. For further information on requirements [see online doc](https://doc.ez.no/display/EZP/Requirements)

  **Before you start**:
  - Create Database: Installation will ask you for credentials/details for which database to use
    *Note: Right now installer only supports MySQL, Postgres support should be (re)added in one of the upcoming releases.*
  - set php.ini memory_limit=256M before running commands below
  - *Optional:* You can also setup Solr to be used by eZ Platform and take note of url it is accessible on

## Install

1. **Install/Extract eZ Platform**:

    There are two ways to install eZ Platform described below, what is common is that you should make sure
    relevant settings are generated into `ezpublish/config/parameters.yml` as a result of this step.

    `parameters.yml` contains settings for your database, mail system, and optionally [Solr](http://lucene.apache.org/solr/)
    if `search_engine` is configured as `solr`, as opposed to default `legacy` *(a limited database powered search engine)*.

    A. **Extract archive** (tar/zip) *from http://share.ez.no/downloads/downloads*

       Extract the eZ Platform 15.01 (or higher) archive to a directory, then execute post install scripts:

       *Note: The post install scripts will ask you to fill in some settings, including database settings.*

       ```bash
       $ cd /<directory>/
       $ curl -sS https://getcomposer.org/installer | php
       $ php -d memory_limit=-1 composer.phar run-script post-install-cmd
       ```


    B. **Install via Composer**

     You can get eZ Platform using composer with the following commands:

     *Note: composer will take its time to download all libraries and when done you will be asked to fill in some settings, including database settings.*

       ```bash
       $ curl -sS https://getcomposer.org/installer | php
       $ php -d memory_limit=-1 composer.phar create-project --no-dev ezsystems/ezplatform <directory> [<version>]
       $ cd /<directory>/
       ```

     Options:
       - `<version>`: Optional, *if omitted you'll get latest*, examples for specifying:
        - `dev-master` to get current development version (pre release) `master` branch
        - `v0.9.0` to pick a specific release/tag
        - `~0.9.0` to pick latests v0.9.x release
       - For core development: Add '--prefer-source' to get full git clones, and remove '--no-dev' to get things like phpunit and behat installed.
       - Further reading: https://getcomposer.org/doc/03-cli.md#create-project

2. **Setup folder rights**:

       Like most things, [Symfony documentation](http://symfony.com/doc/current/book/installation.html#checking-symfony-application-configuration-and-setup)
       applies here, difference being that in eZ Platform directories that needs to be writable by cli and web server
       user are `ezpublish/{cache,logs,sessions}`. Furthermore, future files and directories created by these two users
       will need to inherit those access rights. *For security reasons, there is no need for web server to have access
       to write to other directories.*

       To make sure both have write access to only these, you can perform the following instructions from `<root-dir>`.
       *Note: All instructions assume the installation was extracted with user you plan to use for executing future cli
              commands, and not by web server user.*

       A. **Using ACL on a *Linux/BSD* system that has setfacl command installed**

       The following example is adopted from Symfony doc for eZ Platform:

       ```bash
         $ HTTPDUSER=`ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
         $ sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX ezpublish/{cache,logs,sessions}
         $ sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX ezpublish/{cache,logs,sessions}
       ```

       B. **Using  chown & chmod on any *Linux/BSD/OS X* system**

       Before executing commands below, make sure cli user you'll use is member of web server group, or that you as of
       this change *always* run eZ Platform/Symfony commands using web server user. And if you forget, worst thing that
       happens is that you might need to rerun these commands again.

       *Security note: web group must not be cli users primary group, otherwise web server will get access to it's files*

       In the example below, replace `www-group` with your web server group.

       ```bash
       $ chown -R :www-group ezpublish/{cache,logs,sessions}
       $ chmod -R ug+rwX ezpublish/{cache,logs,sessions}
       $ chmod -R g+s ezpublish/{cache,logs,sessions}
       ```

       C. **Setup folder rights on Windows**

       For your choice of web server you'll need to make sure web server user has read access to `<root-dir>`, and
       write access to the following directories:
       - ezpublish/cache
       - ezpublish/logs
       - ezpublish/sessions *Unless you choose to configure session to be stored by another system then file system*


3. **Configure a VirtualHost**:

    A virtual host setup is the recommended, most secure setup of eZ Publish.
    General virtual host setup template for Apache and Nginx can be found in [doc/ folder](doc/).


4. **Run installation command**:

    You may now complete the eZ Platform installation with ezplatform:install command, example of use:

    ```bash
    $ php -d memory_limit=-1 ezpublish/console ezplatform:install --env prod demo
    ```

    **Note**: Password for the generated `admin` user is `publish`, this name and password is needed when you would like to login to backend Platform UI. Future versions will prompt you for a unique password during installation.

You can now point your browser to the installation and browse the site. To access the Platform UI backend, use the `/ez` URL.

# Installation of Contao Botstatistics Bundle

There are two types of installation.

* with the Contao-Manager, only for Contao Managed-Editon
* via the command line, for Contao Standard-Edition and Managed-Editon


## Installation with Contao-Manager

* search for package: `bugbuster/contao-botstatistics-bundle`
* install the package
* Click on "Install Tool"
* Login and update the database


## Installation via command line

### Installation for Contao Managed-Edition

Installation in a Composer-based Contao 4.4+ Managed-Edition:

* `composer require "bugbuster/contao-botstatistics-bundle"`
* Call http://yourdomain/contao/install
* Login and update the database


### Installation for Contao Standard-Edition

Installation in a Composer-based Contao 4.4+ Standard-Edition

* `composer require "bugbuster/contao-botstatistics-bundle"`

Add in `app/AppKernel.php` following line at the end of the `$bundles` array.

`new BugBuster\BotStatisticsBundle\BugBusterBotStatisticsBundle(),`

Clears the cache and warms up an empty cache:

* `vendor/bin/contao-console cache:clear --env=prod`
* `vendor/bin/contao-console cache:warmup -e prod`
* Call http://yourdomain/contao/install
* Login and update the database


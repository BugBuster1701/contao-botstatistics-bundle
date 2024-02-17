# Installation of Contao Botstatistics Bundle

There are two types of installation.

* with the Contao-Manager, only for Contao Managed-Editon
* via the command line, for Contao Managed-Editon


## Installation with Contao-Manager

* search for package: `bugbuster/contao-botstatistics-bundle`
* install the package
* Update the database


## Installation via command line

Installation in a Composer-based Contao 5.2+ Managed-Edition:

* `composer require "bugbuster/contao-botstatistics-bundle"`
* `php bin/console contao:migrate`

(for Contao 4.13 use "... contao-botstatistics-bundle:^1.0")

# Installation von Contao Botstatistics Bundle

Es gibt zwei Arten der Installation.

* mit dem Contao-Manager, nur für die Contao Managed-Editon
* über die Kommandozeile, für Contao Standard-Edition und Managed-Editon


## Installation über Contao-Manager

* Suche das Paket: `bugbuster/contao-botstatistics-bundle`
* Installation der Erweiterung
* Klick auf "Install Tool"
* Anmelden und Datenbank Update durchführen


## Installation über die Kommandozeile

### Installation in einer Contao Managed-Edition

Installation in einer Composer-basierenden Contao 4.4+ Managed-Edition:

* `composer require "bugbuster/contao-botstatistics-bundle"`
* Aufruf https://deinedomain/contao/install
* Datenbank Update durchführen


### Installation in einer Contao Standard-Edition

Installation in einer Composer-basierenden Contao 4.4+ Standard-Edition:

* `composer require "bugbuster/contao-botstatistics-bundle"`

Einfügen in `app/AppKernel.php` folgende Zeile am Ende des Array `$bundles`:

`new BugBuster\BotStatisticsBundle\BugBusterBotStatisticsBundle(),`

Cache leeren und neu anlegen lassen:

* `vendor/bin/contao-console cache:clear --env=prod`
* `vendor/bin/contao-console cache:warmup -e prod`
* Aufruf https://deinedomain/contao/install
* Datenbank Update durchführen
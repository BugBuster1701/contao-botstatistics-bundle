# Installation von Contao Botstatistics Bundle

Es gibt zwei Arten der Installation.

* mit dem Contao-Manager, nur für die Contao Managed-Editon
* über die Kommandozeile, für die Contao Managed-Editon


## Installation über Contao-Manager

* Suche das Paket: `bugbuster/contao-botstatistics-bundle`
* Installation der Erweiterung
* Datenbank Update durchführen


## Installation über die Kommandozeile

Installation in einer Composer-basierenden Contao 5.2+ Managed-Edition:

* `composer require "bugbuster/contao-botstatistics-bundle"`
* `php bin/console contao:migrate`

(für Contao 4.13 benutze "... contao-botstatistics-bundle:^1.0")<br>
(für Contao 5.3 benutze "... contao-botstatistics-bundle:^1.1")<br>
(für Contao 5.4 benutze "... contao-botstatistics-bundle:^1.2")

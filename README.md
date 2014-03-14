Cleanup Extension
=================

Die CleanUp Extension ist als genereller Cronjob für die Löschung von Dateien gedacht. So können anhand von Konfigurationen bestimmte Ordner und Dateien (wahlweise auch rekursiv) nach einem festgelegten Intervall gelöscht werden. Alle Konfigurationen werden derzeit über eine config.php in einer eigenen Extension oder über die dcaconfig.php von Contao vorgenommen.

The CleanUp extension is intended as a general cronjob for the deletion of files. Therefore configurations may specify files and folders to be deleted (even recursively) after a defined interval. Currently all configuration has to be done via a config.php file within an extension or via the Contao dcaconfig.php.


### Konsole / Console

```
cd /var/www/contao/system/modules/cleanup
php CleanUpCaller.php
```


### HTTP Aufruf / HTTP Request

Da Contao 3.x sämtliche Ordner in system/modules schützt, ist ein direkter Aufruf ohne Anpassungen (wie z.B. Änderung der .htaccess im Root oder dem Ablegen einer .htaccess in system/modules) nur in Contao 2.11 möglich.

As Contao 3+ protects all subfolders within system/modules, direct requests into there are possible only in Contao 2.11 without adjustments (such as a change in the .htaccess of the root or adding an own .htaccess within a designated folder to be whitelisted).

http://www.example.com/system/modules/cleanup/CleanUpCaller.php


### Contao Cronjob

Contao bietet die Möglichkeit sich in die systemeigenen Cronjobs zu integrieren. Dafür muss man nur eins der 5 möglichen Beispiele aus der config.example.php in die dcaconfig.php oder in die config.php der eigenen Extension übernehmen und einkommentieren. Der stündliche und minütige Aufruf ist in Contao 2.11 nicht vorhanden.

Contao provides the ability to integrate own requests into the native system cron jobs. Therefore you have to take over one of the following 5 examples from the config.example.php and paste them into the dcaconfig.php or config.php of your own extension. The hourly and minute request is not available in Contao 2.11.

```php
$GLOBALS['TL_CRON']['monthly'][]    = array('CleanUp\CleanUp', 'run');
$GLOBALS['TL_CRON']['weekly'][]     = array('CleanUp\CleanUp', 'run');
$GLOBALS['TL_CRON']['daily'][]      = array('CleanUp\CleanUp', 'run');

// Contao 3 only
$GLOBALS['TL_CRON']['hourly'][]     = array('CleanUp\CleanUp', 'run');
$GLOBALS['TL_CRON']['minutely'][]   = array('CleanUp\CleanUp', 'run');
```
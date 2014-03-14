Cleanup Extension
=================

Die CleanUp Extension ist als genereller Cronjob für die Löschung von Dateien gedacht. So können anhand von Konfigurationen bestimmte Ordner und Dateien (wahlweise auch rekursiv) nach einem festgelegten Intervall gelöscht werden. Alle Konfigurationen werden derzeit über eine config.php in einer eigenen Extension oder über die dcaconfig.php von Contao vorgenommen. Eine Integration in die localconfig.php (und damit auch im Backend) ist geplant.

### via Konsole

```
cd /var/www/contao/system/modules/cleanup
php CleanUpCaller.php
```

### via Direktaufruf in Contao 2.11

Da Contao 3 sämtliche Ordner in system/modules schützt, ist ein direkter Aufruf ohne Anpassungen (wie z.B. einer Änderung der .htaccess) nur in Contao 2.11 möglich.

http://www.example.com/system/modules/cleanup/CleanUpCaller.php

### via Contao Cronjob

Contao bietet die Möglichkeit sich in die Systemeigenen Cronjobs zu integrieren. Dafür muss man nur eins der 5 möglichen Beispiele aus der config.example.php in die dcaconfig.php oder in die config.php der eigenen Extension übernehmen und einkommentieren. Der stündliche und minütige Aufruf ist in Contao 2.11 nicht vorhanden.
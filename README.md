## Proyecto Laravel base + Laravel Uptime Monitor

## Introduccion

Laravel Uptime monitor es una herramienta para verificar cada cierto tiempo si el sitio esta en linea o no. Esto es util en el caso de que se necesite notificar cuando  una api o servicio que se consume dejo de funcionar 

## Instalacion
Lo primero que se debe realizar es la instalacion de las dependencias mediante el gestor de paquetes Composer

```bash
composer require spatie/laravel-uptime-monitor
```

Se debe añadir en cada proyecto donde se utilizara el 'service provider' en la ruta config/app.php
```bash
'providers' => [
    ...
    Spatie\UptimeMonitor\UptimeMonitorServiceProvider::class,
];
```
Luego, se debe aplicar el comando vendor:publish, el cual permite copiar ciertos directorios u archivos desde la ubicacion original del paquete de un tercero y trasladarlas a nuestro proyecto
```bash
php artisan vendor:publish --provider="Spatie\UptimeMonitor\UptimeMonitorServiceProvider"
```
Una ves aplicado el comando anterior, se descargaran ciertos archivos de configuracion y migraciones necesarios para que el monitor funcione, es necesario contar con conexion a una base de datos en nuestro .env y en database.php,de lo contrario no funcionara. Si ya se cuenta con conexion a la base de datos, se debera correr el comando de migracion.
 ```bash
php artisan migrate
```
Finalmente se debe acceder a la carpeta raiz del proyecto, ingresar a  app/Console/Kernel.php y añadir los comandos que se ejecturaran constantemente para realizar la monitorizacion.
 ```bash
protected function schedule(Schedule $schedule)
{
    $schedule->command('monitor:check-uptime')->everyMinute();
    $schedule->command('monitor:check-certificate')->daily();
}
```
Finalmente se añado el cron job a nuestro servidor para habilitar al monitor y que ejecute los comandos repetidamente.
 ```bash
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```
## Consideraciones en la instalacion

En la carpeta raiz de php, en el archivo php.ini se deben tener habilitado lo siguiente para que no arroje errores de instalacion
 ```bash
extension=curl
extension=fileinfo
extension=intl
extension=pdo_pgsql //en caso de que la db sea en postgresql, caso contrario, habilitar la extension de la base de datos que se utilizara
```
Ademas de lo anterior, se debe descargar el archivo cacert.pem el cual se utilizara para validar los certificados tsl, el link lo dejo a continuacion

[Cacert.pem](https://curl.se/docs/caextract.html)

Una ves descargado, se debe dejar en la ruta raiz de php,por ejemplo:
 ```bash
C:\php74\cacert.pem
```
Finalmente acceder nuevamente a php.ini en la ruta raiz y buscar [curl], y dejarlo de la siguiente manera
```bash
[curl]
; A default value for the CURLOPT_CAINFO option. This is required to be an
; absolute path.

curl.cainfo = "C:\php74\cacert.pem"
```
***La ruta cambiara de acuerdo a la ubicacion de el archivo cacert.pem, por eso se recomienda dejar en la carpeta raiz de php

Si todo se realizo de manera correcta, el monitor esta listo para su funcionamiento

## Funcionamiento

Una ves instalado, el monitor posee las siguientes opciones de uso:
```bash
php artisan monitor:create https://url.com
php artisan monitor:delete https://url.com
php artisan monitor:enable https://url.com
php artisan monitor:disable https://url.com
php artisan monitor:list
```
** donde https://url.com es la url o servicio el cual se desea monitorizar.

#### :create

Se utiliza para añadir a la lista los sitios web/apis del monitor
```bash
Ej: 

php artisan monitor:create https://www.sistemasexpertos.cl/es/
```
#### :delete

Se utiliza para eliminar de la lista los sitios web/apis del monitor
```bash
Ej: 

php artisan monitor:delete https://www.sistemasexpertos.cl/es/
```
#### :enable

Se utiliza para habilitar los sitios web/apis las cuales se busca supervisar
```bash
Ej: 

php artisan monitor:enable https://www.sistemasexpertos.cl/es/
```
#### :disable

Se utiliza para deshabilitar el monitoreo del servicio web dado
```bash
Ej: 

php artisan monitor:disable https://www.sistemasexpertos.cl/es/
```
#### :list

Se utiliza para mostrar la lista de sitios web monitoreados
```bash

php artisan monitor:list 
```
el cual arrojara la lista por consola de los sitios, de la siguiente forma:
```bash
Healthy monitors
================
+-----------------------------+--------------+--------------+-------------------+-----------------------------+--------------------+
| URL                         | Uptime check | Online since | Certificate check | Certificate Expiration date | Certificate Issuer |
+-----------------------------+--------------+--------------+-------------------+-----------------------------+--------------------+
| https://sistemasexpertos.cl | ✅            | 1 week ago   | ✅                 | 3 weeks from now            | E1                 |
+-----------------------------+--------------+--------------+-------------------+-----------------------------+--------------------+
```

## Configuraciones personalizadas

El archivo config/laravel-uptime-monitor.php (el cual se crea cuando hacemos la instalacion del monitor via artisan), es un archivo de configuracion que va en cada proyecto al cual se quiera monitorizar.
Este archivo posee configuraciones personalizadas tanto para el envio de mail/slack, frecuencia de monitoreo y varias cosas mas.
Algunas de las mas importantes son:

#### Notifications

Sirve para añadir canales de notificacion (por ejemplo slack o mail), para enviar los mensajes de eventos del monitor
```bash
'notifications' => [
            \Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckFailed::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckRecovered::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\UptimeCheckSucceeded::class => [],

            \Spatie\UptimeMonitor\Notifications\Notifications\CertificateCheckFailed::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\CertificateExpiresSoon::class => ['slack'],
            \Spatie\UptimeMonitor\Notifications\Notifications\CertificateCheckSucceeded::class => [],
        ],
```
#### Location
Sirve para diferenciar la ubicacion del envio de notificaciones de monitoreo, esto sirve para diferenciar el lugar del cual viene el mensaje.
```bash
EJ:

'location' => 'Hospital Del Salvador',
```
#### Mail
Sirve para enviar las notificaciones via correo
```bash
EJ:

'mail' => [
            'to' => ['your@email.com'],
        ],
```

El archivo de configuracion viene con las explicaciones de cada personalizacion a realizar en cada linea

# Agenda Telefónica

## Introducción

**Agenda Telefónica** es una aplicación web que permite gestionar los contactos (nombre, email y teléfono). Las acciones que permite realizar son: ver contactos, buscar (por nombre o Nº de teléfono), añadir y eliminar. 

Para hacerla más escalable se ha dividido en dos partes:
 - **Backend** (carpeta `/api`): Es una API RESTful creada en **PHP** y usando Symfony. Dicha API conecta con una base de datos **MySQL**
 - **Frontend** (carpeta `/web-app`): Contiene la aplicación Front que consume la API y esté desarrollada con **HTML**, **CSS** y **Javascript**

Actualmente, se pueden ver y editar directamente sin un proceso de registro, en la práctica esta app debería tener un sistema de registro y login para que no es pudiera acceder libremente. 

## Instrucciones de instalación

### 1. Requisitos técnicos previos:
Para ejecutar la aplicación necesitamos un servidor web con:
 - `PHP 8.4.0`
 - `MySQL 9.1.0`

### 2. Instalación de los servidores:

Idealmente, esto debería hacerse con Docker, pero en este caso, para simplificar el desarrollo vamos a hacer una instalación en local de wamp (si estás en Windows) o mamp (si estás en mac)

Puedes descargarlos directamente en:
https://sourceforge.net/projects/wampserver/files/WampServer%203/WampServer%203.0.0/wampserver3.3.7_x64.exe/download
https://www.mamp.info/en/release-notes/mac/

Una vez instalados arrancaremos los servidores. 

### 3. Descarga el repositorio:
Accede a la carpeta raíz de proyectos del servidor instalado (suele ser `C:\wamp64\www` para Wamp y `/Applications/MAMP/htdocs/` para Mamp).

Dentro de esa carpeta descarga el repositorio con el comando:
```
git clone https://github.com/JuanRodriguez91/agenda-telefonica.git
```

Una vez descargado el proyecto, tenemos que instalar las dependencias de la API, para ello accedemos a la carpeta `/api` y en ella ejecutamos el comando:
```
cd api
```
```
composer install
```

### 3. Instalación de la base de datos:
Para instalar la base de datos, primero tenemos que tener un servidor de bases de datos instalado, configurado (Lo cual se explica en el paso anterior).

Una vez instalado el servidor, haz una copia del el fichero [.env.dev](api/.env.dev) en el mismo directorio con el nombre `.env.dev.local`
En `.env.dev.local` cambia la configuración de la base de datos para que coincida con la de tu servidor

Para crear las tablas en la BBDD, puedes hacerlo de dos formas:

#### 3.1. O bien lanzando migración de Symfony

En la consola de comandos, accede a la raíz del proyecto y ejecuta:
```
cd api
```
```
php bin/console doctrine:database:create
```
```
php bin/console doctrine:migrations:migrate
```

#### 3.2. O bien cargando directemente el fichero SQL

Alternativamente puedes ejecutar [agenda_telefonica.sql](agenda_telefonica.sql) en un cliente de bases de datos como phpMyAdmin, MySQL Workbench, etc.

Posteriormente borramos la caché:
```
php bin/console cache:clear
```

#### 4. 
Descripción de la aplicación


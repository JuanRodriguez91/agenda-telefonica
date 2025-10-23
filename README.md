# Agenda Telefónica

## Descripción

![agenda-telefonica.png](agenda-telefonica.png)

**Agenda Telefónica** es una aplicación web que permite gestionar los contactos (nombre, email y teléfono). Las acciones que permite realizar son: ver contactos, buscar (por nombre o N.º de teléfono), añadir y eliminar. 

Para hacer la aplicación más escalable se ha dividido en dos partes:
 - **Backend** (carpeta `/api`): Es una API RESTful creada en **PHP** y usando Symfony. Dicha API conecta con una base de datos **MySQL**
 - **Frontend** (carpeta `/web-app`): Contiene la aplicación Front que consume la API, desarrollada con **HTML**, **CSS** y **Javascript**

_NOTA: Actualmente, se pueden ver y editar directamente sin un proceso de registro, en la práctica esta app debería tener un sistema de registro y login para que no es pudiera acceder libremente._ 

## Instrucciones de instalación

### 1. Requisitos técnicos previos:
Para ejecutar la aplicación necesitamos un servidor web con:
 - `PHP 8.4.0`
 - `MySQL 9.1.0`

También necesitaremos herramientas como **Git** y **Composer**

### 2. Instalación:

### 2.1 Instalación de git:

Para poder descargar el repositorio necesitamos tener instalado GIT. Podemos descargarlo en este enlace:

https://git-scm.com/install/

### 2.2 Instalación de Composer:

Para la instalación de dependencias de nuestra API, necesitamos Composer. Se puede descargar desde el enlace:

https://getcomposer.org/download/


### 2.1 Instalación de los servidores:

Idealmente, esto debería hacerse con Docker, pero en este caso, para simplificar el desarrollo haremos una instalación en local de wamp (si estás en Windows) o mamp (si estás en mac).

Puedes descargarlos directamente en:

https://sourceforge.net/projects/wampserver/files/WampServer%203/WampServer%203.0.0/wampserver3.3.7_x64.exe/download

https://www.mamp.info/en/release-notes/mac/

Una vez instalados arrancaremos los servidores. 

### 3. Descarga el repositorio:
Accede a la carpeta raíz de proyectos del servidor instalado (suele ser `C:\wamp64\www` para Wamp y `/Applications/MAMP/htdocs/` para Mamp).

Dentro de esa carpeta, descarga el repositorio con el comando:
```
git clone https://github.com/JuanRodriguez91/agenda-telefonica.git
```

Una vez descargado el proyecto, tenemos que instalar las dependencias de la API, para ello, accedemos a la carpeta `/api` y dentro de ella ejecutamos el comando:
```
cd api
```
```
composer install
```

### 3. Instalación de la base de datos:
Para instalar la base de datos, primero tenemos que tener un servidor de bases de datos instalado, configurado (Lo cual se explica en el punto 2).

Una vez instalado el servidor, haz una copia del el fichero [.env.dev](api/.env.dev) (dentro de la carpeta `/api`) en el mismo directorio con nombre `.env.dev.local`.

En `.env.dev.local`, cambia la configuración de la base de datos para que coincida con la de tu servidor:

```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=agenda_telefonica
DB_USERNAME=(tu usuario)
DB_PASSWORD=(tu contraseña)
```

Puedes comprobar si la conexión está correctamente configurada esta URL:

http://localhost/agenda-telefonica/api/public/


Una vez configurada la conexión, crearemos la base de datos y las tablas, esto lo podemos hacer de dos formas:

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

#### 4. Ejecutar aplicación
Si hemos seguido los pasos anteriores correctamente, podremos ejecutar la aplicación desde la URL:

http://localhost/agenda-telefonica/web-app/

(Esta URL será válida siempre que hayamos descargado el repositorio en el directorio raíz de nuestro servidor web)


# tirocinio
This repository contains the code of the project developed during the internship at the University of Bologna. It's a small web application that allows you to manage the data of the point of interest of the Emilia-Romagna region.

## Getting started

### Project setup
Install the dependencies
```
npm install
```

Create a file called _config.json_ in _/src/backend/_ that will look like this:
```json
{
    "folder_dump" : "xml",    // the folder that contains the xml files
    "extension_dump" : "gml", // the extension of the xml files
    "host" : "host of the database",
    "db_name" : "name of the database",
    "db_user" : "user of the database",
    "db_password" : "password of the user",
    "port" : "port of the database"
}
```

Modify the file _vue.config.json_ in the root of the project with the following content:
```json
{
    "name_redirect" : "name of the folder to reference",
    "url_redirect" : "the url of the server that will host the backend",
    "path_redirect" : "the path of the server that will host the backend that contains it"
}
```
Create a folder called _xml_ in _/src/backend/_ and put the xml files that contains the dump of the database in it.
You can request the dump of the database from here: [Geoportale regione Emilia-Romagna](https://geoportale.regione.emilia-romagna.it/catalogo/dati-cartografici/ambiente/percorsi-escursionistici).
## Deployment
### Compiles and minifies for production
```
npm run build
```
### Apache server
To deploy the project on an apache server, you need to create a file called _.htaccess_ in the root of the project with the following content:
```apache
  RewriteEngine On
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^POINT1/(.*)$ POINT2/$1 [L,QSA,PT]
```

Sobistute POINT1 with _name redirect_ and POINT2 with _path redirect_ of the _vue.config.json_ file.
## Development
### Compiles and hot-reloads for development
```
npm run serve
```


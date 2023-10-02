# Thesis

This repository is my thesis for the bachelor degree. It's a small web application that allows you to manage the data of the point of interest of the Emilia-Romagna region.

## Getting started

### Project setup

Install the dependencies

```
npm install
```

Create a file called _config.json_ in _/src/backend/_ that will look like this:

```json
{
  "utmZone": "32T", // Time zone of your zone
  "xml_folder_dump": "xml", // Name of the folder where you will place all your xml files
  "host": "localhost", // The ip of the server that will host the backend
  "db_user": "root", // The user of the database mysql
  "db_password": "", // The password of the database mysql
  "db_name": "tirocinio", // The name of the database mysql
  "port": 3306 // The port of the database mysql
}
```

Modify the file _vue.config.json_ in the root of the project with the following content:

```json
{
  "name_redirect": "name of the folder to reference",
  "url_redirect": "the url of the server that will host the backend",
  "path_redirect": "the path of the server that will host the backend that contains it"
}
```

Create a folder where you will place all the xml files (in the example provided in the _config.json_ I called it _xml_) in _/src/backend/_ and put the xml files that contains the dump of the database in it.

You can request the dump of the database from here: [Geoportale regione Emilia-Romagna](https://geoportale.regione.emilia-romagna.it/catalogo/dati-cartografici/ambiente/percorsi-escursionistici).

For this project you will need only the file with _gml_ extension.

## Deployment

### Compiles and minifies for production

```
npm run build
```

It will create a dist folder that contains _ONLY_ the frontned.

## Development

### Compiles and hot-reloads for development

```
npm run serve
```

### Lints and fixes files

```
npm run lint
```

## Credits

This repository was made possible by [vue-leaflet/vue-leaflet](https://github.com/vue-leaflet/vue-leaflet), a very cool library that allows you to use leaflet in vue3 and it works flawlessly
just like the original library ([vue2leaflet](https://vue2-leaflet.netlify.app/)).

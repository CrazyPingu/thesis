<?php

/**
 * This dictionary is used to map the table of the database to the correct insert order for the query
 *
 * @package utils
 *
 */
return [
  'info_museo' => '(objectId, link, globalId, nome)',
  'info_fermata' => '(objectId, gestore, linea)',
  'coordinata' => '(latitudine, longitudine, objectId)',
  'Percorso_escursionistico' => '(objectId, id_percorso, localita, difficolta, nome_numero, sigla, dislivello_salita, dislivello_discesa, lunghezza, gestore, segnavia, tempo_andata, tempo_ritorno, link_google, link, altro_segnavia)',
  'tipologia' => '(tipo)',
  'punto_di_interesse' => '(objectId, descrizione, tipologia)',
  'identificatore' => '(objectId, idPoi)',
];


?>
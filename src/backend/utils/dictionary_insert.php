<?php

/**
 * This dictionary is used to map the table of the database to the correct insert order for the query
 *
 * @package utils
 *
 */
return [
  'info_museo' => '(globalId, idPoi, link, nome)',
  'info_fermata' => '(idPoi, gestore, linea)',
  'coordinata' => '(latitudine, longitudine, idPoi)',
  'percorso_escursionistico' => '(idPercorso, localita, difficolta, nome_numero, sigla, dislivello_salita, dislivello_discesa, lunghezza, gestore, segnavia, tempo_andata, tempo_ritorno, link_google, link, altro_segnavia)',
  'tipologia' => '(tipo)',
  'punto_di_interesse' => '(idPoi, descrizione, tipologia)',
  'identificatore' => '(objectId, idPoi)',
];


?>
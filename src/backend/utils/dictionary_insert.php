<?php

/**
 * This dictionary is used to map the table of the database to the correct insert order for the query
 *
 * @package utils
 *
 */
return [
  'Museo' => '(objectId, nome, globalId, link)',
  'Fermata_bus' => '(objectId, gestore, linea)',
  'coordinata' => '(latitudine, longitudine, objectId)',
  'Percorso_escursionistico' => '(objectId, id_percorso, localita, difficolta, nome_numero, sigla, dislivello_salita, dislivello_discesa, lunghezza, gestore, segnavia, tempo_andata, tempo_ritorno, link_google, link, altro_segnavia)',
  'tipologia' => '(tipo)',
  'Attrezzatura_alberghiera' => '(objectId, id_poi, descrizione, tipologia)',
  'Campeggio' => '(objectId, id_poi, descrizione, tipologia)',
  'Farmacia' => '(objectId, id_poi, descrizione, tipologia)',
  'Limitazione_al_transito' => '(objectId, id_poi, descrizione, tipologia)',
  'Ostello' => '(objectId, id_poi, descrizione, tipologia)',
  'Parcheggio' => '(objectId, id_poi, descrizione, tipologia)',
  'Pericolo_valanghe' => '(objectId, id_poi, descrizione, tipologia)',
  'Punto_panoramico_a_360_gradi' => '(objectId, id_poi, descrizione, tipologia)',
  'Punto_panoramico_orientato_a_Est' => '(objectId, id_poi, descrizione, tipologia)',
  'Punto_panoramico_orientato_a_Nord' => '(objectId, id_poi, descrizione, tipologia)',
  'Punto_panoramico_orientato_a_Ovest' => '(objectId, id_poi, descrizione, tipologia)',
  'Punto_panoramico_orientato_a_Sud' => '(objectId, id_poi, descrizione, tipologia)',
  'Ricovero_di_emergenza' => '(objectId, id_poi, descrizione, tipologia)',
  'Scuderia' => '(objectId, id_poi, descrizione, tipologia)',
  'Sorgente_o_fontana' => '(objectId, id_poi, descrizione, tipologia)',
  'Stazione_ferroviaria' => '(objectId, id_poi, descrizione, tipologia)'
];


?>
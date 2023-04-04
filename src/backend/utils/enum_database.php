<?php

/**
 * This enum is used to map the table read from the xml file to the table in the database
 *
 * @package utils
 *
 */
enum DatabaseTable
{
  case Museo = 'info_museo';
  case Fermata_bus = 'info_fermata';
  case Percorso_escursionistico = 'percorso_escursionistico';
  case tipologia = 'tipologia';
  case Coordinata = 'coordinata';
  case Attrezzatura_alberghiera = 'punto_di_interesse';
  case Campeggio = 'punto_di_interesse';
  case Farmacia = 'punto_di_interesse';
  case Limitazione_al_transito = 'punto_di_interesse';
  case Ostello = 'punto_di_interesse';
  case Parcheggio = 'punto_di_interesse';
  case Pericolo_valanghe = 'punto_di_interesse';
  case Punto_panoramico_a_360_gradi = 'punto_di_interesse';
  case Punto_panoramico_orientato_a_Est = 'punto_di_interesse';
  case Punto_panoramico_orientato_a_Nord = 'punto_di_interesse';
  case Punto_panoramico_orientato_a_Ovest = 'punto_di_interesse';
  case Punto_panoramico_orientato_a_Sud = 'punto_di_interesse';
  case Ricovero_di_emergenza = 'punto_di_interesse';
  case Scuderia = 'punto_di_interesse';
  case Sorgente_o_fontana = 'punto_di_interesse';
  case Stazione_ferroviaria = 'punto_di_interesse';
}

?>
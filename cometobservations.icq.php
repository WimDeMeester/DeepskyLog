<?php

  // Bugzilla bug 119
  $inIndex = 1;
  header('Content-Type: text/plain');
  header('Content-Disposition: attachment; filename="cometobservations.icq"');
  session_start();

  $_SESSION['module'] = 'comets';

  include 'common/entryexit/preludes.php';
  function instrrev($n, $s)
  {
      $x = strpos(chr(0).strrev($n), $s) + 0;

      return ($x == 0) ? 0 : strlen($n) - $x + 1;
  }

  function instrType($instrtypenumeriek)
  {
      switch ($instrtypenumeriek) {
            case 0:
                $instrtype = 'E';
            break;

            case 1:
                $instrtype = 'B';
            break;

            case 2:
                $instrtype = 'R';
            break;

            case 3:
                $instrtype = 'L';
            break;

            case 4:
                $instrtype = 'R';
            break;

            case 5:
                $instrtype = ' ';
            break;

            case 6:
                $instrtype = 'C';
            break;

            case 7:
                $instrtype = ' ';
            break;

            case 8:
                $instrtype = 'M';
            break;

            case 9:
                $instrtype = 'T';
            break;

            default:
            $instrtype = ' ';
        }

      return $instrtype;
  }

  function getDayDecimal($tijd, $dag)
  {
      $uurpuntminuut = $tijd / 100;
      if (!strstr($uurpuntminuut, '.')) {
          $uurpuntminuut .= '.0';
      }
      $uur = explode('.', $uurpuntminuut);
      $uur = $uur[0];
      $minuten = explode('.', $uurpuntminuut);
      $minuten = $minuten[1];
      $percentageuur = $minuten / 60;
      $uurdecimaal = $uur + $percentageuur;
      $dagdecimaal = round(($uurdecimaal / 24) + $dag, 2);

      if ($dagdecimaal < 10) {
          $dagdecimaal = '0'.round($dagdecimaal, 2);
      }
      while (strlen($dagdecimaal) < 5) {
          $dagdecimaal = $dagdecimaal.' ';
      }

      return $dagdecimaal;
  }

  function getInstrumentAperture($instrumentID)
  {
      $objInstrument = new Instruments();
      $apert = $objInstrument->getInstrumentPropertyFromId($instrumentID, 'diameter');
      $apert /= 10;
      if (floor($apert) == ceil($apert)) {
          $apert .= '.0';
      }

      return str_pad($apert, 4, ' ', STR_PAD_LEFT);
  }

  function getInstrumentFocalRatio($instrumentID)
  {
      $objInstrument = new Instruments();
      $focalratio = $objInstrument->getInstrumentPropertyFromId($instrumentID, 'fd');

      return $focalratio;
  }

  function getInstrumentType($instrumentid)
  {
      $instrtypenumeriek = $objInstrument->getInstrumentPropertyFromId($instrumentid, 'type');

      switch ($instrtypenumeriek) {
            case 0:
                $instrtype = 'E';
            break;

            case 1:
                $instrtype = 'B';
            break;

            case 2:
                $instrtype = 'R';
            break;

            case 3:
                $instrtype = 'L';
            break;

            case 4:
                $instrtype = 'R';
            break;

            case 5:
                $instrtype = ' ';
            break;

            case 6:
                $instrtype = 'C';
            break;

            case 7:
                $instrtype = ' ';
            break;

            case 8:
                $instrtype = 'M';
            break;

            case 9:
                $instrtype = 'T';
            break;

            default:
            $instrtype = ' ';
        }
  }

  global $objCometObservation;
  global $objCometObject;
  global $objInstrument;

  $_SESSION['module'] = 'comets';
  $result = $_SESSION['observation_query'];

  if (!empty($result)) {
      //print "ICQ LIST OF OBSERVATIONS";

      //$result bevat observ id's

      /*
       * 	http://kometen.fg-vds.de/vorgabee.htm
       */

      for ($i = 0; $i <= count($result) - 1; ++$i) {
          //algemene info
          $objectid = $objCometObservation->getObjectId($result[$i]);

          //periodic comet code niet gekend? (Drie karakters)

          //1
          $icqname = substr($objCometObject->getIcqName($objectid), 0, 10);
          //geen veld ivm periodiciteit?
          if (is_numeric(substr($icqname, 0, 4))) {
              $icqname = '   '.str_pad($icqname, 7, STR_PAD_RIGHT);
          } else {
              $icqname = str_pad($icqname, 10, STR_PAD_RIGHT);
          }
          echo $icqname;

          echo ' '; //2
          $observationdate = $objCometObservation->getDate($result[$i]);
          echo substr($observationdate, 0, 4); //JJJJ
          echo ' ';
          echo substr($observationdate, 4, 2); //MM
          echo ' ';

          //3
          $dayDecimal = getDayDecimal($objCometObservation->getTime($result[$i]), substr($objCometObservation->getDate($result[$i]), 6, 2));
          $arr = explode('.', $dayDecimal);
          if (isset($arr[1])) {
              $day = $arr[0];
              $decimals = $arr[1];
              echo str_pad($day, 2, '0', STR_PAD_LEFT);
              echo '.'.str_pad($decimals, 3, ' ', STR_PAD_RIGHT);
          } else {
              $day = $arr[0];
              echo str_pad(str_pad($day, 2, '0', STR_PAD_LEFT), 6, ' ', STR_PAD_RIGHT);
          }

          //4 = e = scheidend vermogen
          echo ' ';

          //5 M = magnitude method
          echo str_pad($objCometObservation->getMethode($result[$i]), 1, ' ', STR_PAD_RIGHT);

          //6 [mm.m = visual magnitude of coma

          echo str_pad(str_pad($objCometObservation->getComa($result[$i]), 5, ' ', STR_PAD_LEFT), 5, ' ', STR_PAD_RIGHT);

          //7 :   =magcertainty
          if ($objCometObservation->getMagnitudeUncertain($result[$i])) {
              echo ':';
          } else {
              print ' ';
          }

          //8: reference of comparison
          echo str_pad($objCometObservation->getChart($result[$i]), 2, ' ');

          //9: Aperture of instrument
          echo ' ';
          echo getInstrumentAperture($objCometObservation->getInstrumentId($result[$i]));

          //10: Instrument type
          echo str_pad(instrType($objInstrument->getInstrumentPropertyFromId($objCometObservation->getInstrumentId($result[$i]), 'type')), 1, ' ');

          //11: focal ratio
          $focalratio = getInstrumentFocalRatio($objCometObservation->getInstrumentId($result[$i]));

          $fragm = str_pad(round($focalratio, 0), 1, ' ', STR_PAD_RIGHT);
          echo $fragm;

          //12: magnification
          $magnification = $objCometObservation->getMagnification($result[$i]);
          //print str_pad($magnification, 3, " ", STR_PAD_RIGHT) . " ";

          if ($fragm < 10) {
              echo str_pad(str_pad($magnification, 4, ' ', STR_PAD_LEFT), 5, ' ', STR_PAD_RIGHT);
          } else {
              echo str_pad(str_pad($magnification, 3, ' ', STR_PAD_LEFT), 4, ' ', STR_PAD_RIGHT);
          }

          //13: coma diameters
          echo ' ';
          $coma = str_pad($objCometObservation->getComa($result[$i]), 3, ' ', STR_PAD_LEFT);
          if (floor($coma) == ceil($coma)) {
              $coma .= '.00';
          }
          echo $coma;

          //14: n = special note concerning the appearance of central condensation
          echo ' ';

          //15: DC
          echo str_pad($objCometObservation->getDc($result[$i]), 2, ' ', STR_PAD_RIGHT);
          echo ' ';

          //16: tail
          $tail = $objCometObservation->getTail($result[$i]);
          echo str_pad($tail, 3, ' ', STR_PAD_LEFT);
          echo ' ';

          //17: observation angle
          echo str_pad($objCometObservation->getPa($result[$i]), 3, ' ', STR_PAD_LEFT);

          //18: icq name

          echo $objObserver->getObserverProperty($objCometObservation->getObserverId($result[$i]), 'icqname');
          echo "\r\n";
      }

      /*
       * dit commentaar tijdelijk nog niet verwijderen aub
       *
      for($i=0; $i<count($result); $i++){
          //print $result[$i] . "\r\n";
          $objectid = $objCometObservation->getObjectId($result[$i]);
          $icqname = substr($objCometObject->getIcqName($objectid), 0, 10);

          $datum = $objCometObservation->getDate($result[$i]);
          $jaar = substr($datum, 0, 4);
          $maand = substr($datum, 4, 2);
          $dag = substr($datum, 6, 2);
          $tijd = $objCometObservation->getTime($result[$i]);

          //
          $uurpuntminuut = $tijd/100;
          if(!strstr($uurpuntminuut, ".")) $uurpuntminuut .= ".0";
          $uur = explode(".", $uurpuntminuut);
          $uur = $uur[0];
          $minuten = explode(".", $uurpuntminuut);
          $minuten = $minuten[1];
          $percentageuur = $minuten/60;
          $uurdecimaal = $uur + $percentageuur;
          $dagdecimaal = round(($uurdecimaal/24)+$dag, 2);
          //
          if($dagdecimaal < 10) $dagdecimaal = "0" . round($dagdecimaal, 2);
          while(strlen($dagdecimaal)<5) $dagdecimaal = $dagdecimaal . " ";

          $M = $objCometObservation->getMethode($result[$i]);	//Methode

          if(empty($M)) $M = " ";
          /// ----------------
          //opbouwen ICQ lijn
          //volgorde:  IIIYYYYMnL YYYY MM DD.DD eM mm.m:r AAA.ATF/xxxx &dd.ddnDC &t.ttmANG ICQ XX*OBSxx
          //              icqname  jaar m dagdec
          //									  e hebben we niet, dan methode

          $magnitude = $objCometObservation->getMagnitude($result[$i]);
          $magnitude = round($magnitude, 1);
          if($magnitude == -99.9) $magnitude = str_pad(' ', 4);
          if($magnitude < 10) $magnitude = " " . $magnitude;
          if(strlen($magnitude) < 4) $magnitude .= ".0";
          if($magnitude == 0) $magnitude = "   0";

          if($objCometObservation->getMagnitudeUncertain($result[$i])){
              $onzekerheid = ":";
          }else{
              $onzekerheid = " ";
          }

          $r = $objCometObservation->getChart($result[$i]);
          while(strlen($r)<2) $r = " " . $r;

          $instrumentid = $objCometObservation->getInstrumentId($result[$i]);
          $instrumentdia = $objInstrument->getInstrumentPropertyFromId($instrumentid, "diameter"	)/10;

          if($instrumentdia < 10) $instrumentdia = " " . $instrumentdia;
          if($instrumentdia < 100) $instrumentdia = " " . $instrumentdia;

          //indien geen kommagetal: 2 spaties achter
          if(ceil($instrumentdia) == floor($instrumentdia)) $instrumentdia .= "  ";
          $instrtypenumeriek = $objInstrument->getInstrumentPropertyFromId($instrumentid, "type");

          switch($instrtypenumeriek){
              case 0:
                  $instrtype="E";
              break;

              case 1:
                  $instrtype="B";
              break;

              case 2:
                  $instrtype="R";
              break;

              case 3:
                  $instrtype="L";
              break;

              case 4:
                  $instrtype="R";
              break;

              case 5:
                  $instrtype=" ";
              break;

              case 6:
                  $instrtype="C";
              break;

              case 7:
                  $instrtype=" ";
              break;

              case 8:
                  $instrtype="M";
              break;

              case 9:
                  $instrtype="T";
              break;

              default:
              $instrtype=" ";
          }


          $focalratio = str_pad($objCometObservation->getDc($result[$i]), 2, " ", STR_PAD_LEFT);
          $magnification = str_pad($objCometObservation->getMagnification($result[$i]), 3, " ", STR_PAD_RIGHT);

          $tail = $objCometObservation->getTail($result[$i]);
          if(instrrev("-", $tail)){
              $tail = " - " . abs((int) $tail);
          }elseif(instrrev("+", $tail)){
              $tail = " + " . abs((int) $tail);
          }elseif(instrrev(">", $tail)){
              $tail = " > " . abs((int) $tail);
          }elseif(instrrev("<", $tail)){
              $tail = " < " . abs((int) $tail);
          }
          $tail = str_pad($tail, 4, " ", STR_PAD_LEFT);
          $tail = str_pad($tail, 7, " ", STR_PAD_RIGHT);
          $dc = $objCometObservation->getDc($result[$i]);


          echo $icqname . " " . $jaar . " " . $maand . " " . $dagdecimaal . "  " . $M . " " . $magnitude . $onzekerheid . $r . $instrumentdia . $instrtype . $focalratio . " " . $magnification . $tail . $dc . " ICQ XX\r\n";

   }
   //    print_r($result);
      */
  }

  /*
  IIIYYYYMnL	YYYY MM DD.DD eM mm.m:r AAA.ATF/xxxx &dd.ddnDC &t.ttmANG ICQ XX*OBSxx

  2006W3     	2008 12 27.83  S 09.4 	TK 35  L 4 178   1.5       D7    ICQ XX DEK01
*/

<?php
// accomplishments.php
// The objects class collects all functions needed to calculated and retrieve the accomplishments of an observer.

global $inIndex;
if((!isset($inIndex))||(!$inIndex)) include "../../redirect.php";

class Accomplishments {
  // Calculates the number of different messiers objects the observer has seen and 
  // returns an array [ bronze, silver, gold ] 
  public function calculateMessier($observer)
  { global $objObservation;
    $objObservation = new Observations();
    $numberOfMessiers = $objObservation->getObservedCountFromCatalogOrList($observer,"M");

    return $this->ranking($numberOfMessiers, 3);
  }

  // Calculates the number of different messiers objects the observer has drawn and 
  // returns an array [ bronze, silver, gold ] 
  public function calculateMessierDrawings($observer)
  { global $objObservation;
    $objObservation = new Observations();
    $numberOfDrawings = $objObservation->getDrawingsCountFromCatalog($observer, "M");

    return $this->ranking($numberOfDrawings, 3);
  }

  // Calculates the number of different caldwell objects the observer has seen and 
  // returns an array [ bronze, silver, gold ] 
  public function calculateCaldwell($observer)
  { global $objObservation;
    $objObservation = new Observations();
    $numberOfCaldwells = $objObservation->getObservedCountFromCatalogOrList($observer,"Caldwell");

    return $this->ranking($numberOfCaldwells, 3);
  }

  // Calculates the number of different caldwell objects the observer has drawn and 
  // returns an array [ bronze, silver, gold ] 
  public function calculateCaldwellDrawings($observer)
  { global $objObservation;
    $objObservation = new Observations();
    $numberOfCaldwells = $objObservation->getDrawingsCountFromCatalog($observer,"Caldwell");

    return $this->ranking($numberOfCaldwells, 3);
  }

  // Calculates the number of different herschel objects the observer has seen and 
  // returns an array [ bronze, silver, gold, diamond, platina ] 
  public function calculateHerschel($observer)
  { global $objObservation;
    $objObservation = new Observations();
    $numberOfHerschels = $objObservation->getObservedCountFromCatalogOrList($observer,"H400");

    return $this->ranking($numberOfHerschels, 5);
  }

  // Calculates the number of different herschel objects the observer has drawn and 
  // returns an array [ bronze, silver, gold, diamond, platina ] 
  public function calculateHerschelDrawings($observer)
  { global $objObservation;
    $objObservation = new Observations();
    $numberOfDrawings = $objObservation->getDrawingsCountFromCatalog($observer, "H400");

    return $this->ranking($numberOfDrawings, 5);
  }

  // Calculates the number of different herschel II objects the observer has seen and 
  // returns an array [ bronze, silver, gold, diamond, platina ] 
  public function calculateHerschelII($observer)
  { global $objObservation;
    $objObservation = new Observations();
    $numberOfHerschels = $objObservation->getObservedCountFromCatalogOrList($observer,"HII");

    return $this->ranking($numberOfHerschels, 5);
  }

  // Calculates the number of different herschel II objects the observer has drawn and 
  // returns an array [ bronze, silver, gold, diamond, platina ] 
  public function calculateHerschelIIDrawings($observer)
  { global $objObservation;
    $objObservation = new Observations();
    $numberOfDrawings = $objObservation->getDrawingsCountFromCatalog($observer, "HII");

    return $this->ranking($numberOfDrawings, 5);
  }

  // Calculates the total number of drawings the observer has made and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateDrawings($observer)
  { global $objObservation;
    $objObservation = new Observations();
    $drawingsMade = $objObservation->getDsDrawingsCountFromObserver($observer);

    return $this->ranking($drawingsMade, 10);
  }

  // Calculates the total number of comet observations the observer has made and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateCometObservations($observer) {
    global $objObserver;
    $userCometobservation=$objObserver->getNumberOfCometObservations($observer); 	
    return $this->ranking($userCometobservation, 10);
  }

  // Calculates the number of different comet observed by the observer and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateCometsObserved($observer) {
    global $objCometObservation;
    $userCometObjects = $objCometObservation->getNumberOfObjects($observer);
    return $this->ranking($userCometObjects, 10);
  }

  // Calculates the total number of comet drawings the observer has made and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateCometDrawings($observer) {
    global $objCometObservation;
    $drawingsMade = $objCometObservation->getCometDrawingsCountFromObserver($observer);
    return $this->ranking($drawingsMade, 10);
  }

  // Calculates the number of different open clusters the observer has seen and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateOpenClusters($observer)
  { global $objDatabase;
    $opncl = count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"OPNCL\" and observations.observerid = \"" . $observer . "\""));
    $opncl += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"CLANB\" and observations.observerid = \"" . $observer . "\""));

    return $this->ranking($opncl, 10, 1700);
  }

  // Calculates the number of different open clusters the observer has drawn and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateOpenClusterDrawings($observer)
  { global $objDatabase; 
    $opnclDr = count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"OPNCL\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));
    $opnclDr += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"CLANB\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));

    return $this->ranking($opnclDr, 10, 1700);
  }

  // Calculates the number of different globular clusters the observer has seen and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateGlobularClusters($observer)
  { global $objDatabase;
    $glocl = count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"GLOCL\" and observations.observerid = \"" . $observer . "\""));

    return $this->ranking($glocl, 10, 152);
  }

  // Calculates the number of different globular clusters the observer has drawn and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateGlobularClusterDrawings($observer)
  { global $objDatabase; 
    $gloclDr = count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"GLOCL\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));

    return $this->ranking($gloclDr, 10, 152);
  }

  // Calculates the number of different planetary nebulae the observer has seen and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculatePlanetaryNebulae($observer)
  { global $objDatabase;
    $plnnb = count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"PLNNB\" and observations.observerid = \"" . $observer . "\""));

    return $this->ranking($plnnb, 10, 1023);
  }

  // Calculates the number of different planetary nebulae the observer has drawn and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculatePlanetaryNebulaDrawings($observer)
  { global $objDatabase; 
    $plnnbDr = count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"PLNNB\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));

    return $this->ranking($plnnbDr, 10, 1023);
  }

  // Calculates the number of different galaxies the observer has seen and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateGalaxies($observer)
  { global $objDatabase;
    $galxy = count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"GALXY\" and observations.observerid = \"" . $observer . "\""));

    return $this->ranking($galxy, 10);
  }

  // Calculates the number of different galaxies the observer has drawn and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateGalaxyDrawings($observer)
  { global $objDatabase; 
    $galxyDr = count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"GALXY\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));

    return $this->ranking($galxyDr, 10);
  }

  // Calculates the number of different nebulae the observer has seen and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateNebulae($observer)
  { global $objDatabase;

    $eminb = count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"EMINB\" and observations.observerid = \"" . $observer . "\""));
    $eminb += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"ENRNN\" and observations.observerid = \"" . $observer . "\""));
    $eminb += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"ENSTR\" and observations.observerid = \"" . $observer . "\""));
    $eminb += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"REFNB\" and observations.observerid = \"" . $observer . "\""));
    $eminb += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"RNHII\" and observations.observerid = \"" . $observer . "\""));
    $eminb += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"HII\" and observations.observerid = \"" . $observer . "\""));
    $eminb += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"SNREM\" and observations.observerid = \"" . $observer . "\""));
    $eminb += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"WRNEB\" and observations.observerid = \"" . $observer . "\""));

    return $this->ranking($eminb, 10, 384);
  }

  // Calculates the number of different nebulae the observer has drawn and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateNebulaDrawings($observer)
  { global $objDatabase; 

    $eminbDr = count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"EMINB\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));
    $eminbDr += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"ENRNN\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));
    $eminbDr += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"ENSTR\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));
    $eminbDr += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"REFNB\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));
    $eminbDr += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"RNHII\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));
    $eminbDr += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"HII\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));
    $eminbDr += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"SNREM\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));
    $eminbDr += count($objDatabase->selectRecordsetArray("select DISTINCT(objects.name) from objects,observations where objects.name = observations.objectname and objects.type = \"WRNEB\" and observations.observerid = \"" . $observer . "\" and observations.hasDrawing = 1"));

    return $this->ranking($eminbDr, 10, 384);
  }

  // Calculates the number of different objects the observer has seen and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateDifferentObjects($observer)
  { 
    $objObservation = new Observations();
    $totalDSobjects = $objObservation->getNumberOfObjects($observer);

    return $this->ranking($totalDSobjects, 10);
  }

  // Calculates the number of different objects the observer has drawn and 
  // returns an array [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  public function calculateDifferentObjectDrawings($observer)
  { 
    $objObservation = new Observations();
    $totalDSDrawings = $objObservation->getNumberOfObjectDrawings($observer);

    return $this->ranking($totalDSDrawings, 10);
  }

  // Returns an boolean array with the accomplishments
  private function ranking($numberOfObjects, $categories, $total = 5000) {
  	if ($categories == 3) {
  		return $this->accomplishments3($numberOfObjects);
  	} else if ($categories == 5) {
  		return $this->accomplishments5($numberOfObjects);
  	} else {
  		return $this->accomplishments10($numberOfObjects, $total);  		
  	}
  }
  // Returns a boolean array for [ bronze, silver, gold ] 
  private function accomplishments3($numberOfObjects) {
    return [ $numberOfObjects >= 25 ? 1:0, $numberOfObjects >= 50 ? 1:0, 
             $numberOfObjects >= 110 ? 1:0 ];
  }

  // Returns a boolean array for [ bronze, silver, gold, diamond, platina ] 
  private function accomplishments5($numberOfObjects) {
    return [ $numberOfObjects >= 25 ? 1:0, $numberOfObjects >= 50 ? 1:0, 
             $numberOfObjects >= 100 ? 1:0, $numberOfObjects >= 200 ? 1:0,
             $numberOfObjects >= 400 ? 1:0 ];
  }

  // Returns a boolean array for [ Newbie, Rookie, Beginner, Talented, Skilled, Intermediate, Experienced, Advanced, Senior, Expert ]
  private function accomplishments10($numberOfObjects, $total) {
  	$total1 = 1; 
  	$total10 = ($total / 500) >= 2 ? ($total / 500):2;
  	$total25 = ($total / 200) >= 3 ? ($total / 200):3;
  	$total50 = ($total / 100) >= 4 ? ($total / 100):4;
  	$total100 = ($total / 50) >= 5 ? ($total / 50):5;
  	$total250 = ($total / 20) >= 6 ? ($total / 20):6;
  	$total500 = ($total / 10) >= 7 ? ($total / 10):7;
  	$total1000 = ($total / 5) >= 8 ? ($total / 5):8;
  	$total2500 = ($total / 2) >= 9 ? ($total / 2):9;
  	$total5000 = $total >= 4 ? $total:4;
    return [ $numberOfObjects >= $total1 ? 1:0, $numberOfObjects >= $total10 ? 1:0, 
             $numberOfObjects >= $total25 ? 1:0, $numberOfObjects >= $total50 ? 1:0,
             $numberOfObjects >= ($total / 50) ? 1:0, $numberOfObjects >= ($total / 20) ? 1:0,
             $numberOfObjects >= ($total / 10) ? 1:0, $numberOfObjects >= ($total / 5) ? 1:0,
             $numberOfObjects >= ($total / 2) ? 1:0, $numberOfObjects >= ($total) ? 1:0 ];
  }
  
  // Create an entry for a new observer in the accomplishments table
  public function addObserver($observerId) {
  	$sql = "INSERT INTO accomplishments (observer, messierBronze, messierSilver, messierGold, messierDrawingsBronze, messierDrawingsSilver, messierDrawingsGold, caldwellBronze, caldwellSilver, caldwellGold, caldwellDrawingsBronze, caldwellDrawingsSilver, caldwelldrawingsGold, herschelBronze, herschelSilver, herschelGold, herschelDiamond, herschelPlatina, herschelDrawingsBronze, herschelDrawingsSilver, herschelDrawingsGold, herschelDrawingsDiamond, herschelDrawingsPlatina, herschelIIBronze, herschelIISilver, herschelIIGold, herschelIIDiamond, herschelIIPlatina, herschelIIDrawingsBronze, herschelIIDrawingsSilver, herschelIIDrawingsGold, herschelIIDrawingsDiamond, herschelIIDrawingsPlatina, drawingsNewbie, drawingsRookie, drawingsBeginner, drawingsTalented, drawingsSkilled, drawingsIntermediate, drawingsExperienced, drawingsAdvanced, drawingsSenior, drawingsExpert, cometObservationsNewbie, cometObservationsRookie, cometObservationsBeginner, cometObservationsTalented, cometObservationsSkilled, cometObservationsIntermediate, cometObservationsExperienced, cometObservationsAdvanced, cometObservationsSenior, cometObservationsExpert, cometsObservedNewbie, cometsObservedRookie, cometsObservedBeginner, cometsObservedTalented, cometsObservedSkilled, cometsObservedIntermediate, cometsObservedExperienced, cometsObservedAdvanced, cometsObservedSenior, cometsObservedExpert, cometDrawingsNewbie, cometDrawingsRookie, cometDrawingsBeginner, cometDrawingsTalented, cometDrawingsSkilled, cometDrawingsIntermediate, cometDrawingsExperienced, cometDrawingsAdvanced, cometDrawingsSenior, cometDrawingsExpert, openClusterNewbie, openClusterRookie, openClusterBeginner, openClusterTalented, openClusterSkilled, openClusterIntermediate, openClusterExperienced, openClusterAdvanced, openClusterSenior, openClusterExpert, openClusterDrawingsNewbie, openClusterDrawingsRookie, openClusterDrawingsBeginner, openClusterDrawingsTalented, openClusterDrawingsSkilled, openClusterDrawingsIntermediate, openClusterDrawingsExperienced, openClusterDrawingsAdvanced, openClusterDrawingsSenior, openClusterDrawingsExpert, globularClusterNewbie, globularClusterRookie, globularClusterBeginner, globularClusterTalented, globularClusterSkilled, globularClusterIntermediate, globularClusterExperienced, globularClusterAdvanced, globularClusterSenior, globularClusterExpert, globularClusterDrawingsNewbie, globularClusterDrawingsRookie, globularClusterDrawingsBeginner, globularClusterDrawingsTalented, globularClusterDrawingsSkilled, globularClusterDrawingsIntermediate, globularClusterDrawingsExperienced, globularClusterDrawingsAdvanced, globularClusterDrawingsSenior, globularClusterDrawingsExpert, planetaryNebulaNewbie, planetaryNebulaRookie, planetaryNebulaBeginner, planetaryNebulaTalented, planetaryNebulaSkilled, planetaryNebulaIntermediate, planetaryNebulaExperienced, planetaryNebulaAdvanced, planetaryNebulaSenior, planetaryNebulaExpert, planetaryNebulaDrawingsNewbie, planetaryNebulaDrawingsRookie, planetaryNebulaDrawingsBeginner, planetaryNebulaDrawingsTalented, planetaryNebulaDrawingsSkilled, planetaryNebulaDrawingsIntermediate, planetaryNebulaDrawingsExperienced, planetaryNebulaDrawingsAdvanced, planetaryNebulaDrawingsSenior, planetaryNebulaDrawingsExpert, galaxyNewbie, galaxyRookie, galaxyBeginner, galaxyTalented, galaxySkilled, galaxyIntermediate, galaxyExperienced, galaxyAdvanced, galaxySenior, galaxyExpert, galaxyDrawingsNewbie, galaxyDrawingsRookie, galaxyDrawingsBeginner, galaxyDrawingsTalented, galaxyDrawingsSkilled, galaxyDrawingsIntermediate, galaxyDrawingsExperienced, galaxyDrawingsAdvanced, galaxyDrawingsSenior, galaxyDrawingsExpert, nebulaNewbie, nebulaRookie, nebulaBeginner, nebulaTalented, nebulaSkilled, nebulaIntermediate, nebulaExperienced, nebulaAdvanced, nebulaSenior, nebulaExpert, nebulaDrawingsNewbie, nebulaDrawingsRookie, nebulaDrawingsBeginner, nebulaDrawingsTalented, nebulaDrawingsSkilled, nebulaDrawingsIntermediate, nebulaDrawingsExperienced, nebulaDrawingsAdvanced, nebulaDrawingsSenior, nebulaDrawingsExpert, objectsNewbie, objectsRookie, objectsBeginner, objectsTalented, objectsSkilled, objectsIntermediate, objectsExperienced, objectsAdvanced, objectsSenior, objectsExpert, objectsDrawingsNewbie, objectsDrawingsRookie, objectsDrawingsBeginner, objectsDrawingsTalented, objectsDrawingsSkilled, objectsDrawingsIntermediate, objectsDrawingsExperienced, objectsDrawingsAdvanced, objectsDrawingsSenior, objectsDrawingsExpert) " .
  			"VALUES (\"". $observerId ."\", 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
  			        0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
  			        0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
  			        0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
  			        0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
  			        0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0,
  			        0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);";
  	$run = mysql_query($sql) or die(mysql_error());
  }

  // Delete an entry for a deleted observer in the accomplishments table
  public function deleteObserver($observerId) {
  	$sql = "DELETE FROM accomplishments WHERE observer = \"". $observerId ."\");";
  	$run = mysql_query($sql) or die(mysql_error());
  }
  
  // Returns 1 if the observer has seen 25 messiers
  public function getMessierBronze($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select messierBronze from accomplishments where observer = \"". $observerId . "\";")[0]["messierBronze"];
  }

  // Returns 1 if the observer has seen 50 messiers
  public function getMessierSilver($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select messierSilver from accomplishments where observer = \"". $observerId . "\";")[0]["messierSilver"];
  }

  // Returns 1 if the observer has seen 110 messiers
  public function getMessierGold($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select messierGold from accomplishments where observer = \"". $observerId . "\";")[0]["messierGold"];
  }
  
  // Returns 1 if the observer has drawn 25 messiers
  public function getMessierDrawingsBronze($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select messierDrawingsBronze from accomplishments where observer = \"". $observerId . "\";")[0]["messierDrawingsBronze"];
  }

  // Returns 1 if the observer has drawn 50 messiers
  public function getMessierDrawingsSilver($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select messierDrawingsSilver from accomplishments where observer = \"". $observerId . "\";")[0]["messierDrawingsSilver"];
  }

  // Returns 1 if the observer has drawn 110 messiers
  public function getMessierDrawingsGold($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select messierDrawingsGold from accomplishments where observer = \"". $observerId . "\";")[0]["messierDrawingsGold"];
  }
  
  // Returns 1 if the observer has seen 25 Caldwells
  public function getCaldwellBronze($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CaldwellBronze from accomplishments where observer = \"". $observerId . "\";")[0]["CaldwellBronze"];
  }

  // Returns 1 if the observer has seen 50 Caldwells
  public function getCaldwellSilver($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CaldwellSilver from accomplishments where observer = \"". $observerId . "\";")[0]["CaldwellSilver"];
  }

  // Returns 1 if the observer has seen 110 Caldwells
  public function getCaldwellGold($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CaldwellGold from accomplishments where observer = \"". $observerId . "\";")[0]["CaldwellGold"];
  }
  
  // Returns 1 if the observer has drawn 25 Caldwells
  public function getCaldwellDrawingsBronze($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CaldwellDrawingsBronze from accomplishments where observer = \"". $observerId . "\";")[0]["CaldwellDrawingsBronze"];
  }

  // Returns 1 if the observer has drawn 50 Caldwells
  public function getCaldwellDrawingsSilver($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CaldwellDrawingsSilver from accomplishments where observer = \"". $observerId . "\";")[0]["CaldwellDrawingsSilver"];
  }

  // Returns 1 if the observer has drawn 110 Caldwells
  public function getCaldwellDrawingsGold($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CaldwellDrawingsGold from accomplishments where observer = \"". $observerId . "\";")[0]["CaldwellDrawingsGold"];
  }
  
  // Returns 1 if the observer has seen 25 Herschels
  public function getHerschelBronze($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelBronze from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelBronze"];
  }

  // Returns 1 if the observer has seen 50 Herschels
  public function getHerschelSilver($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelSilver from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelSilver"];
  }

  // Returns 1 if the observer has seen 100 Herschels
  public function getHerschelGold($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelGold from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelGold"];
  }
  
  // Returns 1 if the observer has seen 200 Herschels
  public function getHerschelDiamond($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelDiamond from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelDiamond"];
  }
  
  // Returns 1 if the observer has seen 400 Herschels
  public function getHerschelPlatina($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelPlatina from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelPlatina"];
  }
  
  // Returns 1 if the observer has drawn 25 Herschels
  public function getHerschelDrawingsBronze($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelDrawingsBronze from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelDrawingsBronze"];
  }

  // Returns 1 if the observer has drawn 50 Herschels
  public function getHerschelDrawingsSilver($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelDrawingsSilver from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelDrawingsSilver"];
  }

  // Returns 1 if the observer has drawn 100 Herschels
  public function getHerschelDrawingsGold($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelDrawingsGold from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelDrawingsGold"];
  }
  
  // Returns 1 if the observer has drawn 200 Herschels
  public function getHerschelDrawingsDiamond($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelDrawingsDiamond from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelDrawingsDiamond"];
  }
  
  // Returns 1 if the observer has drawn 400 Herschels
  public function getHerschelDrawingsPlatina($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelDrawingsPlatina from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelDrawingsPlatina"];
  }
  
  // Returns 1 if the observer has seen 25 HerschelIIs
  public function getHerschelIIBronze($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelIIBronze from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelIIBronze"];
  }

  // Returns 1 if the observer has seen 50 HerschelIIs
  public function getHerschelIISilver($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelIISilver from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelIISilver"];
  }

  // Returns 1 if the observer has seen 100 HerschelIIs
  public function getHerschelIIGold($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelIIGold from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelIIGold"];
  }
  
  // Returns 1 if the observer has seen 200 HerschelIIs
  public function getHerschelIIDiamond($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelIIDiamond from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelIIDiamond"];
  }
  
  // Returns 1 if the observer has seen 400 HerschelIIs
  public function getHerschelIIPlatina($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelIIPlatina from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelIIPlatina"];
  }
  
  // Returns 1 if the observer has drawn 25 HerschelIIs
  public function getHerschelIIDrawingsBronze($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelIIDrawingsBronze from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelIIDrawingsBronze"];
  }

  // Returns 1 if the observer has drawn 50 HerschelIIs
  public function getHerschelIIDrawingsSilver($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelIIDrawingsSilver from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelIIDrawingsSilver"];
  }

  // Returns 1 if the observer has drawn 100 HerschelIIs
  public function getHerschelIIDrawingsGold($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelIIDrawingsGold from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelIIDrawingsGold"];
  }
  
  // Returns 1 if the observer has drawn 200 HerschelIIs
  public function getHerschelIIDrawingsDiamond($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelIIDrawingsDiamond from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelIIDrawingsDiamond"];
  }
  
  // Returns 1 if the observer has drawn 400 HerschelIIs
  public function getHerschelIIDrawingsPlatina($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select HerschelIIDrawingsPlatina from accomplishments where observer = \"". $observerId . "\";")[0]["HerschelIIDrawingsPlatina"];
  }

  // Returns 1 if the observer has one drawing
  public function getDrawingsNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select DrawingsNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["DrawingsNewbie"];
  }
  
  // Returns 1 if the observer has 10 drawings
  public function getDrawingsRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select DrawingsRookie from accomplishments where observer = \"". $observerId . "\";")[0]["DrawingsRookie"];
  }
  
  // Returns 1 if the observer has 25 drawings
  public function getDrawingsBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select DrawingsBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["DrawingsBeginner"];
  }
  
  // Returns 1 if the observer has 50 drawings
  public function getDrawingsTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select DrawingsTalented from accomplishments where observer = \"". $observerId . "\";")[0]["DrawingsTalented"];
  }
  
  // Returns 1 if the observer has 100 drawings
  public function getDrawingsSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select DrawingsSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["DrawingsSkilled"];
  }
  
  // Returns 1 if the observer has 250 drawings
  public function getDrawingsIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select DrawingsIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["DrawingsIntermediate"];
  }
  
  // Returns 1 if the observer has 500 drawings
  public function getDrawingsExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select DrawingsExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["DrawingsExperienced"];
  }
  
  // Returns 1 if the observer has 1000 drawings
  public function getDrawingsAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select DrawingsAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["DrawingsAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 drawings
  public function getDrawingsSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select DrawingsSenior from accomplishments where observer = \"". $observerId . "\";")[0]["DrawingsSenior"];
  }
  
  // Returns 1 if the observer has 5000 drawings
  public function getDrawingsExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select DrawingsExpert from accomplishments where observer = \"". $observerId . "\";")[0]["DrawingsExpert"];
  }
  
  // Returns 1 if the observer has one open clusters
  public function getOpenClustersNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterNewbie"];
  }
  
  // Returns 1 if the observer has 10 OpenClusters
  public function getOpenClustersRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterRookie from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterRookie"];
  }
  
  // Returns 1 if the observer has 25 OpenClusters
  public function getOpenClustersBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterBeginner"];
  }
  
  // Returns 1 if the observer has 50 OpenClusters
  public function getOpenClustersTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterTalented from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterTalented"];
  }
  
  // Returns 1 if the observer has 100 OpenClusters
  public function getOpenClustersSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterSkilled"];
  }
  
  // Returns 1 if the observer has 250 OpenClusters
  public function getOpenClustersIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterIntermediate"];
  }
  
  // Returns 1 if the observer has 500 OpenClusters
  public function getOpenClustersExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterExperienced"];
  }
  
  // Returns 1 if the observer has 1000 OpenClusters
  public function getOpenClustersAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 OpenClusters
  public function getOpenClustersSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterSenior from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterSenior"];
  }
  
  // Returns 1 if the observer has 5000 OpenClusters
  public function getOpenClustersExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterExpert from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterExpert"];
  }
  
  // Returns 1 if the observer has one open clusters
  public function getOpenClusterDrawingsNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterDrawingsNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterDrawingsNewbie"];
  }
  
  // Returns 1 if the observer has 10 OpenClusterDrawings
  public function getOpenClusterDrawingsRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterDrawingsRookie from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterDrawingsRookie"];
  }
  
  // Returns 1 if the observer has 25 OpenClusterDrawings
  public function getOpenClusterDrawingsBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterDrawingsBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterDrawingsBeginner"];
  }
  
  // Returns 1 if the observer has 50 OpenClusterDrawings
  public function getOpenClusterDrawingsTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterDrawingsTalented from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterDrawingsTalented"];
  }
  
  // Returns 1 if the observer has 100 OpenClusterDrawings
  public function getOpenClusterDrawingsSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterDrawingsSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterDrawingsSkilled"];
  }
  
  // Returns 1 if the observer has 250 OpenClusterDrawings
  public function getOpenClusterDrawingsIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterDrawingsIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterDrawingsIntermediate"];
  }
  
  // Returns 1 if the observer has 500 OpenClusterDrawings
  public function getOpenClusterDrawingsExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterDrawingsExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterDrawingsExperienced"];
  }
  
  // Returns 1 if the observer has 1000 OpenClusterDrawings
  public function getOpenClusterDrawingsAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterDrawingsAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterDrawingsAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 OpenClusterDrawings
  public function getOpenClusterDrawingsSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterDrawingsSenior from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterDrawingsSenior"];
  }
  
  // Returns 1 if the observer has 5000 OpenClusterDrawings
  public function getOpenClusterDrawingsExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select OpenClusterDrawingsExpert from accomplishments where observer = \"". $observerId . "\";")[0]["OpenClusterDrawingsExpert"];
  }

  // Returns 1 if the observer has one Globular clusters
  public function getGlobularClustersNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterNewbie"];
  }
  
  // Returns 1 if the observer has 10 GlobularClusters
  public function getGlobularClustersRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterRookie from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterRookie"];
  }
  
  // Returns 1 if the observer has 25 GlobularClusters
  public function getGlobularClustersBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterBeginner"];
  }
  
  // Returns 1 if the observer has 50 GlobularClusters
  public function getGlobularClustersTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterTalented from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterTalented"];
  }
  
  // Returns 1 if the observer has 100 GlobularClusters
  public function getGlobularClustersSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterSkilled"];
  }
  
  // Returns 1 if the observer has 250 GlobularClusters
  public function getGlobularClustersIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterIntermediate"];
  }
  
  // Returns 1 if the observer has 500 GlobularClusters
  public function getGlobularClustersExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterExperienced"];
  }
  
  // Returns 1 if the observer has 1000 GlobularClusters
  public function getGlobularClustersAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 GlobularClusters
  public function getGlobularClustersSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterSenior from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterSenior"];
  }
  
  // Returns 1 if the observer has 5000 GlobularClusters
  public function getGlobularClustersExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterExpert from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterExpert"];
  }
  
  // Returns 1 if the observer has one Globular clusters
  public function getGlobularClusterDrawingsNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterDrawingsNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterDrawingsNewbie"];
  }
  
  // Returns 1 if the observer has 10 GlobularClusterDrawings
  public function getGlobularClusterDrawingsRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterDrawingsRookie from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterDrawingsRookie"];
  }
  
  // Returns 1 if the observer has 25 GlobularClusterDrawings
  public function getGlobularClusterDrawingsBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterDrawingsBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterDrawingsBeginner"];
  }
  
  // Returns 1 if the observer has 50 GlobularClusterDrawings
  public function getGlobularClusterDrawingsTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterDrawingsTalented from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterDrawingsTalented"];
  }
  
  // Returns 1 if the observer has 100 GlobularClusterDrawings
  public function getGlobularClusterDrawingsSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterDrawingsSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterDrawingsSkilled"];
  }
  
  // Returns 1 if the observer has 250 GlobularClusterDrawings
  public function getGlobularClusterDrawingsIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterDrawingsIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterDrawingsIntermediate"];
  }
  
  // Returns 1 if the observer has 500 GlobularClusterDrawings
  public function getGlobularClusterDrawingsExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterDrawingsExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterDrawingsExperienced"];
  }
  
  // Returns 1 if the observer has 1000 GlobularClusterDrawings
  public function getGlobularClusterDrawingsAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterDrawingsAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterDrawingsAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 GlobularClusterDrawings
  public function getGlobularClusterDrawingsSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterDrawingsSenior from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterDrawingsSenior"];
  }
  
  // Returns 1 if the observer has 5000 GlobularClusterDrawings
  public function getGlobularClusterDrawingsExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GlobularClusterDrawingsExpert from accomplishments where observer = \"". $observerId . "\";")[0]["GlobularClusterDrawingsExpert"];
  }

  // Returns 1 if the observer has one planetary nebula
  public function getPlanetaryNebulaNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaNewbie"];
  }
  
  // Returns 1 if the observer has 10 PlanetaryNebula
  public function getPlanetaryNebulaRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaRookie from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaRookie"];
  }
  
  // Returns 1 if the observer has 25 PlanetaryNebula
  public function getPlanetaryNebulaBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaBeginner"];
  }
  
  // Returns 1 if the observer has 50 PlanetaryNebula
  public function getPlanetaryNebulaTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaTalented from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaTalented"];
  }
  
  // Returns 1 if the observer has 100 PlanetaryNebula
  public function getPlanetaryNebulaSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaSkilled"];
  }
  
  // Returns 1 if the observer has 250 PlanetaryNebula
  public function getPlanetaryNebulaIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaIntermediate"];
  }
  
  // Returns 1 if the observer has 500 PlanetaryNebula
  public function getPlanetaryNebulaExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaExperienced"];
  }
  
  // Returns 1 if the observer has 1000 PlanetaryNebula
  public function getPlanetaryNebulaAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 PlanetaryNebula
  public function getPlanetaryNebulaSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaSenior from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaSenior"];
  }
  
  // Returns 1 if the observer has 5000 PlanetaryNebula
  public function getPlanetaryNebulaExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaExpert from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaExpert"];
  }
  
  // Returns 1 if the observer has one Globular clusters
  public function getPlanetaryNebulaDrawingsNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaDrawingsNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaDrawingsNewbie"];
  }
  
  // Returns 1 if the observer has 10 PlanetaryNebulaDrawings
  public function getPlanetaryNebulaDrawingsRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaDrawingsRookie from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaDrawingsRookie"];
  }
  
  // Returns 1 if the observer has 25 PlanetaryNebulaDrawings
  public function getPlanetaryNebulaDrawingsBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaDrawingsBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaDrawingsBeginner"];
  }
  
  // Returns 1 if the observer has 50 PlanetaryNebulaDrawings
  public function getPlanetaryNebulaDrawingsTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaDrawingsTalented from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaDrawingsTalented"];
  }
  
  // Returns 1 if the observer has 100 PlanetaryNebulaDrawings
  public function getPlanetaryNebulaDrawingsSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaDrawingsSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaDrawingsSkilled"];
  }
  
  // Returns 1 if the observer has 250 PlanetaryNebulaDrawings
  public function getPlanetaryNebulaDrawingsIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaDrawingsIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaDrawingsIntermediate"];
  }
  
  // Returns 1 if the observer has 500 PlanetaryNebulaDrawings
  public function getPlanetaryNebulaDrawingsExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaDrawingsExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaDrawingsExperienced"];
  }
  
  // Returns 1 if the observer has 1000 PlanetaryNebulaDrawings
  public function getPlanetaryNebulaDrawingsAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaDrawingsAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaDrawingsAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 PlanetaryNebulaDrawings
  public function getPlanetaryNebulaDrawingsSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaDrawingsSenior from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaDrawingsSenior"];
  }
  
  // Returns 1 if the observer has 5000 PlanetaryNebulaDrawings
  public function getPlanetaryNebulaDrawingsExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select PlanetaryNebulaDrawingsExpert from accomplishments where observer = \"". $observerId . "\";")[0]["PlanetaryNebulaDrawingsExpert"];
  }

  // Returns 1 if the observer has one planetary nebula
  public function getGalaxyNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyNewbie"];
  }
  
  // Returns 1 if the observer has 10 Galaxy
  public function getGalaxyRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyRookie from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyRookie"];
  }
  
  // Returns 1 if the observer has 25 Galaxy
  public function getGalaxyBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyBeginner"];
  }
  
  // Returns 1 if the observer has 50 Galaxy
  public function getGalaxyTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyTalented from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyTalented"];
  }
  
  // Returns 1 if the observer has 100 Galaxy
  public function getGalaxySkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxySkilled from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxySkilled"];
  }
  
  // Returns 1 if the observer has 250 Galaxy
  public function getGalaxyIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyIntermediate"];
  }
  
  // Returns 1 if the observer has 500 Galaxy
  public function getGalaxyExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyExperienced"];
  }
  
  // Returns 1 if the observer has 1000 Galaxy
  public function getGalaxyAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 Galaxy
  public function getGalaxySenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxySenior from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxySenior"];
  }
  
  // Returns 1 if the observer has 5000 Galaxy
  public function getGalaxyExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyExpert from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyExpert"];
  }

  // Returns 1 if the observer has one galaxy Drawing
  public function getGalaxyDrawingsNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyDrawingsNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyDrawingsNewbie"];
  }
  
  // Returns 1 if the observer has 10 GalaxyDrawings
  public function getGalaxyDrawingsRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyDrawingsRookie from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyDrawingsRookie"];
  }
  
  // Returns 1 if the observer has 25 GalaxyDrawings
  public function getGalaxyDrawingsBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyDrawingsBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyDrawingsBeginner"];
  }
  
  // Returns 1 if the observer has 50 GalaxyDrawings
  public function getGalaxyDrawingsTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyDrawingsTalented from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyDrawingsTalented"];
  }
  
  // Returns 1 if the observer has 100 GalaxyDrawings
  public function getGalaxyDrawingsSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyDrawingsSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyDrawingsSkilled"];
  }
  
  // Returns 1 if the observer has 250 GalaxyDrawings
  public function getGalaxyDrawingsIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyDrawingsIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyDrawingsIntermediate"];
  }
  
  // Returns 1 if the observer has 500 GalaxyDrawings
  public function getGalaxyDrawingsExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyDrawingsExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyDrawingsExperienced"];
  }
  
  // Returns 1 if the observer has 1000 GalaxyDrawings
  public function getGalaxyDrawingsAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyDrawingsAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyDrawingsAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 GalaxyDrawings
  public function getGalaxyDrawingsSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyDrawingsSenior from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyDrawingsSenior"];
  }
  
  // Returns 1 if the observer has 5000 GalaxyDrawings
  public function getGalaxyDrawingsExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select GalaxyDrawingsExpert from accomplishments where observer = \"". $observerId . "\";")[0]["GalaxyDrawingsExpert"];
  }
  
  // Returns 1 if the observer has one nebula
  public function getNebulaNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaNewbie"];
  }
  
  // Returns 1 if the observer has 10 Nebula
  public function getNebulaRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaRookie from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaRookie"];
  }
  
  // Returns 1 if the observer has 25 Nebula
  public function getNebulaBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaBeginner"];
  }
  
  // Returns 1 if the observer has 50 Nebula
  public function getNebulaTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaTalented from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaTalented"];
  }
  
  // Returns 1 if the observer has 100 Nebula
  public function getNebulaSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaSkilled"];
  }
  
  // Returns 1 if the observer has 250 Nebula
  public function getNebulaIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaIntermediate"];
  }
  
  // Returns 1 if the observer has 500 Nebula
  public function getNebulaExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaExperienced"];
  }
  
  // Returns 1 if the observer has 1000 Nebula
  public function getNebulaAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 Nebula
  public function getNebulaSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaSenior from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaSenior"];
  }
  
  // Returns 1 if the observer has 5000 Nebula
  public function getNebulaExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaExpert from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaExpert"];
  }
  
  // Returns 1 if the observer has one Nebula Drawing
  public function getNebulaDrawingsNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaDrawingsNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaDrawingsNewbie"];
  }
  
  // Returns 1 if the observer has 10 NebulaDrawings
  public function getNebulaDrawingsRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaDrawingsRookie from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaDrawingsRookie"];
  }
  
  // Returns 1 if the observer has 25 NebulaDrawings
  public function getNebulaDrawingsBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaDrawingsBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaDrawingsBeginner"];
  }
  
  // Returns 1 if the observer has 50 NebulaDrawings
  public function getNebulaDrawingsTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaDrawingsTalented from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaDrawingsTalented"];
  }
  
  // Returns 1 if the observer has 100 NebulaDrawings
  public function getNebulaDrawingsSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaDrawingsSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaDrawingsSkilled"];
  }
  
  // Returns 1 if the observer has 250 NebulaDrawings
  public function getNebulaDrawingsIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaDrawingsIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaDrawingsIntermediate"];
  }
  
  // Returns 1 if the observer has 500 NebulaDrawings
  public function getNebulaDrawingsExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaDrawingsExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaDrawingsExperienced"];
  }
  
  // Returns 1 if the observer has 1000 NebulaDrawings
  public function getNebulaDrawingsAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaDrawingsAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaDrawingsAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 NebulaDrawings
  public function getNebulaDrawingsSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaDrawingsSenior from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaDrawingsSenior"];
  }
  
  // Returns 1 if the observer has 5000 NebulaDrawings
  public function getNebulaDrawingsExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select NebulaDrawingsExpert from accomplishments where observer = \"". $observerId . "\";")[0]["NebulaDrawingsExpert"];
  }

  // Returns 1 if the observer has one Objects
  public function getObjectsNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsNewbie"];
  }
  
  // Returns 1 if the observer has 10 Objects
  public function getObjectsRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsRookie from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsRookie"];
  }
  
  // Returns 1 if the observer has 25 Objects
  public function getObjectsBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsBeginner"];
  }
  
  // Returns 1 if the observer has 50 Objects
  public function getObjectsTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsTalented from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsTalented"];
  }
  
  // Returns 1 if the observer has 100 Objects
  public function getObjectsSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsSkilled"];
  }
  
  // Returns 1 if the observer has 250 Objects
  public function getObjectsIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsIntermediate"];
  }
  
  // Returns 1 if the observer has 500 Objects
  public function getObjectsExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsExperienced"];
  }
  
  // Returns 1 if the observer has 1000 Objects
  public function getObjectsAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 Objects
  public function getObjectsSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsSenior from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsSenior"];
  }
  
  // Returns 1 if the observer has 5000 Objects
  public function getObjectsExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsExpert from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsExpert"];
  }
  
  // Returns 1 if the observer has one Objects Drawing
  public function getObjectsDrawingsNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsDrawingsNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsDrawingsNewbie"];
  }
  
  // Returns 1 if the observer has 10 ObjectsDrawings
  public function getObjectsDrawingsRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsDrawingsRookie from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsDrawingsRookie"];
  }
  
  // Returns 1 if the observer has 25 ObjectsDrawings
  public function getObjectsDrawingsBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsDrawingsBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsDrawingsBeginner"];
  }
  
  // Returns 1 if the observer has 50 ObjectsDrawings
  public function getObjectsDrawingsTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsDrawingsTalented from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsDrawingsTalented"];
  }
  
  // Returns 1 if the observer has 100 ObjectsDrawings
  public function getObjectsDrawingsSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsDrawingsSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsDrawingsSkilled"];
  }
  
  // Returns 1 if the observer has 250 ObjectsDrawings
  public function getObjectsDrawingsIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsDrawingsIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsDrawingsIntermediate"];
  }
  
  // Returns 1 if the observer has 500 ObjectsDrawings
  public function getObjectsDrawingsExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsDrawingsExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsDrawingsExperienced"];
  }
  
  // Returns 1 if the observer has 1000 ObjectsDrawings
  public function getObjectsDrawingsAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsDrawingsAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsDrawingsAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 ObjectsDrawings
  public function getObjectsDrawingsSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select ObjectsDrawingsSenior from accomplishments where observer = \"". $observerId . "\";")[0]["ObjectsDrawingsSenior"];
  }
  
  // Returns 1 if the observer has 5000 ObjectsDrawings
  public function getObjectsDrawingsExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select objectsDrawingsExpert from accomplishments where observer = \"". $observerId . "\";")[0]["objectsDrawingsExpert"];
  }

  // Returns 1 if the observer has one Objects Drawing
  public function getCometObservationsNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometObservationsNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["CometObservationsNewbie"];
  }
  
  // Returns 1 if the observer has 10 CometObservations
  public function getCometObservationsRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometObservationsRookie from accomplishments where observer = \"". $observerId . "\";")[0]["CometObservationsRookie"];
  }
  
  // Returns 1 if the observer has 25 CometObservations
  public function getCometObservationsBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometObservationsBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["CometObservationsBeginner"];
  }
  
  // Returns 1 if the observer has 50 CometObservations
  public function getCometObservationsTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometObservationsTalented from accomplishments where observer = \"". $observerId . "\";")[0]["CometObservationsTalented"];
  }
  
  // Returns 1 if the observer has 100 CometObservations
  public function getCometObservationsSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometObservationsSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["CometObservationsSkilled"];
  }
  
  // Returns 1 if the observer has 250 CometObservations
  public function getCometObservationsIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometObservationsIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["CometObservationsIntermediate"];
  }
  
  // Returns 1 if the observer has 500 CometObservations
  public function getCometObservationsExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometObservationsExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["CometObservationsExperienced"];
  }
  
  // Returns 1 if the observer has 1000 CometObservations
  public function getCometObservationsAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometObservationsAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["CometObservationsAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 CometObservations
  public function getCometObservationsSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometObservationsSenior from accomplishments where observer = \"". $observerId . "\";")[0]["CometObservationsSenior"];
  }
  
  // Returns 1 if the observer has 5000 CometObservations
  public function getCometObservationsExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometObservationsExpert from accomplishments where observer = \"". $observerId . "\";")[0]["CometObservationsExpert"];
  }

  // Returns 1 if the observer has one Objects Drawing
  public function getCometsObservedNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometsObservedNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["CometsObservedNewbie"];
  }
  
  // Returns 1 if the observer has 10 CometsObserved
  public function getCometsObservedRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometsObservedRookie from accomplishments where observer = \"". $observerId . "\";")[0]["CometsObservedRookie"];
  }
  
  // Returns 1 if the observer has 25 CometsObserved
  public function getCometsObservedBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometsObservedBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["CometsObservedBeginner"];
  }
  
  // Returns 1 if the observer has 50 CometsObserved
  public function getCometsObservedTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometsObservedTalented from accomplishments where observer = \"". $observerId . "\";")[0]["CometsObservedTalented"];
  }
  
  // Returns 1 if the observer has 100 CometsObserved
  public function getCometsObservedSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometsObservedSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["CometsObservedSkilled"];
  }
  
  // Returns 1 if the observer has 250 CometsObserved
  public function getCometsObservedIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometsObservedIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["CometsObservedIntermediate"];
  }
  
  // Returns 1 if the observer has 500 CometsObserved
  public function getCometsObservedExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometsObservedExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["CometsObservedExperienced"];
  }
  
  // Returns 1 if the observer has 1000 CometsObserved
  public function getCometsObservedAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometsObservedAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["CometsObservedAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 CometsObserved
  public function getCometsObservedSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometsObservedSenior from accomplishments where observer = \"". $observerId . "\";")[0]["CometsObservedSenior"];
  }
  
  // Returns 1 if the observer has 5000 CometsObserved
  public function getCometsObservedExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometsObservedExpert from accomplishments where observer = \"". $observerId . "\";")[0]["CometsObservedExpert"];
  }
  
  // Returns 1 if the observer has one Objects Drawing
  public function getCometDrawingsNewbie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometDrawingsNewbie from accomplishments where observer = \"". $observerId . "\";")[0]["CometDrawingsNewbie"];
  }
  
  // Returns 1 if the observer has 10 CometDrawings
  public function getCometDrawingsRookie($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometDrawingsRookie from accomplishments where observer = \"". $observerId . "\";")[0]["CometDrawingsRookie"];
  }
  
  // Returns 1 if the observer has 25 CometDrawings
  public function getCometDrawingsBeginner($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometDrawingsBeginner from accomplishments where observer = \"". $observerId . "\";")[0]["CometDrawingsBeginner"];
  }
  
  // Returns 1 if the observer has 50 CometDrawings
  public function getCometDrawingsTalented($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometDrawingsTalented from accomplishments where observer = \"". $observerId . "\";")[0]["CometDrawingsTalented"];
  }
  
  // Returns 1 if the observer has 100 CometDrawings
  public function getCometDrawingsSkilled($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometDrawingsSkilled from accomplishments where observer = \"". $observerId . "\";")[0]["CometDrawingsSkilled"];
  }
  
  // Returns 1 if the observer has 250 CometDrawings
  public function getCometDrawingsIntermediate($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometDrawingsIntermediate from accomplishments where observer = \"". $observerId . "\";")[0]["CometDrawingsIntermediate"];
  }
  
  // Returns 1 if the observer has 500 CometDrawings
  public function getCometDrawingsExperienced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometDrawingsExperienced from accomplishments where observer = \"". $observerId . "\";")[0]["CometDrawingsExperienced"];
  }
  
  // Returns 1 if the observer has 1000 CometDrawings
  public function getCometDrawingsAdvanced($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometDrawingsAdvanced from accomplishments where observer = \"". $observerId . "\";")[0]["CometDrawingsAdvanced"];
  }
  
  // Returns 1 if the observer has 2500 CometDrawings
  public function getCometDrawingsSenior($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometDrawingsSenior from accomplishments where observer = \"". $observerId . "\";")[0]["CometDrawingsSenior"];
  }
  
  // Returns 1 if the observer has 5000 CometDrawings
  public function getCometDrawingsExpert($observerId) {
  	global $objDatabase;
  	return $objDatabase->selectRecordsetArray("select CometDrawingsExpert from accomplishments where observer = \"". $observerId . "\";")[0]["CometDrawingsExpert"];
  }
  
  // Recalculates all deepsky accomplishments (for example after adding, removing or changing an observation)
  public function recalculateDeepsky($observerId) {
  	$this->recalculateMessiers($observerId);
  	$this->recalculateCaldwells($observerId);
  	$this->recalculateHerschels($observerId);
  	$this->recalculateHerschelIIs($observerId);
  	$this->recalculateDrawings($observerId);
  	$this->recalculateOpenClusters($observerId);
  	$this->recalculateOpenClusterDrawings($observerId);
  	$this->recalculateGlobularClusters($observerId);
  	$this->recalculateGlobularClusterDrawings($observerId);
  	$this->recalculatePlanetaryNebulae($observerId);
  	$this->recalculatePlanetaryNebulaDrawings($observerId);
  	$this->recalculateGalaxies($observerId);
  	$this->recalculateGalaxyDrawings($observerId);
  	$this->recalculateNebulae($observerId);
  	$this->recalculateNebulaDrawings($observerId);
  	$this->recalculateObjects($observerId);
  	$this->recalculateObjectDrawings($observerId);
  }
  
  // Recalculates all comet accomplishments (for example after adding, removing or changing an observation)
  public function recalculateComets($observerId) {
  	$this->recalculateCometObservations($observerId);
  	$this->recalculateCometsObserved($observerId);
  	$this->recalculateCometDrawings($observerId);
  }
  
  public function recalculateMessiers($observerId) {
  	// MESSIER
  	$messiers = $this->calculateMessier($observerId);
  	$oldMessierBronze = $this->getMessierBronze($observerId);
  	$newMessierBronze = $messiers[0];
  	$sql = "UPDATE accomplishments SET messierBronze = " . $newMessierBronze . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	
  	if ($oldMessierBronze == 0 && $newMessierBronze == 1) {
  		// TODO : Send mail
  		print "Congratulations! You observed all messier objects and receive a bronze messier certificat! <tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  	 
  	$oldMessierSilver = $this->getMessierSilver($observerId);
  	$newMessierSilver = $messiers[1];
  	$sql = "UPDATE accomplishments SET messierSilver = " . $newMessierSilver . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	
  	if ($oldMessierSilver == 0 && $newMessierSilver == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldMessierGold = $this->getMessierGold($observerId);
  	$newMessierGold = $messiers[2];
  	$sql = "UPDATE accomplishments SET messierGold = " . $newMessierGold . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldMessierGold == 0 && $newMessierGold == 1) {
  		// TODO : Send mail
  	}
  	 
  	// MESSIER DRAWINGS
  	$messierDrawings = $this->calculateMessierDrawings($observerId);
  	$oldMessierDrawingsBronze = $this->getMessierDrawingsBronze($observerId);
  	$newMessierDrawingsBronze = $messierDrawings[0];
  	$sql = "UPDATE accomplishments SET messierDrawingsBronze = " . $newMessierDrawingsBronze . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	
  	if ($oldMessierDrawingsBronze == 0 && $newMessierDrawingsBronze == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldMessierDrawingsSilver = $this->getMessierDrawingsSilver($observerId);
  	$newMessierDrawingsSilver = $messierDrawings[1];
  	$sql = "UPDATE accomplishments SET messierDrawingsSilver = " . $newMessierDrawingsSilver . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	
  	if ($oldMessierDrawingsSilver == 0 && $newMessierDrawingsSilver == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldMessierDrawingsGold = $this->getMessierDrawingsGold($observerId);
  	$newMessierDrawingsGold = $messierDrawings[2];
  	$sql = "UPDATE accomplishments SET messierDrawingsGold = " . $newMessierDrawingsGold . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldMessierDrawingsGold == 0 && $newMessierDrawingsGold == 1) {
  		// TODO : Send mail
  	}
  }
  
  public function recalculateCaldwells($observerId) {
  	// CALDWELL
  	$caldwells = $this->calculateCaldwell($observerId);
  	$oldCaldwellBronze = $this->getCaldwellBronze($observerId);
  	$newCaldwellBronze = $caldwells[0];
  	$sql = "UPDATE accomplishments SET CaldwellBronze = " . $newCaldwellBronze . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	
  	if ($oldCaldwellBronze == 0 && $newCaldwellBronze == 1) {
  		// TODO : Send mail
  		print "Congratulations! You observed all Caldwell objects and receive a bronze Caldwell certificat! <tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  	 
  	$oldCaldwellSilver = $this->getCaldwellSilver($observerId);
  	$newCaldwellSilver = $caldwells[1];
  	$sql = "UPDATE accomplishments SET CaldwellSilver = " . $newCaldwellSilver . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	
  	if ($oldCaldwellSilver == 0 && $newCaldwellSilver == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldCaldwellGold = $this->getCaldwellGold($observerId);
  	$newCaldwellGold = $caldwells[2];
  	$sql = "UPDATE accomplishments SET CaldwellGold = " . $newCaldwellGold . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldCaldwellGold == 0 && $newCaldwellGold == 1) {
  		// TODO : Send mail
  	}
  	 
  	// CALDWELL DRAWINGS
  	$caldwellDrawings = $this->calculateCaldwellDrawings($observerId);
  	$oldCaldwellDrawingsBronze = $this->getCaldwellDrawingsBronze($observerId);
  	$newCaldwellDrawingsBronze = $caldwellDrawings[0];
  	$sql = "UPDATE accomplishments SET CaldwellDrawingsBronze = " . $newCaldwellDrawingsBronze . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	
  	if ($oldCaldwellDrawingsBronze == 0 && $newCaldwellDrawingsBronze == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldCaldwellDrawingsSilver = $this->getCaldwellDrawingsSilver($observerId);
  	$newCaldwellDrawingsSilver = $caldwellDrawings[1];
  	$sql = "UPDATE accomplishments SET CaldwellDrawingsSilver = " . $newCaldwellDrawingsSilver . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	
  	if ($oldCaldwellDrawingsSilver == 0 && $newCaldwellDrawingsSilver == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldCaldwellDrawingsGold = $this->getCaldwellDrawingsGold($observerId);
  	$newCaldwellDrawingsGold = $caldwellDrawings[2];
  	$sql = "UPDATE accomplishments SET CaldwellDrawingsGold = " . $newCaldwellDrawingsGold . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldCaldwellDrawingsGold == 0 && $newCaldwellDrawingsGold == 1) {
  		// TODO : Send mail
  	}
  	 
  }

  public function recalculateHerschels($observerId) {
  	// Herschel
  	$herschels = $this->calculateHerschel($observerId);
  	$oldHerschelBronze = $this->getHerschelBronze($observerId);
  	$newHerschelBronze = $herschels[0];
  	$sql = "UPDATE accomplishments SET HerschelBronze = " . $newHerschelBronze . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldHerschelBronze == 0 && $newHerschelBronze == 1) {
  		// TODO : Send mail
  		print "Congratulations! You observed 25 Herschel objects and receive a bronze Herschel certificat! <tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldHerschelSilver = $this->getHerschelSilver($observerId);
  	$newHerschelSilver = $herschels[1];
  	$sql = "UPDATE accomplishments SET HerschelSilver = " . $newHerschelSilver . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldHerschelSilver == 0 && $newHerschelSilver == 1) {
  		// TODO : Send mail
  	}
  
  	$oldHerschelGold = $this->getHerschelGold($observerId);
  	$newHerschelGold = $herschels[2];
  	$sql = "UPDATE accomplishments SET HerschelGold = " . $newHerschelGold . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelGold == 0 && $newHerschelGold == 1) {
  		// TODO : Send mail
  	}
  
    $oldHerschelDiamond = $this->getHerschelDiamond($observerId);
  	$newHerschelDiamond = $herschels[3];
  	$sql = "UPDATE accomplishments SET HerschelDiamond = " . $newHerschelDiamond . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelDiamond == 0 && $newHerschelDiamond == 1) {
  		// TODO : Send mail
  	}
  
    $oldHerschelPlatina = $this->getHerschelPlatina($observerId);
  	$newHerschelPlatina = $herschels[4];
  	$sql = "UPDATE accomplishments SET HerschelPlatina = " . $newHerschelPlatina . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelPlatina == 0 && $newHerschelPlatina == 1) {
  		// TODO : Send mail
  	}
  
  	// Herschel DRAWINGS
  	$herschelDrawings = $this->calculateHerschelDrawings($observerId);
  	$oldHerschelDrawingsBronze = $this->getHerschelDrawingsBronze($observerId);
  	$newHerschelDrawingsBronze = $herschelDrawings[0];
  	$sql = "UPDATE accomplishments SET HerschelDrawingsBronze = " . $newHerschelDrawingsBronze . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldHerschelDrawingsBronze == 0 && $newHerschelDrawingsBronze == 1) {
  		// TODO : Send mail
  	}
  
  	$oldHerschelDrawingsSilver = $this->getHerschelDrawingsSilver($observerId);
  	$newHerschelDrawingsSilver = $herschelDrawings[1];
  	$sql = "UPDATE accomplishments SET HerschelDrawingsSilver = " . $newHerschelDrawingsSilver . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldHerschelDrawingsSilver == 0 && $newHerschelDrawingsSilver == 1) {
  		// TODO : Send mail
  	}
  
  	$oldHerschelDrawingsGold = $this->getHerschelDrawingsGold($observerId);
  	$newHerschelDrawingsGold = $herschelDrawings[2];
  	$sql = "UPDATE accomplishments SET HerschelDrawingsGold = " . $newHerschelDrawingsGold . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelDrawingsGold == 0 && $newHerschelDrawingsGold == 1) {
  		// TODO : Send mail
  	}

  	$oldHerschelDrawingsDiamond = $this->getHerschelDrawingsDiamond($observerId);
  	$newHerschelDrawingsDiamond = $herschelDrawings[3];
  	$sql = "UPDATE accomplishments SET HerschelDrawingsDiamond = " . $newHerschelDrawingsDiamond . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	
  	if ($oldHerschelDrawingsDiamond == 0 && $newHerschelDrawingsDiamond == 1) {
  		// TODO : Send mail
  	}
  	
  	$oldHerschelDrawingsPlatina = $this->getHerschelDrawingsPlatina($observerId);
  	$newHerschelDrawingsPlatina = $herschelDrawings[4];
  	$sql = "UPDATE accomplishments SET HerschelDrawingsPlatina = " . $newHerschelDrawingsPlatina . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	
  	if ($oldHerschelDrawingsPlatina == 0 && $newHerschelDrawingsPlatina == 1) {
  		// TODO : Send mail
  	}
  }

  public function recalculateHerschelIIs($observerId) {
  	// HerschelII
  	$HerschelIIs = $this->calculateHerschelII($observerId);
  	$oldHerschelIIBronze = $this->getHerschelIIBronze($observerId);
  	$newHerschelIIBronze = $HerschelIIs[0];
  	$sql = "UPDATE accomplishments SET HerschelIIBronze = " . $newHerschelIIBronze . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelIIBronze == 0 && $newHerschelIIBronze == 1) {
  		// TODO : Send mail
  		print "Congratulations! You observed 25 HerschelII objects and receive a bronze HerschelII certificat! <tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldHerschelIISilver = $this->getHerschelIISilver($observerId);
  	$newHerschelIISilver = $HerschelIIs[1];
  	$sql = "UPDATE accomplishments SET HerschelIISilver = " . $newHerschelIISilver . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelIISilver == 0 && $newHerschelIISilver == 1) {
  		// TODO : Send mail
  	}
  
  	$oldHerschelIIGold = $this->getHerschelIIGold($observerId);
  	$newHerschelIIGold = $HerschelIIs[2];
  	$sql = "UPDATE accomplishments SET HerschelIIGold = " . $newHerschelIIGold . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelIIGold == 0 && $newHerschelIIGold == 1) {
  		// TODO : Send mail
  	}
  
  	$oldHerschelIIDiamond = $this->getHerschelIIDiamond($observerId);
  	$newHerschelIIDiamond = $HerschelIIs[3];
  	$sql = "UPDATE accomplishments SET HerschelIIDiamond = " . $newHerschelIIDiamond . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelIIDiamond == 0 && $newHerschelIIDiamond == 1) {
  		// TODO : Send mail
  	}
  
  	$oldHerschelIIPlatina = $this->getHerschelIIPlatina($observerId);
  	$newHerschelIIPlatina = $HerschelIIs[4];
  	$sql = "UPDATE accomplishments SET HerschelIIPlatina = " . $newHerschelIIPlatina . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelIIPlatina == 0 && $newHerschelIIPlatina == 1) {
  		// TODO : Send mail
  	}
  
  	// HerschelII DRAWINGS
  	$HerschelIIDrawings = $this->calculateHerschelIIDrawings($observerId);
  	$oldHerschelIIDrawingsBronze = $this->getHerschelIIDrawingsBronze($observerId);
  	$newHerschelIIDrawingsBronze = $HerschelIIDrawings[0];
  	$sql = "UPDATE accomplishments SET HerschelIIDrawingsBronze = " . $newHerschelIIDrawingsBronze . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelIIDrawingsBronze == 0 && $newHerschelIIDrawingsBronze == 1) {
  		// TODO : Send mail
  	}
  
  	$oldHerschelIIDrawingsSilver = $this->getHerschelIIDrawingsSilver($observerId);
  	$newHerschelIIDrawingsSilver = $HerschelIIDrawings[1];
  	$sql = "UPDATE accomplishments SET HerschelIIDrawingsSilver = " . $newHerschelIIDrawingsSilver . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelIIDrawingsSilver == 0 && $newHerschelIIDrawingsSilver == 1) {
  		// TODO : Send mail
  	}
  
  	$oldHerschelIIDrawingsGold = $this->getHerschelIIDrawingsGold($observerId);
  	$newHerschelIIDrawingsGold = $HerschelIIDrawings[2];
  	$sql = "UPDATE accomplishments SET HerschelIIDrawingsGold = " . $newHerschelIIDrawingsGold . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldHerschelIIDrawingsGold == 0 && $newHerschelIIDrawingsGold == 1) {
  		// TODO : Send mail
  	}
  
  	$oldHerschelIIDrawingsDiamond = $this->getHerschelIIDrawingsDiamond($observerId);
  	$newHerschelIIDrawingsDiamond = $HerschelIIDrawings[3];
  	$sql = "UPDATE accomplishments SET HerschelIIDrawingsDiamond = " . $newHerschelIIDrawingsDiamond . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldHerschelIIDrawingsDiamond == 0 && $newHerschelIIDrawingsDiamond == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldHerschelIIDrawingsPlatina = $this->getHerschelIIDrawingsPlatina($observerId);
  	$newHerschelIIDrawingsPlatina = $HerschelIIDrawings[4];
  	$sql = "UPDATE accomplishments SET HerschelIIDrawingsPlatina = " . $newHerschelIIDrawingsPlatina . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldHerschelIIDrawingsPlatina == 0 && $newHerschelIIDrawingsPlatina == 1) {
  		// TODO : Send mail
  	}
  }

  public function recalculateDrawings($observerId) {
  	// drawings
  	$drawings = $this->calculateDrawings($observerId);
  	$oldDrawingsNewbie = $this->getDrawingsNewbie($observerId);
  	$newDrawingsNewbie = $drawings[0];
  	$sql = "UPDATE accomplishments SET drawingsNewbie = " . $newDrawingsNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldDrawingsNewbie == 0 && $newDrawingsNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldDrawingsRookie = $this->getDrawingsRookie($observerId);
  	$newDrawingsRookie = $drawings[1];
  	$sql = "UPDATE accomplishments SET drawingsRookie = " . $newDrawingsRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldDrawingsRookie == 0 && $newDrawingsRookie == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldDrawingsBeginner = $this->getDrawingsBeginner($observerId);
  	$newDrawingsBeginner = $drawings[2];
  	$sql = "UPDATE accomplishments SET drawingsBeginner = " . $newDrawingsBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldDrawingsBeginner == 0 && $newDrawingsBeginner == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldDrawingsTalented = $this->getDrawingsTalented($observerId);
  	$newDrawingsTalented = $drawings[3];
  	$sql = "UPDATE accomplishments SET drawingsTalented = " . $newDrawingsTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldDrawingsTalented == 0 && $newDrawingsTalented == 1) {
  		// TODO : Send mail
  	}
  
 		$oldDrawingsSkilled = $this->getDrawingsSkilled($observerId);
  	$newDrawingsSkilled = $drawings[4];
  	$sql = "UPDATE accomplishments SET drawingsSkilled = " . $newDrawingsSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldDrawingsSkilled == 0 && $newDrawingsSkilled == 1) {
  		// TODO : Send mail
  	}

  	$oldDrawingsIntermediate = $this->getDrawingsIntermediate($observerId);
  	$newDrawingsIntermediate = $drawings[5];
  	$sql = "UPDATE accomplishments SET drawingsIntermediate = " . $newDrawingsIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	
  	if ($oldDrawingsIntermediate == 0 && $newDrawingsIntermediate == 1) {
  		// TODO : Send mail
  	}

  	$oldDrawingsExperienced = $this->getDrawingsExperienced($observerId);
  	$newDrawingsExperienced = $drawings[6];
  	$sql = "UPDATE accomplishments SET drawingsExperienced = " . $newDrawingsExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldDrawingsExperienced == 0 && $newDrawingsExperienced == 1) {
  		// TODO : Send mail
  	}
  	 
    $oldDrawingsAdvanced = $this->getDrawingsAdvanced($observerId);
  	$newDrawingsAdvanced = $drawings[7];
  	$sql = "UPDATE accomplishments SET drawingsAdvanced = " . $newDrawingsAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldDrawingsAdvanced == 0 && $newDrawingsAdvanced == 1) {
  		// TODO : Send mail
  	}
  	
    $oldDrawingsSenior = $this->getDrawingsSenior($observerId);
  	$newDrawingsSenior = $drawings[8];
  	$sql = "UPDATE accomplishments SET drawingsSenior = " . $newDrawingsSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldDrawingsSenior == 0 && $newDrawingsSenior == 1) {
  		// TODO : Send mail
  	}
  	
    $oldDrawingsExpert = $this->getDrawingsExpert($observerId);
  	$newDrawingsExpert = $drawings[9];
  	$sql = "UPDATE accomplishments SET drawingsExpert = " . $newDrawingsExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldDrawingsExpert == 0 && $newDrawingsExpert == 1) {
  		// TODO : Send mail
  	}
  }

  public function recalculateOpenClusters($observerId) {
  	// OpenClusters
  	$OpenClusters = $this->calculateOpenClusters($observerId);
  	$oldOpenClustersNewbie = $this->getOpenClustersNewbie($observerId);
  	$newOpenClustersNewbie = $OpenClusters[0];
  	$sql = "UPDATE accomplishments SET OpenClusterNewbie = " . $newOpenClustersNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClustersNewbie == 0 && $newOpenClustersNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldOpenClustersRookie = $this->getOpenClustersRookie($observerId);
  	$newOpenClustersRookie = $OpenClusters[1];
  	$sql = "UPDATE accomplishments SET OpenClusterRookie = " . $newOpenClustersRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClustersRookie == 0 && $newOpenClustersRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClustersBeginner = $this->getOpenClustersBeginner($observerId);
  	$newOpenClustersBeginner = $OpenClusters[2];
  	$sql = "UPDATE accomplishments SET OpenClusterBeginner = " . $newOpenClustersBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClustersBeginner == 0 && $newOpenClustersBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClustersTalented = $this->getOpenClustersTalented($observerId);
  	$newOpenClustersTalented = $OpenClusters[3];
  	$sql = "UPDATE accomplishments SET OpenClusterTalented = " . $newOpenClustersTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClustersTalented == 0 && $newOpenClustersTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClustersSkilled = $this->getOpenClustersSkilled($observerId);
  	$newOpenClustersSkilled = $OpenClusters[4];
  	$sql = "UPDATE accomplishments SET OpenClusterSkilled = " . $newOpenClustersSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClustersSkilled == 0 && $newOpenClustersSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClustersIntermediate = $this->getOpenClustersIntermediate($observerId);
  	$newOpenClustersIntermediate = $OpenClusters[5];
  	$sql = "UPDATE accomplishments SET OpenClusterIntermediate = " . $newOpenClustersIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldOpenClustersIntermediate == 0 && $newOpenClustersIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClustersExperienced = $this->getOpenClustersExperienced($observerId);
  	$newOpenClustersExperienced = $OpenClusters[6];
  	$sql = "UPDATE accomplishments SET OpenClusterExperienced = " . $newOpenClustersExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClustersExperienced == 0 && $newOpenClustersExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClustersAdvanced = $this->getOpenClustersAdvanced($observerId);
  	$newOpenClustersAdvanced = $OpenClusters[7];
  	$sql = "UPDATE accomplishments SET OpenClusterAdvanced = " . $newOpenClustersAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClustersAdvanced == 0 && $newOpenClustersAdvanced == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldOpenClustersSenior = $this->getOpenClustersSenior($observerId);
  	$newOpenClustersSenior = $OpenClusters[8];
  	$sql = "UPDATE accomplishments SET OpenClusterSenior = " . $newOpenClustersSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClustersSenior == 0 && $newOpenClustersSenior == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldOpenClustersExpert = $this->getOpenClustersExpert($observerId);
  	$newOpenClustersExpert = $OpenClusters[9];
  	$sql = "UPDATE accomplishments SET OpenClusterExpert = " . $newOpenClustersExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClustersExpert == 0 && $newOpenClustersExpert == 1) {
  		// TODO : Send mail
  	}
  }
  
  public function recalculateOpenClusterDrawings($observerId) {
  	// OpenClusterDrawings
  	$OpenClusterDrawings = $this->calculateOpenClusterDrawings($observerId);
  	$oldOpenClusterDrawingsNewbie = $this->getOpenClusterDrawingsNewbie($observerId);
  	$newOpenClusterDrawingsNewbie = $OpenClusterDrawings[0];
  	$sql = "UPDATE accomplishments SET OpenClusterDrawingsNewbie = " . $newOpenClusterDrawingsNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClusterDrawingsNewbie == 0 && $newOpenClusterDrawingsNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldOpenClusterDrawingsRookie = $this->getOpenClusterDrawingsRookie($observerId);
  	$newOpenClusterDrawingsRookie = $OpenClusterDrawings[1];
  	$sql = "UPDATE accomplishments SET OpenClusterDrawingsRookie = " . $newOpenClusterDrawingsRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClusterDrawingsRookie == 0 && $newOpenClusterDrawingsRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClusterDrawingsBeginner = $this->getOpenClusterDrawingsBeginner($observerId);
  	$newOpenClusterDrawingsBeginner = $OpenClusterDrawings[2];
  	$sql = "UPDATE accomplishments SET OpenClusterDrawingsBeginner = " . $newOpenClusterDrawingsBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClusterDrawingsBeginner == 0 && $newOpenClusterDrawingsBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClusterDrawingsTalented = $this->getOpenClusterDrawingsTalented($observerId);
  	$newOpenClusterDrawingsTalented = $OpenClusterDrawings[3];
  	$sql = "UPDATE accomplishments SET OpenClusterDrawingsTalented = " . $newOpenClusterDrawingsTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClusterDrawingsTalented == 0 && $newOpenClusterDrawingsTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClusterDrawingsSkilled = $this->getOpenClusterDrawingsSkilled($observerId);
  	$newOpenClusterDrawingsSkilled = $OpenClusterDrawings[4];
  	$sql = "UPDATE accomplishments SET OpenClusterDrawingsSkilled = " . $newOpenClusterDrawingsSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClusterDrawingsSkilled == 0 && $newOpenClusterDrawingsSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClusterDrawingsIntermediate = $this->getOpenClusterDrawingsIntermediate($observerId);
  	$newOpenClusterDrawingsIntermediate = $OpenClusterDrawings[5];
  	$sql = "UPDATE accomplishments SET OpenClusterDrawingsIntermediate = " . $newOpenClusterDrawingsIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	 
  	if ($oldOpenClusterDrawingsIntermediate == 0 && $newOpenClusterDrawingsIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClusterDrawingsExperienced = $this->getOpenClusterDrawingsExperienced($observerId);
  	$newOpenClusterDrawingsExperienced = $OpenClusterDrawings[6];
  	$sql = "UPDATE accomplishments SET OpenClusterDrawingsExperienced = " . $newOpenClusterDrawingsExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClusterDrawingsExperienced == 0 && $newOpenClusterDrawingsExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldOpenClusterDrawingsAdvanced = $this->getOpenClusterDrawingsAdvanced($observerId);
  	$newOpenClusterDrawingsAdvanced = $OpenClusterDrawings[7];
  	$sql = "UPDATE accomplishments SET OpenClusterDrawingsAdvanced = " . $newOpenClusterDrawingsAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClusterDrawingsAdvanced == 0 && $newOpenClusterDrawingsAdvanced == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldOpenClusterDrawingsSenior = $this->getOpenClusterDrawingsSenior($observerId);
  	$newOpenClusterDrawingsSenior = $OpenClusterDrawings[8];
  	$sql = "UPDATE accomplishments SET OpenClusterDrawingsSenior = " . $newOpenClusterDrawingsSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClusterDrawingsSenior == 0 && $newOpenClusterDrawingsSenior == 1) {
  		// TODO : Send mail
  	}
  	 
  	$oldOpenClusterDrawingsExpert = $this->getOpenClusterDrawingsExpert($observerId);
  	$newOpenClusterDrawingsExpert = $OpenClusterDrawings[9];
  	$sql = "UPDATE accomplishments SET OpenClusterDrawingsExpert = " . $newOpenClusterDrawingsExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldOpenClusterDrawingsExpert == 0 && $newOpenClusterDrawingsExpert == 1) {
  		// TODO : Send mail
  	}
  }

  public function recalculateGlobularClusters($observerId) {
  	// GlobularClusters
  	$GlobularClusters = $this->calculateGlobularClusters($observerId);
  	$oldGlobularClustersNewbie = $this->getGlobularClustersNewbie($observerId);
  	$newGlobularClustersNewbie = $GlobularClusters[0];
  	$sql = "UPDATE accomplishments SET GlobularClusterNewbie = " . $newGlobularClustersNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClustersNewbie == 0 && $newGlobularClustersNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldGlobularClustersRookie = $this->getGlobularClustersRookie($observerId);
  	$newGlobularClustersRookie = $GlobularClusters[1];
  	$sql = "UPDATE accomplishments SET GlobularClusterRookie = " . $newGlobularClustersRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClustersRookie == 0 && $newGlobularClustersRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClustersBeginner = $this->getGlobularClustersBeginner($observerId);
  	$newGlobularClustersBeginner = $GlobularClusters[2];
  	$sql = "UPDATE accomplishments SET GlobularClusterBeginner = " . $newGlobularClustersBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClustersBeginner == 0 && $newGlobularClustersBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClustersTalented = $this->getGlobularClustersTalented($observerId);
  	$newGlobularClustersTalented = $GlobularClusters[3];
  	$sql = "UPDATE accomplishments SET GlobularClusterTalented = " . $newGlobularClustersTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClustersTalented == 0 && $newGlobularClustersTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClustersSkilled = $this->getGlobularClustersSkilled($observerId);
  	$newGlobularClustersSkilled = $GlobularClusters[4];
  	$sql = "UPDATE accomplishments SET GlobularClusterSkilled = " . $newGlobularClustersSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClustersSkilled == 0 && $newGlobularClustersSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClustersIntermediate = $this->getGlobularClustersIntermediate($observerId);
  	$newGlobularClustersIntermediate = $GlobularClusters[5];
  	$sql = "UPDATE accomplishments SET GlobularClusterIntermediate = " . $newGlobularClustersIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClustersIntermediate == 0 && $newGlobularClustersIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClustersExperienced = $this->getGlobularClustersExperienced($observerId);
  	$newGlobularClustersExperienced = $GlobularClusters[6];
  	$sql = "UPDATE accomplishments SET GlobularClusterExperienced = " . $newGlobularClustersExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClustersExperienced == 0 && $newGlobularClustersExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClustersAdvanced = $this->getGlobularClustersAdvanced($observerId);
  	$newGlobularClustersAdvanced = $GlobularClusters[7];
  	$sql = "UPDATE accomplishments SET GlobularClusterAdvanced = " . $newGlobularClustersAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClustersAdvanced == 0 && $newGlobularClustersAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClustersSenior = $this->getGlobularClustersSenior($observerId);
  	$newGlobularClustersSenior = $GlobularClusters[8];
  	$sql = "UPDATE accomplishments SET GlobularClusterSenior = " . $newGlobularClustersSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClustersSenior == 0 && $newGlobularClustersSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClustersExpert = $this->getGlobularClustersExpert($observerId);
  	$newGlobularClustersExpert = $GlobularClusters[9];
  	$sql = "UPDATE accomplishments SET GlobularClusterExpert = " . $newGlobularClustersExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClustersExpert == 0 && $newGlobularClustersExpert == 1) {
  		// TODO : Send mail
  	}
  }
  
  public function recalculateGlobularClusterDrawings($observerId) {
  	// GlobularClusterDrawings
  	$GlobularClusterDrawings = $this->calculateGlobularClusterDrawings($observerId);
  	$oldGlobularClusterDrawingsNewbie = $this->getGlobularClusterDrawingsNewbie($observerId);
  	$newGlobularClusterDrawingsNewbie = $GlobularClusterDrawings[0];
  	$sql = "UPDATE accomplishments SET GlobularClusterDrawingsNewbie = " . $newGlobularClusterDrawingsNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClusterDrawingsNewbie == 0 && $newGlobularClusterDrawingsNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldGlobularClusterDrawingsRookie = $this->getGlobularClusterDrawingsRookie($observerId);
  	$newGlobularClusterDrawingsRookie = $GlobularClusterDrawings[1];
  	$sql = "UPDATE accomplishments SET GlobularClusterDrawingsRookie = " . $newGlobularClusterDrawingsRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClusterDrawingsRookie == 0 && $newGlobularClusterDrawingsRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClusterDrawingsBeginner = $this->getGlobularClusterDrawingsBeginner($observerId);
  	$newGlobularClusterDrawingsBeginner = $GlobularClusterDrawings[2];
  	$sql = "UPDATE accomplishments SET GlobularClusterDrawingsBeginner = " . $newGlobularClusterDrawingsBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClusterDrawingsBeginner == 0 && $newGlobularClusterDrawingsBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClusterDrawingsTalented = $this->getGlobularClusterDrawingsTalented($observerId);
  	$newGlobularClusterDrawingsTalented = $GlobularClusterDrawings[3];
  	$sql = "UPDATE accomplishments SET GlobularClusterDrawingsTalented = " . $newGlobularClusterDrawingsTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClusterDrawingsTalented == 0 && $newGlobularClusterDrawingsTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClusterDrawingsSkilled = $this->getGlobularClusterDrawingsSkilled($observerId);
  	$newGlobularClusterDrawingsSkilled = $GlobularClusterDrawings[4];
  	$sql = "UPDATE accomplishments SET GlobularClusterDrawingsSkilled = " . $newGlobularClusterDrawingsSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClusterDrawingsSkilled == 0 && $newGlobularClusterDrawingsSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClusterDrawingsIntermediate = $this->getGlobularClusterDrawingsIntermediate($observerId);
  	$newGlobularClusterDrawingsIntermediate = $GlobularClusterDrawings[5];
  	$sql = "UPDATE accomplishments SET GlobularClusterDrawingsIntermediate = " . $newGlobularClusterDrawingsIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClusterDrawingsIntermediate == 0 && $newGlobularClusterDrawingsIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClusterDrawingsExperienced = $this->getGlobularClusterDrawingsExperienced($observerId);
  	$newGlobularClusterDrawingsExperienced = $GlobularClusterDrawings[6];
  	$sql = "UPDATE accomplishments SET GlobularClusterDrawingsExperienced = " . $newGlobularClusterDrawingsExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClusterDrawingsExperienced == 0 && $newGlobularClusterDrawingsExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClusterDrawingsAdvanced = $this->getGlobularClusterDrawingsAdvanced($observerId);
  	$newGlobularClusterDrawingsAdvanced = $GlobularClusterDrawings[7];
  	$sql = "UPDATE accomplishments SET GlobularClusterDrawingsAdvanced = " . $newGlobularClusterDrawingsAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClusterDrawingsAdvanced == 0 && $newGlobularClusterDrawingsAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClusterDrawingsSenior = $this->getGlobularClusterDrawingsSenior($observerId);
  	$newGlobularClusterDrawingsSenior = $GlobularClusterDrawings[8];
  	$sql = "UPDATE accomplishments SET GlobularClusterDrawingsSenior = " . $newGlobularClusterDrawingsSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClusterDrawingsSenior == 0 && $newGlobularClusterDrawingsSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGlobularClusterDrawingsExpert = $this->getGlobularClusterDrawingsExpert($observerId);
  	$newGlobularClusterDrawingsExpert = $GlobularClusterDrawings[9];
  	$sql = "UPDATE accomplishments SET GlobularClusterDrawingsExpert = " . $newGlobularClusterDrawingsExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGlobularClusterDrawingsExpert == 0 && $newGlobularClusterDrawingsExpert == 1) {
  		// TODO : Send mail
  	}
  }

  public function recalculatePlanetaryNebulae($observerId) {
  	// PlanetaryNebulae
  	$PlanetaryNebulae = $this->calculatePlanetaryNebulae($observerId);
  	$oldPlanetaryNebulaeNewbie = $this->getPlanetaryNebulaNewbie($observerId);
  	$newPlanetaryNebulaeNewbie = $PlanetaryNebulae[0];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaNewbie = " . $newPlanetaryNebulaeNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaeNewbie == 0 && $newPlanetaryNebulaeNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldPlanetaryNebulaeRookie = $this->getPlanetaryNebulaRookie($observerId);
  	$newPlanetaryNebulaeRookie = $PlanetaryNebulae[1];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaRookie = " . $newPlanetaryNebulaeRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaeRookie == 0 && $newPlanetaryNebulaeRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaeBeginner = $this->getPlanetaryNebulaBeginner($observerId);
  	$newPlanetaryNebulaeBeginner = $PlanetaryNebulae[2];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaBeginner = " . $newPlanetaryNebulaeBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaeBeginner == 0 && $newPlanetaryNebulaeBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaeTalented = $this->getPlanetaryNebulaTalented($observerId);
  	$newPlanetaryNebulaeTalented = $PlanetaryNebulae[3];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaTalented = " . $newPlanetaryNebulaeTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaeTalented == 0 && $newPlanetaryNebulaeTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaeSkilled = $this->getPlanetaryNebulaSkilled($observerId);
  	$newPlanetaryNebulaeSkilled = $PlanetaryNebulae[4];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaSkilled = " . $newPlanetaryNebulaeSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaeSkilled == 0 && $newPlanetaryNebulaeSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaeIntermediate = $this->getPlanetaryNebulaIntermediate($observerId);
  	$newPlanetaryNebulaeIntermediate = $PlanetaryNebulae[5];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaIntermediate = " . $newPlanetaryNebulaeIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaeIntermediate == 0 && $newPlanetaryNebulaeIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaeExperienced = $this->getPlanetaryNebulaExperienced($observerId);
  	$newPlanetaryNebulaeExperienced = $PlanetaryNebulae[6];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaExperienced = " . $newPlanetaryNebulaeExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaeExperienced == 0 && $newPlanetaryNebulaeExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaeAdvanced = $this->getPlanetaryNebulaAdvanced($observerId);
  	$newPlanetaryNebulaeAdvanced = $PlanetaryNebulae[7];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaAdvanced = " . $newPlanetaryNebulaeAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaeAdvanced == 0 && $newPlanetaryNebulaeAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaeSenior = $this->getPlanetaryNebulaSenior($observerId);
  	$newPlanetaryNebulaeSenior = $PlanetaryNebulae[8];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaSenior = " . $newPlanetaryNebulaeSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaeSenior == 0 && $newPlanetaryNebulaeSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaeExpert = $this->getPlanetaryNebulaExpert($observerId);
  	$newPlanetaryNebulaeExpert = $PlanetaryNebulae[9];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaExpert = " . $newPlanetaryNebulaeExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaeExpert == 0 && $newPlanetaryNebulaeExpert == 1) {
  		// TODO : Send mail
  	}
  }

  public function recalculatePlanetaryNebulaDrawings($observerId) {
  	// PlanetaryNebulaDrawings
  	$PlanetaryNebulaDrawings = $this->calculatePlanetaryNebulaDrawings($observerId);
  	$oldPlanetaryNebulaDrawingsNewbie = $this->getPlanetaryNebulaDrawingsNewbie($observerId);
  	$newPlanetaryNebulaDrawingsNewbie = $PlanetaryNebulaDrawings[0];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaDrawingsNewbie = " . $newPlanetaryNebulaDrawingsNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaDrawingsNewbie == 0 && $newPlanetaryNebulaDrawingsNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldPlanetaryNebulaDrawingsRookie = $this->getPlanetaryNebulaDrawingsRookie($observerId);
  	$newPlanetaryNebulaDrawingsRookie = $PlanetaryNebulaDrawings[1];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaDrawingsRookie = " . $newPlanetaryNebulaDrawingsRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaDrawingsRookie == 0 && $newPlanetaryNebulaDrawingsRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaDrawingsBeginner = $this->getPlanetaryNebulaDrawingsBeginner($observerId);
  	$newPlanetaryNebulaDrawingsBeginner = $PlanetaryNebulaDrawings[2];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaDrawingsBeginner = " . $newPlanetaryNebulaDrawingsBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaDrawingsBeginner == 0 && $newPlanetaryNebulaDrawingsBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaDrawingsTalented = $this->getPlanetaryNebulaDrawingsTalented($observerId);
  	$newPlanetaryNebulaDrawingsTalented = $PlanetaryNebulaDrawings[3];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaDrawingsTalented = " . $newPlanetaryNebulaDrawingsTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaDrawingsTalented == 0 && $newPlanetaryNebulaDrawingsTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaDrawingsSkilled = $this->getPlanetaryNebulaDrawingsSkilled($observerId);
  	$newPlanetaryNebulaDrawingsSkilled = $PlanetaryNebulaDrawings[4];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaDrawingsSkilled = " . $newPlanetaryNebulaDrawingsSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaDrawingsSkilled == 0 && $newPlanetaryNebulaDrawingsSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaDrawingsIntermediate = $this->getPlanetaryNebulaDrawingsIntermediate($observerId);
  	$newPlanetaryNebulaDrawingsIntermediate = $PlanetaryNebulaDrawings[5];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaDrawingsIntermediate = " . $newPlanetaryNebulaDrawingsIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaDrawingsIntermediate == 0 && $newPlanetaryNebulaDrawingsIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaDrawingsExperienced = $this->getPlanetaryNebulaDrawingsExperienced($observerId);
  	$newPlanetaryNebulaDrawingsExperienced = $PlanetaryNebulaDrawings[6];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaDrawingsExperienced = " . $newPlanetaryNebulaDrawingsExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaDrawingsExperienced == 0 && $newPlanetaryNebulaDrawingsExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaDrawingsAdvanced = $this->getPlanetaryNebulaDrawingsAdvanced($observerId);
  	$newPlanetaryNebulaDrawingsAdvanced = $PlanetaryNebulaDrawings[7];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaDrawingsAdvanced = " . $newPlanetaryNebulaDrawingsAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaDrawingsAdvanced == 0 && $newPlanetaryNebulaDrawingsAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaDrawingsSenior = $this->getPlanetaryNebulaDrawingsSenior($observerId);
  	$newPlanetaryNebulaDrawingsSenior = $PlanetaryNebulaDrawings[8];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaDrawingsSenior = " . $newPlanetaryNebulaDrawingsSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaDrawingsSenior == 0 && $newPlanetaryNebulaDrawingsSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldPlanetaryNebulaDrawingsExpert = $this->getPlanetaryNebulaDrawingsExpert($observerId);
  	$newPlanetaryNebulaDrawingsExpert = $PlanetaryNebulaDrawings[9];
  	$sql = "UPDATE accomplishments SET PlanetaryNebulaDrawingsExpert = " . $newPlanetaryNebulaDrawingsExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldPlanetaryNebulaDrawingsExpert == 0 && $newPlanetaryNebulaDrawingsExpert == 1) {
  		// TODO : Send mail
  	}
  }

  public function recalculateGalaxies($observerId) {
  	// Galaxies
  	$Galaxies = $this->calculateGalaxies($observerId);
  	$oldGalaxiesNewbie = $this->getGalaxyNewbie($observerId);
  	$newGalaxiesNewbie = $Galaxies[0];
  	$sql = "UPDATE accomplishments SET GalaxyNewbie = " . $newGalaxiesNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxiesNewbie == 0 && $newGalaxiesNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldGalaxiesRookie = $this->getGalaxyRookie($observerId);
  	$newGalaxiesRookie = $Galaxies[1];
  	$sql = "UPDATE accomplishments SET GalaxyRookie = " . $newGalaxiesRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxiesRookie == 0 && $newGalaxiesRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxiesBeginner = $this->getGalaxyBeginner($observerId);
  	$newGalaxiesBeginner = $Galaxies[2];
  	$sql = "UPDATE accomplishments SET GalaxyBeginner = " . $newGalaxiesBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxiesBeginner == 0 && $newGalaxiesBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxiesTalented = $this->getGalaxyTalented($observerId);
  	$newGalaxiesTalented = $Galaxies[3];
  	$sql = "UPDATE accomplishments SET GalaxyTalented = " . $newGalaxiesTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxiesTalented == 0 && $newGalaxiesTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxiesSkilled = $this->getGalaxySkilled($observerId);
  	$newGalaxiesSkilled = $Galaxies[4];
  	$sql = "UPDATE accomplishments SET GalaxySkilled = " . $newGalaxiesSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxiesSkilled == 0 && $newGalaxiesSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxiesIntermediate = $this->getGalaxyIntermediate($observerId);
  	$newGalaxiesIntermediate = $Galaxies[5];
  	$sql = "UPDATE accomplishments SET GalaxyIntermediate = " . $newGalaxiesIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxiesIntermediate == 0 && $newGalaxiesIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxiesExperienced = $this->getGalaxyExperienced($observerId);
  	$newGalaxiesExperienced = $Galaxies[6];
  	$sql = "UPDATE accomplishments SET GalaxyExperienced = " . $newGalaxiesExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxiesExperienced == 0 && $newGalaxiesExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxiesAdvanced = $this->getGalaxyAdvanced($observerId);
  	$newGalaxiesAdvanced = $Galaxies[7];
  	$sql = "UPDATE accomplishments SET GalaxyAdvanced = " . $newGalaxiesAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxiesAdvanced == 0 && $newGalaxiesAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxiesSenior = $this->getGalaxySenior($observerId);
  	$newGalaxiesSenior = $Galaxies[8];
  	$sql = "UPDATE accomplishments SET GalaxySenior = " . $newGalaxiesSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxiesSenior == 0 && $newGalaxiesSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxiesExpert = $this->getGalaxyExpert($observerId);
  	$newGalaxiesExpert = $Galaxies[9];
  	$sql = "UPDATE accomplishments SET GalaxyExpert = " . $newGalaxiesExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxiesExpert == 0 && $newGalaxiesExpert == 1) {
  		// TODO : Send mail
  	}
  }
  
  public function recalculateGalaxyDrawings($observerId) {
  	// GalaxyDrawings
  	$GalaxyDrawings = $this->calculateGalaxyDrawings($observerId);
  	$oldGalaxyDrawingsNewbie = $this->getGalaxyDrawingsNewbie($observerId);
  	$newGalaxyDrawingsNewbie = $GalaxyDrawings[0];
  	$sql = "UPDATE accomplishments SET GalaxyDrawingsNewbie = " . $newGalaxyDrawingsNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxyDrawingsNewbie == 0 && $newGalaxyDrawingsNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldGalaxyDrawingsRookie = $this->getGalaxyDrawingsRookie($observerId);
  	$newGalaxyDrawingsRookie = $GalaxyDrawings[1];
  	$sql = "UPDATE accomplishments SET GalaxyDrawingsRookie = " . $newGalaxyDrawingsRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxyDrawingsRookie == 0 && $newGalaxyDrawingsRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxyDrawingsBeginner = $this->getGalaxyDrawingsBeginner($observerId);
  	$newGalaxyDrawingsBeginner = $GalaxyDrawings[2];
  	$sql = "UPDATE accomplishments SET GalaxyDrawingsBeginner = " . $newGalaxyDrawingsBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxyDrawingsBeginner == 0 && $newGalaxyDrawingsBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxyDrawingsTalented = $this->getGalaxyDrawingsTalented($observerId);
  	$newGalaxyDrawingsTalented = $GalaxyDrawings[3];
  	$sql = "UPDATE accomplishments SET GalaxyDrawingsTalented = " . $newGalaxyDrawingsTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxyDrawingsTalented == 0 && $newGalaxyDrawingsTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxyDrawingsSkilled = $this->getGalaxyDrawingsSkilled($observerId);
  	$newGalaxyDrawingsSkilled = $GalaxyDrawings[4];
  	$sql = "UPDATE accomplishments SET GalaxyDrawingsSkilled = " . $newGalaxyDrawingsSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxyDrawingsSkilled == 0 && $newGalaxyDrawingsSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxyDrawingsIntermediate = $this->getGalaxyDrawingsIntermediate($observerId);
  	$newGalaxyDrawingsIntermediate = $GalaxyDrawings[5];
  	$sql = "UPDATE accomplishments SET GalaxyDrawingsIntermediate = " . $newGalaxyDrawingsIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxyDrawingsIntermediate == 0 && $newGalaxyDrawingsIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxyDrawingsExperienced = $this->getGalaxyDrawingsExperienced($observerId);
  	$newGalaxyDrawingsExperienced = $GalaxyDrawings[6];
  	$sql = "UPDATE accomplishments SET GalaxyDrawingsExperienced = " . $newGalaxyDrawingsExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxyDrawingsExperienced == 0 && $newGalaxyDrawingsExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxyDrawingsAdvanced = $this->getGalaxyDrawingsAdvanced($observerId);
  	$newGalaxyDrawingsAdvanced = $GalaxyDrawings[7];
  	$sql = "UPDATE accomplishments SET GalaxyDrawingsAdvanced = " . $newGalaxyDrawingsAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxyDrawingsAdvanced == 0 && $newGalaxyDrawingsAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxyDrawingsSenior = $this->getGalaxyDrawingsSenior($observerId);
  	$newGalaxyDrawingsSenior = $GalaxyDrawings[8];
  	$sql = "UPDATE accomplishments SET GalaxyDrawingsSenior = " . $newGalaxyDrawingsSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxyDrawingsSenior == 0 && $newGalaxyDrawingsSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldGalaxyDrawingsExpert = $this->getGalaxyDrawingsExpert($observerId);
  	$newGalaxyDrawingsExpert = $GalaxyDrawings[9];
  	$sql = "UPDATE accomplishments SET GalaxyDrawingsExpert = " . $newGalaxyDrawingsExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldGalaxyDrawingsExpert == 0 && $newGalaxyDrawingsExpert == 1) {
  		// TODO : Send mail
  	}
  }

  public function recalculateNebulae($observerId) {
  	// Nebula
  	$Nebula = $this->calculateNebulae($observerId);
  	$oldNebulaNewbie = $this->getNebulaNewbie($observerId);
  	$newNebulaNewbie = $Nebula[0];
  	$sql = "UPDATE accomplishments SET NebulaNewbie = " . $newNebulaNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaNewbie == 0 && $newNebulaNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldNebulaRookie = $this->getNebulaRookie($observerId);
  	$newNebulaRookie = $Nebula[1];
  	$sql = "UPDATE accomplishments SET NebulaRookie = " . $newNebulaRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaRookie == 0 && $newNebulaRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaBeginner = $this->getNebulaBeginner($observerId);
  	$newNebulaBeginner = $Nebula[2];
  	$sql = "UPDATE accomplishments SET NebulaBeginner = " . $newNebulaBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaBeginner == 0 && $newNebulaBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaTalented = $this->getNebulaTalented($observerId);
  	$newNebulaTalented = $Nebula[3];
  	$sql = "UPDATE accomplishments SET NebulaTalented = " . $newNebulaTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaTalented == 0 && $newNebulaTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaSkilled = $this->getNebulaSkilled($observerId);
  	$newNebulaSkilled = $Nebula[4];
  	$sql = "UPDATE accomplishments SET NebulaSkilled = " . $newNebulaSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaSkilled == 0 && $newNebulaSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaIntermediate = $this->getNebulaIntermediate($observerId);
  	$newNebulaIntermediate = $Nebula[5];
  	$sql = "UPDATE accomplishments SET NebulaIntermediate = " . $newNebulaIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaIntermediate == 0 && $newNebulaIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaExperienced = $this->getNebulaExperienced($observerId);
  	$newNebulaExperienced = $Nebula[6];
  	$sql = "UPDATE accomplishments SET NebulaExperienced = " . $newNebulaExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaExperienced == 0 && $newNebulaExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaAdvanced = $this->getNebulaAdvanced($observerId);
  	$newNebulaAdvanced = $Nebula[7];
  	$sql = "UPDATE accomplishments SET NebulaAdvanced = " . $newNebulaAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaAdvanced == 0 && $newNebulaAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaSenior = $this->getNebulaSenior($observerId);
  	$newNebulaSenior = $Nebula[8];
  	$sql = "UPDATE accomplishments SET NebulaSenior = " . $newNebulaSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaSenior == 0 && $newNebulaSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaExpert = $this->getNebulaExpert($observerId);
  	$newNebulaExpert = $Nebula[9];
  	$sql = "UPDATE accomplishments SET NebulaExpert = " . $newNebulaExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaExpert == 0 && $newNebulaExpert == 1) {
  		// TODO : Send mail
  	}
  }
  
  public function recalculateNebulaDrawings($observerId) {
  	// NebulaDrawings
  	$NebulaDrawings = $this->calculateNebulaDrawings($observerId);
  	$oldNebulaDrawingsNewbie = $this->getNebulaDrawingsNewbie($observerId);
  	$newNebulaDrawingsNewbie = $NebulaDrawings[0];
  	$sql = "UPDATE accomplishments SET NebulaDrawingsNewbie = " . $newNebulaDrawingsNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaDrawingsNewbie == 0 && $newNebulaDrawingsNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldNebulaDrawingsRookie = $this->getNebulaDrawingsRookie($observerId);
  	$newNebulaDrawingsRookie = $NebulaDrawings[1];
  	$sql = "UPDATE accomplishments SET NebulaDrawingsRookie = " . $newNebulaDrawingsRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaDrawingsRookie == 0 && $newNebulaDrawingsRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaDrawingsBeginner = $this->getNebulaDrawingsBeginner($observerId);
  	$newNebulaDrawingsBeginner = $NebulaDrawings[2];
  	$sql = "UPDATE accomplishments SET NebulaDrawingsBeginner = " . $newNebulaDrawingsBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaDrawingsBeginner == 0 && $newNebulaDrawingsBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaDrawingsTalented = $this->getNebulaDrawingsTalented($observerId);
  	$newNebulaDrawingsTalented = $NebulaDrawings[3];
  	$sql = "UPDATE accomplishments SET NebulaDrawingsTalented = " . $newNebulaDrawingsTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaDrawingsTalented == 0 && $newNebulaDrawingsTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaDrawingsSkilled = $this->getNebulaDrawingsSkilled($observerId);
  	$newNebulaDrawingsSkilled = $NebulaDrawings[4];
  	$sql = "UPDATE accomplishments SET NebulaDrawingsSkilled = " . $newNebulaDrawingsSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaDrawingsSkilled == 0 && $newNebulaDrawingsSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaDrawingsIntermediate = $this->getNebulaDrawingsIntermediate($observerId);
  	$newNebulaDrawingsIntermediate = $NebulaDrawings[5];
  	$sql = "UPDATE accomplishments SET NebulaDrawingsIntermediate = " . $newNebulaDrawingsIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaDrawingsIntermediate == 0 && $newNebulaDrawingsIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaDrawingsExperienced = $this->getNebulaDrawingsExperienced($observerId);
  	$newNebulaDrawingsExperienced = $NebulaDrawings[6];
  	$sql = "UPDATE accomplishments SET NebulaDrawingsExperienced = " . $newNebulaDrawingsExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaDrawingsExperienced == 0 && $newNebulaDrawingsExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaDrawingsAdvanced = $this->getNebulaDrawingsAdvanced($observerId);
  	$newNebulaDrawingsAdvanced = $NebulaDrawings[7];
  	$sql = "UPDATE accomplishments SET NebulaDrawingsAdvanced = " . $newNebulaDrawingsAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaDrawingsAdvanced == 0 && $newNebulaDrawingsAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaDrawingsSenior = $this->getNebulaDrawingsSenior($observerId);
  	$newNebulaDrawingsSenior = $NebulaDrawings[8];
  	$sql = "UPDATE accomplishments SET NebulaDrawingsSenior = " . $newNebulaDrawingsSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaDrawingsSenior == 0 && $newNebulaDrawingsSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldNebulaDrawingsExpert = $this->getNebulaDrawingsExpert($observerId);
  	$newNebulaDrawingsExpert = $NebulaDrawings[9];
  	$sql = "UPDATE accomplishments SET NebulaDrawingsExpert = " . $newNebulaDrawingsExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldNebulaDrawingsExpert == 0 && $newNebulaDrawingsExpert == 1) {
  		// TODO : Send mail
  	}
  }
  
  public function recalculateObjects($observerId) {
  	// Different Objects
  	$Objects = $this->calculateDifferentObjects($observerId);
  	$oldObjectsNewbie = $this->getObjectsNewbie($observerId);
  	$newObjectsNewbie = $Objects[0];
  	$sql = "UPDATE accomplishments SET objectsNewbie = " . $newObjectsNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsNewbie == 0 && $newObjectsNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldObjectsRookie = $this->getObjectsRookie($observerId);
  	$newObjectsRookie = $Objects[1];
  	$sql = "UPDATE accomplishments SET objectsRookie = " . $newObjectsRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsRookie == 0 && $newObjectsRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsBeginner = $this->getObjectsBeginner($observerId);
  	$newObjectsBeginner = $Objects[2];
  	$sql = "UPDATE accomplishments SET objectsBeginner = " . $newObjectsBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsBeginner == 0 && $newObjectsBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsTalented = $this->getObjectsTalented($observerId);
  	$newObjectsTalented = $Objects[3];
  	$sql = "UPDATE accomplishments SET objectsTalented = " . $newObjectsTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsTalented == 0 && $newObjectsTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsSkilled = $this->getObjectsSkilled($observerId);
  	$newObjectsSkilled = $Objects[4];
  	$sql = "UPDATE accomplishments SET objectsSkilled = " . $newObjectsSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsSkilled == 0 && $newObjectsSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsIntermediate = $this->getObjectsIntermediate($observerId);
  	$newObjectsIntermediate = $Objects[5];
  	$sql = "UPDATE accomplishments SET objectsIntermediate = " . $newObjectsIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsIntermediate == 0 && $newObjectsIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsExperienced = $this->getObjectsExperienced($observerId);
  	$newObjectsExperienced = $Objects[6];
  	$sql = "UPDATE accomplishments SET objectsExperienced = " . $newObjectsExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsExperienced == 0 && $newObjectsExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsAdvanced = $this->getObjectsAdvanced($observerId);
  	$newObjectsAdvanced = $Objects[7];
  	$sql = "UPDATE accomplishments SET objectsAdvanced = " . $newObjectsAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsAdvanced == 0 && $newObjectsAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsSenior = $this->getObjectsSenior($observerId);
  	$newObjectsSenior = $Objects[8];
  	$sql = "UPDATE accomplishments SET objectsSenior = " . $newObjectsSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsSenior == 0 && $newObjectsSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsExpert = $this->getObjectsExpert($observerId);
  	$newObjectsExpert = $Objects[9];
  	$sql = "UPDATE accomplishments SET objectsExpert = " . $newObjectsExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsExpert == 0 && $newObjectsExpert == 1) {
  		// TODO : Send mail
  	}
  }

  public function recalculateObjectDrawings($observerId) {
  	// ObjectsDrawings
  	$ObjectsDrawings = $this->calculateDifferentObjectDrawings($observerId);
  	$oldObjectsDrawingsNewbie = $this->getObjectsDrawingsNewbie($observerId);
  	$newObjectsDrawingsNewbie = $ObjectsDrawings[0];
  	$sql = "UPDATE accomplishments SET ObjectsDrawingsNewbie = " . $newObjectsDrawingsNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  	if ($oldObjectsDrawingsNewbie == 0 && $newObjectsDrawingsNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldObjectsDrawingsRookie = $this->getObjectsDrawingsRookie($observerId);
  	$newObjectsDrawingsRookie = $ObjectsDrawings[1];
  	$sql = "UPDATE accomplishments SET ObjectsDrawingsRookie = " . $newObjectsDrawingsRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsDrawingsRookie == 0 && $newObjectsDrawingsRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsDrawingsBeginner = $this->getObjectsDrawingsBeginner($observerId);
  	$newObjectsDrawingsBeginner = $ObjectsDrawings[2];
  	$sql = "UPDATE accomplishments SET ObjectsDrawingsBeginner = " . $newObjectsDrawingsBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsDrawingsBeginner == 0 && $newObjectsDrawingsBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsDrawingsTalented = $this->getObjectsDrawingsTalented($observerId);
  	$newObjectsDrawingsTalented = $ObjectsDrawings[3];
  	$sql = "UPDATE accomplishments SET ObjectsDrawingsTalented = " . $newObjectsDrawingsTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsDrawingsTalented == 0 && $newObjectsDrawingsTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsDrawingsSkilled = $this->getObjectsDrawingsSkilled($observerId);
  	$newObjectsDrawingsSkilled = $ObjectsDrawings[4];
  	$sql = "UPDATE accomplishments SET ObjectsDrawingsSkilled = " . $newObjectsDrawingsSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsDrawingsSkilled == 0 && $newObjectsDrawingsSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsDrawingsIntermediate = $this->getObjectsDrawingsIntermediate($observerId);
  	$newObjectsDrawingsIntermediate = $ObjectsDrawings[5];
  	$sql = "UPDATE accomplishments SET ObjectsDrawingsIntermediate = " . $newObjectsDrawingsIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsDrawingsIntermediate == 0 && $newObjectsDrawingsIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsDrawingsExperienced = $this->getObjectsDrawingsExperienced($observerId);
  	$newObjectsDrawingsExperienced = $ObjectsDrawings[6];
  	$sql = "UPDATE accomplishments SET ObjectsDrawingsExperienced = " . $newObjectsDrawingsExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsDrawingsExperienced == 0 && $newObjectsDrawingsExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsDrawingsAdvanced = $this->getObjectsDrawingsAdvanced($observerId);
  	$newObjectsDrawingsAdvanced = $ObjectsDrawings[7];
  	$sql = "UPDATE accomplishments SET ObjectsDrawingsAdvanced = " . $newObjectsDrawingsAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsDrawingsAdvanced == 0 && $newObjectsDrawingsAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsDrawingsSenior = $this->getObjectsDrawingsSenior($observerId);
  	$newObjectsDrawingsSenior = $ObjectsDrawings[8];
  	$sql = "UPDATE accomplishments SET objectsDrawingsSenior = " . $newObjectsDrawingsSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsDrawingsSenior == 0 && $newObjectsDrawingsSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldObjectsDrawingsExpert = $this->getObjectsDrawingsExpert($observerId);
  	$newObjectsDrawingsExpert = $ObjectsDrawings[9];
  	$sql = "UPDATE accomplishments SET objectsDrawingsExpert = " . $newObjectsDrawingsExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldObjectsDrawingsExpert == 0 && $newObjectsDrawingsExpert == 1) {
  		// TODO : Send mail
  	}
  }
    
  public function recalculateCometObservations($observerId) {
  	// Comet Observations
  	$CometObservations = $this->calculateCometObservations($observerId);
  	$oldCometObservationsNewbie = $this->getCometObservationsNewbie($observerId);
  	$newCometObservationsNewbie = $CometObservations[0];
  	$sql = "UPDATE accomplishments SET CometObservationsNewbie = " . $newCometObservationsNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometObservationsNewbie == 0 && $newCometObservationsNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldCometObservationsRookie = $this->getCometObservationsRookie($observerId);
  	$newCometObservationsRookie = $CometObservations[1];
  	$sql = "UPDATE accomplishments SET CometObservationsRookie = " . $newCometObservationsRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometObservationsRookie == 0 && $newCometObservationsRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometObservationsBeginner = $this->getCometObservationsBeginner($observerId);
  	$newCometObservationsBeginner = $CometObservations[2];
  	$sql = "UPDATE accomplishments SET CometObservationsBeginner = " . $newCometObservationsBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometObservationsBeginner == 0 && $newCometObservationsBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometObservationsTalented = $this->getCometObservationsTalented($observerId);
  	$newCometObservationsTalented = $CometObservations[3];
  	$sql = "UPDATE accomplishments SET CometObservationsTalented = " . $newCometObservationsTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometObservationsTalented == 0 && $newCometObservationsTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometObservationsSkilled = $this->getCometObservationsSkilled($observerId);
  	$newCometObservationsSkilled = $CometObservations[4];
  	$sql = "UPDATE accomplishments SET CometObservationsSkilled = " . $newCometObservationsSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometObservationsSkilled == 0 && $newCometObservationsSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometObservationsIntermediate = $this->getCometObservationsIntermediate($observerId);
  	$newCometObservationsIntermediate = $CometObservations[5];
  	$sql = "UPDATE accomplishments SET CometObservationsIntermediate = " . $newCometObservationsIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometObservationsIntermediate == 0 && $newCometObservationsIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometObservationsExperienced = $this->getCometObservationsExperienced($observerId);
  	$newCometObservationsExperienced = $CometObservations[6];
  	$sql = "UPDATE accomplishments SET CometObservationsExperienced = " . $newCometObservationsExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometObservationsExperienced == 0 && $newCometObservationsExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometObservationsAdvanced = $this->getCometObservationsAdvanced($observerId);
  	$newCometObservationsAdvanced = $CometObservations[7];
  	$sql = "UPDATE accomplishments SET CometObservationsAdvanced = " . $newCometObservationsAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometObservationsAdvanced == 0 && $newCometObservationsAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometObservationsSenior = $this->getCometObservationsSenior($observerId);
  	$newCometObservationsSenior = $CometObservations[8];
  	$sql = "UPDATE accomplishments SET CometObservationsSenior = " . $newCometObservationsSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometObservationsSenior == 0 && $newCometObservationsSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometObservationsExpert = $this->getCometObservationsExpert($observerId);
  	$newCometObservationsExpert = $CometObservations[9];
  	$sql = "UPDATE accomplishments SET CometObservationsExpert = " . $newCometObservationsExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometObservationsExpert == 0 && $newCometObservationsExpert == 1) {
  		// TODO : Send mail
  	}
  }

  public function recalculateCometsObserved($observerId) {
  	// Comet Observations
  	$CometsObserved = $this->calculateCometsObserved($observerId);
  	$oldCometsObservedNewbie = $this->getCometsObservedNewbie($observerId);
  	$newCometsObservedNewbie = $CometsObserved[0];
  	$sql = "UPDATE accomplishments SET CometsObservedNewbie = " . $newCometsObservedNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometsObservedNewbie == 0 && $newCometsObservedNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldCometsObservedRookie = $this->getCometsObservedRookie($observerId);
  	$newCometsObservedRookie = $CometsObserved[1];
  	$sql = "UPDATE accomplishments SET CometsObservedRookie = " . $newCometsObservedRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometsObservedRookie == 0 && $newCometsObservedRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometsObservedBeginner = $this->getCometsObservedBeginner($observerId);
  	$newCometsObservedBeginner = $CometsObserved[2];
  	$sql = "UPDATE accomplishments SET CometsObservedBeginner = " . $newCometsObservedBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometsObservedBeginner == 0 && $newCometsObservedBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometsObservedTalented = $this->getCometsObservedTalented($observerId);
  	$newCometsObservedTalented = $CometsObserved[3];
  	$sql = "UPDATE accomplishments SET CometsObservedTalented = " . $newCometsObservedTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometsObservedTalented == 0 && $newCometsObservedTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometsObservedSkilled = $this->getCometsObservedSkilled($observerId);
  	$newCometsObservedSkilled = $CometsObserved[4];
  	$sql = "UPDATE accomplishments SET CometsObservedSkilled = " . $newCometsObservedSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometsObservedSkilled == 0 && $newCometsObservedSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometsObservedIntermediate = $this->getCometsObservedIntermediate($observerId);
  	$newCometsObservedIntermediate = $CometsObserved[5];
  	$sql = "UPDATE accomplishments SET CometsObservedIntermediate = " . $newCometsObservedIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometsObservedIntermediate == 0 && $newCometsObservedIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometsObservedExperienced = $this->getCometsObservedExperienced($observerId);
  	$newCometsObservedExperienced = $CometsObserved[6];
  	$sql = "UPDATE accomplishments SET CometsObservedExperienced = " . $newCometsObservedExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometsObservedExperienced == 0 && $newCometsObservedExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometsObservedAdvanced = $this->getCometsObservedAdvanced($observerId);
  	$newCometsObservedAdvanced = $CometsObserved[7];
  	$sql = "UPDATE accomplishments SET CometsObservedAdvanced = " . $newCometsObservedAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometsObservedAdvanced == 0 && $newCometsObservedAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometsObservedSenior = $this->getCometsObservedSenior($observerId);
  	$newCometsObservedSenior = $CometsObserved[8];
  	$sql = "UPDATE accomplishments SET CometsObservedSenior = " . $newCometsObservedSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometsObservedSenior == 0 && $newCometsObservedSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometsObservedExpert = $this->getCometsObservedExpert($observerId);
  	$newCometsObservedExpert = $CometsObserved[9];
  	$sql = "UPDATE accomplishments SET CometsObservedExpert = " . $newCometsObservedExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometsObservedExpert == 0 && $newCometsObservedExpert == 1) {
  		// TODO : Send mail
  	}
  }
  
  public function recalculateCometDrawings($observerId) {
  	// Comet Observations
  	$CometDrawings = $this->calculateCometDrawings($observerId);
  	$oldCometDrawingsNewbie = $this->getCometDrawingsNewbie($observerId);
  	$newCometDrawingsNewbie = $CometDrawings[0];
  	$sql = "UPDATE accomplishments SET CometDrawingsNewbie = " . $newCometDrawingsNewbie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometDrawingsNewbie == 0 && $newCometDrawingsNewbie == 1) {
  		// TODO : Send mail
  		print "Congratulations! You made a drawing of 1 object and are now a drawing newbie!<tekening> Check out your accomplishments at http://www.deepskylog.org/index.php?indexAction=detail_observer3&user=\"" . $observerId . "\"" ;
  	}
  
  	$oldCometDrawingsRookie = $this->getCometDrawingsRookie($observerId);
  	$newCometDrawingsRookie = $CometDrawings[1];
  	$sql = "UPDATE accomplishments SET CometDrawingsRookie = " . $newCometDrawingsRookie . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometDrawingsRookie == 0 && $newCometDrawingsRookie == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometDrawingsBeginner = $this->getCometDrawingsBeginner($observerId);
  	$newCometDrawingsBeginner = $CometDrawings[2];
  	$sql = "UPDATE accomplishments SET CometDrawingsBeginner = " . $newCometDrawingsBeginner . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometDrawingsBeginner == 0 && $newCometDrawingsBeginner == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometDrawingsTalented = $this->getCometDrawingsTalented($observerId);
  	$newCometDrawingsTalented = $CometDrawings[3];
  	$sql = "UPDATE accomplishments SET CometDrawingsTalented = " . $newCometDrawingsTalented . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometDrawingsTalented == 0 && $newCometDrawingsTalented == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometDrawingsSkilled = $this->getCometDrawingsSkilled($observerId);
  	$newCometDrawingsSkilled = $CometDrawings[4];
  	$sql = "UPDATE accomplishments SET CometDrawingsSkilled = " . $newCometDrawingsSkilled . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometDrawingsSkilled == 0 && $newCometDrawingsSkilled == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometDrawingsIntermediate = $this->getCometDrawingsIntermediate($observerId);
  	$newCometDrawingsIntermediate = $CometDrawings[5];
  	$sql = "UPDATE accomplishments SET CometDrawingsIntermediate = " . $newCometDrawingsIntermediate . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometDrawingsIntermediate == 0 && $newCometDrawingsIntermediate == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometDrawingsExperienced = $this->getCometDrawingsExperienced($observerId);
  	$newCometDrawingsExperienced = $CometDrawings[6];
  	$sql = "UPDATE accomplishments SET CometDrawingsExperienced = " . $newCometDrawingsExperienced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometDrawingsExperienced == 0 && $newCometDrawingsExperienced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometDrawingsAdvanced = $this->getCometDrawingsAdvanced($observerId);
  	$newCometDrawingsAdvanced = $CometDrawings[7];
  	$sql = "UPDATE accomplishments SET CometDrawingsAdvanced = " . $newCometDrawingsAdvanced . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometDrawingsAdvanced == 0 && $newCometDrawingsAdvanced == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometDrawingsSenior = $this->getCometDrawingsSenior($observerId);
  	$newCometDrawingsSenior = $CometDrawings[8];
  	$sql = "UPDATE accomplishments SET CometDrawingsSenior = " . $newCometDrawingsSenior . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometDrawingsSenior == 0 && $newCometDrawingsSenior == 1) {
  		// TODO : Send mail
  	}
  
  	$oldCometDrawingsExpert = $this->getCometDrawingsExpert($observerId);
  	$newCometDrawingsExpert = $CometDrawings[9];
  	$sql = "UPDATE accomplishments SET CometDrawingsExpert = " . $newCometDrawingsExpert . " WHERE observer = \"". $observerId ."\";";
  	$run = mysql_query($sql) or die(mysql_error());
  
  	if ($oldCometDrawingsExpert == 0 && $newCometDrawingsExpert == 1) {
  		// TODO : Send mail
  	}
  }
}
?>
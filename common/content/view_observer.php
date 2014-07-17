<?php
// view_observer.php
// shows information of an observer
if ((! isset ( $inIndex )) || (! $inIndex))
	include "../../redirect.php";
elseif (! ($user = $objUtil->checkGetKey ( 'user' )))
	throw new Exception ( LangException015b );
else
	view_observer ();
function view_observer() {
	global $user, $modules, $deepsky, $comets, $baseURL, $instDir, $loggedUser, $objDatabase, $objAccomplishments, $objInstrument, $objPresentations, $objObservation, $objUtil, $objCometObservation, $objObserver, $objLocation;
	$name = $objObserver->getObserverProperty ( $user, 'name' );
	$firstname = $objObserver->getObserverProperty ( $user, 'firstname' );
	$location_id = $objObserver->getObserverProperty ( $user, 'stdlocation' );
	$location_name = $objLocation->getLocationPropertyFromId ( $location_id, 'name' );
	$instrumentname = $objInstrument->getInstrumentPropertyFromId ( $objObserver->getObserverProperty ( $user, 'stdtelescope' ), 'name' );
	$userDSobservation = $objObserver->getNumberOfDsObservations ( $user );
	$totalDSObservations = $objObservation->getNumberOfDsObservations ();
	$userDSYearObservations = $objObservation->getObservationsLastYear ( $user );
	$totalDSYearObservations = $objObservation->getObservationsLastYear ( '%' );
	$userDSObjects = $objObservation->getNumberOfObjects ( $user );
	$totalDSobjects = $objObservation->getNumberOfDifferentObservedDSObjects ();
	$userMobjects = $objObservation->getObservedCountFromCatalogOrList ( $user, "M" );
	$userCaldwellObjects = $objObservation->getObservedCountFromCatalogOrList ( $user, "Caldwell" );
	$userH400objects = $objObservation->getObservedCountFromCatalogOrList ( $user, "H400" );
	$userHIIobjects = $objObservation->getObservedCountFromCatalogOrList ( $user, "HII" );
	$userDSrank = $objObserver->getDsRank ( $user );
	if ($userDSrank === false)
		$userDSrank = "-";
	else
		$userDSrank ++;
	$userCometobservation = $objObserver->getNumberOfCometObservations ( $user );
	$totalCometObservations = $objCometObservation->getNumberOfObservations ();
	$userCometYearObservations = $objCometObservation->getObservationsThisYear ( $user );
	$totalCometYearObservations = $objCometObservation->getNumberOfObservationsThisYear ();
	$userCometObjects = $objCometObservation->getNumberOfObjects ( $user );
	$totalCometobjects = $objCometObservation->getNumberOfDifferentObjects ();
	$cometrank = $objObserver->getCometRank ( $user );
	if ($cometrank === false)
		$cometrank = "-";
	else
		$cometrank ++;
	
	for($i = 0; $i < count ( $modules ); $i ++) {
		if (strcmp ( $$modules [$i], $deepsky ) == 0) {
			$key = $i;
			$information [$i] [0] = $userDSobservation . " / " . $totalDSObservations . "&nbsp;(" . sprintf ( "%.2f", ($userDSobservation / $totalDSObservations) * 100 ) . "%)";
			$information [$i] [1] = $userDSYearObservations . " / " . $totalDSYearObservations . "&nbsp;(" . sprintf ( "%.2f", $userDSYearObservations / $totalDSYearObservations * 100 ) . "%)";
			$information [$i] [2] = $userDSObjects . " / " . $totalDSobjects . "&nbsp;(" . sprintf ( "%.2f", $userDSObjects / $totalDSobjects * 100 ) . "%)";
			$information [$i] [4] = $userDSrank;
		}
		if (strcmp ( $$modules [$i], $comets ) == 0) {
			$information [$i] [0] = $userCometobservation . " / " . $totalCometObservations . " (" . sprintf ( "%.2f", $userCometobservation / $totalCometObservations * 100 ) . "%)";
			$information [$i] [1] = $userCometYearObservations . " / " . $totalCometYearObservations . "&nbsp;(" . sprintf ( "%.2f", $userCometYearObservations / ($totalCometYearObservations ? $totalCometYearObservations : 1) * 100 ) . "%)";
			$information [$i] [2] = $userCometObjects . " / " . $totalCometobjects . " (" . sprintf ( "%.2f", $userCometObjects / $totalCometobjects * 100 ) . "%)";
			$information [$i] [4] = $cometrank;
		}
	}
	echo "<div>";
	$objPresentations->line ( array (
			"<h4>" . $firstname . ' ' . $name . "</h4>" 
	), "L", array (), 30 );
	echo "<hr />";
	// We make some tabs.
	echo "<ul id=\"tabs\" class=\"nav nav-tabs\" data-tabs=\"tabs\">
          <li class=\"active\"><a href=\"#info\" data-toggle=\"tab\">" . GraphInfo . "</a></li>
          <li><a href=\"#observationsPerYear\" data-toggle=\"tab\">" . GraphObservationsTitle . "</a></li>
          <li><a href=\"#objectTypes\" data-toggle=\"tab\">" . GraphObservationsType . "</a></li>
          <li><a href=\"#stars\" data-toggle=\"tab\">" . GraphAccomplishments . "</a></li>
        </ul>";
	
	echo "<div id=\"my-tab-content\" class=\"tab-content\">";
	echo "<div class=\"tab-pane active\" id=\"info\">";
	if (array_key_exists ( 'admin', $_SESSION ) && ($_SESSION ['admin'] == "yes")) 	// admin logged in
	{
		echo "<br />";
		echo "<form class=\"form-horizontal\" role=\"form\" action=\"" . $baseURL . "index.php\" >";
		echo "<input type=\"hidden\" name=\"indexAction\" value=\"change_emailNameFirstname_Password\" />";
		echo "<input type=\"hidden\" name=\"user\" value=\"" . $user . "\" />";
		echo "<div class=\"form-group\">";
		echo "<label class=\"col-sm-2 control-label\">" . LangChangeAccountField1 . "</label>";
		echo "<div class=\"col-sm-5\"><p class=\"form-control-static\">" . $objObserver->getObserverProperty ( $user, 'id' ) . "</p>";
		echo "</div></div>";
		echo "<div class=\"form-group\">
	         <label for=\"email\" class=\"col-sm-2 control-label\">" . LangChangeAccountField2 . "</label>
	         <div class=\"col-sm-5\">
	          <input type=\"email\" name=\"email\" class=\"form-control\" id=\"email\" value=\"" . $objObserver->getObserverProperty ( $user, 'email' ) . "\">
           </div>
	        </div>";
		echo "<div class=\"form-group\">
	         <label for=\"firstname\" class=\"col-sm-2 control-label\">" . LangChangeAccountField3 . "</label>
	         <div class=\"col-sm-5\">
	          <input type=\"text\" name=\"firstname\" class=\"form-control\" id=\"firstname\" value=\"" . $objObserver->getObserverProperty ( $user, 'firstname' ) . "\">
           </div>
	        </div>";
		echo "<div class=\"form-group\">
	         <label for=\"name\" class=\"col-sm-2 control-label\">" . LangChangeAccountField4 . "</label>
	         <div class=\"col-sm-5\">
	          <input type=\"text\" name=\"name\" class=\"form-control\" id=\"name\" value=\"" . $objObserver->getObserverProperty ( $user, 'name' ) . "\">
           </div>
	        </div>";
		echo "<div class=\"form-group\">
	         <label for=\"password\" class=\"col-sm-2 control-label\">" . LangChangeAccountField5 . "</label>
	         <div class=\"col-sm-3\">
	          <input type=\"text\" name=\"password\" class=\"form-control\" id=\"password\" value=\"\" />
           </div>
	         <div class=\"col-sm-2\">
	         	<input type=\"submit\" class=\"btn btn-primary\" name=\"change_password\" value=\"" . "Change password" . "\" />
	         </div>	         		
	        </div>";
		echo "<div class=\"form-group\">";
		echo "<label class=\"col-sm-2 control-label\">" . LangChangeAccountField7 . "</label>";
		echo "<div class=\"col-sm-5\"><p class=\"form-control-static\"><a href=\"" . $baseURL . "index.php?indexAction=detail_location&amp;location=" . urlencode ( $location_id ) . "\">" . $location_name . "</a></p>";
		echo "</div></div>";
		echo "<div class=\"form-group\">";
		echo "<label class=\"col-sm-2 control-label\">" . LangChangeAccountField8 . "</label>";
		echo "<div class=\"col-sm-5\"><p class=\"form-control-static\">" . ($instrumentname ? "<a href=\"" . $baseURL . "index.php?indexAction=detail_instrument&amp;instrument=" . urlencode ( $objObserver->getObserverProperty ( $user, 'stdtelescope' ) ) . "\">" . (($instrumentname == "Naked eye") ? InstrumentsNakedEye : $instrumentname) . "</a>" : "");
		echo "</p></div></div>";
		echo "</form>";
	} else {
		echo "<table class=\"table table-striped\">";
		echo " <tr>
		        <td>" . LangChangeAccountField3 . "</td>
		        <td>" . $objObserver->getObserverProperty ( $user, 'firstname' ) . "</td>
		       </tr>";
		
		echo " <tr>
		        <td>" . LangChangeAccountField4 . "</td>
		        <td>" . $objObserver->getObserverProperty ( $user, 'name' ) . "</td>
		       </tr>";
		echo " <tr>
				    <td>" . LangChangeAccountField7 . "</td>
	          <td><a href=\"" . $baseURL . "index.php?indexAction=detail_location&amp;location=" . urlencode ( $location_id ) . "\">" . $location_name . "</a> 
	          </td>
	         </tr>";
		echo " <tr>
	          <td>" . LangChangeAccountField8 . "</td>
 	          <td>" . ($instrumentname ? "<a href=\"" . $baseURL . "index.php?indexAction=detail_instrument&amp;instrument=" . urlencode ( $objObserver->getObserverProperty ( $user, 'stdtelescope' ) ) . "\">" . (($instrumentname == "Naked eye") ? InstrumentsNakedEye : $instrumentname) . "</a>" : "") . "</td>
 	         </tr>";
	}
	if ($objUtil->checkSessionKey ( 'admin' ) == "yes") {
		echo "<form class=\"form-horizontal\" role=\"form\" action=\"" . $baseURL . "index.php\" >";
		echo "<input type=\"hidden\" name=\"indexAction\" value=\"change_role\" />";
		echo "<input type=\"hidden\" name=\"user\" value=\"" . $user . "\" />";
		echo "<div class=\"form-group\">";
		$content = '';
		if ($user != "admin") {
			echo "<div class=\"form-group\">
	         <label for=\"role\" class=\"col-sm-2 control-label\">" . LangViewObserverRole . "</label>
	         <div class=\"col-sm-3\">
	         		<select name=\"role\" class=\"form-control\">
 	            <option " . (($objObserver->getObserverProperty ( $user, 'role', 2 ) == RoleAdmin) ? "selected=\"selected\"" : "") . " value=\"0\">" . LangViewObserverAdmin . "</option>
 	            <option " . (($objObserver->getObserverProperty ( $user, 'role', 2 ) == RoleUser) ? "selected=\"selected\"" : "") . " value=\"1\">" . LangViewObserverUser . "</option>
 	            <option " . (($objObserver->getObserverProperty ( $user, 'role', 2 ) == RoleCometAdmin) ? "selected=\"selected\"" : "") . " value=\"4\">" . LangViewObserverCometAdmin . "</option>
 	            <option " . (($objObserver->getObserverProperty ( $user, 'role', 2 ) == RoleWaitlist) ? "selected=\"selected\"" : "") . " value=\"2\">" . LangViewObserverWaitlist . "</option>
 	          </select>&nbsp;
           </div>
           <div class=\"col-sm-2\">
                <button type=\"submit\" class=\"btn btn-default\" name=\"change\">" . LangViewObserverChange . "</button>
           </div>
	        </div>";
		} elseif ($objObserver->getObserverProperty ( $user, 'role', 2 ) == RoleWaitlist) {
			echo "<div class=\"form-group\">";
			echo "<label class=\"col-sm-2 control-label\">" . LangViewObserverRole . "</label>";
			echo "<div class=\"col-sm-5\">" . LangViewObserverWaitlist;
			echo "</div></div>";
		} else 		// fixed admin role
		{
			echo "<div class=\"form-group\">";
			echo "<label class=\"col-sm-2 control-label\">" . LangViewObserverRole . "</label>";
			echo "<div class=\"col-sm-5\">" . LangViewObserverAdmin;
			echo "</div></div>";
		}
		echo "</div></form>";
	}
	echo "</table>";
	echo "<hr />";
	echo "<table class=\"table table-striped\">";
	echo " <tr>";
	echo "  <th></th>";
	for($i = 0; $i < count ( $modules ); $i ++) {
		echo " <th>" . $GLOBALS [$modules [$i]];
		echo " </th>";
	}
	echo " </tr>";
	
	echo " <tr>";
	echo "  <td>" . LangViewObserverNumberOfObservations . "</td>";
	for($i = 0; $i < count ( $modules ); $i ++) {
		echo " <td>" . $information [$i] [0];
		echo " </td>";
	}
	echo " </tr>";
	
	echo " <tr>";
	echo "  <td>" . LangTopObserversHeader4 . "</td>";
	for($i = 0; $i < count ( $modules ); $i ++) {
		echo " <td>" . $information [$i] [1];
		echo " </td>";
	}
	echo " </tr>";
	
	echo " <tr>";
	echo "  <td>" . LangTopObserversHeader6 . "</td>";
	for($i = 0; $i < count ( $modules ); $i ++) {
		echo " <td>" . $information [$i] [2];
		echo " </td>";
	}
	echo " </tr>";
	
	echo " <tr>";
	echo "  <td>" . LangTopObserversHeader5 . "</td>";
	for($i = 0; $i < count ( $modules ); $i ++) {
		echo " <td>" . (($key == $i) ? $userMobjects . " / 110" : "-");
		echo " </td>";
	}
	echo " </tr>";
	
	echo " <tr>";
	echo "  <td>" . LangTopObserversHeader5b . "</td>";
	for($i = 0; $i < count ( $modules ); $i ++) {
		echo " <td>" . (($key == $i) ? $userCaldwellObjects . " / 110" : "-");
		echo " </td>";
	}
	echo " </tr>";
	
	echo " <tr>";
	echo "  <td>" . LangTopObserversHeader5c . "</td>";
	for($i = 0; $i < count ( $modules ); $i ++) {
		echo " <td>" . (($key == $i) ? $userH400objects . " / 400" : "-");
		echo " </td>";
	}
	echo " </tr>";
	
	echo " <tr>";
	echo "  <td>" . LangTopObserversHeader5d . "</td>";
	for($i = 0; $i < count ( $modules ); $i ++) {
		echo " <td>" . (($key == $i) ? $userHIIobjects . " / 400" : "-");
		echo " </td>";
	}
	echo " </tr>";
	
	echo " <tr>";
	echo "  <td>" . LangViewObserverRank . "</td>";
	for($i = 0; $i < count ( $modules ); $i ++) {
		echo " <td>" . $information [$i] [4];
		echo " </td>";
	}
	echo " </tr>";
	
	echo "</table>";
	
	if ($loggedUser != "") {
		if ($user != $loggedUser) {
			echo "<br />";
			echo "<a class=\"btn btn-primary\" href=\"" . $baseURL . "index.php?indexAction=new_message&amp;receiver=" . $user . "\">";
			echo "<span class=\"glyphicon glyphicon-envelope\"></span> " . LangMessagePublicList5 . $firstname . "</a>";
		}
	}
	
	echo "<hr />";
	$dir = opendir ( $instDir . 'common/observer_pics' );
	while ( FALSE !== ($file = readdir ( $dir )) ) {
		if (("." == $file) or (".." == $file))
			continue; // skip current directory and directory above
		if (fnmatch ( $user . ".gif", $file ) || fnmatch ( $user . ".jpg", $file ) || fnmatch ( $user . ".png", $file )) {
			echo "<div>";
			echo "<a href=\"" . $baseURL . "common/observer_pics/" . $file . "\" data-lightbox=\"image-1\" data-title=\"\">";
			echo "<img class=\"viewobserver\" src=\"" . $baseURL . "common/observer_pics/" . $file . "\" alt=\"" . $firstname . "&nbsp;" . $name . "\"></img>
	          </a></div>";
			echo "<hr />";
		}
	}
	
	echo "</div>";
	
	// The observations per year page
	echo "<div class=\"tab-pane\" id=\"observationsPerYear\">";
	// GRAFIEK
	// Check the date of the first observation
	$currentYear = date ( "Y" );
	$sql = $objDatabase->selectSingleValue ( "select MIN(date) from observations where observerid=\"" . $user . "\";", "MIN(date)", $currentYear . "0606" );
	$sql2 = $objDatabase->selectSingleValue ( "select MIN(date) from cometobservations where observerid=\"" . $user . "\";", "MIN(date)", $currentYear . "0606" );
	$startYear = min ( floor ( $sql / 10000 ), floor ( $sql2 / 10000 ) );
	// Add the JavaScript to initialize the chart on document ready
	echo "<script type=\"text/javascript\">
  
	  	      var chart;
	  	      $(document).ready(function() {
	  	      chart = new Highcharts.Chart({
	  	        chart: {
	  	          renderTo: 'container',
	  	          defaultSeriesType: 'line',
	  	          marginRight: 130,
	  	          marginBottom: 25
	  	        },
	  	        title: {
	  	          text: \"" . GraphTitle1 . " " . html_entity_decode ( $firstname, ENT_QUOTES, "UTF-8" ) . " " . html_entity_decode ( $name, ENT_QUOTES, "UTF-8" ) . "\",
	  	          x: -20 //center
	  	        },
	  	        subtitle: {
	  	          text: '" . GraphSource . $baseURL . "',
	  	          x: -20
	  	        },
	  	        xAxis: {
	  	          categories: [";
	
	for($i = $startYear; $i <= $currentYear; $i ++) {
		if ($i != $currentYear) {
			echo "'" . $i . "', ";
		} else {
			echo "'" . $i . "'";
		}
	}
	
	echo "]
	  	        },
	  	        yAxis: {
	  	          title: {
	  	            text: '" . GraphObservations . "'
	  	        },
	  	        plotLines: [{
	  	          value: 0,
	  	          width: 1,
	  	          color: '#808080'
	  	        }]
	  	      },
	  	      tooltip: {
	  	        formatter: function() {
	  	                            return '<b>'+ this.series.name +'</b><br/>'+
	  	        this.x +': '+ this.y;
	  	        }
	  	                    },
	  	                    legend: {
	  	                    layout: 'vertical',
	  	                    align: 'right',
	  	                    verticalAlign: 'top',
	  	                    x: -10,
	  	                        y: 100,
	  	                    borderWidth: 0
	  	      },
	  	                    series: [{
	  	                      name: '" . html_entity_decode ( $deepsky, ENT_QUOTES, "UTF-8" ) . "',
	  	                        data: [";
	for($i = $startYear; $i <= $currentYear; $i ++) {
		$obs = $objDatabase->selectSingleValue ( "select COUNT(date) from observations where observerid=\"" . $user . "\" and date >= \"" . $i . "0101\" and date <= \"" . $i . "1231\";", "COUNT(date)", "0" );
		if ($i != $currentYear) {
			echo $obs . ", ";
		} else {
			echo $obs;
		}
	}
	echo "                    ]
	  	                      }, {
                              name: '" . html_entity_decode ( $comets, ENT_QUOTES, "UTF-8" ) . "',
                                data: [";
	
	for($i = $startYear; $i <= $currentYear; $i ++) {
		$obs = $objDatabase->selectSingleValue ( "select COUNT(date) from cometobservations where observerid=\"" . $user . "\" and date >= \"" . $i . "0101\" and date <= \"" . $i . "1231\";", "COUNT(date)", "0" );
		if ($i != $currentYear) {
			echo $obs . ", ";
		} else {
			echo $obs;
		}
	}
	
	echo "                     ] }]
	  	                      });
	  	                      });
  
	  	                      </script>";
	
	// Show graph
	echo "<div id=\"container\" style=\"width: 800px; height: 400px; margin: 0 auto\"></div>";
	echo "</div>";
	
	// The tab with the object types
	echo "<div class=\"tab-pane\" id=\"objectTypes\">";
	// Pie chart
	$objectsArray = array ();
	$colors = Array ();
	
	$all = count ( $objDatabase->selectRecordsetArray ( "select * from observations where observerid=\"" . $user . "\"" ) );
	if ($all == 0) {
		$all = 1;
	}
	$rest = 0;
	
	$cometobservations = count ( $objDatabase->selectRecordsetArray ( "select * from cometobservations where observerid = \"" . $user . "\"" ) );
	$all += $cometobservations;
	
	if (($cometobservations / $all) >= 0.01) {
		$objectsArray ["comets"] = $cometobservations;
	} else {
		$rest += $cometobservations;
	}
	$colors ["comets"] = "#4572A7";
	
	$aster = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"ASTER\" and observations.observerid = \"" . $user . "\"" ) );
	$aster += count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"AA8STAR\" and observations.observerid = \"" . $user . "\"" ) );
	$aster += count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"AA4STAR\" and observations.observerid = \"" . $user . "\"" ) );
	$aster += count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"AA3STAR\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($aster / $all) >= 0.01) {
		$objectsArray ["ASTER"] = $aster;
	} else {
		$rest += $aster;
	}
	$colors ["ASTER"] = "#AA4643";
	
	$brtnb = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"BRTNB\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($brtnb / $all) >= 0.01) {
		$objectsArray ["BRTNB"] = $brtnb;
	} else {
		$rest += $brtnb;
	}
	$colors ["BRTNB"] = "#89A54E";
	
	$ds = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"DS\" and observations.observerid = \"" . $user . "\"" ) );
	$ds += count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"AA2STAR\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($ds / $all) >= 0.01) {
		$objectsArray ["DS"] = $ds;
	} else {
		$rest += $ds;
	}
	$colors ["DS"] = "#80699B";
	
	$star = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"AA1STAR\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($star / $all) >= 0.01) {
		$objectsArray ["AA1STAR"] = $star;
	} else {
		$rest += $star;
	}
	$colors ["AA1STAR"] = "#3D96AE";
	
	$drknb = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"DRKNB\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($drknb / $all) >= 0.01) {
		$objectsArray ["DRKNB"] = $drknb;
	} else {
		$rest += $drknb;
	}
	$colors ["DRKNB"] = "#DB843D";
	
	$galcl = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"GALCL\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($galcl / $all) >= 0.01) {
		$objectsArray ["GALCL"] = $galcl;
	} else {
		$rest += $galcl;
	}
	$colors ["GALCL"] = "#92A8CD";
	
	$galxy = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"GALXY\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($galxy / $all) >= 0.01) {
		$objectsArray ["GALXY"] = $galxy;
	} else {
		$rest += $galxy;
	}
	$colors ["GALXY"] = "#68302F";
	
	$plnnb = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"PLNNB\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($plnnb / $all) >= 0.01) {
		$objectsArray ["PLNNB"] = $plnnb;
	} else {
		$rest += $plnnb;
	}
	$colors ["PLNNB"] = "#A47D7C";
	
	$opncl = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"OPNCL\" and observations.observerid = \"" . $user . "\"" ) );
	$opncl += count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"CLANB\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($opncl / $all) >= 0.01) {
		$objectsArray ["OPNCL"] = $opncl;
	} else {
		$rest += $opncl;
	}
	$colors ["OPNCL"] = "#B5CA92";
	
	$glocl = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"GLOCL\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($glocl / $all) >= 0.01) {
		$objectsArray ["GLOCL"] = $glocl;
	} else {
		$rest += $glocl;
	}
	$colors ["GLOCL"] = "#00FF00";
	
	$eminb = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"EMINB\" and observations.observerid = \"" . $user . "\"" ) );
	$eminb += count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"ENRNN\" and observations.observerid = \"" . $user . "\"" ) );
	$eminb += count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"ENSTR\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($eminb / $all) >= 0.01) {
		$objectsArray ["EMINB"] = $eminb;
	} else {
		$rest += $eminb;
	}
	$colors ["EMINB"] = "#C0FFC0";
	
	$refnb = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"REFNB\" and observations.observerid = \"" . $user . "\"" ) );
	$refnb += count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"RNHII\" and observations.observerid = \"" . $user . "\"" ) );
	$refnb += count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"HII\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($refnb / $all) >= 0.01) {
		$objectsArray ["REFNB"] = $refnb;
	} else {
		$rest += $refnb;
	}
	$colors ["REFNB"] = "#0000C0";
	
	$nonex = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"NONEX\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($nonex / $all) >= 0.01) {
		$objectsArray ["NONEX"] = $nonex;
	} else {
		$rest += $nonex;
	}
	$colors ["NONEX"] = "#C0C0FF";
	
	$snrem = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"SNREM\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($snrem / $all) >= 0.01) {
		$objectsArray ["SNREM"] = $snrem;
	} else {
		$rest += $snrem;
	}
	$colors ["SNREM"] = "#808000";
	
	$quasr = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"QUASR\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($quasr / $all) >= 0.01) {
		$objectsArray ["QUASR"] = $quasr;
	} else {
		$rest += $quasr;
	}
	$colors ["QUASR"] = "#C0C000";
	
	$wrneb = count ( $objDatabase->selectRecordsetArray ( "select objects.* from objects,observations where objects.name = observations.objectname and objects.type = \"WRNEB\" and observations.observerid = \"" . $user . "\"" ) );
	
	if (($wrneb / $all) >= 0.01) {
		$objectsArray ["WRNEB"] = $wrneb;
	} else {
		$rest += $wrneb;
	}
	$colors ["WRNEB"] = "#008080";
	
	$objectsArray ["REST"] = $rest;
	$colors ["REST"] = "#00FFFF";
	echo "<script type=\"text/javascript\">
  
			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container2',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false
					},
					title: {
						text: \"" . ObjectsSeenGraph . html_entity_decode ( $firstname, ENT_QUOTES, "UTF-8" ) . " " . html_entity_decode ( $name, ENT_QUOTES, "UTF-8" ) . "\"
					},
                subtitle: {
                  text: '" . GraphSource . $baseURL . "'
                },
					tooltip: {
						formatter: function() {
							return '<b>'+ this.point.name +'</b>: '+ Math.round(this.percentage * 100) / 100 + '%';
						}
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							showCheckbox: true,
							dataLabels: {
								enabled: true,
								color: '#000000',
								connectorColor: '#000000',
								formatter: function() {
									return '<b>'+ this.point.name +'</b>: '+ this.y;
								}
							}
						}
					},
				    series: [{
						type: 'pie',
						name: 'Objects seen',
						data: [";
	
	foreach ( $objectsArray as $key => $value ) {
		if ($key != "REST") {
			print "{name: \"" . html_entity_decode ( $GLOBALS [$key], ENT_QUOTES, "UTF-8" ) . "\", color: '" . $colors [$key] . "', y: " . $value . "}, ";
		} else {
			print "{name: \"" . html_entity_decode ( $GLOBALS [$key], ENT_QUOTES, "UTF-8" ) . "\", color: '" . $colors [$key] . "', y: " . $value . "}";
		}
	}
	echo "
						]
					}]
				});
			});
  
		</script>";
	echo "<div id=\"container2\" style=\"width: 800px; height: 400px; margin: 0 auto\"></div>";
	
	echo "</div>";
	
	// Draw the stars
	echo "<div class=\"tab-pane\" id=\"stars\">";
	// Some javascript for the tooltips
	echo "<script>
         $(function() {
  	       $( document ).tooltip();
         });
  		  </script>
  		";
	
	// Messier
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Messier</h4>";
	
	drawStar ( $objAccomplishments->getMessierBronze ( $user ), "bronze", "Bronzen messier certificaat! Je hebt 25 verschillende messier objecten waargenomen!", "Neem minimaal 25 verschillende messier objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getMessierSilver ( $user ), "silver", "Zilveren messier certificaat! Je hebt 50 verschillende messier objecten waargenomen!", "Neem minimaal 50 verschillende messier objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getMessierGold ( $user ), "gold", "Gouden messier certificaat! Je hebt alle 110 messier objecten waargenomen!", "Neem alle 110 messier objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Messier Drawings
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings of Messier objects</h4>";
	
	drawStar ( $objAccomplishments->getMessierDrawingsBronze ( $user ), "bronze", "Bronzen messier certificaat! Je hebt 25 verschillende messier objecten waargenomen!", "Neem minimaal 25 verschillende messier objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getMessierDrawingsSilver ( $user ), "silver", "Zilveren messier certificaat! Je hebt 50 verschillende messier objecten waargenomen!", "Neem minimaal 50 verschillende messier objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getMessierDrawingsGold ( $user ), "gold", "Gouden messier certificaat! Je hebt alle 110 messier objecten waargenomen!", "Neem alle 110 messier objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Caldwell
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Caldwell</h4>";
	
	drawStar ( $objAccomplishments->getCaldwellBronze ( $user ), "bronze", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCaldwellSilver ( $user ), "silver", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCaldwellGold ( $user ), "gold", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Caldwell drawings
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings Caldwell objects</h4>";
	
	drawStar ( $objAccomplishments->getCaldwellDrawingsBronze ( $user ), "bronze", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCaldwellDrawingsSilver ( $user ), "silver", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCaldwellDrawingsGold ( $user ), "gold", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Herschel - 400
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Herschel 400</h4>";
	
	drawStar ( $objAccomplishments->getHerschelBronze ( $user ), "bronze", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelSilver ( $user ), "silver", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelGold ( $user ), "gold", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelDiamond ( $user ), "diamond", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelPlatina ( $user ), "platinum", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Herschel 400 drawings
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings Herschel 400 objects</h4>";
	
	drawStar ( $objAccomplishments->getHerschelDrawingsBronze ( $user ), "bronze", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelDrawingsSilver ( $user ), "silver", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelDrawingsGold ( $user ), "gold", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelDrawingsDiamond ( $user ), "diamond", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelDrawingsPlatina ( $user ), "platinum", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Herschel II
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Herschel II</h4>";
	
	drawStar ( $objAccomplishments->getHerschelIIBronze ( $user ), "bronze", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelIISilver ( $user ), "silver", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelIIGold ( $user ), "gold", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelIIDiamond ( $user ), "diamond", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelIIPlatina ( $user ), "platinum", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Herschel II drawings
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings Herschel II objects</h4>";
	
	drawStar ( $objAccomplishments->getHerschelIIDrawingsBronze ( $user ), "bronze", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelIIDrawingsSilver ( $user ), "silver", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelIIDrawingsGold ( $user ), "gold", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelIIDrawingsDiamond ( $user ), "diamond", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getHerschelIIDrawingsPlatina ( $user ), "platinum", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Total number of drawings
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings</h4>";
	
	drawStar ( $objAccomplishments->getDrawingsNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getDrawingsRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getDrawingsBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getDrawingsTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getDrawingsSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getDrawingsIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getDrawingsExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getDrawingsAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getDrawingsSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getDrawingsExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Total number of open clusters
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Open clusters seen</h4>";
	
	drawStar ( $objAccomplishments->getOpenClustersNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClustersRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClustersBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClustersTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClustersSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClustersIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClustersExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClustersAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClustersSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClustersExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Total number of open clusters drawn
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings of open clusters</h4>";
	
	drawStar ( $objAccomplishments->getOpenClusterDrawingsNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClusterDrawingsRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClusterDrawingsBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClusterDrawingsTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClusterDrawingsSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClusterDrawingsIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClusterDrawingsExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClusterDrawingsAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClusterDrawingsSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getOpenClusterDrawingsExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	
	echo "</div>";
	
	// Total number of globular clusters
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Globular clusters seen</h4>";
	
	drawStar ( $objAccomplishments->getGlobularClustersNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClustersRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClustersBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClustersTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClustersSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClustersIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClustersExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClustersAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClustersSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClustersExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Total number of globular clusters drawn
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings of globular clusters</h4>";
	
	drawStar ( $objAccomplishments->getGlobularClusterDrawingsNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClusterDrawingsRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClusterDrawingsBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClusterDrawingsTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClusterDrawingsSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClusterDrawingsIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClusterDrawingsExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClusterDrawingsAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClusterDrawingsSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGlobularClusterDrawingsExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	
	echo "</div>";
	
	// Total number of planetary nebulae
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Planetary Nebulae seen</h4>";
	
	drawStar ( $objAccomplishments->getPlanetaryNebulaNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Total number of planetary nebulae drawn
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings of planetary nebulae</h4>";
	
	drawStar ( $objAccomplishments->getPlanetaryNebulaDrawingsNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaDrawingsRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaDrawingsBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaDrawingsTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaDrawingsSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaDrawingsIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaDrawingsExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaDrawingsAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaDrawingsSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getPlanetaryNebulaDrawingsExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	
	echo "</div>";
	
	// Total number of galaxies
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Galaxies seen</h4>";
	
	drawStar ( $objAccomplishments->getGalaxyNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxySkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxySenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Total number of galaxies drawn
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings of galaxies</h4>";
	
	drawStar ( $objAccomplishments->getGalaxyDrawingsNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyDrawingsRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyDrawingsBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyDrawingsTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyDrawingsSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyDrawingsIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyDrawingsExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyDrawingsAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyDrawingsSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getGalaxyDrawingsExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	
	echo "</div>";
	
	// Total number of nebulae
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Nebulae seen</h4>";
	
	drawStar ( $objAccomplishments->getNebulaNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Total number of nebulae drawn
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings of nebulae</h4>";
	
	drawStar ( $objAccomplishments->getNebulaDrawingsNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaDrawingsRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaDrawingsBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaDrawingsTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaDrawingsSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaDrawingsIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaDrawingsExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaDrawingsAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaDrawingsSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getNebulaDrawingsExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	
	echo "</div>";
	
	// Total number of different objects
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Different objects seen</h4>";
	
	drawStar ( $objAccomplishments->getObjectsNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Total number of nebulae drawn
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings of different objects</h4>";
	
	drawStar ( $objAccomplishments->getObjectsDrawingsNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	
	echo "</div>";
	
	// Total number of comet observations
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Total comet observations</h4>";
	
	drawStar ( $objAccomplishments->getCometObservationsNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometObservationsRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometObservationsBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometObservationsTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometObservationsSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometObservationsIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometObservationsExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometObservationsAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometObservationsSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometObservationsExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	echo "</div>";
	
	// Total number of different comets seen
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Different comets seen</h4>";
	
	drawStar ( $objAccomplishments->getObjectsDrawingsNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getObjectsDrawingsExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	
	echo "</div>";
	
	// Total number of different comet drawings
	echo "<div class=\"accomplishmentRow\">";
	echo "<h4>Drawings of comets</h4>";
	
	drawStar ( $objAccomplishments->getCometDrawingsNewbie ( $user ), "newbie", "Bronzen caldwell certificaat! Je hebt 25 verschillende caldwell objecten waargenomen!", "Neem minimaal 25 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometDrawingsRookie ( $user ), "rookie", "Zilveren caldwell certificaat! Je hebt 50 verschillende caldwell objecten waargenomen!", "Neem minimaal 50 verschillende caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometDrawingsBeginner ( $user ), "beginner", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometDrawingsTalented ( $user ), "talented", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometDrawingsSkilled ( $user ), "skilled", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometDrawingsIntermediate ( $user ), "intermediate", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometDrawingsExperienced ( $user ), "experienced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometDrawingsAdvanced ( $user ), "advanced", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometDrawingsSenior ( $user ), "senior", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	drawStar ( $objAccomplishments->getCometDrawingsExpert ( $user ), "expert", "Gouden caldwell certificaat! Je hebt alle 110 caldwell objecten waargenomen!", "Neem alle 110 caldwell objecten waar om dit certificaat te krijgen!" );
	
	echo "</div>";
	
	echo "</div>";
	echo "<br />";
	
	echo "</div>";
	echo "</div>";
}
function drawStar($done, $color, $tooltip, $tooltipToDo) {
	global $baseURL;
	
	// TODO : Vul tooptips
	// TODO : Tekst komt uit taalfiles!
	// TODO : Alle accomplishments tonen!
	if ($done) {
		print "<div class=\"star\" id=\"" . $color . "\">";
		print "<div class=\"accomplishmentText\" title=\"" . $tooltip . "\">" . ucfirst ( $color ) . "</div>";
		print "</div>";
	} else {
		print "<div class=\"star notAccomplished\" id=\"" . $color . "\">";
		print "<div class=\"accomplishmentText\" title=\"" . $tooltipToDo . "\">" . ucfirst ( $color ) . "</div>";
		print "</div>";
	}
}

?>

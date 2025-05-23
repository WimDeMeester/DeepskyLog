<?php
// sessions.php
// The session class collects all functions needed to add, remove and adapt DeepskyLog sessions from the database.
global $inIndex;
if ((! isset ( $inIndex )) || (! $inIndex))
	include "../../redirect.php";
class Sessions {
	public function getSessionPropertiesFromId($id) 	// returns the properties of the session with id
	{
		global $objDatabase;
		return $objDatabase->selectRecordArray ( "SELECT * FROM sessions WHERE id=\"" . $id . "\"" );
	}
	public function getSessionPropertyFromId($id, $property, $defaultValue = '') 	// returns the property of the given session
	{
		global $objDatabase;
		return $objDatabase->selectSingleValue ( "SELECT " . $property . " FROM sessions WHERE id = \"" . $id . "\"", $property, $defaultValue );
	}
	public function getAllSessionsForUser($user) 	// returns all the sessions for a given user
	{
		global $objDatabase;
		return $objDatabase->selectRecordsetArray ( "SELECT * FROM sessions WHERE observerid=\"" . $user . "\"" );
	}
	public function getAllActiveSessionsForUser($user) 	// returns all the active sessions for a given user
	{
		global $objDatabase;
		return $objDatabase->selectRecordsetArray ( "SELECT * FROM sessions WHERE observerid=\"" . $user . "\" and active=\"1\"" );
	}
	public function validateSession() {
		global $loggedUser, $instDir, $_FILES;
		if (! ($loggedUser))
			throw new Exception (_('You should be logged in to be able to send messages.'));

			// The observers
		$observers = Array ();

		$count = array_count_values ( $_POST ['addedObserver'] );
		if (isset ( $_POST ['deletedObserver'] )) {
			$countRemoved = array_count_values ( $_POST ['deletedObserver'] );
		} else {
			$countRemoved = Array ();
		}

		foreach ( $count as $k => $v ) {
			$val = $v;
			$val2 = 0;
			if (array_key_exists ( $k, $countRemoved )) {
				$val2 = $countRemoved [$k];
			}
			if (($val - $val2) == 1) {
				$observers [] = $k;
			}
		}
		$current_session = $this->addSession ( $_POST ['sessionname'], $_POST ['beginday'], $_POST ['beginmonth'], $_POST ['beginyear'], $_POST ['beginhours'], $_POST ['beginminutes'], $_POST ['endday'], $_POST ['endmonth'], $_POST ['endyear'], $_POST ['endhours'], $_POST ['endminutes'], $_POST ['site'], $_POST ['weather'], $_POST ['equipment'], $_POST ['comments'], $_POST ['description_language'], $observers, - 1 );

		if ($_FILES ['picture'] ['tmp_name'] != "") 		// picture to upload
		{
			$upload_dir = $instDir . 'deepsky/sessions';
			$dir = opendir ( $upload_dir );
			$original_image = $_FILES ['picture'] ['tmp_name'];
			$destination_image = $upload_dir . "/" . $current_session . "_resized.jpg";
			require_once $instDir . "common/control/resize.php"; // resize code
			$new_image = image_createThumb ( $original_image, $destination_image, 490, 490, 100 );
			move_uploaded_file ( $_FILES ['picture'] ['tmp_name'], $upload_dir . "/" . $current_session . ".jpg" );
		}
	}
	public function addSession($sessionname, $beginday, $beginmonth, $beginyear, $beginhours, $beginminutes, $endday, $endmonth, $endyear, $endhours, $endminutes, $location, $weather, $equipment, $comments, $language, $observers, $sessionid) {
		global $objDatabase, $loggedUser, $dateformat, $entryMessage;
		// Make sure not to insert bad code in the database
		$name = preg_replace ( "/(\")/", "", $sessionname );
		$name = preg_replace ( "/;/", ",", $name );

		$begindate = date ( 'Y-m-d H:i:s', mktime ( $beginhours, $beginminutes, 0, $beginmonth, $beginday, $beginyear ) );
		$enddate = date ( 'Y-m-d H:i:s', mktime ( $endhours, $endminutes, 0, $endmonth, $endday, $endyear ) );

		// Check if the date already is used in another session.
		$existing_sessions = $this->getAllActiveSessionsForUser ( $loggedUser );
		$return = false;
		for($i = 0; $i < count ( $existing_sessions ); $i ++) {
			if ($existing_sessions[$i]["id"] != $sessionid) {
				$session_begindate = $existing_sessions [$i] ['begindate'];
				$session_enddate = $existing_sessions [$i] ['enddate'];

				// Check if the begindate of the new session is in one of the other existing sessions
				$start_ts = strtotime($session_begindate);
				$end_ts = strtotime($session_enddate);
				$user_ts = strtotime($begindate);
				// Check that user date is between start & end
				if (($user_ts >= $start_ts) && ($user_ts <= $end_ts)) {
					$return = true;
				}

				// Check if the enddate of the new session is in one of the other existing sessions
				$user_ts = strtotime($enddate);

				// Check that user date is between start & end
				if (($user_ts >= $start_ts) && ($user_ts <= $end_ts)) {
					// Don't take into account the session that will be adapted
					$return = true;
				}
			}
		}
		if ($return) {
			$entryMessage = _('The new session overlaps with an existing session. Please adapt the date so that there is no longer an overlap.');
			$_GET ['indexAction'] = 'add_session';
			return;
		}

		// Auto-generate the session name
		if ($name == "") {
			if ($beginday == $endday && $beginmonth == $endmonth && $beginyear == $endyear) {
				$name = sprintf(
                    _("Observing session from %s"), 
                    date($dateformat, mktime(0, 0, 0, $beginmonth, $beginday, $beginyear))
                );
			} else {
				$name = sprintf(
                    _("Observing session from %s till %s"), 
                    date($dateformat, mktime(0, 0, 0, $beginmonth, $beginday, $beginyear)), 
                    date($dateformat, mktime(0, 0, 0, $endmonth, $endday, $endyear))
                );
			}
		}

		$weather = preg_replace ( "/(\")/", "", $weather );
		$weather = preg_replace ( "/;/", ",", $weather );

		$equipment = preg_replace ( "/(\")/", "", $equipment );
		$equipment = preg_replace ( "/;/", ",", $equipment );

		$comments = preg_replace ( "/(\")/", "", $comments );
		$comments = preg_replace ( "/;/", ",", $comments );

		// First check whether the session already exists
		if ($sessionid > 0) {
			// Check if there is a deleted observer
			$observersFromDatabase = $objDatabase->selectSingleArray ( "SELECT observer from sessionObservers where sessionid=\"" . $sessionid . "\";", "observer" );
			for($i = 0; $i < count ( $observersFromDatabase ); $i ++) {
				if (! in_array ( $observersFromDatabase [$i], $observers )) {
					$objDatabase->execSQL ( "DELETE from sessionObservers where sessionid=\"" . $sessionid . "\" AND observer=\"" . $observersFromDatabase [$i] . "\"" );
				}
			}
			// Update the session
			$this->updateSession ( $sessionid, $name, $begindate, $enddate, $location, $weather, $equipment, $comments, $language );

			// First make sure to remove all old observations
			$objDatabase->execSQL ( "DELETE from sessionObservations where sessionid=\"" . $sessionid . "\"" );
			// Add observations to the session
			$this->addObservations ( $sessionid, $beginyear, $beginmonth, $beginday, $endyear, $endmonth, $endday, $observers );

			// Check if there is a new observer
			$observersFromDatabase = $objDatabase->selectSingleArray ( "SELECT observer from sessionObservers where sessionid=\"" . $sessionid . "\";", "observer" );
			// Add the logged user to the list of the observers
			$observersFromDatabase [] = $loggedUser;
			for($i = 0; $i < count ( $observers ); $i ++) {
				if (! in_array ( $observers [$i], $observersFromDatabase )) {
					// The observer is not in the database. We have to add a new user.
					$this->addObserver ( $sessionid, $observers [$i] );

					$objDatabase->execSQL ( "INSERT into sessions (name, observerid, begindate, enddate, locationid, weather, equipment, comments, language, active) VALUES(\"" . $name . "\", \"" . $observers [$i] . "\", \"" . $begindate . "\", \"" . $enddate . "\", \"" . $location . "\", \"" . $weather . "\", \"" . $equipment . "\", \"" . $comments . "\", \"" . $language . "\", 0)" );
					$newId = $objDatabase->insert_id ();
					// Also add the extra observers to the sessionObservers table
					for($j = 0; $j < count ( $observers ); $j ++) {
						if ($j != $i) {
							$objDatabase->execSQL ( "INSERT into sessionObservers (sessionid, observer) VALUES(\"" . $newId . "\", \"" . $observers [$j] . "\");" );
						}
					}
				}
			}
			// Add observations to the session
			$observers [] = $loggedUser;
		} else {
			// First add a new session with the observer which created the session (and set to active)
			$objDatabase->execSQL ( "INSERT into sessions (name, observerid, begindate, enddate, locationid, weather, equipment, comments, language, active) VALUES(\"" . $name . "\", \"" . $loggedUser . "\", \"" . $begindate . "\", \"" . $enddate . "\", \"" . $location . "\", \"" . $weather . "\", \"" . $equipment . "\", \"" . $comments . "\", \"" . $language . "\", 1)" );
			$sessionid = $objDatabase->selectSingleValue ( "SELECT id FROM sessions ORDER BY id DESC LIMIT 1", 'id' );
			// Get the id of the new session

			for($i = 1; $i < count ( $observers ); $i ++) {
				// Add the observers to the sessionObservers table
				$this->addObserver ( $sessionid, $observers [$i] );

				// Add the new session also for the other observers (and set to inactive)
				$objDatabase->execSQL ( "INSERT into sessions (name, observerid, begindate, enddate, locationid, weather, equipment, comments, language, active) VALUES(\"" . $name . "\", \"" . $observers [$i] . "\", \"" . $begindate . "\", \"" . $enddate . "\", \"" . $location . "\", \"" . $weather . "\", \"" . $equipment . "\", \"" . $comments . "\", \"" . $language . "\", 0)" );
				$newId = $objDatabase->insert_id ();
				// Also add the extra observers to the sessionObservers table
				for($j = 0; $j < count ( $observers ); $j ++) {
					if ($j != $i) {
						$objDatabase->execSQL ( "INSERT into sessionObservers (sessionid, observer) VALUES(\"" . $newId . "\", \"" . $observers [$j] . "\");" );
					}
				}
			}
			$this->addObservations ( $sessionid, $beginyear, $beginmonth, $beginday, $endyear, $endmonth, $endday, $observers );
		}
		return $sessionid;
	}
	private function addObserver($id, $observer) {
		global $objDatabase, $objMessages, $loggedUser, $objObserver, $baseURL;
		$objDatabase->execSQL ( "INSERT into sessionObservers (sessionid, observer) VALUES(\"" . $id . "\", \"" . $observer . "\");" );

		$observername = $objObserver->getObserverProperty ( $loggedUser, "firstname" ) . " " . $objObserver->getObserverProperty ( $loggedUser, "name" );
		$subject = sprintf(_("%s made a new session where you are an observer"), $observername);
		$sessionname = $this->getSessionPropertyFromId ( $id, "name" );
		$content = sprintf(
            _("%s made the session '%s'."), 
            $observername, $sessionname);
        $content .= "<br /><br />" . 
            sprintf(_("A similar session is prepared for you. Check %sAdd/Sessions</a> to validate the session."), 
                "<a href=\"http://www.deepskylog.org/index.php?indexAction=add_session\">");
		$content .= "<br /><br />" . _('Send message to ') . "<a href=\"http://www.deepskylog.org/index.php?indexAction=new_message&amp;receiver=" . urlencode ( $loggedUser ) . "&amp;subject=Re:%20" . urlencode ( $sessionname ) . "\">" . $observername . "</a>";
		$content .= "<br /><br />Zend een bericht naar " . $observername;
		if ($loggedUser != $observer) {
			$objMessages->sendMessage ( $loggedUser, $observer, $subject, $content );
		}
	}
	private function addObservations($id, $beginyear, $beginmonth, $beginday, $endyear, $endmonth, $endday, $observers) {
		global $objDatabase;
		$begindate = sprintf ( "%4d%02d%02d", $beginyear, $beginmonth, $beginday );
		$enddate = sprintf ( "%4d%02d%02d", $endyear, $endmonth, $endday );
		// Add all observations to the sessionObservations table

		for($i = 0; $i < count ( $observers ); $i ++) {
			// Select the observations of the observers in this session
			$obsids = $objDatabase->selectSingleArray ( "SELECT id from observations where observerid=\"" . $observers [$i] . "\" and date>=\"" . $begindate . "\" and date<=\"" . $enddate . "\";", "id" );
			for($cnt = 0; $cnt < count ( $obsids ); $cnt ++) {
				// Add the observations to the sessionObservations table
				$objDatabase->execSQL ( "INSERT into sessionObservations (sessionid, observationid) VALUES(\"" . $id . "\", \"" . $obsids [$cnt] . "\");" );
			}
		}
	}
	public function updateSession($id, $name, $begindate, $enddate, $location, $weather, $equipment, $comments, $language) {
		global $objDatabase, $dateformat;
		// Here we change the session
		// Make sure not to insert bad code in the database
		$name = html_entity_decode ( $name, ENT_COMPAT, "ISO-8859-15" );
		$name = preg_replace ( "/(\")/", "", $name );
		$name = preg_replace ( "/;/", ",", $name );

		// Auto-generate the session name
		if ($name == "") {
			$beginyear = substr ( $begindate, 0, 4 );
			$beginmonth = substr ( $begindate, 5, 2 );
			$beginday = substr ( $begindate, 8, 2 );
			$endyear = substr ( $enddate, 0, 4 );
			$endmonth = substr ( $enddate, 5, 2 );
			$endday = substr ( $enddate, 8, 2 );
			if ($begindate == $enddate) {
				$name = sprintf(
                    _("Observing session from %s"), 
                    date($dateformat, mktime(0, 0, 0, $beginmonth, $beginday, $beginyear))
                );
			} else {
				$name = sprintf(
                    _("Observing session from %s till %s"),
                    date($dateformat, mktime(0, 0, 0, $beginmonth, $beginday, $beginyear)), 
                    date($dateformat, mktime(0, 0, 0, $endmonth, $endday, $endyear))
                );
			}
		}
		$objDatabase->execSQL ( "UPDATE sessions set name=\"" . $name . "\" where id=\"" . $id . "\";" );
		$objDatabase->execSQL ( "UPDATE sessions set begindate=\"" . $begindate . "\" where id=\"" . $id . "\";" );
		$objDatabase->execSQL ( "UPDATE sessions set enddate=\"" . $enddate . "\" where id=\"" . $id . "\";" );
		$objDatabase->execSQL ( "UPDATE sessions set locationid=\"" . $location . "\" where id=\"" . $id . "\";" );
		$objDatabase->execSQL ( "UPDATE sessions set weather=\"" . $weather . "\" where id=\"" . $id . "\";" );
		$objDatabase->execSQL ( "UPDATE sessions set equipment=\"" . $equipment . "\" where id=\"" . $id . "\";" );
		$objDatabase->execSQL ( "UPDATE sessions set comments=\"" . $comments . "\" where id=\"" . $id . "\";" );
		$objDatabase->execSQL ( "UPDATE sessions set language=\"" . $language . "\" where id=\"" . $id . "\";" );
		$objDatabase->execSQL ( "UPDATE sessions set active=\"1\" where id=\"" . $id . "\";" );
	}
	public function removeAllSessionObservations($sessionid) {
		global $objDatabase;
		$objDatabase->execSQL ( "DELETE FROM sessionObservations WHERE sessionid=\"" . $sessionid . "\"" );
	}
	public function getListWithInactiveSessions($userid) {
		global $objDatabase;
		return $objDatabase->selectRecordsetArray ( "SELECT id from sessions where observerid = \"" . $userid . "\" and active = \"0\";" );
	}
	public function getListWithActiveSessions($userid) {
		global $objDatabase;
		return $objDatabase->selectRecordsetArray ( "SELECT id from sessions where observerid = \"" . $userid . "\" and active = \"1\";" );
	}
	public function getListWithAllActiveSessions() {
		global $objDatabase;
		return $objDatabase->selectRecordsetArray ( "SELECT id from sessions where active = \"1\";" );
	}
	public function getObservers($id) {
		global $objDatabase;
		return $objDatabase->selectRecordsetArray ( "SELECT observer from sessionObservers where sessionid = \"" . $id . "\";" );
	}
	public function getObservations($id) {
		global $objDatabase, $objObservation, $objObject, $objObserver, $objInstrument;
		$obs = $objDatabase->selectRecordsetArray ( "SELECT observationid from sessionObservations where sessionid = \"" . $id . "\";" );
		$qobs = Array ();
		for($i = 0; $i < count ( $obs ); $i ++) {
			$obsid = $obs [$i] ["observationid"];
			$qobs [$i] = $objObservation->getAllInfoDsObservation ( $obsid );
			$qobs [$i] ["observationid"] = $obsid;
			$qobs [$i] ["objecttype"] = $objObject->getDsoProperty ( $qobs [$i] ['objectname'], "type" );
			$qobs [$i] ["objectconstellation"] = $objObject->getDsoProperty ( $qobs [$i] ['objectname'], "con" );
			$qobs [$i] ["objectmagnitude"] = $objObject->getDsoProperty ( $qobs [$i] ['objectname'], "mag" );
			$qobs [$i] ["objectsurfacebrigthness"] = $objObject->getDsoProperty ( $qobs [$i] ['objectname'], "subr" );
			$observerid = $objObservation->getDsObservationProperty ( $obsid, "observerid" );
			$qobs [$i] ["observername"] = $objObserver->getObserverProperty ( $observerid, "firstname" ) . " " . $objObserver->getObserverProperty ( $observerid, "name" );
			$qobs [$i] ["observationdescription"] = $objObservation->getDsObservationProperty ( $obsid, "description" );
			$qobs [$i] ["observationdate"] = $objObservation->getDsObservationProperty ( $obsid, "date" );
			$qobs [$i] ["instrumentname"] = $objInstrument->getInstrumentPropertyFromId ( $qobs [$i] ['instrumentid'], "name" );
			$qobs [$i] ["instrumentdiameter"] = $objInstrument->getInstrumentPropertyFromId ( $qobs [$i] ['instrumentid'], "diameter" );
		}
		return $qobs;
	}
	public function showInactiveSessions($userid) {
		global $baseURL, $loggedUser, $objUtil, $objLocation, $objPresentations, $loggedUserName, $objObserver;
		$sessions = $this->getListWithInactiveSessions ( $userid );
		if ($sessions != null) {
			echo "<table class=\"table sort-table table-condensed table-striped table-hover tablesorter custom-popup\">";
			echo "<thead>";
			echo "<th>" . _("Name") . "</th>";
			echo "<th>" . _("Begin") . "</th>";
			echo "<th>" . _("End") . "</th>";
			echo "<th>" . _("Location") . "</th>";
			echo "<th>" . _("Extra observers") . "</th>";
			echo "<th class=\"filter-false columnSelector-disable\" data-sorter=\"false\"></th>";
			echo "</thead>";
			$count=0;
			foreach ($sessions as $key => $value) {
				$session = $this->getSessionPropertiesFromId ( $value ['id'] );
				echo "<tr>";
				echo "<td style=\"vertical-align: middle\">" . $session ['name'] . "</td>";
				echo "<td style=\"vertical-align: middle\">" . $session ['begindate'] . "</td>";
				echo "<td style=\"vertical-align: middle\">" . $session ['enddate'] . "</td>";
				echo "<td style=\"vertical-align: middle\">" . $objLocation->getLocationPropertyFromId ( $session ['locationid'], "name" ) . "</td>";
				echo "<td style=\"vertical-align: middle\">";
				$observers = $this->getObservers ( $value ['id'] );
				if (count ( $observers ) > 0) {
					for($cnt = 0; $cnt < count ( $observers ) - 1; $cnt ++) {
						print $objObserver->getObserverProperty ( $observers [$cnt] ['observer'], "firstname" ) . " " . $objObserver->getObserverProperty ( $observers [$cnt] ['observer'], "name" ) . " - ";
					}
					print $objObserver->getObserverProperty ( $observers [count ( $observers ) - 1] ['observer'], "firstname" ) . " " . $objObserver->getObserverProperty ( $observers [count ( $observers ) - 1] ['observer'], "name" );
				}
				echo "</td>";
				echo "<td>";
				// Add the session
				echo ("<a href=\"" . $baseURL . "index.php?indexAction=adapt_session&amp;sessionid=" . urlencode ( $value ['id'] ) . "\" class=\"btn btn-primary\" title=\"" . _("Add session") . "\"><span class=\"glyphicon glyphicon-plus\"></span></a>");
				echo "&nbsp;&nbsp;";
				// Remove the session
				echo ("<a href=\"" . $baseURL . "index.php?indexAction=validate_delete_existingsession&amp;sessionid=" . urlencode ( $value ['id'] ) . "\" class=\"btn btn-primary\" title=\"" . _("Delete session") . "\"><span class=\"glyphicon glyphicon-minus\"></span></a>");
				echo "</td></tr>";
				$count++;
			}
			echo "</table>";

			$objUtil->addPager ( "", $count );
			echo "<hr />";
		}
	}
	public function showListSessions($sessions, $observer) {
		global $baseURL, $loggedUser, $objUtil, $objDatabase, $objLocation, $objPresentations, $loggedUserName, $objObserver, $instDir;
		// Get the number of sessions
		if (count ( $sessions ) == 0) 	// ================================================================================================== no result present =======================================================================================
		{
			echo "<h4>" . sprintf(
                _("No sessions available for %s!"),
                $objObserver->getObserverProperty($observer, "firstname") . " " 
                . $objObserver->getObserverProperty($observer, "name")
             ) . "</h4>";
		} else { // =============================================================================================== START OBSERVATION PAGE OUTPUT =====================================================================================
			echo "<div id=\"main\">";
			if ($observer == "-1") {
				$content1 = "<h4>" . _("All sessions");
			} else {
				$content1 = "<h4>" . sprintf(
                    _("Sessions of %s"),
                    $objObserver->getObserverProperty($observer, "firstname") . " " 
                    . $objObserver->getObserverProperty($observer, "name")
                );
			}
			$content1 .= "</h4>";

			echo $content1;

			if ($sessions != null) {
				echo "<table class=\"table sort-table table-condensed table-striped table-hover tablesorter custom-popup\">";
				echo "<thead>";
				echo "<tr>";
				echo "<th>" . _("Name") . "</th>";
				echo "<th>" . _("Begin") . "</th>";
				echo "<th>" . _("End") . "</th>";
				echo "<th>" . _("Location") . "</th>";
				echo "<th>" . _("Extra observers") . "</th>";
				echo "<th class=\"filter-false columnSelector-disable\" data-sorter=\"false\">" . _("Picture") . "</th>";
				echo "<th>" . ucfirst(_("observations")) . "</th>";
				echo "</tr>";
				echo "</thead>";
				for($cnt = 0; $cnt < count ( $sessions ); $cnt ++) {
					// First we have to put all the sessions in an array, to be able to sort
					$allSessions [] = $this->getSessionPropertiesFromId ( $sessions [$cnt] ['id'] );
				}
				for($cnt = 0; $cnt < count ( $sessions ); $cnt ++) {
					echo "<tr>";
					echo "<td><a href=\"" . $baseURL . "index.php?indexAction=adapt_session&amp;sessionid=" . $allSessions [$cnt] ['id'] . "\">" . $allSessions [$cnt] ['name'] . "</a></td>";
					echo "<td>" . $allSessions [$cnt] ['begindate'] . "</td>";
					echo "<td>" . $allSessions [$cnt] ['enddate'] . "</td>";
					echo "<td><a href=\"" . $baseURL . "index.php?indexAction=detail_location&location=" . $allSessions [$cnt] ['locationid'] . "\">" . $objLocation->getLocationPropertyFromId ( $allSessions [$cnt] ['locationid'], "name" ) . "</a></td>";
					echo "<td>";
					print "<a href=\"" . $baseURL . "index.php?indexAction=detail_observer&user=" . $allSessions [$cnt] ['observerid'] . "\">" . $objObserver->getObserverProperty ( $allSessions [$cnt] ['observerid'], "firstname" ) . " " . $objObserver->getObserverProperty ( $allSessions [$cnt] ['observerid'], "name" ) . "</a>";
					$observers = $this->getObservers ( $allSessions [$cnt] ['id'] );
					if (count ( $observers ) > 0) {
						echo " - ";
						for($cnt2 = 0; $cnt2 < count ( $observers ); $cnt2 ++) {
							print "<a href=\"" . $baseURL . "index.php?indexAction=detail_observer&user=" . $observers [$cnt2] ['observer'] . "\">" . $objObserver->getObserverProperty ( $observers [$cnt2] ['observer'], "firstname" ) . " " . $objObserver->getObserverProperty ( $observers [$cnt2] ['observer'], "name" ) . "</a>";
							if ($cnt2 < count ( $observers ) - 1) {
								echo " - ";
							}
						}
					}
					// A link to the picture
					if (file_exists ( $instDir . 'deepsky/sessions/' . $allSessions [$cnt] ["id"] . ".jpg" )) {
						echo "</td><td class=\"gallery clearfix\">";
						echo "<a href=\"" . $baseURL . 'deepsky/sessions/' . $allSessions [$cnt] ["id"] . ".jpg\" data-lightbox=\"image-1\" data-title=\"" . $allSessions [$cnt] ['name'] . "\">" . _("Picture") . "</a></td>";
					} else {
						echo "</td><td> &nbsp; </td>";
					}

					echo "</td><td><a href=\"" . $baseURL . "index.php?indexAction=result_selected_observations&sessionid=" . $allSessions [$cnt] ["id"] . "\">";

					// the number of observations
					$numberOfObservations = $objDatabase->selectRecordsetArray ( "SELECT COUNT(sessionid) from sessionObservations where sessionid = \"" . $allSessions [$cnt] ["id"] . "\";" );
					echo $numberOfObservations[0]['COUNT(sessionid)'] . " " . _("observations");
					echo "</a></td></tr>";
				}
				echo "</table>";

				$objUtil->addPager ( "", count ( $sessions ) );

				echo "<hr />";
			}
		}
	}
	public function validateDeleteSession() 	// validates and deletes a session
	{
		global $objUtil, $objDatabase;
		if (($sessionid = $objUtil->checkGetKey ( 'sessionid' )) && $objUtil->checkAdminOrUserID ( $this->getSessionPropertyFromId ( $sessionid, 'observerid' ) )) {
			$objDatabase->execSQL("DELETE FROM sessions WHERE id=\"" . $sessionid . "\"" );
			$objDatabase->execSQL("DELETE FROM sessionObservations WHERE sessionid=\"" . $sessionid . "\"" );
			$objDatabase->execSQL("DELETE FROM sessionObservers WHERE sessionid=\"" . $sessionid . "\"" );
			return _("The session is removed from DeepskyLog.");
		}
	}
	public function addObservationToSessions($current_observation) {
		global $objObservation, $objDatabase;
		$obs = $objObservation->getAllInfoDsObservation ( $current_observation );
		$dateWithoutTime = $obs ['date'];
		$date = substr ( $dateWithoutTime, 0, 4 ) . "-" . substr ( $dateWithoutTime, 4, 2 ) . "-" . substr ( $dateWithoutTime, 6, 2 ) . " ";
		$time = $obs ['time'];
		if ($time > 0) {
			if ($time < 1000) {
				$date = $date . "0" . substr ( $time, 0, 1 ) . ":" . substr ( $time, 1, 2 ) . ":00";
			} else {
				$date = $date . substr ( $time, 0, 2 ) . ":" . substr ( $time, 2, 2 ) . ":00";
			}
		} else {
			$date = $date . "00:00:00";
		}

		// First remove the observation from the existing sessions
		$objDatabase->execSQL ( "DELETE from sessionObservations where observationid =  \"" . $current_observation . "\"" );
		$sessions = $objDatabase->selectRecordsetArray ( "SELECT * from sessions where begindate <= \"" . $date . "\" and enddate >= \"" . $date . "\" and active = 1" );

		// We now have a list with all sessions, but we only have one observer. Get the other observers from the sessionObservers table
		for($i = 0; $i < count ( $sessions ); $i ++) {
			$users [] = $sessions [$i] ['observerid'];
			$extraUsers = $objDatabase->selectRecordsetArray ( "SELECT * from sessionObservers where sessionid =  \"" . $sessions [$i] ['id'] . "\"" );
			for($cnt = 0; $cnt < count ( $extraUsers ); $cnt ++) {
				$users [] = $extraUsers [$cnt] ['observer'];
			}
			if (in_array ( $obs ['observerid'], $users )) {
				$objDatabase->execSQL ( "INSERT into sessionObservations (sessionid, observationid) VALUES (\"" . $sessions [$i] ['id'] . "\", \"" . $obs ['id'] . "\");" );
			}
			$users = Array ();
		}
	}
	public function validateChangeSession() {
		global $loggedUser, $objUtil, $objLocation, $instDir;
		if (! ($loggedUser))
			throw new Exception (_('You should be logged in to be able to send messages.'));

		$sessionid = $objUtil->checkRequestKey ( 'sessionid' );

		// The observers
		$observers = Array ();

		$count = array_count_values ( $_POST ['addedObserver'] );
		if (isset ( $_POST ['deletedObserver'] )) {
			$countRemoved = array_count_values ( $_POST ['deletedObserver'] );
		} else {
			$countRemoved = Array ();
		}

		foreach ( $count as $k => $v ) {
			$val = $v;
			$val2 = 0;
			if (array_key_exists ( $k, $countRemoved )) {
				$val2 = $countRemoved [$k];
			}
			if (($val - $val2) == 1) {
				$observers [] = $k;
			}
		}

		// Add the new location if needed
		// Location of the session
		$sites = $objLocation->getSortedLocationsList ( "name", $loggedUser, 1 );
		$theLoc = $this->getSessionPropertyFromId ( $objUtil->checkRequestKey ( 'sessionid' ), 'locationid' );
		$theLocName = $objLocation->getLocationPropertyFromId ( $theLoc, "name" );
		$found = 1;
		// Check if the number is owned by the loggedUser
		if ($objLocation->getLocationPropertyFromId ( $theLoc, "observer" ) != $loggedUser) {
			$found = 0;
			for($i = 0; $i < count ( $sites ); $i ++) {
				if (strcmp ( $sites [$i] [1], $theLocName ) == 0) {
					$theLoc = $sites [$i] [0];
					$found = 1;
				}
			}
		}
		if ($found == 0) {
			$id = $objLocation->addLocation ( $theLocName, $objLocation->getLocationPropertyFromId ( $theLoc, "longitude" ), $objLocation->getLocationPropertyFromId ( $theLoc, "latitude" ), $objLocation->getLocationPropertyFromId ( $theLoc, "country" ), $objLocation->getLocationPropertyFromId ( $theLoc, "timezone" ) );
			$objLocation->setLocationProperty ( $id, "limitingMagnitude", $objLocation->getLocationPropertyFromId ( $theLoc, "limitingMagnitude" ) );
			$objLocation->setLocationProperty ( $id, "skyBackground", $objLocation->getLocationPropertyFromId ( $theLoc, "skyBackground" ) );
			$objLocation->setLocationProperty ( $id, "observer", $loggedUser );
			$objLocation->setLocationProperty ( $id, "locationactive", 1 );
			$site = $id;
		} else {
			$site = $_POST ['site'];
		}

		$current_session = $this->addSession ( $_POST ['sessionname'], $_POST ['beginday'], $_POST ['beginmonth'], $_POST ['beginyear'], $_POST ['beginhours'], $_POST ['beginminutes'], $_POST ['endday'], $_POST ['endmonth'], $_POST ['endyear'], $_POST ['endhours'], $_POST ['endminutes'], $site, $_POST ['weather'], $_POST ['equipment'], $_POST ['comments'], $_POST ['description_language'], $observers, $sessionid );

		if ($_FILES ['picture'] ['tmp_name'] != "") 		// picture to upload
		{
			$upload_dir = $instDir . 'deepsky/sessions';
			$dir = opendir ( $upload_dir );
			$original_image = $_FILES ['picture'] ['tmp_name'];
			$destination_image = $upload_dir . "/" . $current_session . "_resized.jpg";
			require_once $instDir . "common/control/resize.php"; // resize code
			$new_image = image_createThumb ( $original_image, $destination_image, 490, 490, 100 );
			move_uploaded_file ( $_FILES ['picture'] ['tmp_name'], $upload_dir . "/" . $current_session . ".jpg" );
		}
	}
}
?>

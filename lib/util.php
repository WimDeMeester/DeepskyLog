<?php
/**
 * Several handy functions
 *
 * PHP Version 7
 *
 * @category Utilities
 * @package  DeepskyLog
 * @author   DeepskyLog Developers <deepskylog@groups.io>
 * @license  GPL2 <https://opensource.org/licenses/gpl-2.0.php>
 * @link     http://www.deepskylog.org
 */
global $inIndex;
if ((!isset($inIndex)) || (!$inIndex)) {
    include "../../redirect.php";
}

/**
 * Several handy functions
 *
 * @category Utilities
 * @package  DeepskyLog
 * @author   DeepskyLog Developers <deepskylog@groups.io>
 * @license  GPL2 <https://opensource.org/licenses/gpl-2.0.php>
 * @link     http://www.deepskylog.org
 */
class Utils
{
    /**
     * Returns a printable time
     *
     * @param string $thetime The string to print for the time.
     *
     * @return double The value of the time. -1 is not visible, else the time.
     */
    public function hourminuteTimeToValue($thetime)
    {
        if ($thetime == "-") {
            return -1;
        } elseif ($thetime == ":") {
            return -1;
        }
        if (($thepos = strpos($thetime, ":")) === false) {
            return -1;
        }
        if (strpos($thetime, "(") === "0") {
            $thetime = substr($thetime, 1);
        }
        if (!(is_numeric($thehour = substr($thetime, 0, $thepos)))) {
            return -1;
        }
        if (!(is_numeric($theminute = substr($thetime, $thepos + 1, 2)))) {
            return -1;
        }
        return 1 * (($thehour * 100) + $theminute);
    }

    /**
     * Checks if the $theHour is between the two other hours.
     *
     * @param integer $thehour   The hour to check.
     * @param integer $firsthour The hour to check.
     * @param integer $lasthour  The hour to check.
     *
     * @return bool True if the time is between the two other times.
     */
    public function checkNightHourMinuteBetweenOthers(
        $thehour,
        $firsthour,
        $lasthour
    ) {
        $thehourvalue = $this->hourminuteTimeToValue($thehour);
        $thefirstvalue = $this->hourminuteTimeToValue($firsthour);
        $thelastvalue = $this->hourminuteTimeToValue($lasthour);

        if ($thehourvalue < 1200) {
            if ($thelastvalue > 1200) {
                return false;
            }
            if ($thelastvalue < $thehourvalue) {
                return false;
            }
            if ($thefirstvalue > 1200) {
                return true;
            }
            if ($thefirstvalue > $thehourvalue) {
                return false;
            }
            return true;
        } else {
            if ($thefirstvalue < 1200) {
                return false;
            }
            if ($thefirstvalue > $thehourvalue) {
                return false;
            }
            if ($thelastvalue < 1200) {
                return true;
            }
            if ($thelastvalue < $thehourvalue) {
                return false;
            }
            return true;
        }
    }

    /**
     * Checks if there is an overlap between the two given time periods
     *
     * @param integer $firststart  The start of the first time interval.
     * @param integer $firstend    The end of the first time interval.
     * @param integer $secondstart The start of the second time interval.
     * @param integer $secondend   The end of the second time interval.
     *
     * @return bool True if the two time intervals overlap.
     */
    public function checkNightHourMinutePeriodOverlap(
        $firststart,
        $firstend,
        $secondstart,
        $secondend
    ) {
        $firststartvalue = $this->hourminuteTimeToValue($firststart);
        $firstendvalue = $this->hourminuteTimeToValue($firstend);
        $secondstartvalue = $this->hourminuteTimeToValue($secondstart);
        $secondendvalue = $this->hourminuteTimeToValue($secondend);
        if ($secondstartvalue < $secondendvalue) {
            return ((($firststartvalue > $secondstartvalue)
                && ($firststartvalue < $secondendvalue))
                || (($firstendvalue > $secondstartvalue)
                && ($firstendvalue < $secondendvalue))
                || (($firststartvalue < $secondend)
                && ($firstendvalue > $secondendvalue))
                || (($firststartvalue < $secondstartvalue)
                && ($firststartvalue > $firstendvalue))
                | (($firstendvalue > $secondendvalue)
                && ($firststartvalue > $firstendvalue)));
        } else {
            return ($firststartvalue > $secondstartvalue)
                || ($firststartvalue < $secondendvalue)
                || ($firstendvalue > $secondstartvalue)
                || ($firstendvalue < $secondendvalue)
                || (($firststartvalue < $secondstartvalue)
                && ($firstendvalue > $secondendvalue)
                && ($firststartvalue > $firstendvalue));
        }
    }

    /**
     * The constructor
     */
    public function __construct()
    {
        foreach ($_POST as $foo => $bar) {
            if (!is_array($_POST [$foo])) {
                $_POST [$foo] = htmlentities(
                    stripslashes($bar),
                    ENT_COMPAT,
                    "UTF-8",
                    0
                );
            }
        }
        foreach ($_GET as $foo => $bar) {
            $_GET [$foo] = htmlentities(stripslashes($bar), ENT_COMPAT, "UTF-8", 0);
        }
    }


    /**
     * Creates an argo navis file from an array of objects.
     *
     * @param array $result The array with the objects.
     *
     */
    public function argoObjects($result)
    {
        global $objObserver, $loggedUser, $objPresentations, $objAtlas;
        $result = $this->sortResult($result);
        $atlas = $objObserver->getObserverProperty(
            $loggedUser,
            'standardAtlasCode',
            'urano'
        );

        foreach ($result as $key => $valueA) {
            echo "DSL " . $valueA ['objectname'] . "|" .
                    $objPresentations->raArgoToString($valueA['objectra']) .
                    "|" . $objPresentations->decToArgoString(
                        $valueA['objectdecl']
                    ) . "|" . $GLOBALS["argo" . $valueA['objecttype']] .
                    "|" . $objPresentations->presentationInt(
                        $valueA ['objectmagnitude'],
                        99.9,
                        ''
                    ) . "|" . $valueA ['objectsize'] . ";" .
                    $objAtlas->atlasCodes[$atlas] . " " .
                    $valueA[$atlas] . ";" . "CR " . $valueA['objectcontrast']. ";" .
                    $valueA['objectseen'] . ";" .
                    $valueA['objectlastseen'] . "\n";
        }
    }

    /**
     * Check if the given user is an admin.
     *
     * @param string $toCheck The userid to check.
     *
     * @return boolean True id the given user is an admin.
     */
    public function checkAdminOrUserID($toCheck)
    {
        global $loggedUser;
        return ((array_key_exists('admin', $_SESSION)
            && ($_SESSION['admin'] == "yes")) || ($loggedUser == $toCheck));
    }

    /**
     * Check if the key exist in the given array.
     *
     * @param array  $theArray The array to check.
     * @param string $key      The key to check.
     * @param string $default  The default to return if the key was not found.
     *                         The standard value is an empty string.
     *
     * @return string The value of the found key, or the standard value.
     */
    public function checkArrayKey($theArray, $key, $default = '')
    {
        return (array_key_exists($key, $theArray) && ($theArray[$key] != ''))
            ? $theArray [$key] : $default;
    }

    /**
     * Returns a date string YYYYMMDD or MMDD if the date given year or month
     * equals the year or month in the $GET keys.
     *
     * @param integer $year  The year to check.
     * @param integer $month The month to check.
     * @param integer $day   The day to check.
     *
     * @return string The value of the date, in YYYYMMDD format if
     *                $year == GET[$year]
     *                or MMDD if $month == $GET[$month].
     */
    public function checkGetDate($year, $month, $day)
    {
        if ($year = $this->checkGetKey($year)) {
            return sprintf("%04d", $year) .
                sprintf("%02d", $this->checkGetKey($month, '00')) .
                sprintf("%02d", $this->checkGetKey($day, '00'));
        } elseif ($month = $this->checkGetKey($month)) {
            return sprintf("%02d", $month) .
                sprintf("%02d", $this->checkGetKey($day, '00'));
        }
        return '';
    }

    /**
     * Returns a localized date. This means MMDDYYYY for US and DDMMYYYY for Europe
     *
     * @param integer $date The date to convert.
     *
     * @return string The date in localized form.
     */
    public function getLocalizedDate($date)
    {
        global $dateformat;
        $date = sscanf($date, "%4d%2d%2d");
        $dateTimeText = date(
            $dateformat,
            mktime(0, 0, 0, $date[1], $date[2], $date[0])
        );

        return $dateTimeText;
    }

    /**
     * Returns the key from the $GET array. If the key is not found, the $default
     * value is returned.
     *
     * @param string $key     The key to look for.
     * @param string $default The value to return if the key does not exist.
     *                        Default value is ''.
     *
     * @return string The key from the $GET array. If the key is not found,
     *                the $default value is returned.
     */
    public function checkGetKey($key, $default = '')
    {
        return (array_key_exists($key, $_GET) && ($_GET [$key] != ''))
            ? $_GET[$key] : $default;
    }

    /**
     * Returns time or coordinate when the hr, min and sec are in the $GET array.
     * If the keys are not found,  is returned.
     *
     * @param string $hr  The key describing the number of hours.
     * @param string $min The key describing the number of minutes.
     * @param string $sec The key describing the number of seconds.
     *
     * @return integer The time if the hr, min and sec keys exists in the $GET
     *                 array. If the keys are not found, '' is returned.
     */
    public function checkGetTimeOrDegrees($hr, $min, $sec)
    {
        $test = $this->checkGetKey($hr) . $this->checkGetKey($min) .
            $this->checkGetKey($sec);
        if ($test) {
            if (substr($this->checkGetKey($hr), 0, 1) == "-") {
                return -(abs($this->checkGetKey($hr, 0))
                    + ($this->checkGetKey($min, 0) / 60)
                    + ($this->checkGetKey($sec, 0) / 3600));
            } else {
                return $this->checkGetKey($hr, 0)
                    + ($this->checkGetKey($min, 0) / 60)
                    + ($this->checkGetKey($sec, 0) / 3600);
            }
        } else {
            return '';
        }
    }

    /**
     * Checks if the value is in the limits.
     *
     * @param double $value The value to check.
     * @param double $low   The lower limit.
     * @param double $high  The higher limit.
     *
     * @return boolean Checks if the given value lies inside the given limits.
     */
    public function checkLimitsInclusive($value, $low, $high)
    {
        return (($value >= $low) && ($value <= $high));
    }

    /**
     * Returns the key from the $POST array. If the key is not found, the $default
     * value is returned.
     *
     * @param string $key     The key to look for.
     * @param string $default The value to return if the key does not exist.
     *                        Default value is ''.
     *
     * @return string The key from the $POST array. If the key is not found,
     *                the $default value is returned.
     */
    public function checkPostKey($key, $default = '')
    {
        return (array_key_exists($key, $_POST) && ($_POST[$key] != ''))
            ? $_POST[$key] : $default;
    }

    /**
     * Returns the key from the $REQUEST array. If the key is not found,
     * the POST and GET arrays are checked.
     *
     * @param string $key     The key to look for.
     * @param string $default The value to return if the key does not exist.
     *                        Default value is ''.
     *
     * @return string The key from the $REQUEST array. If the key is not found,
     *                the POST and GET arrays are checked.
     */
    public function checkRequestKey($key, $default = '')
    {
        return ((array_key_exists($key, $_REQUEST) && ($_REQUEST[$key] != ''))
            ? $_REQUEST[$key] :
                ((array_key_exists($key, $_POST) && ($_POST [$key] != ''))
            ? $_POST [$key] :
                ((array_key_exists($key, $_GET) && ($_GET [$key] != ''))
            ? $_GET [$key] : $default)));
    }

    /**
     * Returns the key from the $SESSION array. If the key is not found,
     * the $default value is returned.
     *
     * @param string $key     The key to look for.
     * @param string $default The value to return if the key does not exist.
     *                        Default value is ''.
     *
     * @return string The key from the $SESSION array. If the key is not found,
     *                the $default value is returned.
     */
    public function checkSessionKey($key, $default = '')
    {
        return (array_key_exists($key, $_SESSION) && ($_SESSION [$key] != ''))
            ? $_SESSION [$key] : $default;
    }

    /**
     * Checks if the given user is logged in.
     *
     * @param string $toCheck The userid to check.
     *
     * @return boolean True is the given user is logged in.
     */
    public function checkUserID($toCheck)
    {
        global $loggedUser;
        return ($loggedUser == $toCheck);
    }

    /**
     * Encodes the html decoded string to utf8.
     *
     * @param string $string The string to encode.
     *
     * @return string The utf8 encoded string.
     */
    public function safeEncode($string)
    {
        return mb_convert_encoding(html_entity_decode($string), 'ISO-8859-1', 'UTF-8');
    }

    /**
     * Corrects the name for XML export. Changes all unwanted characters to _.
     *
     * @param string $value The string to correct.
     *
     * @return string The corrected string for XML export.
     */
    public function correctForXmlExport($value)
    {
        $correctedValue = $this->safeEncode(
            preg_replace("/\s+/", "_", $value)
        );
        $correctedValue = $this->safeEncode(
            preg_replace("/\+/", "_", $correctedValue)
        );
        $correctedValue = $this->safeEncode(
            preg_replace("/\//", "_", $correctedValue)
        );
        $correctedValue = $this->safeEncode(
            preg_replace("/\,/", "_", $correctedValue)
        );
        $correctedValue = $this->safeEncode(
            preg_replace("/\(/", "_", $correctedValue)
        );
        $correctedValue = $this->safeEncode(
            preg_replace("/\)/", "_", $correctedValue)
        );
        $correctedValue = $this->safeEncode(
            preg_replace("/ /", "_", $correctedValue)
        );

        return $correctedValue;
    }

    /**
     * Returns the color for the component of a double star.
     *
     * @param string $component The component to use.
     *
     * @return string The string with the color or the empty string if there
     *                were no colors entered.
     */
    public function getColorForOalExport($component)
    {
        if ($component > 0) {
            if ($component == 1) {
                $col = "white";
            }
            if ($component == 2) {
                $col = "red";
            }
            if ($component == 3) {
                $col = "orange";
            }
            if ($component == 4) {
                $col = "yellow";
            }
            if ($component == 5) {
                $col = "green";
            }
            if ($component == 6) {
                $col = "blue";
            } else {
                $col = "";
            }
            return $col;
        }
    }

    /**
     * Prints the OAL file from an array of observations.
     *
     * @param array $result The array of observations to use.
     *
     * @return string The OAL file.
     */
    public function comastObservations($result)
    {
        global $objPresentations, $objObservation, $objCatalog, $objSession;
        global $objDatabase, $loggedUser;
        include_once "cometobjects.php";
        include_once "observers.php";
        include_once "instruments.php";
        include_once "locations.php";
        include_once "lenses.php";
        include_once "filters.php";
        include_once "cometobservations.php";
        include_once "icqmethod.php";
        include_once "icqreferencekey.php";
        include_once "catalogs.php";
        include_once "setup/vars.php";
        include_once "setup/databaseInfo.php";

        $observer = $GLOBALS['objObserver'];
        $location = $GLOBALS['objLocation'];

        $dom = new DomDocument('1.0', 'UTF-8');

        $observers = array();
        $sites = array();
        $objects = array();
        $scopes = array();
        $eyepieces = array();
        $lenses = array();
        $filters = array();

        $cntObservers = 0;
        $cntSites = 0;
        $cntObjects = 0;
        $cntScopes = 0;
        $cntEyepieces = 0;
        $cntLens = 0;
        $cntFilter = 0;

        $allObs = $result;

        foreach ($result as $key => $value) {
            $obs = $objObservation->getAllInfoDsObservation($value['observationid']);
            $objectname = $obs['objectname'];
            $observerid = $obs['observerid'];
            $inst = $obs['instrumentid'];
            $loc = $obs['locationid'];
            $visibility = $obs['visibility'];
            $seeing = $obs['seeing'];
            $limmag = $obs['limmag'];
            $filt = $obs['filterid'];
            $eyep = $obs['eyepieceid'];
            $lns = $obs['lensid'];

            if (in_array($observerid, $observers) == false) {
                $observers[$cntObservers] = $observerid;
                $cntObservers = $cntObservers + 1;
            }

            if (in_array($loc, $sites) == false) {
                $sites[$cntSites] = $loc;
                $cntSites = $cntSites + 1;
            }

            if (in_array($objectname, $objects) == false) {
                $objects[$cntObjects] = $objectname;
                $cntObjects = $cntObjects + 1;
            }

            if (in_array($inst, $scopes) == false) {
                $scopes[$cntScopes] = $inst;
                $cntScopes = $cntScopes + 1;
            }

            if (in_array($eyep, $eyepieces) == false) {
                $eyepieces[$cntEyepieces] = $eyep;
                $cntEyepieces = $cntEyepieces + 1;
            }

            if (in_array($lns, $lenses) == false) {
                $lenses [$cntLens] = $lns;
                $cntLens = $cntLens + 1;
            }

            if (in_array($filt, $filters) == false) {
                $filters[$cntFilter] = $filt;
                $cntFilter = $cntFilter + 1;
            }
        }

        // add root fcga -> The header
        $fcgaInfo = $dom->createElement('oal:observations');
        $fcgaDom = $dom->appendChild($fcgaInfo);

        $attr = $dom->createAttribute("version");
        $fcgaInfo->appendChild($attr);

        $attrText = $dom->createTextNode("2.1");
        $attr->appendChild($attrText);

        $attr = $dom->createAttribute("xmlns:oal");
        $fcgaInfo->appendChild($attr);

        $attrText = $dom->createTextNode(
            "http://groups.google.com/group/openastronomylog"
        );
        $attr->appendChild($attrText);

        $attr = $dom->createAttribute("xmlns:xsi");
        $fcgaInfo->appendChild($attr);

        $attrText = $dom->createTextNode(
            "http://www.w3.org/2001/XMLSchema-instance"
        );
        $attr->appendChild($attrText);

        $attr = $dom->createAttribute("xsi:schemaLocation");
        $fcgaInfo->appendChild($attr);

        $attrText = $dom->createTextNode(
            "http://groups.google.com/group/openastronomylog oal21.xsd"
        );
        $attr->appendChild($attrText);

        // add root - <observers>
        $observersDom = $fcgaDom->appendChild($dom->createElement('observers'));

        foreach ($observers as $key => $value) {
            $observer2 = $dom->createElement('observer');
            $observerChild = $observersDom->appendChild($observer2);
            $attr = $dom->createAttribute("id");
            $observer2->appendChild($attr);

            $correctedValue = $this->safeEncode(preg_replace("/\s+/", "_", $value));
            $attrText = $dom->createTextNode("usr_" . $correctedValue);
            $attr->appendChild($attrText);

            $name = $observerChild->appendChild($dom->createElement('name'));
            $name->appendChild(
                $dom->createCDATASection(
                    $this->safeEncode(
                        $observer->getObserverProperty($value, 'firstname')
                    )
                )
            );

            $surname = $observerChild->appendChild($dom->createElement('surname'));
            $surname->appendChild(
                $dom->createCDataSection(
                    $this->safeEncode($observer->getObserverProperty($value, 'name'))
                )
            );

            $account = $observerChild->appendChild($dom->createElement('account'));
            $account->appendChild(
                $dom->createCDataSection($this->safeEncode($value))
            );

            $attr = $dom->createAttribute("name");
            $account->appendChild($attr);

            $attrText = $dom->createTextNode("www.deepskylog.org");
            $attr->appendChild($attrText);

            if ($observer->getObserverProperty($value, 'fstOffset') != 0.0) {
                $fst = $observerChild->appendChild($dom->createElement('fstOffset'));
                $fst->appendChild(
                    $dom->createTextNode(
                        ($observer->getObserverProperty($value, 'fstOffset'))
                    )
                );
            }
        }

        // add root - <sites>
        $observersDom = $fcgaDom->appendChild($dom->createElement('sites'));

        foreach ($sites as $key => $value) {
            $site2 = $dom->createElement('site');
            $siteChild = $observersDom->appendChild($site2);
            $attr = $dom->createAttribute("id");
            $site2->appendChild($attr);

            $attrText = $dom->createTextNode("site_" . $value);
            $attr->appendChild($attrText);

            $name = $siteChild->appendChild($dom->createElement('name'));
            $name->appendChild(
                $dom->createCDATASection(
                    $this->safeEncode(
                        $location->getLocationPropertyFromId($value, 'name')
                    )
                )
            );

            $longitude = $siteChild->appendChild($dom->createElement('longitude'));
            $longitude->appendChild(
                $dom->createTextNode(
                    $location->getLocationPropertyFromId($value, 'longitude')
                )
            );

            $attr = $dom->createAttribute("unit");
            $longitude->appendChild($attr);

            $attrText = $dom->createTextNode("deg");
            $attr->appendChild($attrText);

            $latitude = $siteChild->appendChild($dom->createElement('latitude'));
            $latitude->appendChild(
                $dom->createTextNode(
                    $location->getLocationPropertyFromId($value, 'latitude')
                )
            );

            $attr = $dom->createAttribute("unit");
            $latitude->appendChild($attr);

            $attrText = $dom->createTextNode("deg");
            $attr->appendChild($attrText);

            // ELEVATION
            $elevation = $siteChild->appendChild($dom->createElement('elevation'));
            $elevation->appendChild(
                $dom->createTextNode(
                    $location->getLocationPropertyFromId($value, 'elevation')
                )
            );

            $timezone = $siteChild->appendChild($dom->createElement('timezone'));
            $dateTimeZone = new DateTimeZone(
                $location->getLocationPropertyFromId($value, 'timezone')
            );
            $datestr = "01/01/2008";
            $dateTime = new DateTime($datestr, $dateTimeZone);
            // Returns the time difference in seconds
            $timedifference = $dateTimeZone->getOffset($dateTime);
            $timedifference = $timedifference / 60.0;

            $tz = $location->getLocationPropertyFromId($value, 'timezone');
            if (strncmp($tz, "Etc/GMT", 7) == 0) {
                $timedifference = -$timedifference;
            }

            $timezone->appendChild($dom->createTextNode($timedifference));
        }

        // add root - <sessions> We export all the sessions of the logged observer
        $observersDom = $fcgaDom->appendChild($dom->createElement('sessions'));

        if ($loggedUser != "") {
            $sessions = $objSession->getAllSessionsForUser($loggedUser);

            $usedSessions = array();
            // Only add session for which the location is also exported
            for ($scnt = 0; $scnt < count($sessions); $scnt++) {
                if (in_array($sessions [$scnt]['locationid'], $sites)) {
                    $session = $dom->createElement('session');
                    $sessionChild = $observersDom->appendChild($session);
                    $attr = $dom->createAttribute("id");
                    $session->appendChild($attr);

                    $attrText = $dom->createTextNode("se_" . $sessions[$scnt]['id']);
                    $attr->appendChild($attrText);

                    $attr = $dom->createAttribute("lang");
                    $session->appendChild($attr);

                    $attrText = $dom->createTextNode($sessions[$scnt]['language']);
                    $attr->appendChild($attrText);

                    $begin = $sessionChild->appendChild(
                        $dom->createElement('begin')
                    );
                    $begindate = $sessions[$scnt]['begindate'];
                    $begindate = str_replace(" ", "T", $begindate) . "+00:00";
                    $begin->appendChild($dom->createTextNode($begindate));

                    $end = $sessionChild->appendChild($dom->createElement('end'));
                    $enddate = $sessions[$scnt]['enddate'];
                    $enddate = str_replace(" ", "T", $enddate) . "+00:00";
                    $end->appendChild($dom->createTextNode($enddate));

                    $site = $sessionChild->appendChild($dom->createElement('site'));
                    $site->appendChild(
                        $dom->createTextNode(
                            "site_" . $sessions[$scnt]['locationid']
                        )
                    );

                    $weather = $sessionChild->appendChild(
                        $dom->createElement('weather')
                    );
                    $weather->appendChild(
                        $dom->createCDATASection(
                            $this->safeEncode($sessions[$scnt]['weather'])
                        )
                    );

                    $equipment = $sessionChild->appendChild(
                        $dom->createElement('equipment')
                    );
                    $equipment->appendChild(
                        $dom->createCDATASection(
                            $this->safeEncode($sessions[$scnt]['equipment'])
                        )
                    );

                    $comments = $sessionChild->appendChild(
                        $dom->createElement('comments')
                    );
                    $comments->appendChild(
                        $dom->createCDATASection(
                            $this->safeEncode($sessions[$scnt]['comments'])
                        )
                    );

                    // TODO : Also add images of the session to the export
                    $usedSessions [] = $sessions[$scnt]['id'];
                }
            }
        }

        // add root - <targets>
        $observersDom = $fcgaDom->appendChild($dom->createElement('targets'));

        foreach ($objects as $key => $value) {
            $object2 = $dom->createElement('target');
            $objectChild = $observersDom->appendChild($object2);
            $attr = $dom->createAttribute("id");
            $object2->appendChild($attr);

            $correctedValue = $this->correctForXmlExport($value);
            $attrText = $dom->createTextNode("_" . $correctedValue);
            $attr->appendChild($attrText);

            $attr = $dom->createAttribute("xsi:type");
            $object2->appendChild($attr);

            $object = $GLOBALS ['objObject']->getAllInfoDsObject($value);

            $type = $object["type"];
            if ($type == "OPNCL" || $type == "SMCOC" || $type == "LMCOC") {
                $type = "oal:deepSkyOC";
            } elseif ($type == "GALXY") {
                $type = "oal:deepSkyGX";
            } elseif ($type == "GALCL") {
                $type = "oal:deepSkyCG";
            } elseif ($type == "PLNNB") {
                $type = "oal:deepSkyPN";
            } elseif ($type == "ASTER" || $type == "AA1STAR"
                || $type == "AA3STAR" || $type == "AA4STAR" || $type == "AA8STAR"
            ) {
                $type = "oal:deepSkyAS";
            } elseif ($type == "DS") {
                $type = "oal:deepSkyDS";
            } elseif ($type == "GLOCL" || $type == "GXAGC" || $type == "LMCGC"
                || $type == "SMCGC"
            ) {
                $type = "oal:deepSkyGC";
            } elseif ($type == "BRTNB" || $type == "CLANB" || $type == "EMINB"
                || $type == "ENRNN" || $type == "ENSTR"
                || $type == "GXADN" || $type == "GACAN" || $type == "HII"
                || $type == "LMCCN" || $type == "LMCDN" || $type == "REFNB"
                || $type == "RNHII" || $type == "SMCCN" || $type == "SMCDN"
                || $type == "SNREM" || $type == "STNEB" || $type == "WRNEB"
            ) {
                $type = "oal:deepSkyGN";
            } elseif ($type == "QUASR") {
                $type = "oal:deepSkyQS";
            } elseif ($type == "DRKNB") {
                $type = "oal:deepSkyDN";
            } elseif ($type == "NONEX") {
                $type = "oal:deepSkyNA";
            }
            $attrText = $dom->createTextNode($type);
            $attr->appendChild($attrText);

            $datasource = $objectChild->appendChild(
                $dom->createElement('datasource')
            );
            $datasource->appendChild(
                $dom->createCDATASection($this->safeEncode($object["datasource"]))
            );

            $name = $objectChild->appendChild($dom->createElement('name'));
            $name->appendChild(
                $dom->createCDATASection(($objCatalog->checkObject($value)))
            );

            $altnames = $GLOBALS['objObject']->getAlternativeNames($value);
            foreach ($altnames as $key2 => $value2) {
                // go through names array
                if (trim($value2) != trim($value)) {
                    if (trim($value2) != "") {
                        $alias = $objectChild->appendChild(
                            $dom->createElement('alias')
                        );
                        $alias->appendChild(
                            $dom->createCDataSection(
                                trim($objCatalog->checkObject($value2))
                            )
                        );
                    }
                }
            }

            $position = $objectChild->appendChild($dom->createElement('position'));

            $raDom = $dom->createElement('ra');
            $ra = $position->appendChild($raDom);
            $ra->appendChild($dom->createTextNode($object["ra"] * 15.0));

            $attr = $dom->createAttribute("unit");
            $raDom->appendChild($attr);

            $attrText = $dom->createTextNode("deg");
            $attr->appendChild($attrText);

            $decDom = $dom->createElement('dec');
            $dec = $position->appendChild($decDom);
            $dec->appendChild($dom->createTextNode($object["decl"]));

            $attr = $dom->createAttribute("unit");
            $decDom->appendChild($attr);

            $attrText = $dom->createTextNode("deg");
            $attr->appendChild($attrText);

            $constellation = $objectChild->appendChild(
                $dom->createElement('constellation')
            );
            $constellation->appendChild($dom->createCDATASection(($object["con"])));

            if ($object["diam2"] > 0.0 && $object["diam2"] != 99.9) {
                $sdDom = $dom->createElement('smallDiameter');
                $diam2 = $objectChild->appendChild($sdDom);
                $sDiameter = $object["diam2"] / 60.0;
                $diam2->appendChild($dom->createTextNode($sDiameter));

                $attr = $dom->createAttribute("unit");
                $sdDom->appendChild($attr);

                $attrText = $dom->createTextNode("arcmin");
                $attr->appendChild($attrText);
            }

            $diameter1 = $object["diam1"];
            if ($diameter1 > 0.0 && $diameter1 != 99.9) {
                $ldDom = $dom->createElement('largeDiameter');
                $diam1 = $objectChild->appendChild($ldDom);
                $lDiameter = $diameter1 / 60.0;
                $diam1->appendChild($dom->createTextNode($lDiameter));

                $attr = $dom->createAttribute("unit");
                $ldDom->appendChild($attr);

                $attrText = $dom->createTextNode("arcmin");
                $attr->appendChild($attrText);
            }

            if ($object["mag"] < 99.0) {
                $mag = $objectChild->appendChild($dom->createElement('visMag'));
                $mag->appendChild($dom->createTextNode(($object["mag"])));
            }

            if ($object["subr"] < 99.0) {
                $mag = $objectChild->appendChild($dom->createElement('surfBr'));
                $mag->appendChild($dom->createTextNode(($object["subr"])));

                $attr = $dom->createAttribute("unit");
                $mag->appendChild($attr);

                $attrText = $dom->createTextNode("mags-per-squarearcmin");
                $attr->appendChild($attrText);
            }

            if ($type != "oal:deepSkyCG" && $type != "oal:deepSkyGC"
                && $type != "oal:deepSkyNA" && $type != "oal:deepSkyOC"
                && $type != "oal:deepSkyPN" && $type != "oal:deepSkyQS"
            ) {
                if ($object["pa"] < 999.0) {
                    $pa = $objectChild->appendChild($dom->createElement('pa'));
                    $pa->appendChild($dom->createTextNode(($object ["pa"])));
                }
            }
        }

        // add root - <scopes>
        $observersDom = $fcgaDom->appendChild($dom->createElement('scopes'));

        foreach ($scopes as $key => $value) {
            $fixMag = $GLOBALS ['objInstrument']->getInstrumentPropertyFromId(
                $value,
                'fixedMagnification'
            );
            if ($fixMag != 1) {
                $inName = $GLOBALS ['objInstrument']->getInstrumentPropertyFromId(
                    $value,
                    'name'
                );
                if ($inName != "") {
                    $scope2 = $dom->createElement('scope');
                    $siteChild = $observersDom->appendChild($scope2);
                    $attr = $dom->createAttribute("id");
                    $scope2->appendChild($attr);

                    $attrText = $dom->createTextNode("opt_" . $value);
                    $attr->appendChild($attrText);

                    $attr = $dom->createAttribute("xsi:type");
                    $scope2->appendChild($attr);

                    $fixMag = $GLOBALS['objInstrument']->getInstrumentPropertyFromId(
                        $value,
                        'fixedMagnification'
                    );
                    if ($fixMag > 0) {
                        $typeLong = "oal:fixedMagnificationOpticsType";
                    } else {
                        $typeLong = "oal:scopeType";
                    }
                    $tp = $GLOBALS ['objInstrument']->getInstrumentPropertyFromId(
                        $value,
                        'type'
                    );
                    if ($tp == INSTRUMENTOTHER || $tp == INSTRUMENTREST) {
                        $typeShort = "";
                    } elseif ($tp == INSTRUMENTNAKEDEYE) {
                        $typeShort = "A";
                    } elseif ($tp == INSTRUMENTBINOCULARS
                        || $tp == INSTRUMENTFINDERSCOPE
                    ) {
                        $typeShort = "B";
                    } elseif ($tp == INSTRUMENTREFRACTOR) {
                        $typeShort = "R";
                    } elseif ($tp == INSTRUMENTREFLECTOR) {
                        $typeShort = "N";
                    } elseif ($tp == INSTRUMENTCASSEGRAIN) {
                        $typeShort = "C";
                    } elseif ($tp == INSTRUMENTKUTTER) {
                        $typeShort = "K";
                    } elseif ($tp == INSTRUMENTMAKSUTOV) {
                        $typeShort = "M";
                    } elseif ($tp == INSTRUMENTSCHMIDTCASSEGRAIN) {
                        $typeShort = "S";
                    }

                    if ($typeShort == "B") {
                        $typeLong = "oal:fixedMagnificationOpticsType";
                    }
                    $attrText = $dom->createTextNode($typeLong);
                    $attr->appendChild($attrText);

                    $name = $siteChild->appendChild($dom->createElement('model'));
                    $name->appendChild(
                        $dom->createCDATASection(
                            $this->safeEncode(
                                $GLOBALS['objInstrument']->
                                getInstrumentPropertyFromId(
                                    $value,
                                    'name'
                                )
                            )
                        )
                    );

                    $type = $siteChild->appendChild($dom->createElement('type'));
                    $type->appendChild($dom->createCDATASection(($typeShort)));

                    $aperture = $siteChild->appendChild(
                        $dom->createElement('aperture')
                    );
                    $aperture->appendChild(
                        $dom->createTextNode(
                            $GLOBALS['objInstrument']->getInstrumentPropertyFromId(
                                $value,
                                'diameter'
                            )
                        )
                    );

                    $fixMag = $GLOBALS['objInstrument']->getInstrumentPropertyFromId(
                        $value,
                        'fixedMagnification'
                    );
                    if ($fixMag > 0) {
                        $magnification = $siteChild->appendChild(
                            $dom->createElement('magnification')
                        );
                        $magnification->appendChild(
                            $dom->createTextNode(
                                $GLOBALS ['objInstrument']->
                                getInstrumentPropertyFromId(
                                    $value,
                                    'fixedMagnification'
                                )
                            )
                        );
                    } else {
                        if ($typeShort == "B") {
                            $magnification = $siteChild->appendChild(
                                $dom->createElement('magnification')
                            );
                            $magnification->appendChild($dom->createTextNode("1"));
                        } else {
                            $focalLength = $siteChild->appendChild(
                                $dom->createElement('focalLength')
                            );
                            $focalLength->appendChild(
                                $dom->createTextNode(
                                    $GLOBALS['objInstrument']->
                                    getInstrumentPropertyFromId(
                                        $value,
                                        'fd'
                                    ) * $GLOBALS['objInstrument']
                                        ->getInstrumentPropertyFromId(
                                            $value,
                                            'diameter'
                                        )
                                )
                            );
                        }
                    }
                }
            }
        }

        // add root - <eyepieces>
        $observersDom = $fcgaDom->appendChild($dom->createElement('eyepieces'));

        foreach ($eyepieces as $key => $value) {
            if ($value != "" && $value > 0) {
                $eyepiece2 = $dom->createElement('eyepiece');
                $eyepieceChild = $observersDom->appendChild($eyepiece2);
                $attr = $dom->createAttribute("id");
                $eyepiece2->appendChild($attr);

                $attrText = $dom->createTextNode("ep_" . $value);
                $attr->appendChild($attrText);

                $model = $eyepieceChild->appendChild($dom->createElement('model'));
                $model->appendChild(
                    $dom->createCDATASection(
                        $this->safeEncode(
                            $GLOBALS['objEyepiece']->getEyepiecePropertyFromId(
                                $value,
                                'name'
                            )
                        )
                    )
                );

                $focalLength = $eyepieceChild->appendChild(
                    $dom->createElement('focalLength')
                );
                $focalLength->appendChild(
                    $dom->createTextNode(
                        $GLOBALS['objEyepiece']->getEyepiecePropertyFromId(
                            $value,
                            'focalLength'
                        )
                    )
                );

                $mfl = $GLOBALS['objEyepiece']->getEyepiecePropertyFromId(
                    $value,
                    'maxFocalLength'
                );
                if ($mfl > 0) {
                    $maxFocalLength = $eyepieceChild->appendChild(
                        $dom->createElement('maxFocalLength')
                    );
                    $maxFocalLength->appendChild(
                        $dom->createTextNode(
                            $GLOBALS['objEyepiece']->getEyepiecePropertyFromId(
                                $value,
                                'maxFocalLength'
                            )
                        )
                    );
                }

                $apparentFOV = $eyepieceChild->appendChild(
                    $dom->createElement('apparentFOV')
                );
                $apparentFOV->appendChild(
                    $dom->createTextNode(
                        $GLOBALS['objEyepiece']->getEyepiecePropertyFromId(
                            $value,
                            'apparentFOV'
                        )
                    )
                );

                $attr = $dom->createAttribute("unit");
                $apparentFOV->appendChild($attr);

                $attrText = $dom->createTextNode("deg");
                $attr->appendChild($attrText);
            }
        }

        // add root - <lenses>
        $observersDom = $fcgaDom->appendChild($dom->createElement('lenses'));

        foreach ($lenses as $key => $value) {
            if ($value != "" && $value > 0) {
                $lens2 = $dom->createElement('lens');
                $lensChild = $observersDom->appendChild($lens2);
                $attr = $dom->createAttribute("id");
                $lens2->appendChild($attr);

                $attrText = $dom->createTextNode("le_" . $value);
                $attr->appendChild($attrText);

                $model = $lensChild->appendChild($dom->createElement('model'));
                $model->appendChild(
                    $dom->createCDATASection(
                        $this->safeEncode(
                            $GLOBALS['objLens']->getLensPropertyFromId(
                                $value,
                                'name'
                            )
                        )
                    )
                );

                $factor = $lensChild->appendChild($dom->createElement('factor'));
                $factor->appendChild(
                    $dom->createTextNode(
                        $GLOBALS ['objLens']->getLensPropertyFromId($value, 'factor')
                    )
                );
            }
        }

        // add root - <filters>
        $observersDom = $fcgaDom->appendChild($dom->createElement('filters'));

        foreach ($filters as $key => $value) {
            if ($value != "" && $value > 0) {
                $filter2 = $dom->createElement('filter');
                $filterChild = $observersDom->appendChild($filter2);
                $attr = $dom->createAttribute("id");
                $filter2->appendChild($attr);

                $attrText = $dom->createTextNode("flt_" . $value);
                $attr->appendChild($attrText);

                $model = $filterChild->appendChild($dom->createElement('model'));
                $model->appendChild(
                    $dom->createCDATASection(
                        $this->safeEncode(
                            $GLOBALS['objFilter']->getFilterPropertyFromId(
                                $value,
                                'name'
                            )
                        )
                    )
                );

                $tp = $GLOBALS['objFilter']->getFilterPropertyFromId($value, 'type');
                if ($tp == 0) {
                    $filType = "other";
                } elseif ($tp == 1) {
                    $filType = "broad band";
                } elseif ($tp == 2) {
                    $filType = "narrow band";
                } elseif ($tp == 3) {
                    $filType = "O-III";
                } elseif ($tp == 4) {
                    $filType = "H-beta";
                } elseif ($tp == 5) {
                    $filType = "H-alpha";
                } elseif ($tp == 6) {
                    $filType = "color";
                } elseif ($tp == 7) {
                    $filType = "neutral";
                } elseif ($tp == 8) {
                    $filType = "corrective";
                }

                $type = $filterChild->appendChild($dom->createElement('type'));
                $type->appendChild($dom->createCDATASection($filType));

                if ($filType == "color") {
                    $col = $GLOBALS['objFilter']->getFilterPropertyFromId(
                        $value,
                        'color'
                    );
                    if ($col == 1) {
                        $colName = "light red";
                    } elseif ($col == 2) {
                        $colName = "red";
                    } elseif ($col == 3) {
                        $colName = "deep red";
                    } elseif ($col == 4) {
                        $colName = "orange";
                    } elseif ($col == 5) {
                        $colName = "light yellow";
                    } elseif ($col == 6) {
                        $colName = "deep yellow";
                    } elseif ($col == 7) {
                        $colName = "yellow";
                    } elseif ($col == 8) {
                        $colName = "yellow-green";
                    } elseif ($col == 9) {
                        $colName = "light green";
                    } elseif ($col == 10) {
                        $colName = "green";
                    } elseif ($col == 11) {
                        $colName = "medium blue";
                    } elseif ($col == 12) {
                        $colName = "pale blue";
                    } elseif ($col == 13) {
                        $colName = "blue";
                    } elseif ($col == 14) {
                        $colName = "deep blue";
                    } elseif ($col == 15) {
                        $colName = "violet";
                    }
                    if ($colName != "") {
                        $color = $filterChild->appendChild(
                            $dom->createElement('color')
                        );
                        $color->appendChild($dom->createCDATASection($colName));
                    }

                    $wf = $GLOBALS['objFilter']->getFilterPropertyFromId(
                        $value,
                        'wratten'
                    );
                    if ($wf != "") {
                        $wratten = $filterChild->appendChild(
                            $dom->createElement('wratten')
                        );
                        $wratten->appendChild(
                            $dom->createCDATASection(
                                $GLOBALS['objFilter']->getFilterPropertyFromId(
                                    $value,
                                    'wratten'
                                )
                            )
                        );
                    }

                    $sf = $GLOBALS ['objFilter']->getFilterPropertyFromId(
                        $value,
                        'schott'
                    );
                    if ($sf != "") {
                        $schott = $filterChild->appendChild(
                            $dom->createElement('schott')
                        );
                        $schott->appendChild(
                            $dom->createCDATASection(
                                $GLOBALS['objFilter']->getFilterPropertyFromId(
                                    $value,
                                    'schott'
                                )
                            )
                        );
                    }
                }
            }
        }

        // add root - <imagers> DeepskyLog has no imagers
        $observersDom = $fcgaDom->appendChild($dom->createElement('imagers'));

        // Add the observations.
        foreach ($allObs as $key => $value) {
            $obs = $GLOBALS['objObservation']->getAllInfoDsObservation(
                $value['observationid']
            );
            $objectname = $obs['objectname'];
            $observerid = $obs['observerid'];
            $inst = $obs['instrumentid'];
            $loc = $obs['locationid'];
            $visibility = $obs['visibility'];
            $seeing = $obs['seeing'];
            $limmag = $obs['limmag'];
            $filt = $obs['filterid'];
            $eyep = $obs['eyepieceid'];
            $lns = $obs['lensid'];

            $observation = $fcgaDom->appendChild($dom->createElement('observation'));
            $attr = $dom->createAttribute("id");
            $observation->appendChild($attr);

            $attrText = $dom->createTextNode("obs_" . $value ['observationid']);
            $attr->appendChild($attrText);

            $correctedValue = $this->safeEncode(
                preg_replace("/\s+/", "_", $observerid)
            );
            $observer = $observation->appendChild($dom->createElement('observer'));
            $observer->appendChild($dom->createTextNode("usr_" . $correctedValue));

            $site = $observation->appendChild($dom->createElement('site'));
            $site->appendChild($dom->createTextNode("site_" . $loc));

            // Check whether this observation is part of a session...
            for ($scnt = 0; $scnt < count($usedSessions); $scnt++) {
                $sessionObs = $objDatabase->selectRecordsetArray(
                    "select * from sessionObservations where sessionid = \"" .
                    $usedSessions [$scnt] . "\" and observationid = \"" .
                    $value ['observationid'] . "\""
                );

                if (count($sessionObs) >= 1) {
                    $session = $observation->appendChild(
                        $dom->createElement('session')
                    );
                    $session->appendChild(
                        $dom->createTextNode("se_" . $usedSessions[$scnt])
                    );
                }
            }

            $target = $observation->appendChild(
                $dom->createElement('target')
            );
            $correctedValue = $this->correctForXmlExport(
                $objCatalog->checkObject($objectname)
            );

            $target->appendChild($dom->createTextNode("_" . $correctedValue));

            if ($obs ["time"] >= 0) {
                $time = sprintf(
                    "T%02d:%02d:00+00:00",
                    (int)($obs["time"] % 2400 / 100),
                    $obs["time"] % 2400 - (int)($obs["time"] % 2400 / 100) * 100
                );
            } else {
                $time = "T22:00:00+00:00";
            }

            $year = ( int ) ($obs ["date"] / 10000);
            $month = ( int ) (($obs ["date"] - $year * 10000) / 100);
            $day = ( int ) (($obs ["date"] - $year * 10000 - $month * 100));
            if ($day == 0) {
                $day = 1;
            } elseif ($day > 31) {
                $day = 31;
            }
            $date = sprintf("%4d-%02d-%02d", $year, $month, $day);

            $begin = $observation->appendChild($dom->createElement('begin'));
            $begin->appendChild($dom->createTextNode($date . $time));

            if ($obs["SQM"] > 0) {
                $magPerSquareArcsecond = $observation->appendChild(
                    $dom->createElement('sky-quality')
                );
                $magPerSquareArcsecond->appendChild(
                    $dom->createTextNode($obs["SQM"])
                );

                $attr = $dom->createAttribute("unit");
                $magPerSquareArcsecond->appendChild($attr);

                $attrText = $dom->createTextNode("mags-per-squarearcsec");
                $attr->appendChild($attrText);
            } elseif ($obs["limmag"] > 0) {
                $faintestStar = $observation->appendChild(
                    $dom->createElement('faintestStar')
                );
                $faintestStar->appendChild($dom->createTextNode($obs["limmag"]));
            }

            if ($obs["seeing"] > 0) {
                $seeing = $observation->appendChild($dom->createElement('seeing'));
                $seeing->appendChild($dom->createTextNode($obs["seeing"]));
            }

            $fm = $GLOBALS['objInstrument']->getInstrumentPropertyFromId(
                $inst,
                'fixedMagnification'
            );
            if ($fm != 1) {
                $scope = $observation->appendChild($dom->createElement('scope'));
                $scope->appendChild($dom->createTextNode("opt_" . $inst));
            }

            if ($eyep > 0) {
                $eyepiece = $observation->appendChild(
                    $dom->createElement('eyepiece')
                );
                $eyepiece->appendChild($dom->createTextNode("ep_" . $eyep));
            }

            if ($lns > 0) {
                $lens = $observation->appendChild($dom->createElement('lens'));
                $lens->appendChild($dom->createTextNode("le_" . $lns));
            }

            if ($filt > 0) {
                $filter = $observation->appendChild($dom->createElement('filter'));
                $filter->appendChild($dom->createTextNode("flt_" . $filt));
            }

            $magni = 0;
            $fm = $GLOBALS['objInstrument']->getInstrumentPropertyFromId(
                $inst,
                'fixedMagnification'
            );
            if ($fm > 0) {
                $magni = $GLOBALS['objInstrument']->getInstrumentPropertyFromId(
                    $inst,
                    'fixedMagnification'
                );
            } elseif ($obs["magnification"] > 0) {
                $magni = $obs["magnification"];
            } elseif ($eyep > 0 && $fm != 0) {
                $factor = 1.0;
                $lf = $GLOBALS['objLens']->getFilterPropertyFromId($lns, 'factor');
                if ($lf > 0) {
                    $factor = $GLOBALS['objLens']->getFilterPropertyFromId(
                        $lns,
                        'factor'
                    );
                }
                $magni = sprintf(
                    "%.2f",
                    $GLOBALS['objInstrument']->getInstrumentPropertyFromId(
                        $inst,
                        'fixedMagnification'
                    )
                    * $GLOBALS['objInstrument']->getInstrumentPropertyFromId(
                        $inst,
                        'diameter'
                    )
                    * $factor / $GLOBALS['objEyepiece']->getEyepiecePropertyFromId(
                        $eyep,
                        'focalLength'
                    )
                );
            }

            // Replace , with .
            $magni = str_replace(",", ".", $magni);

            if ($magni > 0) {
                $magnification = $observation->appendChild(
                    $dom->createElement('magnification')
                );
                $magnification->appendChild($dom->createTextNode((int) $magni));
            }

            $result = $observation->appendChild($dom->createElement('result'));

            if ($obs["extended"] > 0) {
                $attr = $dom->createAttribute("extended");
                $result->appendChild($attr);

                $attrText = $dom->createTextNode("true");
                $attr->appendChild($attrText);
            }

            $attr = $dom->createAttribute("lang");
            $result->appendChild($attr);

            $attrText = $dom->createTextNode($obs["language"]);
            $attr->appendChild($attrText);

            if ($obs["mottled"] > 0) {
                $attr = $dom->createAttribute("mottled");
                $result->appendChild($attr);

                $attrText = $dom->createTextNode("true");
                $attr->appendChild($attrText);
            }

            $object = $GLOBALS['objObject']->getAllInfoDsObject($objectname);

            $type = $object["type"];

            if ($type == "OPNCL" || $type == "SMCOC" || $type == "LMCOC") {
                if ($obs["partlyUnresolved"] > 0) {
                    $attr = $dom->createAttribute("partlyUnresolved");
                    $result->appendChild($attr);

                    $attrText = $dom->createTextNode("true");
                    $attr->appendChild($attrText);
                }

                if ($obs["unusualShape"] > 0) {
                    $attr = $dom->createAttribute("unusualShape");
                    $result->appendChild($attr);

                    $attrText = $dom->createTextNode("true");
                    $attr->appendChild($attrText);
                }

                if ($obs["colorContrasts"] > 0) {
                    $attr = $dom->createAttribute("colorContrasts");
                    $result->appendChild($attr);

                    $attrText = $dom->createTextNode("true");
                    $attr->appendChild($attrText);
                }
            }

            if ($type == "DS") {
                if ($obs["equalBrightness"] >= 0) {
                    $attr = $dom->createAttribute("equalBrightness");
                    $result->appendChild($attr);

                    if ($obs["equalBrightness"] == 0) {
                        $attrText = $dom->createTextNode("false");
                    } else {
                        $attrText = $dom->createTextNode("true");
                    }
                    $attr->appendChild($attrText);
                }

                if ($obs["niceField"] >= 0) {
                    $attr = $dom->createAttribute("niceSurrounding");
                    $result->appendChild($attr);

                    if ($obs["niceField"] == 0) {
                        $attrText = $dom->createTextNode("false");
                    } else {
                        $attrText = $dom->createTextNode("true");
                    }
                    $attr->appendChild($attrText);
                }

                $col = $this->getColorForOalExport("component1");

                if ($col != "") {
                    $colorMain = $result->appendChild(
                        $dom->createElement('colorMain')
                    );
                    $colorMain->appendChild($dom->createTextNode($col1));
                }

                $col = $this->getColorForOalExport("component2");

                if ($col != "") {
                    $colorCompanion = $result->appendChild(
                        $dom->createElement('colorCompanion')
                    );
                    $colorCompanion->appendChild($dom->createTextNode($col2));
                }
            }

            if ($obs["resolved"] > 0) {
                $attr = $dom->createAttribute("resolved");
                $result->appendChild($attr);

                $attrText = $dom->createTextNode("true");
                $attr->appendChild($attrText);
            }

            if ($obs["stellar"] > 0) {
                $attr = $dom->createAttribute("stellar");
                $result->appendChild($attr);

                $attrText = $dom->createTextNode("true");
                $attr->appendChild($attrText);
            }

            $attr = $dom->createAttribute("xsi:type");
            $result->appendChild($attr);

            $object = $GLOBALS['objObject']->getAllInfoDsObject($objectname);

            $type = $object["type"];
            if ($type == "OPNCL" || $type == "SMCOC" || $type == "LMCOC") {
                $type = "oal:findingsDeepSkyOCType";
            } elseif ($type == "DS") {
                $type = "oal:findingsDeepSkyDSType";
            } else {
                $type = "oal:findingsDeepSkyType";
            }
            $attrText = $dom->createTextNode($type);
            $attr->appendChild($attrText);

            $description = $result->appendChild($dom->createElement('description'));
            $description->appendChild(
                $dom->createCDATASection(
                    mb_convert_encoding(
                        $objPresentations->br2nl(
                            html_entity_decode($obs["description"])
                        ),
                        'ISO-8859-1',
                        'UTF-8'
                    )
                )
            );

            $rat = $obs["visibility"];
            if ($rat == 0) {
                $rat = 99;
            }

            if ($obs["smallDiameter"] > 0) {
                $smallDiameter = $result->appendChild(
                    $dom->createElement('smallDiameter')
                );
                $smallDiameter->appendChild(
                    $dom->createTextNode($obs["smallDiameter"])
                );

                $attr = $dom->createAttribute("unit");
                $smallDiameter->appendChild($attr);

                $attrText = $dom->createTextNode("arcsec");
                $attr->appendChild($attrText);
            }

            if ($obs["largeDiameter"] > 0) {
                $largeDiameter = $result->appendChild(
                    $dom->createElement('largeDiameter')
                );
                $largeDiameter->appendChild(
                    $dom->createTextNode($obs["largeDiameter"])
                );

                $attr = $dom->createAttribute("unit");
                $largeDiameter->appendChild($attr);

                $attrText = $dom->createTextNode("arcsec");
                $attr->appendChild($attrText);
            }

            $rating = $result->appendChild($dom->createElement('rating'));
            $rating->appendChild($dom->createTextNode($rat));

            if ($obs["clusterType"] != "" && $obs["clusterType"] != 0) {
                $character = $result->appendChild($dom->createElement('character'));
                $character->appendChild(
                    $dom->createCDATASection($obs["clusterType"])
                );
            }
        }

        // generate xml
        $dom->formatOutput = true; // set the formatOutput attribute of
        // domDocument to true
        // save XML as string or file
        $xmloutput = $dom->saveXML();  // put string in test1

        print $xmloutput;
    }

    /**
     * Creates a csv file from an array of objects.
     *
     * @param array $result The array of objects.
     *
     * @return string The string with csv file with the objects.
     */
    public function csvObjects($result)
    {
        global $objObject, $objPresentations, $objObserver, $loggedUser;
        $result = $this->sortResult($result);
        echo html_entity_decode(_("Name;Altname;RA;Decl;Constellation;Type;Magnitude;SurfaceBrightness;Diameter;Position Angle;Page;ContrastReserve;OptimalMagnification;Seen;Last Seen")) . "\n";
        foreach ($result as $key => $valueA) {
            $alt = "";
            $alts = $objObject->getAlternativeNames($valueA['objectname']);
            foreach ($alts as $key => $value) {
                if ($value != $valueA['objectname']) {
                    $alt .= " - " . trim($value);
                }
            }
            $alt = ($alt ? substr($alt, 3) : '');
            echo $valueA['objectname'] . ";" . $alt . ";"
                . $objPresentations->raToStringHMS($valueA['objectra']) . ";"
                . $objPresentations->decToStringDegMinSec($valueA ['objectdecl'], 0)
                . ";" . $GLOBALS[$valueA['objectconstellation']] . ";"
                . $GLOBALS[$valueA['objecttype']] . ";"
                . $objPresentations->presentationInt1(
                    $valueA['objectmagnitude'],
                    99.9,
                    ''
                ) . ";" . $objPresentations->presentationInt1(
                    $valueA['objectsurfacebrightness'],
                    99.9,
                    ''
                ) . ";"
                . $valueA['objectsize'] . ";"
                . $objPresentations->presentationInt($valueA['objectpa'], 999, '')
                . ";" . $valueA[$objObserver->getObserverProperty(
                    $loggedUser,
                    'standardAtlasCode',
                    'urano'
                )] . ";" . $valueA['objectcontrast'] . ";"
                . trim(
                    preg_replace('/\s+/', ' ', $valueA['objectoptimalmagnification'])
                )
                . ";" . $valueA['objectseen'] . ";"
                . $valueA['objectlastseen'] . "\n";
        }
    }

    /**
     * Creates a skylist file from an array of objects.
     *
     * @param array $result The array of objects.
     *
     * @return string The string with skySafari file with the objects.
     */
    public function skylistObjects($result)
    {
        global $objObject, $objPresentations, $objObserver, $loggedUser;
        $result = $this->sortResult($result);

        echo "SkySafariObservingListVersion=3.0\n";

        foreach ($result as $key => $row) {
            echo "\n";
            echo "SkyObject=BeginObject\n";

            $objectType = $row['objecttype'];
            $objectId = $this->getSkyListObjectId($objectType);

            $objectNames = $this->getObjectNames($row, $objectId);

            echo "   ObjectID=" . $objectId . ",-1,-1" . "\n";
            foreach ($objectNames as $key => $objectName) {
                echo "   CatalogNumber=" . $objectName . "\n";
            }

            echo "EndObject=SkyObject\n";
        }
    }

    /**
     * Returns a list of objectnames. Will return the name of the object if the
     * name starts with  M, NGC, IC, C, Cr, Tr, STF, STFA, HD, Mel, or SAO.
     * If the object does not belong in one of these catalogues, we return an
     * array with all the names / alternative names of this object. Used in the
     * export to skylist format (SkySafari).
     *
     * @param array   $row      The array with the objectnames.
     * @param integer $objectId The id of the type of the object in SkySafari.
     *
     * @return array An array with the object names.
     */
    public function getObjectNames($row, &$objectId)
    {
        global $objObject;

        $objectNames = [];
        $names = [];

        // Fix the name of the object.
        $objectName = $this->fixObjectName(trim($row['objectname']), $objectId);
        // Add the name of the object to the names array.
        array_push($names, $objectName);

        // Get all the alternative names for the given object.
        $altNames = $objObject->getAlternativeNames($row['objectname']);
        // Loop over all the alternative names
        foreach ($altNames as $key => $altName) {
            // Fix the names
            $altName = $this->fixObjectName(trim($altName), $objectId);
            // If name is not the same as the objectname, we add the alternative
            // name to the names array.
            if ($altName != $objectName) {
                array_push($names, trim($altName));
            }
        }

        $objectName = "";
        reset($names);
        // We loop over the names array.
        foreach ($names as $key => $name) {
            if (preg_match("/(?i)^(M|NGC|IC|C|Cr|Tr|STF|STFA|HD)\s*\d+$/", $name)) {
                // These catalogs are best known to Skysafari
                $objectName = $name;
                break;
            } elseif (preg_match("/(?i)^(Mel|SAO)\s*\d+$/", $name)) {
                // Then these
                $objectName = $name;
                break;
            }
        }
        // objectname is known if one of the names was from the M, NGC, IC,
        // ... , Mel or SAO catalogues.
        if ($objectName != "") {
            // if the objectname was known, add the objectname to the
            // objectNames array.
            array_push($objectNames, $objectName);
        } else {
            reset($names);
            // Loop over the objectnames and add all names and alternative names.
            foreach ($names as $key => $name) {
                array_push($objectNames, $name);
            }
        }

        return $objectNames;
    }

    /**
     * Returns the corrected name of the object. Used in the export to skylist
     * format (SkySafari).
     *
     * @param string  $objectName The name of the object.
     * @param integer $objectId   The id of the type of the object in SkySafari.
     *
     * @return string The corrected name of the object.
     */
    public function fixObjectName($objectName, &$objectId)
    {
        $regexPK = "/(?i)^PK(\s*)(\d+)(\+|-)(\d+)(\.)(0*)(\d*)/";
        $regexMi = "/(?i)^Mi\s*(\d+)-(\d+)$/";
        $regexSteph = "/(?i)^Steph\s*(\d+)$/";

        $objectName = trim($objectName);

        if (preg_match($regexPK, $objectName)) {
            $objectName = preg_replace($regexPK, "PK $2$3$4$5$7", $objectName);
        } elseif (preg_match($regexMi, $objectName)) {
            $objectName = preg_replace($regexMi, "Minkowski $1-$2", $objectName);
        } elseif (preg_match($regexSteph, $objectName)) {
            $objectName = preg_replace($regexSteph, "Stephenson $1", $objectName);
        } elseif ($objectName == "Beta Cyg") {
            $objectName = "Beta1 Cygni";
        } elseif ($objectName == "Dddm 1") {
            $objectName = "KO 1";
        } elseif ($objectName == "Stephenson 1") {
            $objectName = "HD 175426";
            $objectId = 2;
        }

        return $objectName;
    }

    /**
     * Returns the corrected name of the object. Used in the export to skylist
     * format (SkySafari).
     *
     * @param string $haystack The string to search in.
     * @param string $needle   The id of the type of the object in SkySafari.
     *
     * @return boolean True if the haystack starts with the needle.
     */
    public function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Returns the object type in skysafari format.
     * 2 for a double or multiple star, 4 for a deepsky object.
     *
     * @param string $objectType The DeepskyLog object type.
     *
     * @return integer 2 for double / multiple star, 4 for deepsky.
     */
    public function getSkyListObjectId($objectType)
    {
        if (preg_match("/(?i)^AA\d+STAR$/", $objectType)) {
            return 2;
        } elseif ($objectType == "DS") {
            return 2;
        } else {
            return 4;
        }
    }

    /**
     * Returns the julian day when the normal day (and optionally time) is given.
     *
     * @param string  $dateYYYYMMDD The date in the YYYYMMDD format.
     * @param integer $timeHHMM     The time in HHMM format, or null.
     *
     * @return integer The julian day.
     */
    public function getJulianDay($dateYYYYMMDD, $timeHHMM = null)
    {
        $dateStrArray = sscanf($dateYYYYMMDD, "%4d%2d%2d");
        $dateStr = $dateStrArray[2] . "-" . $dateStrArray[1] . "-"
            . $dateStrArray[0];
        $date = strtotime($dateStr);

        if ($timeHHMM != null) {
            $timeStr = $timeHHMM;
            if ($timeStr >= "0") {
                $hours = (int)($timeStr / 100);
                $minutes = $timeStr - (100 * $hours);
                $timeStr = sprintf("%d:%02d", $hours, $minutes);
            } else {
                $timeStr = "";
            }
            $time = strtotime($timeStr);
            $dayFraction = -0.5 + ($time % (24 * 60 * 60)) / (24 * 60 * 60);
        } else {
            $dayFraction = 0;
        }

        return unixtojd($date) + $dayFraction;
    }

    /**
     * Creates a CSV file with the observations.
     *
     * @param array $result The array with the observations.
     *
     * @return string A csv file with the observations.
     */
    public function csvObservations($result)
    {
        global $objLens, $objFilter, $objEyepiece, $objLocation, $objPresentations;
        global $objObservation, $objObserver, $objInstrument;
        foreach ($result as $key => $value) {
            $obs = $objObservation->getAllInfoDsObservation($value['observationid']);
            $date = sscanf($obs ['date'], "%4d%2d%2d");
            $time = $obs['time'];
            if ($time >= "0") {
                $hours = (int) ($time / 100);
                $minutes = $time - (100 * $hours);
                $time = sprintf("%d:%02d", $hours, $minutes);
            } else {
                $time = "";
            }
            echo html_entity_decode($obs['objectname'])
                . ";" . html_entity_decode(
                    $objObserver->getObserverProperty(
                        $obs['observerid'],
                        'firstname'
                    ) . " " . $objObserver->getObserverProperty(
                        $obs['observerid'],
                        'name'
                    )
                ) . ";"
                . $date[2] . "-" . $date[1] . "-" . $date[0] . ";" . $time . ";"
                . html_entity_decode(
                    $objLocation->getLocationPropertyFromId(
                        $obs['locationid'],
                        'name'
                    )
                ) . ";" . html_entity_decode(
                    $objInstrument->getInstrumentPropertyFromId(
                        $obs['instrumentid'],
                        'name'
                    )
                ) . ";" . html_entity_decode(
                    $objEyepiece->getEyepiecePropertyFromId(
                        $obs['eyepieceid'],
                        'name'
                    )
                )
                . ";" . html_entity_decode(
                    $objFilter->getFilterPropertyFromId($obs['filterid'], 'name')
                ) . ";" . html_entity_decode(
                    $objLens->getLensPropertyFromId($obs['lensid'], 'name')
                ) . ";" . $obs['seeing'] . ";" . $obs['limmag'] . ";" . $obs['SQM'] . ";"
                . $objPresentations->presentationInt($obs['visibility'], "0", "")
                . ";" . $obs['language'] . ";"
                . preg_replace(
                    "/(\")/",
                    "",
                    preg_replace(
                        "/(\r\n|\n|\r)/",
                        "",
                        preg_replace(
                            "/;/",
                            ",",
                            $objPresentations->br2nl(
                                html_entity_decode(
                                    $obs['description'],
                                    ENT_COMPAT,
                                    'UTF-8'
                                )
                            )
                        )
                    )
                ) . "\n";
        }
    }

    /**
     * Creates a skylist file from an array of observations.
     *
     * @param array $result The array of objects.
     *
     * @return string The string with skySafari file with the observations.
     */
    public function skylistObservations($result)
    {
        global $objLens, $objFilter, $objEyepiece, $objLocation, $objPresentations;
        global $objObservation, $objObserver, $objInstrument;

        echo "SkySafariObservingListVersion=3.0\n";

        foreach ($result as $key => $value) {
            $obs = $objObservation->getAllInfoDsObservation($value['observationid']);

            echo "\n";
            echo "SkyObject=BeginObject\n";

            $objectId = $this->getSkyListObjectId($value['objecttype']);
            echo "   ObjectID=" . $objectId . ",-1,-1" . "\n";

            $objectNames = $this->getObjectNames($obs, $objectId);
            foreach ($objectNames as $key => $objectName) {
                echo "   CatalogNumber=" . $objectName . "\n";
            }

            echo "   DateObserved="
                . $this->getJulianDay($obs['date'], $obs['time']) . "\n";
            echo "   Location="
                . html_entity_decode(
                    $objLocation->getLocationPropertyFromId(
                        $obs['locationid'],
                        'name'
                    )
                ) . "\n";
            echo "   Comment=" . preg_replace(
                "/(\")/",
                "",
                preg_replace(
                    "/(\r\n|\n|\r)/",
                    "",
                    preg_replace(
                        "/;/",
                        ",",
                        $objPresentations->br2nl(
                            html_entity_decode(
                                $obs['description'],
                                ENT_COMPAT,
                                'UTF-8'
                            )
                        )
                    )
                )
            ) . "\n";
            $instrument = html_entity_decode(
                $objInstrument->getInstrumentPropertyFromId(
                    $obs['instrumentid'],
                    'name'
                )
            );
            $eyepiece = html_entity_decode(
                $objEyepiece->getEyepiecePropertyFromId($obs['eyepieceid'], 'name')
            );
            $lens = html_entity_decode(
                $objLens->getLensPropertyFromId($obs['lensid'], 'name')
            );
            $filter = html_entity_decode(
                $objFilter->getFilterPropertyFromId($obs['filterid'], 'name')
            );
            echo "   Equipment=" . $instrument;
            if (!empty($eyepiece)) {
                echo ", " . $eyepiece;
            }
            if (!empty($lens)) {
                echo ", " . $lens;
            }
            if (!empty($filter)) {
                echo ", " . $filter;
            }
            echo("\n");

            echo "EndObject=SkyObject\n";
        }
    }

    /**
     * Creates a pdf file from an array of comet observations.
     *
     * @param array $result The array of comet observations.
     *
     * @return string The pdf file with the comet observations.
     */
    public function pdfCometObservations($result)
    {
        include_once "cometobjects.php";
        include_once "observers.php";
        include_once "instruments.php";
        include_once "locations.php";
        include_once "cometobservations.php";
        include_once "icqmethod.php";
        include_once "icqreferencekey.php";
        include_once "setup/vars.php";
        include_once "setup/databaseInfo.php";
        global $instDir, $objCometObject, $loggedUser, $dateformat;
        $result = $this->sortResult($result);

        $objects = new CometObjects();
        $observer = new Observers();
        $instrument = new Instruments();
        $observation = new CometObservations();
        $location = new Locations();
        $util = $this;
        $ICQMETHODS = new ICQMETHOD();
        $ICQREFERENCEKEYS = new ICQREFERENCEKEY();
        $_GET['pdfTitle'] = "CometObservations.pdf";
        // Create pdf file
        $pdf = new Cezpdf('a4', 'portrait');
        $pdf->ezStartPageNumbers(300, 20, 10);

        $fontdir = $instDir . 'lib/fonts/Helvetica.afm';
        $pdf->selectFont($fontdir);
        $pdf->ezText(mb_convert_encoding((html_entity_decode(_("DeepskyLog Comet Observations"))) . "\n", 'ISO-8859-1', 'UTF-8'));

        foreach ($result as $key => $value) {
            $objectname = $objCometObject->getName(
                $observation->getObjectId($value)
            );

            $pdf->ezText(html_entity_decode($objectname), "14");

            $observerid = $observation->getObserverId($value);

            if ($observer->getObserverProperty($loggedUser, 'UT')) {
                $date = sscanf($observation->getDate($value), "%4d%2d%2d");
                $time = $observation->getTime($value);
            } else {
                $date = sscanf($observation->getLocalDate($value), "%4d%2d%2d");
                $time = $observation->getLocalTime($value);
            }
            $hour = (int) ($time / 100);
            $minute = $time - $hour * 100;
            $formattedDate = date(
                $dateformat,
                mktime(0, 0, 0, $date[1], $date[2], $date[0])
            );

            if ($minute < 10) {
                $minute = "0" . $minute;
            }

            $observername = sprintf(
                _("Observed by %s on %s"),
                $observer->getObserverProperty($observerid, 'firstname') . " " .
                $observer->getObserverProperty($observerid, 'name'),
                $formattedDate . " (" . $hour . ":" . $minute . ")"
            );

            $pdf->ezText(html_entity_decode($observername), "12");

            // Location and instrument
            if (($observation->getLocationId($value) != 0
                && $observation->getLocationId($value) != 1)
                || $observation->getInstrumentId($value) != 0
            ) {
                if ($observation->getLocationId($value) != 0
                    && $observation->getLocationId($value) != 1
                ) {
                    $locationname = _("Location") . " : "
                        . $location->getLocationPropertyFromId(
                            $observation->getLocationId($value),
                            'name'
                        );
                    $extra = ", ";
                } else {
                    $locationname = "";
                }

                if ($observation->getInstrumentId($value) != 0) {
                    $instr = $instrument->getInstrumentPropertyFromId(
                        $observation->getInstrumentId($value),
                        'name'
                    );
                    if ($instr == "Naked eye") {
                        $instr = _("Naked Eye");
                    }

                    $locationname = $locationname . $extra
                        . html_entity_decode(_("Instrument")) . " : " . $instr;

                    if (strcmp($observation->getMagnification($value), "") != 0) {
                        $locationname = $locationname . " ("
                            . $observation->getMagnification($value) . " x)";
                    }
                }

                $pdf->ezText(html_entity_decode($locationname), "12");
            }

            // Method
            $method = $observation->getMethode($value);

            if (strcmp($method, "") != 0) {
                $methodstr = html_entity_decode(_("Magnitude method"))
                    . " : " . $method . " - "
                    . $ICQMETHODS->getDescription($method);

                $pdf->ezText(html_entity_decode($methodstr), "12");
            }

            // Used chart
            $chart = $observation->getChart($value);

            if (strcmp($chart, "") != 0) {
                $chartstr = html_entity_decode(_("Magnitude reference chart"))
                    . " : " . $chart . " - "
                    . $ICQREFERENCEKEYS->getDescription($chart);

                $pdf->ezText(html_entity_decode($chartstr), "12");
            }

            // Magnitude
            $magnitude = $observation->getMagnitude($value);

            if ($magnitude != -99.9) {
                $magstr = "";

                if ($observation->getMagnitudeWeakerThan($value)) {
                    $magstr = $magstr . _("Weaker than") . " ";
                }
                $magstr = $magstr . html_entity_decode(_("Magnitude"))
                    . " : " . sprintf("%.01f", $magnitude);

                if ($observation->getMagnitudeUncertain($value)) {
                    $magstr = $magstr . " (" . _("Uncertain") . ")";
                }

                $pdf->ezText(html_entity_decode($magstr), "12");
            }

            // Degree of condensation
            $dc = $observation->getDc($value);
            $coma = $observation->getComa($value);

            $dcstr = "";
            $extra = "";

            if (strcmp($dc, "") != 0 || $coma != -99) {
                if (strcmp($dc, "") != 0) {
                    $dcstr = $dcstr . html_entity_decode(_("Degree of condensation")) . " : "
                        . $dc;
                    $extra = ", ";
                }

                // Coma
                if ($coma != -99) {
                    $dcstr = $dcstr . $extra . html_entity_decode(_("Coma"))
                        . " : " . $coma . "'";
                }

                $pdf->ezText(html_entity_decode($dcstr), "12");
            }

            // Tail
            $tail = $observation->getTail($value);
            $pa = $observation->getPa($value);

            $tailstr = "";
            $extra = "";

            if ($tail != -99 || $pa != -99) {
                if ($tail != -99) {
                    $tailstr = $tailstr . html_entity_decode(_("Tail length"))
                        . " : " . $tail . "'";
                    $extra = ", ";
                }

                if ($pa != -99) {
                    $tailstr = $tailstr . $extra
                        . html_entity_decode(_("Position angle of tail"))
                        . " : " . $pa . "";
                }

                $pdf->ezText(html_entity_decode($tailstr), "12");
            }

            // Description
            $description = $observation->getDescription($value);

            if (strcmp($description, "") != 0) {
                $descstr = _("Description") . " : " . strip_tags($description);
                $pdf->ezText(html_entity_decode($descstr), "12");
            }

            $upload_dir = $instDir . 'comets/' . 'cometdrawings';
            $dir = opendir($upload_dir);

            while (false !== ($file = readdir($dir))) {
                if ("." == $file or ".." == $file) {
                    continue; // skip current directory and directory above
                }
                if (fnmatch($value . ".gif", $file)
                    || fnmatch($value . ".jpg", $file)
                    || fnmatch($value . ".png", $file)
                ) {
                    $pdf->ezImage(
                        $upload_dir . "/" . $value . ".jpg",
                        0,
                        500,
                        "none",
                        "left"
                    );
                }
            }

            $pdf->ezText("");
        }

        $pdf->ezStream();
    }

    /**
     * Creates a pdf document from an array of objects.
     *
     * @param array $result The array of objects.
     *
     * @return string The pdf file with the object.
     */
    public function pdfObjectnames($result)
    {
        global $instDir;
        $page = 1;
        $i = 0;
        $result = $this->sortResult($result);
        foreach ($result as $key => $valueA) {
            $obs1[] = array(
               $valueA['showname']
            );
        }
        // Create pdf file
        $pdf = new Cezpdf('a4', 'landscape');
        $pdf->ezStartPageNumbers(450, 15, 10);
        $pdf->selectFont($instDir . 'lib/fonts/Helvetica.afm');
        $pdf->ezText(mb_convert_encoding(html_entity_decode($_GET['pdfTitle']), 'ISO-8859-1', 'UTF-8'), 18);
        $pdf->ezText("\n", 5);
        $pdf->ezColumnsStart(array('num' => 10));
        $pdf->ezTable(
            $obs1,
            '',
            '',
            array(
            "width" => "750",
            "cols" => array(
                array(
                    'justification' => 'left',
                    'width' => 80
                )
            ),
            "fontSize" => "7",
            "showLines" => "0",
            "showHeadings" => "0",
            "rowGap" => "0",
            "colGap" => "0"
            )
        );
        $pdf->ezStream();
    }

    /**
     * Sort the result based on the 'sortOrder' cookie.
     *
     * @param array $result The array to sort.
     *
     * @return array The sorted array.
     */
    public function sortResult($result)
    {
        $sortOrderArray = explode(",", trim($_COOKIE['sortOrder'], "|"));

        foreach ($sortOrderArray as $sort) {
            $sort = trim($sort, ")");
            $sort = explode("(", $sort);
            $sortName[] = $sort[0];
            // 0 = up, 1 = down
            $sortOrder[] = $sort[1];
        }
        // Multicolumn sort
        $sort = array();

        $cnt = 0;
        foreach ($sortName as $sName) {
            foreach ($result as $k => $v) {
                if (!isset($v[$sName])) {
                    $sName = 'objectname';
                }
                if (is_array($v) && $v[$sName] == "") {
                    if ($sortOrder[$cnt] == 1) {
                        $sort[$sName][$k] = -99.0;
                    } else {
                        $sort[$sName][$k] = +99.0;
                    }
                } else {
                    if (is_array($v)) {
                        $sort[$sName][$k] = $v[$sName];
                    }
                }
            }
            $cnt++;
        }
        $cnt = 0;
        $dynamicSort = array();
        foreach ($sortName as $sName) {
            if (isset($sort[$sName])) {
                $dynamicSort[] = $sort[$sName];
                if ($sortOrder[$cnt] == 0) {
                    $dynamicSort[] = SORT_ASC;
                } else {
                    $dynamicSort[] = SORT_DESC;
                }
                $dynamicSort[] = SORT_NATURAL;
                $cnt++;
            }
        }
        $param = array_merge($dynamicSort, array(&$result));
        call_user_func_array('array_multisort', $param);

        // Return the sorted result
        return $result;
    }


    /**
     * Creates a pdf document from an array of objects.
     *
     * @param array $result The array of objects.
     *
     * @return string The pdf file with the object.
     */
    public function pdfObjects($result)
    {
        global $instDir, $objAtlas, $objObserver, $objPresentations, $loggedUser;

        $result = $this->sortResult($result);

        foreach ($result as $key => $valueA) {
            $obs1[] = array(
                "Name" => $valueA['showname'],
                "ra" => $objPresentations->raToString($valueA['objectra']),
                "decl" => $objPresentations->decToString($valueA['objectdecl'], 0),
                "mag" => $objPresentations->presentationInt1(
                    $valueA['objectmagnitude'],
                    99.9,
                    ''
                ),
                "sb" => $objPresentations->presentationInt1(
                    $valueA['objectsurfacebrightness'],
                    99.9,
                    ''
                ),
                "con" => $GLOBALS[$valueA['objectconstellation']],
                "diam" => $valueA['objectsize'],
                "pa" => $objPresentations->presentationInt(
                    $valueA['objectpa'],
                    999,
                    "-"
                ),
                "type" => $GLOBALS[$valueA['objecttype']],
                "page" => $valueA[$objObserver->getObserverProperty(
                    $this->checkSessionKey('deepskylog_id', ''),
                    'standardAtlasCode',
                    'urano'
                )],
                "contrast" => $valueA['objectcontrast'],
                "magnification" => $valueA['objectoptimalmagnificationvalue'],
                "seen" => $valueA['objectseen'],
                "seendate" => $valueA['objectlastseen']
            );
        }

        $pdf = new Cezpdf('a4', 'landscape');
        $pdf->ezStartPageNumbers(450, 15, 10);
        $fontdir = $instDir . 'lib/fonts/Helvetica.afm';
        $pdf->selectFont($fontdir);
        $pdf->ezTable(
            $obs1,
            array(
                "Name" => html_entity_decode(_("Name")),
                "ra" => html_entity_decode(_("Right Ascension")),
                "decl" => html_entity_decode(_("Declination")),
                "type" => html_entity_decode(_("Type")),
                "con" => html_entity_decode(_("Constellation")),
                "mag" => html_entity_decode(_("Mag.")),
                "sb" => html_entity_decode(_("Surf. Brig.")),
                "diam" => html_entity_decode(_("Diameter")),
                "pa" => html_entity_decode(_("Pos. Angle")),
                "page" => html_entity_decode(
                    $objAtlas->atlasCodes[$objObserver->getObserverProperty(
                        $loggedUser,
                        'standardAtlasCode',
                        'urano'
                    )]
                ),
                "contrast" => html_entity_decode(_("Contr. res.")),
                "magnification" => html_entity_decode(_("Opt. mag.")),
                "seen" => html_entity_decode(_("Seen")),
                "seendate" => html_entity_decode(_("Last Seen"))
            ),
            mb_convert_encoding(html_entity_decode($_GET['pdfTitle']), 'ISO-8859-1', 'UTF-8'),
            array(
            "width" => "750",
            "cols" => array(
                  "Name" => array(
                        'justification' => 'left',
                        'width' => 100
                  ),
                  "ra" => array(
                        'justification' => 'center',
                        'width' => 65
                  ),
                  "decl" => array(
                        'justification' => 'center',
                        'width' => 50
                  ),
                  "type" => array(
                        'justification' => 'left',
                        'width' => 110
                  ),
                  "con" => array(
                        'justification' => 'left',
                        'width' => 90
                  ),
                  "mag" => array(
                        'justification' => 'center',
                        'width' => 35
                  ),
                  "sb" => array(
                        'justification' => 'center',
                        'width' => 35
                  ),
                  "diam" => array(
                        'justification' => 'center',
                        'width' => 65
                  ),
                  "pa" => array(
                        'justification' => 'center',
                        'width' => 30
                  ),
                  "page" => array(
                        'justification' => 'center',
                        'width' => 45
                  ),
                  "contrast" => array(
                        'justification' => 'center',
                        'width' => 35
                  ),
                  "magnification" => array(
                        'justification' => 'center',
                        'width' => 35
                  ),
                  "seen" => array(
                        'justification' => 'center',
                        'width' => 50
                  ),
                  "seendate" => array(
                        'justification' => 'center',
                        'width' => 50
                  )
            ),
            "fontSize" => "7"
            )
        );
        $pdf->ezStream();
    }

    /**
     * Creates a detailed pdf document from an array of objects.
     *
     * @param array $result The array of objects.
     *
     * @return string The pdf file with the object.
     */
    public function pdfObjectsDetails($result)
    {
        global $dateformat, $baseURL, $instDir, $objObserver, $loggedUser;
        global $objLocation, $objInstrument, $objPresentations;
        $result = $this->sortResult($result);

        $pdf = new Cezpdf('a4', 'landscape');
        $pdf->selectFont($instDir . 'lib/fonts/Helvetica.afm');

        $bottom = 40;
        $bottomsection = 30;
        $top = 550;
        $header = 570;
        $footer = 10;
        $xleft = 20;
        $xmid = 431;
        $fontSizeSection = 10;
        $fontSizeText = 8;
        $descriptionLeadingSpace = 20;
        $sectionBarSpace = 3;
        $deltalineSection = 2;

        $deltaline = $fontSizeText + 4;
        $pagenr = 0;
        $y = 0;
        $xbase = $xmid;
        $sectionBarHeight = $fontSizeSection + 4;
        $SectionBarWidth = 400 + $sectionBarSpace;

        $theDate = date('d/m/Y');
        $pdf->addTextWrap($xleft, $header, 8, mb_convert_encoding($theDate, 'ISO-8859-1', 'UTF-8'), 100);

        if ($loggedUser
            && $objObserver->getObserverProperty($loggedUser, 'name')
            && $objLocation->getLocationPropertyFromId(
                $objObserver->getObserverProperty($loggedUser, 'stdlocation'),
                'name'
            )
            && $objInstrument->getInstrumentPropertyFromId(
                $objObserver->getObserverProperty(
                    $loggedUser,
                    'stdtelescope'
                ),
                'name'
            )
        ) {
            $pdf->addTextWrap(
                $xleft,
                $footer,
                8,
                mb_convert_encoding(
                    html_entity_decode(
                        sprintf(
                            _("Prepared for %s with %s at %s"),
                            $objObserver->getObserverProperty($loggedUser, 'firstname')
                            . ' ' .
                            $objObserver->getObserverProperty($loggedUser, 'name'),
                            $objInstrument->getInstrumentPropertyFromId(
                                $objObserver->getObserverProperty(
                                    $loggedUser,
                                    'stdtelescope'
                                ),
                                'name'
                            ),
                            $objLocation->getLocationPropertyFromId(
                                $objObserver->getObserverProperty(
                                    $loggedUser,
                                    'stdlocation'
                                ),
                                'name'
                            )
                        )
                    ),
                    'ISO-8859-1',
                    'UTF-8'
                ),
                $xmid + $SectionBarWidth,
                'center'
            );
        }
        $pdf->addTextWrap(
            $xleft,
            $header,
            10,
            mb_convert_encoding(html_entity_decode($_GET['pdfTitle']), 'ISO-8859-1', 'UTF-8'),
            $xmid + $SectionBarWidth,
            'center'
        );

        $pdf->addTextWrap(
            $xmid + $SectionBarWidth - $sectionBarSpace - 100,
            $header,
            8,
            mb_convert_encoding(_("Page ") . '1', 'ISO-8859-1', 'UTF-8'),
            100,
            'right'
        );
        foreach ($result as $key => $valueA) {
            $con = $valueA['objectconstellation'];
            if ($y < $bottom) {
                $y = $top;
                if ($xbase == $xmid) {
                    if ($pagenr++) {
                        $pdf->newPage();
                        $pdf->addTextWrap(
                            $xleft,
                            $header,
                            8,
                            mb_convert_encoding($theDate, 'ISO-8859-1', 'UTF-8'),
                            100
                        );
                        if ($loggedUser
                            && $objObserver->getObserverProperty(
                                $loggedUser,
                                'name'
                            )
                            && $objLocation->getLocationPropertyFromId(
                                $objObserver->getObserverProperty(
                                    $loggedUser,
                                    'stdlocation'
                                ),
                                'name'
                            )
                            && $objInstrument->getInstrumentPropertyFromId(
                                $objObserver->getObserverProperty(
                                    $loggedUser,
                                    'stdtelescope'
                                ),
                                'name'
                            )
                        ) {
                            $pdf->addTextWrap(
                                $xleft,
                                $footer,
                                8,
                                mb_convert_encoding(
                                    html_entity_decode(
                                        sprintf(
                                            _("Prepared for %s with %s at %s"),
                                            $objObserver->getObserverProperty(
                                                $loggedUser,
                                                'name'
                                            ) . ' ' .
                                            $objObserver->getObserverProperty(
                                                $loggedUser,
                                                'firstname'
                                            ),
                                            $objInstrument->getInstrumentPropertyFromId(
                                                $objObserver->getObserverProperty(
                                                    $loggedUser,
                                                    'stdtelescope'
                                                ),
                                                'name'
                                            ),
                                            $objLocation->getLocationPropertyFromId(
                                                $objObserver->getObserverProperty(
                                                    $loggedUser,
                                                    'stdlocation'
                                                ),
                                                'name'
                                            )
                                        )
                                    ),
                                    'ISO-8859-1',
                                    'UTF-8'
                                ),
                                $xmid + $SectionBarWidth,
                                'center'
                            );
                        }
                        $pdf->addTextWrap(
                            $xleft,
                            $header,
                            10,
                            mb_convert_encoding(html_entity_decode($_GET['pdfTitle']), 'ISO-8859-1', 'UTF-8'),
                            $xmid + $SectionBarWidth,
                            'center'
                        );
                        $pdf->addTextWrap(
                            $xmid + $SectionBarWidth - $sectionBarSpace - 100,
                            $header,
                            8,
                            mb_convert_encoding(_("Page ") . $pagenr, 'ISO-8859-1', 'UTF-8'),
                            100,
                            'right'
                        );
                    }
                    $xbase = $xleft;
                } else {
                    $xbase = $xmid;
                }
            }
            $pdf->addTextWrap($xbase, $y, $fontSizeText, mb_convert_encoding($valueA['objectseen'], 'ISO-8859-1', 'UTF-8'), 45); // seen
            $pdf->addTextWrap($xbase + 45, $y, $fontSizeText, mb_convert_encoding($valueA['objectlastseen'], 'ISO-8859-1', 'UTF-8'), 55); // last seen
            $pdf->addTextWrap(
                $xbase + 85,
                $y,
                $fontSizeText,
                mb_convert_encoding(
                    $valueA['showname'],
                    'ISO-8859-1',
                    'UTF-8'
                ),
                85
            ); // object
            $pdf->addTextWrap($xbase + 150, $y, $fontSizeText, mb_convert_encoding($valueA['objecttype'], 'ISO-8859-1', 'UTF-8'), 30); // type
            $pdf->addTextWrap($xbase + 180, $y, $fontSizeText, mb_convert_encoding($valueA['objectconstellation'], 'ISO-8859-1', 'UTF-8'), 20); // constellation
            $pdf->addTextWrap($xbase + 200, $y, $fontSizeText, mb_convert_encoding($objPresentations->presentationInt1($valueA['objectmagnitude'], 99.9, ''), 'ISO-8859-1', 'UTF-8'), 17, 'left'); // mag
            $pdf->addTextWrap($xbase + 217, $y, $fontSizeText, mb_convert_encoding($objPresentations->presentationInt1($valueA['objectsurfacebrightness'], 99.9, ''), 'ISO-8859-1', 'UTF-8'), 18, 'left'); // sb
            $pdf->addTextWrap(
                $xbase + 235,
                $y,
                $fontSizeText,
                mb_convert_encoding(
                    $objPresentations->raToStringHM($valueA ['objectra']) . ' ' .
                    $objPresentations->decToString($valueA['objectdecl'], 0),
                    'ISO-8859-1',
                    'UTF-8'
                ),
                60
            ); // ra - decl
            $pdf->addTextWrap(
                $xbase + 295,
                $y,
                $fontSizeText,
                mb_convert_encoding(
                    $valueA['objectsize'] . '/' .
                    $objPresentations->presentationInt($valueA['objectpa'], 999, "-"),
                    'ISO-8859-1',
                    'UTF-8'
                ),
                55
            ); // size
            $pdf->addTextWrap(
                $xbase + 351,
                $y,
                $fontSizeText,
                mb_convert_encoding(
                    $objPresentations->presentationInt1(
                        $valueA['objectcontrast'],
                        '',
                        ''
                    ),
                    'ISO-8859-1',
                    'UTF-8'
                ),
                17,
                'left'
            ); // contrast
            $pdf->addTextWrap(
                $xbase + 368,
                $y,
                $fontSizeText,
                mb_convert_encoding((int) $valueA['objectoptimalmagnification'], 'ISO-8859-1', 'UTF-8'),
                17,
                'left'
            ); // magnification
            $pdf->addTextWrap(
                $xbase + 380,
                $y,
                $fontSizeText,
                mb_convert_encoding(
                    '<b>'
                    . $valueA[($loggedUser ? $objObserver->getObserverProperty(
                        $loggedUser,
                        'standardAtlasCode',
                        'urano'
                    ) : 'urano')]
                    . '</b>',
                    'ISO-8859-1',
                    'UTF-8'
                ),
                20,
                'right'
            ); // atlas page

            $y -= $deltaline;
            if (array_key_exists('objectlistdescription', $valueA)
                && $valueA['objectlistdescription']
            ) {
                $theText = $objPresentations->br2nl(
                    $valueA['objectlistdescription']
                );
                $theText = $pdf->addTextWrap(
                    $xbase + $descriptionLeadingSpace,
                    $y,
                    $fontSizeText,
                    '<i>' . mb_convert_encoding($theText, 'ISO-8859-1', 'UTF-8'),
                    $xmid - $xleft - $descriptionLeadingSpace - 10
                );
                $y -= $deltaline;
                while ($theText) {
                    if ($y < $bottomsection) {
                        $y = $top;
                        if ($xbase == $xmid) {
                            if ($pagenr++) {
                                $pdf->newPage();
                                $pdf->addTextWrap(
                                    $xleft,
                                    $header,
                                    8,
                                    mb_convert_encoding($theDate, 'ISO-8859-1', 'UTF-8'),
                                    100
                                );
                                if ($objObserver->getObserverProperty(
                                    $loggedUser,
                                    'name'
                                )
                                    && $objLocation->getLocationPropertyFromId(
                                        $objObserver->getObserverProperty(
                                            $loggedUser,
                                            'stdlocation'
                                        ),
                                        'name'
                                    )
                                    && $objInstrument->getInstrumentPropertyFromId(
                                        $objObserver->getObserverProperty(
                                            $loggedUser,
                                            'stdtelescope'
                                        ),
                                        'name'
                                    )
                                ) {
                                    $pdf->addTextWrap(
                                        $xleft,
                                        $footer,
                                        8,
                                        mb_convert_encoding(
                                            html_entity_decode(
                                                sprintf(
                                                    _("Prepared for %s with %s at %s"),
                                                    $objObserver
                                                        ->getObserverProperty(
                                                            $loggedUser,
                                                            'name'
                                                        ) . ' ' .
                                                    $objObserver
                                                        ->getObserverProperty(
                                                            $loggedUser,
                                                            'firstname'
                                                        ),
                                                    $objInstrument
                                                        ->getInstrumentPropertyFromId(
                                                            $objObserver
                                                            ->getObserverProperty(
                                                                $loggedUser,
                                                                'stdtelescope'
                                                            ),
                                                            'name'
                                                        ),
                                                    $objLocation
                                                        ->getLocationPropertyFromId(
                                                            $objObserver
                                                                ->getObserverProperty(
                                                                    $loggedUser,
                                                                    'stdlocation'
                                                                ),
                                                            'name'
                                                        )
                                                )
                                            ),
                                            'ISO-8859-1',
                                            'UTF-8'
                                        ),
                                        $xmid + $SectionBarWidth,
                                        'center'
                                    );
                                }
                                $pdf->addTextWrap(
                                    $xleft,
                                    $header,
                                    10,
                                    mb_convert_encoding(
                                        html_entity_decode($_GET['pdfTitle']),
                                        'ISO-8859-1',
                                        'UTF-8'
                                    ),
                                    $xmid + $SectionBarWidth,
                                    'center'
                                );
                                $pdf->addTextWrap(
                                    $xmid + $SectionBarWidth - $sectionBarSpace
                                    - 100,
                                    $header,
                                    8,
                                    mb_convert_encoding(_("Page ") . $pagenr, 'ISO-8859-1', 'UTF-8'),
                                    100,
                                    'right'
                                );
                            }
                            $xbase = $xleft;
                        } else {
                            $xbase = $xmid;
                        }
                    }
                    $theText = $pdf->addTextWrap(
                        $xbase + $descriptionLeadingSpace,
                        $y,
                        $fontSizeText,
                        mb_convert_encoding($theText, 'ISO-8859-1', 'UTF-8'),
                        $xmid - $xleft - $descriptionLeadingSpace - 10
                    );
                    $y -= $deltaline;
                }
                $pdf->addText(0, 0, 10, '</i>');
            } elseif (array_key_exists('objectdescription', $valueA)
                && $valueA['objectdescription']
            ) {
                $theText = $objPresentations->br2nl($valueA['objectdescription']);
                $theText = $pdf->addTextWrap(
                    $xbase + $descriptionLeadingSpace,
                    $y,
                    $fontSizeText,
                    '<i>' .
                    mb_convert_encoding($theText, 'ISO-8859-1', 'UTF-8'),
                    $xmid - $xleft - $descriptionLeadingSpace - 10
                );
                $y -= $deltaline;
                while ($theText) {
                    if ($y < $bottomsection) {
                        $y = $top;
                        if ($xbase == $xmid) {
                            if ($pagenr++) {
                                $pdf->newPage();
                                $pdf->addTextWrap(
                                    $xleft,
                                    $header,
                                    8,
                                    mb_convert_encoding($theDate, 'ISO-8859-1', 'UTF-8'),
                                    100
                                );
                                if ($objObserver->getObserverProperty(
                                    $loggedUser,
                                    'name'
                                )
                                    && $objLocation->getLocationPropertyFromId(
                                        $objObserver->getObserverProperty(
                                            $loggedUser,
                                            'stdlocation'
                                        ),
                                        'name'
                                    )
                                    && $objInstrument->getInstrumentPropertyFromId(
                                        $objObserver->getObserverProperty(
                                            $loggedUser,
                                            'stdtelescope'
                                        ),
                                        'name'
                                    )
                                ) {
                                    $pdf->addTextWrap(
                                        $xleft,
                                        $footer,
                                        8,
                                        mb_convert_encoding(
                                            html_entity_decode(
                                                sprintf(
                                                    _("Prepared for %s with %s at %s"),
                                                    $objObserver->getObserverProperty(
                                                        $loggedUser,
                                                        'firstname'
                                                    ) . ' ' .
                                                    $objObserver->getObserverProperty(
                                                        $loggedUser,
                                                        'name'
                                                    ),
                                                    $objInstrument
                                                        ->getInstrumentPropertyFromId(
                                                            $objObserver
                                                                ->getObserverProperty(
                                                                    $loggedUser,
                                                                    'stdtelescope'
                                                                ),
                                                            'name'
                                                        ),
                                                    $objLocation
                                                        ->getLocationPropertyFromId(
                                                            $objObserver
                                                                ->getObserverProperty(
                                                                    $loggedUser,
                                                                    'stdlocation'
                                                                ),
                                                            'name'
                                                        )
                                                )
                                            ),
                                            'ISO-8859-1',
                                            'UTF-8'
                                        ),
                                        $xmid + $SectionBarWidth,
                                        'center'
                                    );
                                }
                                $pdf->addTextWrap(
                                    $xleft,
                                    $header,
                                    10,
                                    mb_convert_encoding(
                                        html_entity_decode($_GET ['pdfTitle']),
                                        'ISO-8859-1',
                                        'UTF-8'
                                    ),
                                    $xmid + $SectionBarWidth,
                                    'center'
                                );
                                $pdf->addTextWrap(
                                    $xmid + $SectionBarWidth - $sectionBarSpace
                                    - 100,
                                    $header,
                                    8,
                                    mb_convert_encoding(_("Page ") . $pagenr, 'ISO-8859-1', 'UTF-8'),
                                    100,
                                    'right'
                                );
                            }
                            $xbase = $xleft;
                        } else {
                            $xbase = $xmid;
                        }
                    }
                    $theText = $pdf->addTextWrap(
                        $xbase + $descriptionLeadingSpace,
                        $y,
                        $fontSizeText,
                        mb_convert_encoding($theText, 'ISO-8859-1', 'UTF-8'),
                        $xmid - $xleft - $descriptionLeadingSpace - 10
                    );
                    $y -= $deltaline;
                }
                $pdf->addText(0, 0, 10, '</i>');
            }
        }
        $pdf->ezStream();
    }

    /**
     * Creates a first pdf page for reports or the atlas.
     *
     * @param integer $y                The y coordinates for the text.
     * @param integer $top              The top coordinates for the text.
     * @param integer $xbase            The x base coordinates.
     * @param integer $xmid             The center coordinates for x.
     * @param integer $pagenr           The page number.
     * @param string  $pdf              The pdf file.
     * @param integer $xleft            The left x coordinates.
     * @param integer $header           The location (in y) of the header.
     * @param integer $fontSizeText     The Font size for the text.
     * @param string  $theDate          The date to print.
     * @param integer $footer           The location (in y) of the footer.
     * @param integer $SectionBarWidth  The width of the Section Bar.
     * @param integer $sectionBarSpace  The space to use in the Section Bar.
     * @param integer $deltalineSection The delta line in the section.
     * @param integer $sectionBarHeight The height of the section bar.
     * @param integer $fontSizeSection  The font size in the section.
     * @param integer $deltaline        The delta line.
     * @param integer $showelements     Describes which elements to show
     *                                  h - information on observer, telescope, date)
     *                                  e - ephemerids of sun and moon
     *                                  p - pagenumber
     *                                  t - title
     *                                  l - legend
     * @param integer $reportdata       The data to report.
     *
     * @return None
     */
    public function firstpage(
        &$y,
        $top,
        &$xbase,
        $xmid,
        &$pagenr,
        $pdf,
        $xleft,
        $header,
        $fontSizeText,
        $theDate,
        $footer,
        $SectionBarWidth,
        $sectionBarSpace,
        $deltalineSection,
        $sectionBarHeight,
        $fontSizeSection,
        $deltaline,
        $showelements,
        $reportdata
    ) {
        global $objObserver, $loggedUser, $objLocation, $objInstrument;
        $y = $top;
        $xbase = $xleft;
        $pdf->addTextWrap(
            $xleft,
            $header,
            $fontSizeText,
            mb_convert_encoding($theDate, 'ISO-8859-1', 'UTF-8'),
            100
        );
        if ($objObserver->getObserverProperty($loggedUser, 'name')
            && $objLocation->getLocationPropertyFromId(
                $objObserver->getObserverProperty($loggedUser, 'stdlocation'),
                'name'
            ) && $objInstrument->getInstrumentPropertyFromId(
                $objObserver->getObserverProperty($loggedUser, 'stdtelescope'),
                'name'
            ) && (strpos($showelements, 'h') !== false)
        ) {
            $pdf->addTextWrap(
                $xleft,
                $footer,
                $fontSizeText,
                mb_convert_encoding(
                    html_entity_decode(
                        sprintf(
                            _("Prepared for %s with %s at %s"),
                            $objObserver->getObserverProperty($loggedUser, 'firstname') .
                            ' ' .
                            $objObserver->getObserverProperty($loggedUser, 'name'),
                            $objInstrument->getInstrumentPropertyFromId(
                                $objObserver->getObserverProperty(
                                    $loggedUser,
                                    'stdtelescope'
                                ),
                                'name'
                            ),
                            $objLocation->getLocationPropertyFromId(
                                $objObserver->getObserverProperty(
                                    $loggedUser,
                                    'stdlocation'
                                ),
                                'name'
                            ) .
                            _(' on ') . $this->checkSessionKey('globalDay')
                            . ' ' .
                            $GLOBALS ['Month' . $this->checkSessionKey('globalMonth')]
                            . ' ' . $this->checkSessionKey('globalYear')
                        )
                    ),
                    'ISO-8859-1',
                    'UTF-8'
                ),
                $xmid + $SectionBarWidth,
                'center'
            );
        }
        if ($objObserver->getObserverProperty($loggedUser, 'name')
            && $objLocation->getLocationPropertyFromId(
                $objObserver->getObserverProperty($loggedUser, 'stdlocation'),
                'name'
            )
            && (strpos($showelements, 'e') !== false)
        ) {
            $pdf->addTextWrap(
                $xleft,
                $footer - $deltaline,
                $fontSizeText,
                mb_convert_encoding(
                    _('Sun down: ') . $_SESSION['efemerides']['sset'] . _(' to ') .
                    $_SESSION['efemerides']['srise'] . " - "
                        . _('Nautical night: ') .
                    $_SESSION['efemerides']['naute'] . _(' to ') .
                    $_SESSION['efemerides']['nautb'] . " - "
                        . _('Astronomical night: ') .
                    $_SESSION['efemerides']['astroe'] . _(' to ') .
                    $_SESSION['efemerides']['astrob'] . " - " . _('Moon up: ') .
                    $_SESSION['efemerides']['moon0'] . _(' to ') .
                    $_SESSION['efemerides']['moon2'],
                    'ISO-8859-1',
                    'UTF-8'
                ),
                $xmid + $SectionBarWidth,
                'center'
            );
        }
        if (strpos($showelements, 'p') !== false) {
            $pdf->addTextWrap(
                $xmid + $SectionBarWidth - $sectionBarSpace - 100,
                $header,
                $fontSizeText,
                mb_convert_encoding(_("Page ") . $pagenr, 'ISO-8859-1', 'UTF-8'),
                100,
                'right'
            );
        }
        if (strpos($showelements, 't') !== false) {
            $pdf->addTextWrap(
                $xleft,
                $header,
                10,
                mb_convert_encoding(html_entity_decode($this->checkRequestKey('pdfTitle')), 'ISO-8859-1', 'UTF-8'),
                $xmid + $SectionBarWidth,
                'center'
            );
        }
        if (strpos($showelements, 'l') !== false) {
            $pdf->line(
                $xbase - $sectionBarSpace,
                $y + $fontSizeText + $sectionBarSpace,
                $xbase + $SectionBarWidth,
                $y + $fontSizeText + $sectionBarSpace
            );
            reset($reportdata);
            $deltaymax = 0;
            foreach ($reportdata as $key => $dataelement) {
                if ($dataelement['fieldwidth']) {
                    $justification = 'left';
                    if (strpos($dataelement['fieldstyle'], 'r') !== false) {
                        $justification = 'right';
                    }
                    if (strpos($dataelement['fieldstyle'], 'c') !== false) {
                        $justification = 'center';
                    }
                    if (strpos($dataelement['fieldstyle'], 'b') !== false) {
                        $pdf->addText(0, 0, $fontSizeText, '<b>');
                    }
                    if (strpos($dataelement['fieldstyle'], 'i') !== false) {
                        $pdf->addText(0, 0, $fontSizeText, '<i>');
                    }
                    $pdf->addTextWrap(
                        $xbase + $dataelement['fieldposition'],
                        $y - ($deltaline * $dataelement['fieldline']),
                        $fontSizeText,
                        mb_convert_encoding($dataelement['fieldlegend'], 'ISO-8859-1', 'UTF-8'),
                        $dataelement['fieldwidth'],
                        $justification
                    );
                    $deltaymax = max($deltaymax, $dataelement['fieldline']);
                    if (strpos($dataelement['fieldstyle'], 'b') !== false) {
                        $pdf->addText(0, 0, $fontSizeText, '</b>');
                    }
                    if (strpos($dataelement['fieldstyle'], 'i') !== false) {
                        $pdf->addText(0, 0, $fontSizeText, '</i>');
                    }
                }
            }
            $y -= $deltaline * ($deltaymax);
            $pdf->line(
                $xbase - $sectionBarSpace,
                $y - $sectionBarSpace,
                $xbase + $SectionBarWidth,
                $y - $sectionBarSpace
            );
            $y -= ($deltaline + $sectionBarSpace);
        }
        $xbase = $xleft;
    }

    /**
     * Creates a new pdf page for reports or the atlas.
     *
     * @param integer $y                The y coordinates for the text.
     * @param integer $top              The top coordinates for the text.
     * @param integer $bottom           The bottom coordinates for the text.
     * @param integer $xbase            The x base coordinates.
     * @param integer $xmid             The center coordinates for x.
     * @param integer $pagenr           The page number.
     * @param string  $pdf              The pdf file.
     * @param integer $xleft            The left x coordinates.
     * @param integer $header           The location (in y) of the header.
     * @param integer $fontSizeText     The Font size for the text.
     * @param string  $theDate          The date to print.
     * @param integer $footer           The location (in y) of the footer.
     * @param integer $SectionBarWidth  The width of the Section Bar.
     * @param integer $sectionBarSpace  The space to use in the Section Bar.
     * @param integer $sort             The column to sort on.
     * @param string  $con              The constellation.
     * @param integer $deltalineSection The delta line in the section.
     * @param integer $sectionBarHeight The height of the section bar.
     * @param integer $fontSizeSection  The font size in the section.
     * @param integer $deltaline        The delta line.
     * @param boolean $i                True if the text should be italic.
     * @param boolean $b                True if the text should be bold.
     * @param integer $showelements     Describes which elements to show
     *                                  h - information on observer, telescope, date)
     *                                  e - ephemerids of sun and moon
     *                                  p - pagenumber
     *                                  t - title
     *                                  l - legend
     * @param integer $reportdata       The data to report.
     *
     * @return None
     */
    public function newpage(
        &$y,
        $top,
        $bottom,
        &$xbase,
        $xmid,
        &$pagenr,
        $pdf,
        $xleft,
        $header,
        $fontSizeText,
        $theDate,
        $footer,
        $SectionBarWidth,
        $sectionBarSpace,
        $sort,
        $con,
        $deltalineSection,
        $sectionBarHeight,
        $fontSizeSection,
        $deltaline,
        $i,
        $b,
        $showelements,
        $reportdata
    ) {
        global $objObserver, $loggedUser, $objLocation, $objInstrument;
        if ($i) {
            $pdf->addText(0, 0, $fontSizeText, '</i>');
        }
        if ($b) {
            $pdf->addText(0, 0, $fontSizeText, '</b>');
        }
        $y = $top;
        if ($xbase == $xmid) {
            if ($pagenr++) {
                $pdf->newPage();
                $pdf->addTextWrap(
                    $xleft,
                    $header,
                    $fontSizeText,
                    mb_convert_encoding($theDate, 'ISO-8859-1', 'UTF-8'),
                    100
                );
                if ($objObserver->getObserverProperty($loggedUser, 'name')
                    && $objLocation->getLocationPropertyFromId(
                        $objObserver->getObserverProperty(
                            $loggedUser,
                            'stdlocation'
                        ),
                        'name'
                    )
                    && $objInstrument->getInstrumentPropertyFromId(
                        $objObserver->getObserverProperty(
                            $loggedUser,
                            'stdtelescope'
                        ),
                        'name'
                    )
                    && (strpos($showelements, 'h') !== false)
                ) {
                    $pdf->addTextWrap(
                        $xleft,
                        $footer,
                        $fontSizeText,
                        mb_convert_encoding(
                            html_entity_decode(
                                sprintf(
                                    _("Prepared for %s with %s at %s"),
                                    $objObserver->getObserverProperty(
                                        $loggedUser,
                                        'firstname'
                                    ) . ' ' . $objObserver->getObserverProperty(
                                        $loggedUser,
                                        'name'
                                    ),
                                    $objInstrument->getInstrumentPropertyFromId(
                                        $objObserver->getObserverProperty(
                                            $loggedUser,
                                            'stdtelescope'
                                        ),
                                        'name'
                                    ),
                                    $objLocation->getLocationPropertyFromId(
                                        $objObserver->getObserverProperty(
                                            $loggedUser,
                                            'stdlocation'
                                        ),
                                        'name'
                                    ) .
                                    _(' on ') .
                                    $this->checkSessionKey('globalDay') . ' ' .
                                    $GLOBALS['Month' .
                                    $this->checkSessionKey('globalMonth')]
                                    . ' ' . $this->checkSessionKey('globalYear')
                                )
                            ),
                            'ISO-8859-1',
                            'UTF-8'
                        ),
                        $xmid + $SectionBarWidth,
                        'center'
                    );
                }
                if ($objObserver->getObserverProperty($loggedUser, 'name')
                    && $objLocation->getLocationPropertyFromId(
                        $objObserver->getObserverProperty(
                            $loggedUser,
                            'stdlocation'
                        ),
                        'name'
                    )
                    && (strpos($showelements, 'e') !== false)
                ) {
                    $pdf->addTextWrap(
                        $xleft,
                        $footer - $deltaline,
                        $fontSizeText,
                        mb_convert_encoding(
                            _('Sun down: ') . $_SESSION['efemerides']['sset'] .
                            _(' to ') . $_SESSION['efemerides']['srise'] . " - " .
                            _('Nautical night: ') .
                            $_SESSION['efemerides']['naute'] .
                            _(' to ') . $_SESSION['efemerides']['nautb'] . " - " .
                            _('Astronomical night: ') .
                            $_SESSION['efemerides']['astroe'] ._(' to ') .
                            $_SESSION['efemerides']['astrob'] . " - " .
                            _('Moon up: ') .
                            $_SESSION['efemerides']['moon0'] . _(' to ') .
                            $_SESSION['efemerides']['moon2'],
                            'ISO-8859-1',
                            'UTF-8'
                        ),
                        $xmid + $SectionBarWidth,
                        'center'
                    );
                }
                if (strpos($showelements, 'p') !== false) {
                    $pdf->addTextWrap(
                        $xmid + $SectionBarWidth - $sectionBarSpace - 100,
                        $header,
                        $fontSizeText,
                        mb_convert_encoding(_("Page ") . $pagenr, 'ISO-8859-1', 'UTF-8'),
                        100,
                        'right'
                    );
                }
                if (strpos($showelements, 't') !== false) {
                    $pdf->addTextWrap(
                        $xleft,
                        $header,
                        10,
                        mb_convert_encoding(
                            html_entity_decode($this->checkRequestKey('pdfTitle')),
                            'ISO-8859-1',
                            'UTF-8'
                        ),
                        $xmid + $SectionBarWidth,
                        'center'
                    );
                }
            }
            $xbase = $xleft;
        } else {
            $pdf->setLineStyle(0.5);
            $pdf->line(
                ($xbase + $SectionBarWidth + $xmid - $sectionBarSpace) / 2,
                $top + $fontSizeText,
                ($xbase + $SectionBarWidth + $xmid - $sectionBarSpace) / 2,
                $bottom + $fontSizeText
            );
            $pdf->setLineStyle(1);
            $xbase = $xmid;
        }
        if (strpos($showelements, 'l') !== false) {
            $pdf->line(
                $xbase - $sectionBarSpace,
                $y + $fontSizeText + $sectionBarSpace,
                $xbase + $SectionBarWidth,
                $y + $fontSizeText + $sectionBarSpace
            );
            reset($reportdata);
            $deltaymax = 0;
            foreach ($reportdata as $key => $dataelement) {
                if ($dataelement['fieldwidth']) {
                    $justification = 'left';
                    if (strpos($dataelement['fieldstyle'], 'r') !== false) {
                        $justification = 'right';
                    }
                    if (strpos($dataelement['fieldstyle'], 'c') !== false) {
                        $justification = 'center';
                    }
                    if (strpos($dataelement['fieldstyle'], 'b') !== false) {
                        $pdf->addText(0, 0, $fontSizeText, '<b>');
                    }
                    if (strpos($dataelement['fieldstyle'], 'i') !== false) {
                        $pdf->addText(0, 0, $fontSizeText, '<i>');
                    }
                    $pdf->addTextWrap(
                        $xbase + $dataelement['fieldposition'],
                        $y - ($deltaline * $dataelement['fieldline']),
                        $fontSizeText,
                        mb_convert_encoding($dataelement['fieldlegend'], 'ISO-8859-1', 'UTF-8'),
                        $dataelement['fieldwidth'],
                        $justification
                    );
                    $deltaymax = max($deltaymax, $dataelement ['fieldline']);
                    if (strpos($dataelement['fieldstyle'], 'b') !== false) {
                        $pdf->addText(0, 0, $fontSizeText, '</b>');
                    }
                    if (strpos($dataelement['fieldstyle'], 'i') !== false) {
                        $pdf->addText(0, 0, $fontSizeText, '</i>');
                    }
                }
            }
            $y -= $deltaline * ($deltaymax);
            $pdf->line(
                $xbase - $sectionBarSpace,
                $y - $sectionBarSpace,
                $xbase + $SectionBarWidth,
                $y - $sectionBarSpace
            );
            $y -= ($deltaline + $sectionBarSpace);
        }
        if ($sort) {
            $y -= $deltalineSection;
            $pdf->rectangle(
                $xbase - $sectionBarSpace,
                $y - $sectionBarSpace,
                $SectionBarWidth,
                $sectionBarHeight
            );
            $pdf->addText(
                $xbase,
                $y,
                $fontSizeSection,
                mb_convert_encoding($GLOBALS[$$sort], 'ISO-8859-1', 'UTF-8')
            );
            $y -= $deltaline + $deltalineSection;
        }
        if ($i) {
            $pdf->addText(0, 0, $fontSizeText, '<i>');
        }
        if ($b) {
            $pdf->addText(0, 0, $fontSizeText, '<b>');
        }
    }

    /**
     * Creates a pdf document from an array of objects.
     *
     * @param string $reportuser   The user for whom to create the report.
     * @param string $reportname   The name for the report.
     * @param string $reportlayout The name of the layout to use.
     * @param array  $result       The array with the objects.
     * @param string $sort         The column to sort on.
     *
     * @return None
     */
    public function pdfReportPersonalised(
        $reportuser,
        $reportname,
        $reportlayout,
        $result,
        $sort = ''
    ) {
        global $objReportLayout, $dateformat, $baseURL, $instDir, $objObserver;
        global $loggedUser, $objLocation, $objInstrument, $objPresentations;

        $result = $this->sortResult($result);

        $reportdata = $objReportLayout->getReportData(
            $reportuser,
            $reportname,
            $reportlayout
        );
        if ($sort == 'objectconstellation') {
            $sort = 'con';
        } else {
            $sort = '';
        }
        $indexlist = array();

        $pagesize = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'pagesize'
        );
        $pageorientation = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'pageorientation'
        );
        $bottom = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'bottom'
        );
        $top = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'top'
        );
        $header = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'header'
        );
        $footer = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'footer'
        );
        $xleft = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'xleft'
        );
        $xmid = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'xmid'
        );
        $fontSizeSection = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'fontSizeSection'
        );
        $fontSizeText = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'fontSizeText'
        );
        $sectionBarSpace = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'sectionbarspace'
        );
        $deltalineSection = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'deltalineSection'
        );
        $deltaline = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'deltalineExtra'
        ) + $fontSizeText;
        $deltaobjectline = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'deltaobjectline'
        );
        $pagenr = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'startpagenumber'
        );
        $sectionBarHeight = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'sectionBarHeightextra'
        ) + $fontSizeSection;
        $SectionBarWidth = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'SectionBarWidthbase'
        ) + $sectionBarSpace;
        $showelements = $objReportLayout->getLayoutFieldPosition(
            $reportuser,
            $reportname,
            $reportlayout,
            'showelements'
        );

        $pdf = new Cezpdf($pagesize, $pageorientation);
        $pdf->selectFont($instDir . 'lib/fonts/Helvetica.afm');

        $actualsort = '';
        $theDate = date('d/m/Y');
        $this->firstpage(
            $y,
            $top,
            $xbase,
            $xmid,
            $pagenr,
            $pdf,
            $xleft,
            $header,
            $fontSizeText,
            $theDate,
            $footer,
            $SectionBarWidth,
            $sectionBarSpace,
            $deltalineSection,
            $sectionBarHeight,
            $fontSizeSection,
            $deltaline,
            $showelements,
            $reportdata
        );

        foreach ($result as $key => $valueA) {
            $con = $valueA['objectconstellation'];
            $deltaymax = 0;
            reset($reportdata);
            foreach ($reportdata as $key => $dataelement) {
                if ($dataelement['fieldwidth']) {
                    if (($dataelement['fieldname'] == "objectlistdescription")) {
                        if (array_key_exists('objectlistdescription', $valueA)
                            && $valueA['objectlistdescription']
                        ) {
                            $deltaymax = max($deltaymax, $dataelement['fieldline']);
                        }
                    } elseif ($dataelement['fieldname'] == "objectdescription") {
                        if (array_key_exists('objectdescription', $valueA)
                            && ($valueA ['objectdescription'] != '')
                        ) {
                            $deltaymax = max($deltaymax, $dataelement['fieldline']);
                        }
                    } else {
                        $deltaymax = max($deltaymax, $dataelement['fieldline']);
                    }
                }
            }
            $deltaymax++;
            if (($y - ($deltaline * $deltaymax) < $bottom) && $sort) {
                $this->newpage(
                    $y,
                    $top,
                    $bottom,
                    $xbase,
                    $xmid,
                    $pagenr,
                    $pdf,
                    $xleft,
                    $header,
                    $fontSizeText,
                    $theDate,
                    $footer,
                    $SectionBarWidth,
                    $sectionBarSpace,
                    $sort,
                    $con,
                    $deltalineSection,
                    $sectionBarHeight,
                    $fontSizeSection,
                    $deltaline,
                    "",
                    "",
                    $showelements,
                    $reportdata
                );
            } elseif (($y - ($deltaline * $deltaymax) < $bottom) && (!($sort))) {
                $this->newpage(
                    $y,
                    $top,
                    $bottom,
                    $xbase,
                    $xmid,
                    $pagenr,
                    $pdf,
                    $xleft,
                    $header,
                    $fontSizeText,
                    $theDate,
                    $footer,
                    $SectionBarWidth,
                    $sectionBarSpace,
                    $sort,
                    $con,
                    $deltalineSection,
                    $sectionBarHeight,
                    $fontSizeSection,
                    $deltaline,
                    "",
                    "",
                    $showelements,
                    $reportdata
                );
                if (strpos($showelements, 's') !== false) {
                    $pdf->setLineStyle(0.5);
                    $pdf->line(
                        $xbase - $sectionBarSpace,
                        $y + (($deltaline + $deltaobjectline) * .75),
                        $xbase + $SectionBarWidth,
                        $y + (($deltaline + $deltaobjectline) * .75)
                    );
                    $pdf->setLineStyle(1);
                }
            } elseif ($sort && ($$sort != $actualsort)) {
                $ycalc = $y - ($deltaline * $deltaymax) -
                    $sectionBarSpace - $deltalineSection;
                if ($ycalc < $bottom) {
                    $this->newpage(
                        $y,
                        $top,
                        $bottom,
                        $xbase,
                        $xmid,
                        $pagenr,
                        $pdf,
                        $xleft,
                        $header,
                        $fontSizeText,
                        $theDate,
                        $footer,
                        $SectionBarWidth,
                        $sectionBarSpace,
                        $sort,
                        $con,
                        $deltalineSection,
                        $sectionBarHeight,
                        $fontSizeSection,
                        $deltaline,
                        "",
                        "",
                        $showelements,
                        $reportdata
                    );
                } else {
                    $y -= $deltalineSection;
                    $pdf->rectangle(
                        $xbase - $sectionBarSpace,
                        $y - $sectionBarSpace,
                        $SectionBarWidth,
                        $sectionBarHeight
                    );
                    $pdf->addText(
                        $xbase,
                        $y,
                        $fontSizeSection,
                        mb_convert_encoding($GLOBALS[$$sort], 'ISO-8859-1', 'UTF-8')
                    );
                    $y -= $deltaline + $deltalineSection;
                }
                $indexlist [$$sort] = $pagenr;
            } elseif (strpos($showelements, 's') !== false) {
                $pdf->setLineStyle(0.5);
                $pdf->line(
                    $xbase - $sectionBarSpace,
                    $y + (($deltaline + $deltaobjectline) * .75),
                    $xbase + $SectionBarWidth,
                    $y + (($deltaline + $deltaobjectline) * .75)
                );
                $pdf->setLineStyle(1);
            }
            reset($reportdata);
            $deltaymax = 0;
            foreach ($reportdata as $key => $dataelement) {
                if ($dataelement['fieldwidth']) {
                    if ($y - ($deltaline * $dataelement['fieldline']) < $bottom) {
                        $this->newpage(
                            $y,
                            $top,
                            $bottom,
                            $xbase,
                            $xmid,
                            $pagenr,
                            $pdf,
                            $xleft,
                            $header,
                            $fontSizeText,
                            $theDate,
                            $footer,
                            $SectionBarWidth,
                            $sectionBarSpace,
                            $sort,
                            $con,
                            $deltalineSection,
                            $sectionBarHeight,
                            $fontSizeSection,
                            $deltaline,
                            "",
                            "",
                            $showelements,
                            $reportdata
                        );
                    }
                    $justification = 'left';
                    $i = '';
                    $b = '';
                    if (strpos($dataelement['fieldstyle'], 'r') !== false) {
                        $justification = 'right';
                    }
                    if (strpos($dataelement['fieldstyle'], 'c') !== false) {
                        $justification = 'center';
                    }
                    if (strpos($dataelement['fieldstyle'], 'b') !== false) {
                        $b = "<b>";
                        $pdf->addText(0, 0, $fontSizeText, '<b>');
                    }
                    if (strpos($dataelement['fieldstyle'], 'i') !== false) {
                        $i = '<i>';
                        $pdf->addText(0, 0, $fontSizeText, '<i>');
                    }
                    if ($dataelement ['fieldname'] == "showname") {
                        if ($valueA [$dataelement ['fieldname']]) {
                            $pdf->addTextWrap(
                                $xbase + $dataelement['fieldposition'],
                                $y - ($deltaline * $dataelement['fieldline']),
                                $fontSizeText,
                                mb_convert_encoding(
                                    $dataelement['fieldafter'] .
                                    html_entity_decode(
                                        $valueA[$dataelement['fieldname']]
                                    ) . $dataelement['fieldafter'],
                                    'ISO-8859-1',
                                    'UTF-8'
                                ),
                                $dataelement['fieldwidth'],
                                $justification
                            );
                            $deltaymax = max($deltaymax, $dataelement['fieldline']);
                        }
                    } elseif ($dataelement['fieldname'] == "objectuseratlaspage") {
                        $pdf->addTextWrap(
                            $xbase + $dataelement['fieldposition'],
                            $y - ($deltaline * $dataelement['fieldline']),
                            $fontSizeText,
                            mb_convert_encoding(
                                $dataelement ['fieldbefore'] .
                                html_entity_decode(
                                    $valueA[($loggedUser
                                        ? $objObserver->getObserverProperty(
                                            $loggedUser,
                                            'standardAtlasCode',
                                            'urano'
                                        ) : 'urano')]
                                ) . $dataelement ['fieldafter'],
                                'ISO-8859-1',
                                'UTF-8'
                            ),
                            $dataelement ['fieldwidth'],
                            $justification
                        );
                        $deltaymax = max($deltaymax, $dataelement['fieldline']);
                    } elseif ($dataelement['fieldname'] == "objectlistdescription") {
                        if (array_key_exists('objectlistdescription', $valueA)
                            && ($valueA['objectlistdescription'] != '')
                        ) {
                            $theText = $dataelement ['fieldbefore'] .
                                html_entity_decode(
                                    $objPresentations->br2nl(
                                        $valueA['objectlistdescription']
                                    )
                                ) . $dataelement ['fieldafter'];
                            $theText = $pdf->addTextWrap(
                                $xbase + $dataelement['fieldposition'],
                                $y - ($deltaline * $dataelement['fieldline']),
                                $fontSizeText,
                                mb_convert_encoding($theText, 'ISO-8859-1', 'UTF-8'),
                                $dataelement['fieldwidth'],
                                $justification
                            );
                            while ($theText) {
                                $y -= $deltaline;
                                $ycalc = $y -
                                    ($deltaline * $dataelement['fieldline']);
                                if ($ycalc < $bottom) {
                                    $this->newpage(
                                        $y,
                                        $top,
                                        $bottom,
                                        $xbase,
                                        $xmid,
                                        $pagenr,
                                        $pdf,
                                        $xleft,
                                        $header,
                                        $fontSizeText,
                                        $theDate,
                                        $footer,
                                        $SectionBarWidth,
                                        $sectionBarSpace,
                                        $sort,
                                        $con,
                                        $deltalineSection,
                                        $sectionBarHeight,
                                        $fontSizeSection,
                                        $deltaline,
                                        $i,
                                        $b,
                                        $showelements,
                                        $reportdata
                                    );
                                    $y += ($deltaline * $dataelement['fieldline']);
                                }
                                $theText = $pdf->addTextWrap(
                                    $xbase + $dataelement['fieldposition'],
                                    $y - ($deltaline * $dataelement['fieldline']),
                                    $fontSizeText,
                                    mb_convert_encoding($theText, 'ISO-8859-1', 'UTF-8'),
                                    $dataelement['fieldwidth'],
                                    $justification
                                );
                            }
                            $deltaymax = max($deltaymax, $dataelement['fieldline']);
                        }
                    } elseif ($dataelement ['fieldname'] == "objectdescription") {
                        if (array_key_exists('objectlistdescription', $valueA)
                            && ($valueA['objectlistdescription'] != '')
                        ) {
                            $theText = $dataelement['fieldbefore'] .
                                html_entity_decode(
                                    $objPresentations->br2nl(
                                        $valueA['objectlistdescription']
                                    )
                                ) . $dataelement ['fieldafter'];
                            $theText = $pdf->addTextWrap(
                                $xbase + $dataelement['fieldposition'],
                                $y - ($deltaline * $dataelement['fieldline']),
                                $fontSizeText,
                                mb_convert_encoding($theText, 'ISO-8859-1', 'UTF-8'),
                                $dataelement['fieldwidth'],
                                $justification
                            );
                            while ($theText) {
                                $y -= $deltaline;
                                $yCalc = $y -
                                    ($deltaline * $dataelement['fieldline']);
                                if ($yCalc < $bottom) {
                                    $this->newpage(
                                        $y,
                                        $top,
                                        $bottom,
                                        $xbase,
                                        $xmid,
                                        $pagenr,
                                        $pdf,
                                        $xleft,
                                        $header,
                                        $fontSizeText,
                                        $theDate,
                                        $footer,
                                        $SectionBarWidth,
                                        $sectionBarSpace,
                                        $sort,
                                        $con,
                                        $deltalineSection,
                                        $sectionBarHeight,
                                        $fontSizeSection,
                                        $deltaline,
                                        $i,
                                        $b,
                                        $showelements,
                                        $reportdata
                                    );
                                    $y += ($deltaline * $dataelement['fieldline']);
                                }
                                $theText = $pdf->addTextWrap(
                                    $xbase + $dataelement['fieldposition'],
                                    $y - ($deltaline * $dataelement['fieldline']),
                                    $fontSizeText,
                                    mb_convert_encoding($theText, 'ISO-8859-1', 'UTF-8'),
                                    $dataelement['fieldwidth'],
                                    $justification
                                );
                            }
                            $deltaymax = max($deltaymax, $dataelement['fieldline']);
                        } elseif (array_key_exists('objectdescription', $valueA)
                            && ($valueA['objectdescription'] != '')
                        ) {
                            $theText = $dataelement ['fieldbefore'] .
                                html_entity_decode(
                                    $objPresentations->br2nl(
                                        $valueA['objectdescription']
                                    )
                                ) . $dataelement['fieldafter'];
                            $theText = $pdf->addTextWrap(
                                $xbase + $dataelement['fieldposition'],
                                $y - ($deltaline * $dataelement ['fieldline']),
                                $fontSizeText,
                                mb_convert_encoding($theText, 'ISO-8859-1', 'UTF-8'),
                                $dataelement['fieldwidth'],
                                $justification
                            );
                            while ($theText) {
                                $y -= $deltaline;
                                $ycalc = $y -
                                    ($deltaline * $dataelement['fieldline']);
                                if ($ycalc < $bottom) {
                                    $this->newpage(
                                        $y,
                                        $top,
                                        $bottom,
                                        $xbase,
                                        $xmid,
                                        $pagenr,
                                        $pdf,
                                        $xleft,
                                        $header,
                                        $fontSizeText,
                                        $theDate,
                                        $footer,
                                        $SectionBarWidth,
                                        $sectionBarSpace,
                                        $sort,
                                        $con,
                                        $deltalineSection,
                                        $sectionBarHeight,
                                        $fontSizeSection,
                                        $deltaline,
                                        $i,
                                        $b,
                                        $showelements,
                                        $reportdata
                                    );
                                    $y += ($deltaline * $dataelement['fieldline']);
                                }
                                $theText = $pdf->addTextWrap(
                                    $xbase + $dataelement['fieldposition'],
                                    $y - ($deltaline * $dataelement['fieldline']),
                                    $fontSizeText,
                                    mb_convert_encoding($theText, 'ISO-8859-1', 'UTF-8'),
                                    $dataelement['fieldwidth'],
                                    $justification
                                );
                            }
                            $deltaymax = max($deltaymax, $dataelement['fieldline']);
                        }
                    } else {
                        if (trim($valueA[$dataelement['fieldname']]) != '') {
                            $pdf->addTextWrap(
                                $xbase + $dataelement['fieldposition'],
                                $y - ($deltaline * $dataelement['fieldline']),
                                $fontSizeText,
                                mb_convert_encoding(
                                    html_entity_decode(
                                        $dataelement['fieldbefore'] .
                                        $valueA[$dataelement['fieldname']] .
                                        $dataelement['fieldafter']
                                    ),
                                    'ISO-8859-1',
                                    'UTF-8'
                                ),
                                $dataelement['fieldwidth'],
                                $justification
                            );
                            $deltaymax = max($deltaymax, $dataelement['fieldline']);
                        }
                    }
                    if (strpos($dataelement['fieldstyle'], 'b') !== false) {
                        $pdf->addText(0, 0, $fontSizeText, '</b>');
                    }
                    if (strpos($dataelement['fieldstyle'], 'i') !== false) {
                        $pdf->addText(0, 0, $fontSizeText, '</i>');
                    }
                }
            }
            $y -= $deltaline * ($deltaymax);
            $y -= ($deltaline + $deltaobjectline);
            if ($sort) {
                $actualsort = $$sort;
            }
        }
        if ((strpos($showelements, 'i') !== false)
            && (count($indexlist) > 0) && ($sort)
        ) {
            $base = $xmid;
            $this->newpage(
                $y,
                $top,
                $bottom,
                $xbase,
                $xmid,
                $pagenr,
                $pdf,
                $xleft,
                $header,
                $fontSizeText,
                $theDate,
                $footer,
                $SectionBarWidth,
                $sectionBarSpace,
                '',
                '',
                $deltalineSection,
                $sectionBarHeight,
                $fontSizeSection,
                $deltaline,
                "",
                "",
                $showelements,
                $reportdata
            );
            $pdf->setLineStyle(0.5);
            $y = $top;
            foreach ($indexlist as $key => $value) {
                $pdf->line(
                    $xbase - $sectionBarSpace,
                    $y + (($deltaline + $deltaobjectline) * .75),
                    $xbase + $SectionBarWidth,
                    $y + (($deltaline + $deltaobjectline) * .75)
                );
                $pdf->addTextWrap(
                    $xbase,
                    $y,
                    $fontSizeText,
                    mb_convert_encoding($key, 'ISO-8859-1', 'UTF-8'),
                    50,
                    'left'
                );
                $pdf->addTextWrap(
                    $xbase + $SectionBarWidth - $sectionBarSpace - 50,
                    $y,
                    $fontSizeText,
                    mb_convert_encoding(trim($value), 'ISO-8859-1', 'UTF-8'),
                    50,
                    'right'
                );

                $y -= ($deltaline + $deltaobjectline);
                if (($y - ($deltaline + $deltaobjectline)) < $bottom) {
                    $this->newpage(
                        $y,
                        $top,
                        $bottom,
                        $xbase,
                        $xmid,
                        $pagenr,
                        $pdf,
                        $xleft,
                        $header,
                        $fontSizeText,
                        $theDate,
                        $footer,
                        $SectionBarWidth,
                        $sectionBarSpace,
                        '',
                        '',
                        $deltalineSection,
                        $sectionBarHeight,
                        $fontSizeSection,
                        $deltaline,
                        "",
                        "",
                        $showelements,
                        $reportdata
                    );
                    $pdf->setLineStyle(0.5);
                }
            }
        }
        $pdf->ezStream();
    }

    /**
     * Creates a pdf document from an array of observations.
     *
     * @param array $result The array with the observations.
     *
     * @return string The pdf file with the observations.
     */
    public function pdfObservations($result)
    {
        global $loggedUser, $dateformat, $instDir, $objObservation, $objObserver;
        global $objInstrument, $objLocation, $objPresentations, $objObject;
        global $objFilter, $objEyepiece, $objLens;

        $result = $this->sortResult($result);
        $pdf = new Cezpdf('a4', 'portrait');
        $pdf->ezStartPageNumbers(300, 30, 10);
        $pdf->selectFont($instDir . 'lib/fonts/Helvetica.afm');
        $pdf->ezText(mb_convert_encoding(html_entity_decode($_GET['pdfTitle']) . "\n", 'ISO-8859-1', 'UTF-8'));
        $i = 0;
        foreach ($result as $key => $value) {
            if ($i++ > 0) {
                $pdf->ezNewPage();
            }
            $obs = $objObservation->getAllInfoDsObservation($value['observationid']);
            $object = $objObject->getAllInfoDsObject($obs['objectname']);
            if ($loggedUser
                && ($objObserver->getObserverProperty($loggedUser, 'UT'))
            ) {
                $date = sscanf($obs["date"], "%4d%2d%2d");
            } else {
                $date = sscanf($obs["localdate"], "%4d%2d%2d");
            }
            if ($obs['seeing'] > -1) {
                $seeing = true;
            } else {
                $seeing = false;
            }
            $formattedDate = date(
                $dateformat,
                mktime(0, 0, 0, $date[1], $date[2], $date[0])
            );
            $temp = array(
                "Name" => html_entity_decode(_("Name")) . " : "
                    . $obs['objectname'],
                "altname" => html_entity_decode(_("Alternative name")) . " : "
                    . $object["altname"],
                "type" => $GLOBALS[$object['type']]
                    . html_entity_decode(_(" in "))
                    . $GLOBALS[$object['con']],
                "visibility" => (($obs['visibility'])
                    ? (html_entity_decode(_("Visibility")) . " : "
                    . $GLOBALS['Visibility' . $obs['visibility']])
                    : ''),
                "seeing" => (($seeing) ? (_("Seeing") . " : "
                    . $GLOBALS['Seeing' . $obs ['seeing']]) : ''),
                "limmag" => (($obs['limmag']) ? (_("Limiting magnitude") . " : "
                    . $obs ['limmag']) : ''),
                "filter" => (($obs['filterid']) ? (_("Filter")
                    . " : "
                    . $objFilter->getFilterPropertyFromId($obs['filterid'], 'name'))
                    : ''),
                "eyepiece" => (($obs['eyepieceid']) ? (
                    _("Eyepiece")
                    . " : "
                    . $objEyepiece->getEyepiecePropertyFromId(
                        $obs['eyepieceid'],
                        'name'
                    )
                )
                    : ''),
                "lens" => (($obs['lensid'])
                    ? (_("Lens") . " : "
                    . $objLens->getLensPropertyFromId($obs['lensid'], 'name')) : ''),
                "observer" => html_entity_decode(
                    sprintf(
                        _("Observed by %s on %s"),
                        $objObserver->getObserverProperty(
                            $obs['observerid'],
                            'firstname'
                        )
                        . " "
                        . $objObserver->getObserverProperty($obs['observerid'], 'name'),
                        $formattedDate
                    )
                ),
                "instrument" => html_entity_decode(_("Instrument")) . " : "
                    . $objInstrument->getInstrumentPropertyFromId(
                        $obs['instrumentid'],
                        'name'
                    ),
                "location" => html_entity_decode(_("Location")) . " : "
                    . $objLocation->getLocationPropertyFromId(
                        $obs['locationid'],
                        'name'
                    ),
                "description" => $objPresentations->br2nl(
                    html_entity_decode($obs['description'])
                ),
                "desc" => html_entity_decode(_("Description"))
            );
            $obs1[] = $temp;
            $nm = $obs['objectname'];
            if ($object["altname"]) {
                $nm = $nm . " (" . $object["altname"] . ")";
            }
            $pdf->ezText($nm, "14");
            $tmp = array(
                array(
                    "type" => $temp ["type"]
                )
            );
            $pdf->ezTable(
                $tmp,
                array(
                    "type" => mb_convert_encoding(html_entity_decode(_("Type")), 'ISO-8859-1', 'UTF-8')
                ),
                "",
                array(
                    "width" => "500",
                    "showHeadings" => "0",
                    "showLines" => "0",
                    "shaded" => "0"
                )
            );
            $tmp = array(
                array(
                    "location" => $temp["location"],
                    "instrument" => $temp["instrument"]
               )
            );
            $pdf->ezTable(
                $tmp,
                array(
                    "location" => mb_convert_encoding(html_entity_decode(_("Name")), 'ISO-8859-1', 'UTF-8'),
                    "instrument" => mb_convert_encoding(html_entity_decode(_("Alternative name")), 'ISO-8859-1', 'UTF-8')
                ),
                "",
                array(
                    "width" => "500",
                    "showHeadings" => "0",
                    "showLines" => "0",
                    "shaded" => "0"
                )
            );
            $tmp = array(
                array(
                    "eyepiece" => $temp["eyepiece"]
                )
            );
            if ($obs['eyepieceid']) {
                $pdf->ezTable(
                    $tmp,
                    array(
                        "eyepiece" => "test"
                    ),
                    "",
                    array(
                        "width" => "500",
                        "showHeadings" => "0",
                        "showLines" => "0",
                        "shaded" => "0"
                    )
                );
            }
            $tmp = array(
                array(
                    "filter" => $temp["filter"]
                )
            );
            if ($obs['filterid']) {
                $pdf->ezTable(
                    $tmp,
                    array(
                        "filter" => "test"
                    ),
                    "",
                    array(
                        "width" => "500",
                        "showHeadings" => "0",
                        "showLines" => "0",
                        "shaded" => "0"
                    )
                );
            }
            $tmp = array(
                array(
                    "lens" => $temp["lens"]
                )
            );
            if ($obs['lensid']) {
                $pdf->ezTable(
                    $tmp,
                    array(
                        "lens" => "test"
                    ),
                    "",
                    array(
                        "width" => "500",
                        "showHeadings" => "0",
                        "showLines" => "0",
                        "shaded" => "0"
                    )
                );
            }
            $tmp = array(
                array(
                    "seeing" => $temp["seeing"]
                )
            );
            if ($seeing) {
                $pdf->ezTable(
                    $tmp,
                    array(
                        "seeing" => "test"
                    ),
                    "",
                    array(
                        "width" => "500",
                        "showHeadings" => "0",
                        "showLines" => "0",
                        "shaded" => "0"
                    )
                );
            }
            $tmp = array(
                array(
                    "limmag" => $temp["limmag"]
               )
            );
            if ($obs['limmag']) {
                $pdf->ezTable(
                    $tmp,
                    array(
                        "limmag" => "test"
                    ),
                    "",
                    array(
                        "width" => "500",
                        "showHeadings" => "0",
                        "showLines" => "0",
                        "shaded" => "0"
                    )
                );
            }
            $tmp = array(
                array(
                    "visibility" => $temp["visibility"]
                )
            );
            if ($obs['visibility']) {
                $pdf->ezTable(
                    $tmp,
                    array(
                        "visibility" => "test"
                    ),
                    "",
                    array(
                        "width" => "500",
                        "showHeadings" => "0",
                        "showLines" => "0",
                        "shaded" => "0"
                    )
                );
            }
            $tmp = array(
                array(
                    "observer" => $temp["observer"]
                )
            );
            $pdf->ezTable(
                $tmp,
                array(
                    "observer" => mb_convert_encoding(html_entity_decode(_("Name")), 'ISO-8859-1', 'UTF-8')
                ),
                "",
                array(
                    "width" => "500",
                    "showHeadings" => "0",
                    "showLines" => "0",
                    "shaded" => "0"
                )
            );
            $pdf->ezText("");

            $pdf->ezText(mb_convert_encoding(_("Description"), 'ISO-8859-1', 'UTF-8'), "12");
            $pdf->ezText("");
            $tmp = array(
                array(
                    "description" => $temp["description"]
                )
            );

            $pdf->ezTable(
                $tmp,
                array(
                    "description" => mb_convert_encoding(html_entity_decode(_("Name")), 'ISO-8859-1', 'UTF-8')
                ),
                "",
                array(
                    "width" => "500",
                    "showHeadings" => "0",
                    "showLines" => "0",
                    "shaded" => "0"
                )
            );
            $hasDrawing = $objObservation->getDsObservationProperty(
                $value['observationid'],
                'hasDrawing'
            );
            if ($hasDrawing) {
                $pdf->ezText("");
                $pdf->ezImage(
                    $instDir . "deepsky/drawings/" . $value['observationid']
                    . ".jpg",
                    0,
                    500,
                    "none",
                    "left"
                );
            }
            $pdf->ezText("");
        }
        $pdf->ezStream();
    }

    /**
     * Sorts a record set.
     *
     * @param array $data The record set to sort
     *
     * @return array The sorted record set.
     */
    public function recordsetSort(array $data)
    {
        $_argList = func_get_args();
        $_data = array_shift($_argList);
        if (empty($_data)) {
            return $_data;
        }
        $_max = count($_argList);
        $_params = array();
        $_cols = array();
        $_rules = array();
        for ($_i = 0; $_i < $_max; $_i += 3) {
            $_name = ( string ) $_argList [$_i];
            if (!in_array($_name, array_keys(current($_data)))) {
                continue;
            }
            if (!isset($_argList[($_i + 1)]) || is_string($_argList[($_i + 1)])) {
                $_order = SORT_ASC;
                $_mode = SORT_REGULAR;
                $_i -= 2;
            } elseif (3 > $_argList[($_i + 1)]) {
                $_order = SORT_ASC;
                $_mode = $_argList[($_i + 1)];
                $_i--;
            } else {
                $_order = $_argList[($_i + 1)] == SORT_ASC ? SORT_ASC : SORT_DESC;
                if (!isset($_argList[($_i + 2)])
                    || is_string($_argList[($_i + 2)])
                ) {
                    $_mode = SORT_REGULAR;
                    $_i--;
                } else {
                    $_mode = $_argList[($_i + 2)];
                }
            }
            $_mode = (($_mode != SORT_NUMERIC)
                ? (($_argList[($_i + 2)] != SORT_STRING)
                    ? SORT_REGULAR : SORT_STRING)
                : SORT_NUMERIC);
            $_rules[] = array(
                'name' => $_name,
                'order' => $_order,
                'mode' => $_mode
            );
        }
        foreach ($_data as $_k => $_row) {
            foreach ($_rules as $_rule) {
                if (!isset($_cols[$_rule['name']])) {
                    $_cols[$_rule['name']] = array();
                    $_params[] = &$_cols[$_rule['name']];
                    $_params[] = $_rule['order'];
                    $_params[] = $_rule['mode'];
                }
                $_cols[$_rule['name']][$_k] = strtolower($_row[$_rule['name']]);
            }
        }
        $_params[] = &$_data;
        call_user_func_array('array_multisort', $_params);
        return $_data;
    }

    /**
     * Removes a string from a link.
     *
     * @param string $link  The link to use.
     * @param string $value The value to remove from the link.
     *
     * @return string The link without the string.
     */
    public function removeFromLink($link, $value)
    {
        return (($a = strpos($link, $value))
            ? (($b = strpos($link, '&', $a + 1))
            ? substr($link, 0, $a) . substr($link, $b)
            : substr($link, 0, $a - 5))
            : $link);
    }

    /**
     * Returns an rss file with deepsky and comet observations of the last month.
     *
     * @return string The rss file with observations.
     */
    public function rssObservations()
    {
        global $objObservation, $objInstrument, $objLocation, $objPresentations;
        global $objObserver, $baseURL, $objUtil;
        $dom = new DomDocument('1.0', 'US-ASCII');

        // add root fcga -> The header
        $rssInfo = $dom->createElement('rss');
        $rssDom = $dom->appendChild($rssInfo);

        $attr = $dom->createAttribute("version");
        $rssInfo->appendChild($attr);

        $attrText = $dom->createTextNode("2.0");
        $attr->appendChild($attrText);

        $attr = $dom->createAttribute("xmlns:content");
        $rssInfo->appendChild($attr);

        $attrText = $dom->createTextNode("http://purl.org/rss/1.0/modules/content/");
        $attr->appendChild($attrText);

        $attr = $dom->createAttribute("xmlns:dc");
        $rssInfo->appendChild($attr);

        $attrText = $dom->createTextNode("http://purl.org/dc/elements/1.1/");
        $attr->appendChild($attrText);

        $attr = $dom->createAttribute("xmlns:atom");
        $rssInfo->appendChild($attr);

        $attrText = $dom->createTextNode("http://www.w3.org/2005/Atom");
        $attr->appendChild($attrText);

        // add root - <channel>
        $channelDom = $rssDom->appendChild($dom->createElement('channel'));

        // add root - <channel> - <title>
        $titleDom = $channelDom->appendChild($dom->createElement('title'));
        $titleDom->appendChild($dom->createTextNode("DeepskyLog"));

        // add root - <channel> - <description>
        $descDom = $channelDom->appendChild($dom->createElement('description'));
        $descDom->appendChild(
            $dom->createTextNode(
                "DeepskyLog - visual deepsky and comets observations"
            )
        );

        // add root - <channel> - <atom>
        $atomDom = $channelDom->appendChild($dom->createElement('atom:link'));

        $attr = $dom->createAttribute("href");
        $atomDom->appendChild($attr);

        $attrText = $dom->createTextNode($baseURL . "observations.rss");
        $attr->appendChild($attrText);

        $attr = $dom->createAttribute("rel");
        $atomDom->appendChild($attr);

        $attrText = $dom->createTextNode("self");
        $attr->appendChild($attrText);

        $attr = $dom->createAttribute("type");
        $atomDom->appendChild($attr);

        $attrText = $dom->createTextNode("application/rss+xml");
        $attr->appendChild($attrText);

        // add root - <channel> - <link>
        $linkDom = $channelDom->appendChild($dom->createElement('link'));
        $linkDom->appendChild($dom->createTextNode("http://www.deepskylog.org/"));

        $theDate = date('r');

        // add root - <channel> - <link>
        $lbdDom = $channelDom->appendChild($dom->createElement('lastBuildDate'));
        $lbdDom->appendChild($dom->createTextNode($theDate));

        // Get the new deepsky observations of the last month
        $theDate = date('Ymd', strtotime('-1 month'));

        $_GET['minyear'] = substr($theDate, 0, 4);
        $_GET['minmonth'] = substr($theDate, 4, 2);
        $_GET['minday'] = substr($theDate, 6, 2);

        $query = array(
            "catalog" => '%',
            "mindate" => $objUtil->checkGetDate('minyear', 'minmonth', 'minday')
        );

        $result = $objObservation->getObservationFromQuery($query, 'A');

        foreach ($result as $key => $value) {
            // add root - <channel> - <item>
            $itemDom = $channelDom->appendChild($dom->createElement('item'));

            $titleDom = $itemDom->appendChild($dom->createElement('title'));
            $titleDom->appendChild(
                $dom->createTextNode(
                    $value['observername'] . " : " . $value['objectname']
                    . " with " . htmlspecialchars_decode(
                        $objInstrument->getInstrumentPropertyFromId(
                            $value['instrumentid'],
                            'name'
                        )
                    ) . " from " .
                    $objLocation->getLocationPropertyFromId(
                        $objObservation->getDsObservationProperty(
                            $value['observationid'],
                            'locationid'
                        ),
                        'name'
                    )
                )
            );
            $linkDom = $itemDom->appendChild($dom->createElement('link'));
            $linkDom->appendChild(
                $dom->createCDATASection(
                    $baseURL
                    . "index.php?indexAction=detail_observation&observation="
                    . $value['observationid'] . "&QobsKey=0&dalm=D"
                )
            );

            $descDom = $itemDom->appendChild($dom->createElement('description'));
            $descDom->appendChild(
                $dom->createCDATASection(
                    $objPresentations->br2nl(
                        mb_convert_encoding($value['observationdescription'], 'ISO-8859-1', 'UTF-8')
                    )
                )
            );

            $authorDom = $itemDom->appendChild($dom->createElement('dc:creator'));
            $authorDom->appendChild(
                $dom->createCDATASection($value['observername'])
            );

            $guidDom = $itemDom->appendChild($dom->createElement('guid'));
            $guidDom->appendChild(
                $dom->createTextNode("deepsky" . $value['observationid'])
            );

            $attr = $dom->createAttribute("isPermaLink");
            $guidDom->appendChild($attr);

            $attrText = $dom->createTextNode("false");
            $attr->appendChild($attrText);

            $pubDateDom = $itemDom->appendChild($dom->createElement('pubDate'));

            date_default_timezone_set('UTC');

            $time = -999;

            $obs = $objObservation->getAllInfoDsObservation($value['observationid']);
            $time = $obs['time'];

            if ($time >= "0") {
                $hour = (int)($time / 100);
                $minute = $time - (100 * $hour);
            } else {
                $hour = 0;
                $minute = 0;
            }
            $date = $value['observationdate'];

            $year = substr($date, 0, 4);
            $month = substr($date, 4, 2);
            $day = substr($date, 6, 2);

            $pubDateDom->appendChild(
                $dom->createTextNode(
                    date("r", mktime($hour, $minute, 0, $month, $day, $year))
                )
            );
        }

        include_once "cometobjects.php";
        include_once "observers.php";
        include_once "instruments.php";
        include_once "locations.php";
        include_once "cometobservations.php";
        include_once "icqmethod.php";
        include_once "icqreferencekey.php";
        global $instDir, $objCometObject;
        $objects = new CometObjects();
        $observer = new Observers();
        $instrument = new Instruments();
        $observation = new CometObservations();
        $location = new Locations();
        $util = $this;
        $ICQMETHODS = new ICQMETHOD();
        $ICQREFERENCEKEYS = new ICQREFERENCEKEY();

        $cometsResult = $observation->getObservationFromQuery($query);

        foreach ($cometsResult as $key => $value) {
            $objectname = $objCometObject->getName(
                $observation->getObjectId($value)
            );

            // add root - <channel> - <item>
            $itemDom = $channelDom->appendChild($dom->createElement('item'));

            $title = htmlspecialchars_decode($objectname);

            // Location and instrument
            if ($observation->getLocationId($value) != 0
                && $observation->getLocationId($value) != 1
            ) {
                $title = $title . " from " .
                    htmlspecialchars_decode(
                        $location->getLocationPropertyFromId(
                            $observation->getLocationId($value),
                            'name'
                        )
                    );
            }

            if ($observation->getInstrumentId($value) != 0) {
                $title = $title . " with " .
                    htmlspecialchars_decode(
                        $instrument->getInstrumentPropertyFromId(
                            $observation->getInstrumentId($value),
                            'name'
                        )
                    );
            }

            $titleDom = $itemDom->appendChild($dom->createElement('title'));
            $titleDom->appendChild($dom->createTextNode($title));
            $linkDom = $itemDom->appendChild($dom->createElement('link'));
            $linkDom->appendChild(
                $dom->createCDATASection(
                    $baseURL .
                    "index.php?indexAction=comets_detail_observation&observation=" .
                    $value
                )
            );

            // Description
            $description = $observation->getDescription($value);

            if (strcmp($description, "") != 0) {
                $descDom = $itemDom->appendChild($dom->createElement('description'));
                $descDom->appendChild(
                    $dom->createCDATASection(
                        $objPresentations->br2nl(mb_convert_encoding($description, 'ISO-8859-1', 'UTF-8'))
                    )
                );
            } else {
                $descDom = $itemDom->appendChild($dom->createElement('description'));
                $descDom->appendChild($dom->createCDATASection(""));
            }

            $observerid = $observation->getObserverId($value);
            $observername = $observer->getObserverProperty(
                $observerid,
                'firstname'
            ) . " " . $observer->getObserverProperty($observerid, 'name');

            $authorDom = $itemDom->appendChild($dom->createElement('dc:creator'));
            $authorDom->appendChild($dom->createCDATASection($observername));

            $guidDom = $itemDom->appendChild($dom->createElement('guid'));
            $guidDom->appendChild($dom->createTextNode("comet" . $value));

            $attr = $dom->createAttribute("isPermaLink");
            $guidDom->appendChild($attr);

            $attrText = $dom->createTextNode("false");
            $attr->appendChild($attrText);

            $pubDateDom = $itemDom->appendChild($dom->createElement('pubDate'));

            date_default_timezone_set('UTC');

            $date = sscanf($observation->getLocalDate($value), "%4d%2d%2d");
            $time = $observation->getLocalTime($value);

            $hour = (int) ($time / 100);
            $minute = $time - $hour * 100;

            $pubDateDom->appendChild(
                $dom->createTextNode(
                    date(
                        "r",
                        mktime($hour, $minute, 0, $date[1], $date[2], $date[0])
                    )
                )
            );
        }

        // generate xml
        $dom->formatOutput = true; // set the formatOutput attribute of
        // domDocument to true
        // save XML as string or file
        $out = $dom->saveXML();   // put string in test1

        print $out;
    }

    /**
     * Checks which action to do when something is clicked in the quick pick panel.
     *
     * @return string The file to include or no file at all.
     */
    private function _utilitiesCheckIndexActionDSquickPick()
    {
        global $objObject, $entryMessage;

        if ($this->checkGetKey('indexAction') == 'quickpick') {
            if ($this->checkGetKey('object')) {
                if ($temp = $objObject->getExactDsObject($_GET['object'])) {
                    $_GET['object'] = $temp;
                    if (array_key_exists('searchObservationsQuickPick', $_GET)) {
                        return 'lib/observations_new.php';
                    } elseif (array_key_exists('newObservationQuickPick', $_GET)) {
                        return 'deepsky/content/newObservation.php';
                    } else {
                        return 'deepsky/content/view_object.php';
                    }
                } else {
                    $_GET ['object'] = ucwords(trim($_GET['object']));
                    if (array_key_exists('searchObservationsQuickPick', $_GET)) {
                        return 'deepsky/content/selected_observations.php';
                    } elseif (array_key_exists('newObservationQuickPick', $_GET)) {
                        return 'deepsky/content/setup_objects_query.php';
                    } else {
                        return 'deepsky/content/setup_objects_query.php';
                    }
                }
            } else {
                if (array_key_exists('searchObservationsQuickPick', $_GET)) {
                    return 'deepsky/content/setup_observations_query.php';
                } elseif (array_key_exists('newObservationQuickPick', $_GET)) {
                    return 'deepsky/content/newObservation.php';
                } else {
                    return 'deepsky/content/setup_objects_query.php';
                }
            }
        }
    }

    /**
     * Goes to the default page for the comets or deepsky module.
     *
     * @return string The file to include or no file at all.
     */
    private function _utilitiesGetIndexActionDefaultAction()
    {
        global $lastReadObservation, $loggedUser, $objObserver;
        if ($_SESSION['module'] == 'deepsky') {
            $_GET['catalog'] = '%';
            $theDate = date('Ymd', strtotime('-1 month'));
            $_GET['minyear'] = substr($theDate, 0, 4);
            $_GET['minmonth'] = substr($theDate, 4, 2);
            $_GET['minday'] = substr($theDate, 6, 2);
            $lastReadObservation = ($loggedUser
                ? $objObserver->getLastReadObservation($loggedUser) : -1);
            return 'deepsky/content/main.php';
        } elseif ($_SESSION['module'] == 'comets') {
            $theDate = date('Ymd', strtotime('-1 year'));
            $_GET['minyear'] = substr($theDate, 0, 4);
            $_GET['minmonth'] = substr($theDate, 4, 2);
            $_GET['minday'] = substr($theDate, 6, 2);
            $_GET['observer'] = '';
            $_GET['instrument'] = '';
            $_GET['site'] = '';
            $_GET['maxyear'] = '';
            $_GET['maxmonth'] = '';
            $_GET['maxday'] = '';
            $_GET['mindiameter'] = '';
            $_GET['maxdiameter'] = '';
            $_GET['mindiameterunits'] = '';
            $_GET['maxdiameterunits'] = '';
            $_GET['maxmag'] = '';
            $_GET['minmag'] = '';
            $_GET['description'] = '';
            $_GET['object'] = '';
            $_GET['mintail'] = '';
            $_GET['maxtail'] = '';
            $_GET['mincoma'] = '';
            $_GET['maxcoma'] = '';
            $_GET['mindc'] = '';
            $_GET['maxdc'] = '';
            return 'comets/content/selected_observations2.php';
        }
    }

    /**
     * Decides which page to visit.
     *
     * @return string The file to include or no file at all.
     */
    public function utilitiesDispatchIndexAction()
    {
        global $loggedUser;

        $admin = false;
        if (array_key_exists('admin', $_SESSION)
            && ($_SESSION['admin'] == "yes")
        ) {
            $admin = true;
        }

        $action = "";
        if (array_key_exists('indexAction', $_GET)) {
            $action = $_GET['indexAction'];
        }

        switch ($action) {
            case 'add_csv':
                if ($loggedUser) {
                    return 'deepsky/content/new_observationcsv.php';
                }
                break;
            case 'add_xml':
                if ($loggedUser) {
                    return 'deepsky/content/newObservationXml.php';
                }
                break;
            case 'add_xml_invalid':
                if ($loggedUser) {
                    return 'deepsky/content/newObservationXmlInvalid.php';
                }
                break;
            case 'add_object':
                if ($loggedUser) {
                    return 'deepsky/content/new_object.php';
                }
                break;
            case 'add_observation':
                if ($loggedUser) {
                    return 'deepsky/content/newObservation.php';
                }
                break;
            case 'detail_object':
                return 'deepsky/content/view_object.php';
                break;
            case 'changeToken':
                return 'common/content/change_password.php';
                break;
            case 'detail_observation':
                return 'deepsky/content/view_observation.php';
                break;
            case 'view_catalogs':
                return 'deepsky/content/view_catalogs.php';
                break;
            case 'atlaspage':
                return 'deepsky/content/dsatlas.php';
                break;
            case 'downloadAstroImageCatalogs':
                return 'deepsky/content/downloadastroimagecatalogs.php';
                break;
            case 'import_csv_list':
                if ($loggedUser) {
                    return 'deepsky/content/new_listdatacsv.php';
                }
                break;
            case 'listaction':
                return 'deepsky/content/tolist.php';
                break;
            case 'manage_csv_object':
                if ($loggedUser && $admin) {
                    return 'deepsky/content/manage_objects_csv.php';
                }
                break;
            case 'query_objects':
                return 'deepsky/content/setup_objects_query.php';
                break;
            case 'query_observations':
                return 'deepsky/content/setup_observations_query.php';
                break;
            case 'rank_objects':
                return 'deepsky/content/top_objects.php';
                break;
            case 'rank_observers':
                return 'deepsky/content/top_observers.php';
                break;
            case 'statistics':
                return 'common/content/statistics.php';
                break;
            case 'view_lenses':
                return 'common/content/view_lenses.php';
                break;
            case 'overview_lenses':
                if ($loggedUser && $admin) {
                    return 'common/content/overview_lenses.php';
                }
                break;
            case 'result_query_objects':
                return 'deepsky/content/selected_objects.php';
                break;
            case 'result_selected_observations':
                return 'deepsky/content/selected_observations.php';
                break;
            case 'show_drawings':
                return 'deepsky/content/showDrawings.php';
                break;
            case 'result_selected_sessions':
                return 'deepsky/content/selected_sessions.php';
                break;
            case 'result_my_sessions':
                return 'deepsky/content/my_sessions.php';
                break;
            case 'view_observer_catalog':
                return 'deepsky/content/details_observer_catalog.php';
                break;
            case 'objectsSets':
                return 'common/content/objectsSets.php';
                break;
            case 'view_atlaspages':
                return 'common/content/atlasPages.php';
                break;
            case 'change_account':
                if ($loggedUser) {
                    return 'common/content/change_account.php';
                }
                break;
            case 'adapt_eyepiece':
                if ($loggedUser) {
                    return 'common/content/change_eyepiece.php';
                }
                break;
            case 'adapt_filter':
                if ($loggedUser) {
                    return 'common/content/change_filter.php';
                }
                break;
            case 'adapt_instrument':
                if ($loggedUser) {
                    return 'common/content/change_instrument.php';
                }
                break;
            case 'adapt_lens':
                if ($loggedUser) {
                    return 'common/content/change_lens.php';
                }
                break;
            case 'adapt_site':
                if ($loggedUser) {
                    return 'common/content/change_site.php';
                }
                break;
            case 'adapt_session':
                if ($loggedUser) {
                    return 'deepsky/content/change_session.php';
                }
                break;
            case 'add_session':
                if ($loggedUser) {
                    return 'deepsky/content/new_session.php';
                }
                break;
            case 'add_eyepiece':
                if ($loggedUser) {
                    return 'common/content/new_eyepiece.php';
                }
                break;
            case 'add_filter':
                if ($loggedUser) {
                    return 'common/content/new_filter.php';
                }
                break;
            case 'add_instrument':
                if ($loggedUser) {
                    return 'common/content/new_instrument.php';
                }
                break;
            case 'add_lens':
                if ($loggedUser) {
                    return 'common/content/new_lens.php';
                }
                break;
            case 'view_sites':
                if ($loggedUser) {
                    return 'common/content/locations.php';
                }
                break;
            case 'view_lists':
                if ($loggedUser) {
                    return 'deepsky/content/view_list.php';
                }
                break;
            case 'detail_eyepiece':
                return 'common/content/change_eyepiece.php';
                break;
            case 'detail_filter':
                return 'common/content/change_filter.php';
                break;
            case 'detail_instrument':
                return 'common/content/change_instrument.php';
                break;
            case 'detail_lens':
                return 'common/content/change_lens.php';
                break;
            case 'detail_location':
                return 'common/content/change_site.php';
                break;
            case 'detail_observer':
                return 'common/content/view_observer.php';
                break;
            case 'message':
                return 'common/content/message.php';
                break;
            case 'reportsLayout':
                if ($loggedUser) {
                    return 'common/content/reportslayout.php';
                }
                break;
            case 'site_result':
                if ($loggedUser) {
                    return 'common/content/getLocation.php';
                }
                break;
            case 'subscribe':
                return 'common/content/register.php';
                break;
            case 'privacy':
                return 'common/content/privacy.php';
                break;
            case 'overview_eyepieces':
                if ($loggedUser) {
                    return 'common/content/overview_eyepieces.php';
                }
                break;
            case 'view_eyepieces':
                if ($loggedUser) {
                    return 'common/content/view_eyepieces.php';
                }
                break;
            case 'view_filters':
                if ($loggedUser) {
                    return 'common/content/view_filters.php';
                }
                break;
            case 'overview_filters':
                if ($loggedUser) {
                    return 'common/content/overview_filters.php';
                }
                break;
            case 'view_instruments':
                if ($loggedUser) {
                    return 'common/content/view_instruments.php';
                }
                break;
            case 'overview_instruments':
                if ($loggedUser) {
                    return 'common/content/overview_instruments.php';
                }
                break;
            case 'view_lenses':
                if ($loggedUser) {
                    return 'common/content/overview_lenses.php';
                }
                break;
            case 'overview_locations':
                if ($loggedUser) {
                    return 'common/content/overview_locations.php';
                }
                break;
            case 'add_location':
                if ($loggedUser) {
                    return 'common/content/new_location.php';
                }
                break;
            case 'view_observers':
                if ($loggedUser) {
                    return 'common/content/overview_observers.php';
                }
                break;
            case 'show_messages':
                return 'common/content/messages.php';
                break;
            case 'view_message':
                return 'common/content/view_message.php';
                break;
            case 'new_message':
                return 'common/content/new_message.php';
                break;
            case 'admin_check_objects':
                if ($loggedUser) {
                    return 'deepsky/control/admincheckobjects.php';
                }
                break;
            case 'comets_all_observations':
                return 'comets/content/overview_observations.php';
                break;
            case 'comets_detail_object':
                return 'comets/content/view_object.php';
                break;
            case 'comets_detail_observation':
                return 'comets/content/view_observation.php';
                break;
            case 'comets_adapt_observation':
                if ($loggedUser) {
                    return 'comets/content/new_observation.php';
                }
                break;
            case 'comets_add_observation':
                if ($loggedUser) {
                    return 'comets/content/new_observation.php';
                }
                break;
            case 'comets_result_query_observations':
                return 'comets/content/selected_observations.php';
                break;
            case 'comets_detail_observation':
                return 'comets/content/view_observation.php';
                break;
            case 'comets_add_object':
                if ($loggedUser) {
                    return 'comets/content/new_object.php';
                }
                break;
            case 'comets_detail_object':
                return 'comets/content/view_object.php';
                break;
            case 'comets_view_objects':
                return 'comets/content/overview_objects.php';
                break;
            case 'comets_all_observations':
                return 'comets/content/overview_observations.php';
                break;
            case 'comets_result_query_objects':
                return 'comets/content/execute_query_objects.php';
                break;
            case 'comets_result_selected_observations':
                return 'comets/content/selected_observations2.php';
                break;
            case 'comets_rank_observers':
                return 'comets/content/top_observers.php';
                break;
            case 'comets_rank_objects':
                return 'comets/content/top_objects.php';
                break;
            case 'comets_query_observations':
                return 'comets/content/setup_observations_query.php';
                break;
            case 'comets_query_objects':
                return 'comets/content/setup_objects_query.php';
                break;
            case 'main':
                return 'deepsky/content/main.php';
                break;
            case 'downloadForms':
                return 'common/content/downloadForms.php';
                break;
            case 'quickpick':
                return $this->_utilitiesCheckIndexActionDSquickPick();
                break;
            default:
                return $this->_utilitiesGetIndexActionDefaultAction();
        }
    }

    /**
     * Sets the cookie for the used module.
     *
     * @param string $module The used module (comets or deepsky)
     *
     * @return None
     */
    public function utilitiesSetModuleCookie($module)
    {
        if (!array_key_exists('module', $_SESSION)
            || (array_key_exists('module', $_SESSION)
            && $_SESSION['module'] != $module)
        ) {
            $_SESSION['module'] = $module;
            $cookietime = time() + 365 * 24 * 60 * 60; // 1 year
            setcookie("module", $module, $cookietime, "/", "", false);
        }
    }

    /**
     * Returns a string describing the number of drawings already made.
     *
     * @param string $number The number of drawings.
     *
     * @return string The number of drawings already made.
     */
    public function getDrawAccomplishment($number)
    {
        return sprintf(_('You have made %s drawings'), $number);
    }

    /**
     * Returns a string describing the number of drawings still to make.
     *
     * @param string $number The number of drawings.
     *
     * @return string The number of drawings still to make.
     */
    public function getDrawToAccomplish($number)
    {
        return sprintf(
            _('Make %s drawings to receive this DeepskyLog star!'),
            $number
        );
    }

    /**
     * Returns a string describing the number of observations already made.
     *
     * @param string $number The number of observations.
     *
     * @return string The number of observations already made.
     */
    public function getSeenAccomplishment($number)
    {
        return sprintf(_('You have made %s observations!'), $number);
    }

    /**
     * Returns a string describing the number of observations still to make.
     *
     * @param string $number The number of observations.
     *
     * @return string The number of observations still to make.
     */
    public function getSeenToAccomplish($number)
    {
        return sprintf(
            _('Make %s observations to receive this DeepskyLog star!'),
            $number
        );
    }

    /**
     * Returns a string with the html code to add a button to select to columns
     * in a table.
     *
     * @return string The html code to add a button to select to columns in
     *                a table.
     */
    public function addTableColumSelector()
    {
        // Add the button for the columns
        echo "
            <div class=\"columnSelectorWrapper\">
              <input id=\"colSelect1\" type=\"checkbox\" class=\"hidden\">
              <label class=\"columnSelectorButton\" for=\"colSelect1\">" .
              _('Select columns') .
            "</label>
              <div id=\"columnSelector\" class=\"columnSelector\">
              </div>
            </div>";
    }

    /**
     * Returns a string with the html code to add a pager for the table.
     *
     * @param string $id The id for the pager.
     *
     * @return string The html code to add a pager for the table.
     */
    public function addTablePager($id = "")
    {
        echo "
            <div id=\"pager" . $id . "\" class=\"pager\">
                <form>
                    <span class=\"glyphicon glyphicon-step-backward first\"></span>
                    <span class=\"glyphicon glyphicon-backward prev\"></span>
                    <span class=\"pagedisplay\"></span>
                    <span class=\"glyphicon glyphicon-forward next\"></span>
                    <span class=\"glyphicon glyphicon-step-forward last\"></span>
                    <select class=\"pagesize\">
                        <option selected=\"selected\" value=\"10\">10</option>
                        <option value=\"20\">20</option>
                        <option value=\"30\">30</option>
                        <option value=\"40\">40</option>
                    </select>
                    <select class=\"gotoPage\" title=\"Select page number\"></select>
                </form>
            </div>";
    }

    /**
     * Returns a string with the javascript code to add a table.
     *
     * @param string $id          The id for the table.
     * @param bool   $columSelect True if the columns are selectable.
     *
     * @return string The javascript code to add a table.
     */
    public function addTableJavascript($id = "", $columSelect = true)
    {
        global $dateformat;
        // Make the table sorter, add the pager and add the column chooser
        echo "<script type=\"text/javascript\">";
        echo "   var date = new Date();
             date.setTime(date.getTime()+(24*60*60*1000));
             var expires = \"; expires=\"+date.toGMTString();
            document.cookie = \"sortOrder=\|showname(0)\|\"+expires+\"; path=/\";";
        echo "// add astrotime parser. Use with class=sorter-astrotime
              $.tablesorter.addParser({
                // set a unique id
                id: 'astrotime',
                is: function(s, table, cell, \$cell) {
                  // return false so this parser is not auto detected
                  return false;
                },
                format: function(s, table, cell, cellIndex) {
                  // format your data for normalization
                  var time = s.split(\":\");
                  var hour = time[0];
                  if (hour < 12) {
                    hour += 24;
                  }
                  return \"\" + hour + time[1];
                },
                // set type, either numeric or text
                type: 'numeric'
              });";

        echo "// add astrotime parser. Use with class=sorter-degrees
              $.tablesorter.addParser({
                // set a unique id
                id: 'degrees',
                is: function(s, table, cell, \$cell) {
                  // return false so this parser is not auto detected
                  return false;
                },
                format: function(s, table, cell, cellIndex) {
                  // format your data for normalization
              s = s.replace('°', '.');
              s = s.replace(/[^0-9-.]/g, '');

              if(s == '-'){s = '0'};

                  return s;
                },
                // set type, either numeric or text
                type: 'numeric'
              });";

        echo "// add instrument parser. Use with class=sorter-instruments
                             $.tablesorter.addParser({
                               // set a unique id
                               id: 'instruments',
                               is: function(s, table, cell, \$cell) {
                                 return false;
                               },
                               format: function(s, table, cell, cellIndex) {
                                 // format your data for normalization
                             s = s.replace('°', '.');
                              s = s.replace(/.+\(/, '');
                             s = s.replace(/[^0-9-.]/g, '');

                             if(s == '-'){s = '0'};

                                 return s;
                               },
                               // set type, either numeric or text
                               type: 'numeric'
                             });";

        echo "// add astrotime parser. Use with class=sorter-months
              $.tablesorter.addParser({
                // set a unique id
                id: 'months',
                is: function(s, table, cell, \$cell) {
                  // return false so this parser is not auto detected
                  return false;
                },
                format: function(s, table, cell, cellIndex) {
                  // format your data for normalization
                  var months = s.split(\" \");
              var fraction = 0.75;
              if (months[0] == \"" . _("start") . "\") {
                fraction = 0.0;
              } else if (months[0] == \"" . _("begin") . "\") {
                fraction = 0.25;
              } else if (months[0] == \"" . _("mid") . "\") {
                fraction = 0.5;
              }

              var month = 1;
              if (months[1] == \"" . $GLOBALS ['Month2Short'] . "\") {
                month = 2;
              } else if (months[1] == \"" . $GLOBALS ['Month3Short'] . "\") {
                month = 3;
              } else if (months[1] == \"" . $GLOBALS ['Month4Short'] . "\") {
                month = 4;
              } else if (months[1] == \"" . $GLOBALS ['Month5Short'] . "\") {
                month = 5;
              } else if (months[1] == \"" . $GLOBALS ['Month6Short'] . "\") {
                month = 6;
              } else if (months[1] == \"" . $GLOBALS ['Month7Short'] . "\") {
                month = 7;
              } else if (months[1] == \"" . $GLOBALS ['Month8Short'] . "\") {
                month = 8;
              } else if (months[1] == \"" . $GLOBALS ['Month9Short'] . "\") {
                month = 9;
              } else if (months[1] == \"" . $GLOBALS ['Month10Short'] . "\") {
                month = 10;
              } else if (months[1] == \"" . $GLOBALS ['Month11Short'] . "\") {
                month = 11;
              } else if (months[1] == \"" . $GLOBALS ['Month12Short'] . "\") {
                month = 12;
              }

                  return \"\" + month + fraction;
                },
                // set type, either numeric or text
                type: 'numeric'
              });";

        echo "$(function(){
         $(\".sort-table" . $id . "\").tablesorter({
             theme: \"bootstrap\",
                delayInit: \"true\",
                filter_searchFiltered: \"true\",
            stringTo: \"bottom\",
               dateFormat : \"";

        if ($dateformat == "d/m/Y") {
            echo "ddmmyyyy";
        } else {
            echo "mmddyyyy";
        }
        // Make sure the columnSelector module is only loaded when the
        // columnSelector is indeed used.
        echo "\", // set the default date format
               headerTemplate: '{content} {icon}',
               widgets: [\"reorder\", \"uitheme\", ";
        if ($columSelect) {
            echo "\"columnSelector\", ";
        }
        echo "\"filter\", \"stickyHeaders\"],
               widgetOptions : {
                 // target the column selector markup
                 columnSelector_container : $('.columnSelector'),
                 // column status, true = display, false = hide
                 // disable = do not display on list
                 columnSelector_columns : {
                   0: 'disable',  /* set to disabled; not allowed to unselect it */
                   1 : false,     /* start with column hidden */
                   2 : true,
                 },
            // remember selected columns (requires $.tablesorter.storage)
            columnSelector_saveColumns: true,

            // container layout
            columnSelector_layout : '<label><input type=\"checkbox\">{name}</label>',
            columnSelector_name  : 'data-selector-name',

            /* Responsive Media Query settings */
            // enable/disable mediaquery breakpoints
            columnSelector_mediaquery: true,
            // toggle checkbox name
            columnSelector_mediaqueryName: 'Auto: ',
            // breakpoints checkbox initial setting
            columnSelector_mediaqueryState: false,
            columnSelector_breakpoints : ['20em','30em','40em','50em','60em','70em'],
            columnSelector_priority : 'data-priority',

            reorder_axis        : 'x', // 'x' or 'xy'
            reorder_delay       : 300,
            reorder_helperClass : 'tablesorter-reorder-helper',
            reorder_helperBar   : 'tablesorter-reorder-helper-bar',
            reorder_noReorder   : 'reorder-false',
            reorder_blocked     : 'reorder-block-left reorder-block-end',
            reorder_complete    : null // callback
   }
   })

   // Add the sort order to a cookie to read out when needed...
   .bind(\"sortEnd\", function(sorter) {
      currentSort = sorter.target.config.sortList;
      var columns = \"\|\";
      for (column = 0;column < currentSort.length;column++) {
         columns = columns
            + $(sorter.target.config.headerList[currentSort[column][0]])[0].id +
            \"(\"+ (currentSort[column][1]) + \")\" + \",\";
      }
      columns = columns.substring(0, columns.length - 1);
      columns = columns + \"\|\";
        var date = new Date();
        date.setTime(date.getTime()+(24*60*60*1000));
        var expires = \"; expires=\"+date.toGMTString();
       document.cookie = \"sortOrder=\"+columns+expires+\"; path=/\";
   });

    var pagerOptions = {

    // target the pager markup - see the HTML block below
    container: $(\"#pager" . $id . "\"),
    ajaxUrl: null,

    // modify the url after all processing has been applied
    customAjaxUrl: function(table, url) { return url; },

    ajaxProcessing: function(ajax){
      if (ajax && ajax.hasOwnProperty('data')) {
        // return [ \"data\", \"total_rows\" ];
        return [ ajax.total_rows, ajax.data ];
      }
    },

    output: '{startRow} to {endRow} ({totalRows})',
    updateArrows: true,

    // starting page of the pager (zero based index)
    page: 0,

    // Number of visible rows - default is 10
    size: 10,

    savePages : false,

    //defines custom storage key
    storageKey:'tablesorter-pager',
    fixedHeight: false,
    removeRows: true,

    // css class names of pager arrows
    cssNext: '.next', // next page arrow
    cssPrev: '.prev', // previous page arrow
    cssFirst: '.first', // go to first page arrow
    cssLast: '.last', // go to last page arrow
    cssGoto: '.gotoPage', // select dropdown to allow choosing a page

    cssPageDisplay: '.pagedisplay', // location of where the output is displayed
    cssPageSize: '.pagesize',

    cssDisabled: 'disabled',
    cssErrorRow: 'tablesorter-errorRow' // ajax error information row

  };

  // initialize column selector using default settings
  // note: no container is defined!
  $(\".bootstrap-popup\").tablesorter({
    theme: 'blue',
    widgets: ['columnSelector', 'stickyHeaders']
  });

      // bind to pager events
      // *********************
      $(\".sort-table" . $id .
        "\").bind('pagerChange pagerComplete pagerInitialized pageMoved',
        function(e, c){
         var msg = '\"</span> event triggered, '
         + (e.type === 'pagerChange' ? 'going to' : 'now on')
         + ' page <span class=\"typ\">' + (c.page + 1) + '/'
         + c.totalPages + '</span>';
         $('#display')
         .append('<li><span class=\"str\">\"' + e.type + msg + '</li>')
         .find('li:first').remove();
  })

  // initialize the pager plugin
  // ****************************
  $(\".sort-table" . $id . "\").tablesorterPager(pagerOptions);

   });";

        echo "</script>";
    }

    /**
     * Returns a string to add a pager to a table.
     *
     * @param string $name          The name for the pager.
     * @param int    $count         The number of rows in a table.
     * @param bool   $tableSelector True if the table selector should be shown.
     *
     * @return string The html code to add a pager for the table.
     */
    public function addPager($name, $count, $tableSelector = true)
    {
        // We limit the number of rows in a table to 1000.
        $max = 1000;

        // For internet explorer, we limit the number of rows in the tables
        // to 400 items.
        if (preg_match('/(?i)msie [2-9]/', $_SERVER['HTTP_USER_AGENT'])) {
            $max = 400;
        }

        if ($count < $max) {
            echo $this->addTablePager($name);

            echo $this->addTableJavascript($name, $tableSelector);
        }
    }
}

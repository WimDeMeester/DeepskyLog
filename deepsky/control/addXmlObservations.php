<?php
/**
 * Adds observations from an OpenAstronomyLog xml file to the database.
 *
 * PHP Version 7
 *
 * @category Deepsky/import
 * @package  DeepskyLog
 * @author   DeepskyLog Developers <deepskylog@groups.io>
 * @license  GPL2 <https://opensource.org/licenses/gpl-2.0.php>
 * @link     http://www.deepskylog.org
 */
global $inIndex, $loggedUser;

if ((!isset($inIndex)) || (!$inIndex)) {
    include '../../redirect.php';
} elseif (!$loggedUser) {
    throw new Exception(
        _('You need to be logged in to change your locations or equipment.')
    );
} else {
    addXmlObservations();
}

/**
 * Adds observations from an openAstronomyLog xml file.
 *
 * @return double The value of the time. -1 is not visible, else the time.
 */
function addXmlObservations()
{
    global $baseURL, $entryMessage, $objSession, $mailTo, $mailFrom;
    global $loggedUser, $objConstellation, $objObject, $objCatalog;
    global $objLocation, $objInstrument, $objFilter, $objEyepiece, $objLens;
    global $objDatabase, $objObserver, $objObservation, $developversion;
    global $objMessage;

    if (!$_POST['obsid']) {
        if ($_FILES['xml']['tmp_name'] != '') {
            $xmlfile = $_FILES['xml']['tmp_name'];
        } else {
            // No filename is given, so return an error.
            $entryMessage .= _('Invalid XML file!');
            $_GET['indexAction'] = 'add_xml';

            return;
        }
    }

    // Make a DomDocument from the file.
    $dom = new DomDocument();

    if ($_POST['obsid']) {
        $xmlfile = "/tmp/". $_REQUEST['uniqName'];
    } else {
        $xmlfile = realpath($xmlfile);
    }

    $dom->Load($xmlfile);

    $searchNode = $dom->getElementsByTagName('observations');
    $version = $searchNode->item(0)->getAttribute('version');

    if ($version != '2.0' && $version != '2.1') {
        // Version is too old or too new. We are not sure we can import the file
        $entryMessage .=
            _('DeepskyLog only supports openAstronomyLog version 2.0 or 2.1');
        $_GET['indexAction'] = 'add_xml';

        return;
    }

    // Use the correct schema definition to check the xml file.
    $xmlschema = str_replace(
        ' ',
        '/',
        $searchNode->item(0)->getAttribute('xsi:schemaLocation')
    );

    // Use the correct oal definitions.
    if ($version == '2.0') {
        $xmlschema = $baseURL . 'xml/oal20/oal20.xsd';
    } elseif ($version == '2.1') {
        $xmlschema = $baseURL . 'xml/oal21/oal21.xsd';
    }

    // Validate the XML file against the schema
    if (empty($_POST['obsid'])) {
        if (!$dom->schemaValidate($xmlschema)) {
            $_GET['indexAction'] = 'add_xml_invalid';

            return;
        }
    }

    // The XML file is valid or we want to try to import an invalid XML file.
    // Let's start reading in the file.
    // Only 2.0 and 2.1 files!
    $searchNode = $dom->getElementsByTagName('observers');
    $observer = $searchNode->item(0)->getElementsByTagName('observer');
    $observerArray = [];

    $id = '';

    foreach ($observer as $observer) {
        $tmpObserverArray = [];
        // Get the id and the name of the observers in the comast file
        $oalid = $observer->getAttribute('id');

        if (empty($_POST['obsid'])) {
            $name = htmlentities(
                ($observer->getElementsByTagName('name')->item(0)->nodeValue),
                ENT_COMPAT,
                'UTF-8',
                0
            );
            $tmpObserverArray['name'] = $name;

            $surname = htmlentities(
                ($observer->getElementsByTagName('surname')->item(0)->nodeValue),
                ENT_COMPAT,
                'UTF-8',
                0
            );
            $tmpObserverArray['surname'] = $surname;

            // Get the fstOffset if known, else, just set fstOffset to 0.
            if ($observer->getElementsByTagName('fstOffset')->item(0)) {
                $fstOffset[$oalid] = $observer->
                    getElementsByTagName('fstOffset')->item(0)->nodeValue;
            } else {
                $fstOffset[$oalid] = 0.0;
            }

            // Get the deepskyLog id if the account is given and the
            // name is www.deepskylog.org
            $observerid = $observer->getElementsByTagName('account');
            $obsid = '';
            foreach ($observerid as $observerid) {
                if ($observerid->getAttribute('name') == 'www.deepskylog.org') {
                    $obsid = $observerid->nodeValue;
                }
            }

            // Get the name of the observer which is logged in in DeepskyLog
            $deepskylog_username = $objObserver->getObserverProperty(
                $_SESSION['deepskylog_id'],
                'firstname'
            ) . ' ' .
            $objObserver->getObserverProperty($_SESSION['deepskylog_id'], 'name');

            if ($obsid != '') {
                // If the deepskylog account name was set in the account
                //section of the OAL file...
                if ($obsid == $_SESSION['deepskylog_id']) {
                    // ...we check if this is the same as the observer that
                    // is logged in.
                    $id = $oalid;
                }
            } elseif (strcasecmp(
                html_entity_decode($deepskylog_username),
                html_entity_decode(trim($name) . ' ' . trim($surname))
            ) == 0
            ) {
                // If the name and surname of the oal user is the same as the user
                // that is logged in...
                $id = $_SESSION['deepskylog_id'];
            }
            $tmpObserverArray['id'] = $id;
        } else {
            // Use the user id of the logged in user. Only for invalid xml
            // files (SkySafari...)
            $id = $_POST['obsid'];
            $tmpObserverArray['name'] = $objObserver->getObserverProperty(
                $_SESSION['deepskylog_id'],
                'firstname'
            );
            $tmpObserverArray['surname'] = $objObserver->getObserverProperty(
                $_SESSION['deepskylog_id'],
                'name'
            );
            $tmpObserverArray['id'] = $_POST['obsid'];
        }
        $observerArray[$oalid] = $tmpObserverArray;
    }

    if ($id == '') {
        // If there is no user found, we exit the OAL import and print an
        // error message.
        $entryMessage .= sprintf(
            _('No observations for user %s in this OpenAstronomyLog file!'),
            $deepskylog_username
        );
        $_GET['indexAction'] = 'add_xml';

        return;
    } else {
        // If there is a user found, we immediately set the fstOffset to the
        // value from the OAL file, if this value is not 0.0!
        if ($fstOffset[$oalid] != 0.0) {
            $objObserver->setObserverProperty(
                $_SESSION['deepskylog_id'],
                'fstOffset',
                $fstOffset[$oalid]
            );
        }
    }

    // Make a list of all targets
    $targets = $dom->getElementsByTagName('targets');
    $target = $targets->item(0)->getElementsByTagName('target');

    $targetArray = [];

    foreach ($target as $target) {
        $targetInfoArray = [];
        $targetid = $target->getAttribute('id');
        $targetInfoArray['name'] = $target->
                getElementsByTagName('name')->item(0)->nodeValue;

        // Get all the aliases for the name
        $aliases = $target->getElementsByTagName('alias');
        $aliasesArray = [];
        $cnt = 0;
        foreach ($aliases as $aliases) {
            $aliasesArray['alias' . $cnt] = $aliases->nodeValue;
            $cnt = $cnt + 1;
        }
        // Check if the datasource is defined. If this is the case, get it.
        // Otherwise, set to OAL
        if ($target->getElementsByTagName('datasource')->item(0)) {
            $targetInfoArray['datasource'] = $target->
                    getElementsByTagName('datasource')->item(0)->nodeValue;
        } else {
            $targetInfoArray['datasource'] = 'OAL';
        }

        $valid = true;

        // Get the type
        if ($target->getAttribute('xsi:type')) {
            $type = $target->getAttribute('xsi:type');

            $next = 1;

            if ($type == 'oal:deepSkyAS') {
                $targetInfoArray['type'] = 'ASTER';
            } elseif ($type == 'oal:deepSkyDS') {
                $targetInfoArray['type'] = 'DS';
            } elseif ($type == 'oal:deepSkySC' || $type == 'oal:deepSkyOC') {
                $targetInfoArray['type'] = 'OPNCL';
            } elseif ($type == 'oal:deepSkyGC') {
                $targetInfoArray['type'] = 'GLOCL';
            } elseif ($type == 'oal:deepSkyGX') {
                $targetInfoArray['type'] = 'GALXY';
            } elseif ($type == 'oal:deepSkyCG') {
                $targetInfoArray['type'] = 'GALCL';
            } elseif ($type == 'oal:deepSkyGN') {
                $targetInfoArray['type'] = 'BRTNB';
            } elseif ($type == 'oal:deepSkyGN') {
                $targetInfoArray['type'] = 'BRTNB';
            } elseif ($type == 'oal:deepSkyPN') {
                $targetInfoArray['type'] = 'PLNNB';
            } elseif ($type == 'oal:deepSkyQS') {
                $targetInfoArray['type'] = 'QUASR';
            } elseif ($type == 'oal:deepSkyDN') {
                $targetInfoArray['type'] = 'DRKNB';
            } elseif ($type == 'oal:deepSkyNA') {
                $targetInfoArray['type'] = 'NONEX';
            } elseif ($type == 'oal:observationTargetType') {
                // DeepskyPlanner and SkySafari export the targets as
                // oal:observationTargetType.
                // We should check if the object is already known in DeepskyLog.
                // If that is the case, we should use it.
                // If it is not known, the target should be invalid.
                $targetInfoArray['type'] = 'TARGET';
            } else {
                $next = 0;
                $valid = false;
            }
        } else {
            $valid = false;
        }

        $targetInfoArray['known'] = $next;

        if ($valid) {
            if ($targetInfoArray['type'] != 'TARGET') {
                // Get Ra and convert it to degrees
                if (!$target->getElementsByTagName('position')->item(0)) {
                    $valid = false;
                } else {
                    $ratarget = $target->getElementsByTagName('position')
                        ->item(0)->getElementsByTagName('ra')->item(0);
                    if (!$ratarget) {
                        $valid = false;
                    } else {
                        $unit = $ratarget->getAttribute('unit');
                        if ($unit == 'deg') {
                            $ra = $ratarget->nodeValue;
                        } elseif ($unit == 'rad') {
                            $ra = Rad2Deg($ratarget->nodeValue);
                        } elseif ($unit == 'arcmin') {
                            $ra = $ratarget->nodeValue / 60.0;
                        } elseif ($unit == 'arcsec') {
                            $ra = $ratarget->nodeValue / 3600.0;
                        }
                        $targetInfoArray['ra'] = $ra / 15.0;
                    }

                    $dectarget = $target->getElementsByTagName('position')
                        ->item(0)->getElementsByTagName('dec')->item(0);
                    if (!$dectarget) {
                        $valid = false;
                    } else {
                        // Get Dec and convert it to degrees
                        $unit = $dectarget->getAttribute('unit');
                        if ($unit == 'deg') {
                            $dec = $dectarget->nodeValue;
                        } elseif ($unit == 'rad') {
                            $dec = Rad2Deg($dectarget->nodeValue);
                        } elseif ($unit == 'arcmin') {
                            $dec = $dectarget->nodeValue / 60.0;
                        } elseif ($unit == 'arcsec') {
                            $dec = $dectarget->nodeValue / 3600.0;
                        }
                        $targetInfoArray['dec'] = $dec;
                    }

                    // Get the constellation
                    $targetInfoArray['constellation']
                        = $objConstellation->getConstellationFromCoordinates(
                            $targetInfoArray['ra'],
                            $targetInfoArray['dec']
                        );

                    // Check if the magnitude is defined. If this is the case,
                    // get it. Otherwise, set to 99.9
                    if ($target->getElementsByTagName('visMag')->item(0)) {
                        $targetInfoArray['mag'] = $target
                            ->getElementsByTagName('visMag')->item(0)->nodeValue;
                    } else {
                        $targetInfoArray['mag'] = '99.9';
                    }

                    // Check if the surface brightness is defined.
                    // If this is the case, get it. Otherwise, set to 99.9
                    if ($target->getElementsByTagName('surfBr')->item(0)) {
                        // Get surface brightness and convert it
                        $unit = $target->getElementsByTagName('surfBr')
                            ->item(0)->getAttribute('unit');

                        if ($unit == 'mags-per-squarearcmin') {
                            $subr = $target->getElementsByTagName('surfBr')
                                ->item(0)->nodeValue;
                        } else {
                            $subr = $target->getElementsByTagName('surfBr')
                                ->item(0)->nodeValue - 8.89;
                        }

                        $targetInfoArray['subr'] = $subr;
                    } else {
                        $targetInfoArray['subr'] = '99.9';
                    }

                    // Check if the position angle is defined.
                    // If this is the case, get it. Otherwise, set to 999
                    if ($target->getElementsByTagName('pa')->item(0)) {
                        $targetInfoArray['pa'] = $target
                            ->getElementsByTagName('pa')->item(0)->nodeValue;
                    } else {
                        $targetInfoArray['pa'] = '999';
                    }

                    // Check if the largeDiameter is defined. If this is the
                    // case, get it. Otherwise, set to 0
                    $ldtarget = $target->getElementsByTagName('largeDiameter')
                        ->item(0);
                    if ($ldtarget) {
                        // Get unit of the largeDiameter and convert it to
                        // arcseconds
                        $unit = $ldtarget->getAttribute('unit');

                        if ($unit == 'deg') {
                            $diam1 = $ldtarget->nodeValue * 3600.0;
                        } elseif ($unit == 'rad') {
                            $diam1 = Rad2Deg($ldtarget->nodeValue) * 3600.0;
                        } elseif ($unit == 'arcmin') {
                            $diam1 = $ldtarget->nodeValue * 60.0;
                        } elseif ($unit == 'arcsec') {
                            $diam1 = $ldtarget->nodeValue;
                        }
                        $targetInfoArray['diam1'] = $diam1;
                    } else {
                        $targetInfoArray['diam1'] = '0';
                    }

                    // Check if the smallDiameter is defined. If this is the
                    // case, get it. Otherwise, set to 0
                    $sdtarget = $target->getElementsByTagName('smallDiameter')
                        ->item(0);
                    if ($sdtarget) {
                        // Get the unit of the small diameter and convert it to
                        // arcseconds
                        $unit = $sdtarget->getAttribute('unit');

                        if ($unit == 'deg') {
                            $diam2 = $sdtarget->nodeValue * 3600.0;
                        } elseif ($unit == 'rad') {
                            $diam2 = Rad2Deg($sdtarget->nodeValue) * 3600.0;
                        } elseif ($unit == 'arcmin') {
                            $diam2 = $sdtarget->nodeValue * 60.0;
                        } elseif ($unit == 'arcsec') {
                            $diam2 = $sdtarget->nodeValue;
                        }
                        $targetInfoArray['diam2'] = $diam2;
                    } else {
                        $targetInfoArray['diam2'] = '0';
                    }
                }
            }
            $targetInfoArray['valid'] = $valid;
            $targetInfoArray['aliases'] = $aliasesArray;
            $targetArray[$targetid] = $targetInfoArray;
        }
    }

    // SITES
    $sites = $dom->getElementsByTagName('sites');
    $site = $sites->item(0)->getElementsByTagName('site');

    $siteArray = [];

    foreach ($site as $site) {
        $siteInfoArray = [];
        $siteid = $site->getAttribute('id');

        $siteInfoArray['name'] = htmlentities(
            ($site->getElementsByTagName('name')->item(0)->nodeValue),
            ENT_COMPAT,
            'UTF-8',
            0
        );
        // Get longitude and convert it to degrees
        $lsite = $site->getElementsByTagName('longitude')->item(0);
        $unit = $lsite->getAttribute('unit');
        if ($unit == 'deg') {
            $longitude = $lsite->nodeValue;
        } elseif ($unit == 'rad') {
            $longitude = Rad2Deg($lsite->nodeValue);
        } elseif ($unit == 'arcmin') {
            $longitude = $lsite->nodeValue / 60.0;
        } elseif ($unit == 'arcsec') {
            $longitude = $lsite->nodeValue / 3600.0;
        }
        $siteInfoArray['longitude'] = $longitude;

        // Get latitude and convert it to degrees
        $lsite = $site->getElementsByTagName('latitude')->item(0);
        $unit = $lsite->getAttribute('unit');
        if ($unit == 'deg') {
            $latitude = $lsite->nodeValue;
        } elseif ($unit == 'rad') {
            $latitude = Rad2Deg($lsite->nodeValue);
        } elseif ($unit == 'arcmin') {
            $latitude = $lsite->nodeValue / 60.0;
        } elseif ($unit == 'arcsec') {
            $latitude = $lsite->nodeValue / 3600.0;
        }
        $siteInfoArray['latitude'] = $latitude;

        // Get the timezone
        $xmlfile2 = 'http://api.geonames.org/timezone?lat='
                . $latitude . '&lng=' . $longitude
                . '&username=deepskylog';
        $timezones = simplexml_load_file($xmlfile2);

        $siteInfoArray['timezone'] = $timezones->timezone->timezoneId;
        $siteInfoArray['country'] = $timezones->timezone->countryName;

        $siteInfoArray['country'] = $timezones->timezone->countryName;

        if ($siteInfoArray['timezone'] == '') {
            $siteInfoArray['timezone'] = 'UTC';
        }
        $siteArray[$siteid] = $siteInfoArray;
    }
    // SESSIONS
    $sessions = $dom->getElementsByTagName('sessions');
    $session = $sessions->item(0)->getElementsByTagName('session');

    $sessionArray = [];

    foreach ($session as $session) {
        $sessionInfoArray = [];
        $sessionid = $session->getAttribute('id');
        $sessionLang = $session->getAttribute('lang');

        $sessionInfoArray['lang'] = $sessionLang;

        // Get the begindate and convert it to the DeepskyLog format
        $beginDate = strtotime(
            $session->getElementsByTagName('begin')->item(0)->nodeValue
        );
        $sessionInfoArray['begindate'] = date('Y-m-d H:i:s', $beginDate);

        $endDate = strtotime(
            $session->getElementsByTagName('end')->item(0)->nodeValue
        );
        $sessionInfoArray['enddate'] = date('Y-m-d H:i:s', $endDate);

        // Get siteid -> Maybe we still have to add the site later
        $siteid = $session->getElementsByTagName('site')->item(0)->nodeValue;
        $sessionInfoArray['site'] = $siteid;

        // Get all coObservers
        if ($session->getElementsByTagName('coObserver')->item(0)) {
            $coObs = $session->getElementsByTagName('coObserver');

            $coObsArray = [];
            foreach ($coObs as $coObs) {
                $coObsArray[] = $coObs->nodeValue;
            }
            $sessionInfoArray['coObservers'] = $coObsArray;
        }

        // Get weather
        if ($session->getElementsByTagName('weather')->item(0)) {
            $sessionInfoArray['weather'] = htmlentities(
                $session->getElementsByTagName('weather')->item(0)->nodeValue,
                ENT_COMPAT,
                'UTF-8',
                0
            );
        }

        // Get the equipment
        if ($session->getElementsByTagName('equipment')->item(0)) {
            $sessionInfoArray['equipment'] = htmlentities(
                $session->getElementsByTagName('equipment')->item(0)->nodeValue,
                ENT_COMPAT,
                'UTF-8',
                0
            );
        }

        // Get the comments
        if ($session->getElementsByTagName('comments')->item(0)) {
            $sessionInfoArray['comments'] = htmlentities(
                $session->getElementsByTagName('comments')->item(0)->nodeValue,
                ENT_COMPAT,
                'UTF-8',
                0
            );
        }

        // We don't use the image tag of the session element to import,
        // only to export
        $sessionArray[$sessionid] = $sessionInfoArray;
    }

    // SCOPES
    $scopes = $dom->getElementsByTagName('scopes');
    $scope = $scopes->item(0)->getElementsByTagName('scope');

    $scopeArray = [];

    foreach ($scope as $scope) {
        $scopeInfoArray = [];
        $scopeid = $scope->getAttribute('id');

        $scopeInfoArray['name'] = htmlentities(
            ($scope->getElementsByTagName('vendor')->item(0)->nodeValue),
            ENT_COMPAT,
            'UTF-8',
            0
        ) . ' ' . htmlentities(
            ($scope->getElementsByTagName('model')->item(0)->nodeValue),
            ENT_COMPAT,
            'UTF-8',
            0
        );
        $scopeInfoArray['diameter'] = $scope->getElementsByTagName('aperture')
            ->item(0)->nodeValue;

        $tp = $scope->getAttribute('xsi:type');

        if ($tp == 'oal:scopeType') {
            if ($scope->getElementsByTagName('focalLength')->item(0)) {
                $type = $scope->getElementsByTagName('type')->item(0)->nodeValue;
                if ($type == 'A' || $type == 'Naked Eye') {
                    $typeToSave = INSTRUMENTNAKEDEYE;
                } elseif ($type == 'B' || $type == 'Binoculars') {
                    $typeToSave = INSTRUMENTBINOCULARS;
                } elseif ($type == 'R' || $type == 'Refractor') {
                    $typeToSave = INSTRUMENTREFRACTOR;
                } elseif ($type == 'N' || $type == 'Newton') {
                    $typeToSave = INSTRUMENTREFLECTOR;
                } elseif ($type == 'C' || $type == 'Cassegrain') {
                    $typeToSave = INSTRUMENTCASSEGRAIN;
                } elseif ($type == 'K' || $type == 'Kutter') {
                    $typeToSave = INSTRUMENTKUTTER;
                } elseif ($type = "Finder") {
                    $typeToSave = INSTRUMENTFINDERSCOPE;
                } elseif ($type == 'M' || $type == 'Maksutov') {
                    $typeToSave = INSTRUMENTMAKSUTOV;
                } elseif ($type == 'S' || $type == 'Schmidt-Cassegrain') {
                    $typeToSave = INSTRUMENTSCHMIDTCASSEGRAIN;
                } else {
                    $typeToSave = INSTRUMENTOTHER;
                }
            } else {
                $typeToSave = INSTRUMENTOTHER;
            }
        } else {
            $typeToSave = INSTRUMENTBINOCULARS;
        }
        $scopeInfoArray['type'] = $typeToSave;

        // Check if the focal length exists. If so,
        // we are using a telescope, else a binocular.
        if ($scope->getElementsByTagName('focalLength')->item(0)) {
            $fl = $scope->getElementsByTagName('focalLength')->item(0)
                ->nodeValue;
            $scopeInfoArray['fd'] = $fl / $scopeInfoArray['diameter'];
            $scopeInfoArray['fixedMagnification'] = 0;
        } else {
            $scopeInfoArray['fd'] = 0;
            $scopeInfoArray['fixedMagnification'] = $scope
                ->getElementsByTagName('magnification')->item(0)->nodeValue;
        }

        $scopeArray[$scopeid] = $scopeInfoArray;
    }

    // EYEPIECES
    $eyepieces = $dom->getElementsByTagName('eyepieces');
    $eyepiece = $eyepieces->item(0)->getElementsByTagName('eyepiece');

    $eyepieceArray = [];

    foreach ($eyepiece as $eyepiece) {
        $eyepieceInfoArray = [];
        $eyepieceid = $eyepiece->getAttribute('id');

        $eyepieceInfoArray['name'] = htmlentities(
            ($eyepiece->getElementsByTagName('model')->item(0)->nodeValue),
            ENT_COMPAT,
            'UTF-8',
            0
        );

        $eyepieceInfoArray['focalLength'] = $eyepiece
            ->getElementsByTagName('focalLength')->item(0)->nodeValue;

        // Check if the maximal focal length exists.
        // If so, we are using a zoom eyepiece
        if ($eyepiece->getElementsByTagName('maxFocalLength')->item(0)) {
            $eyepieceInfoArray['maxFocalLength'] = $eyepiece
                ->getElementsByTagName('maxFocalLength')->item(0)->nodeValue;
        } else {
            $eyepieceInfoArray['maxFocalLength'] = -1;
        }

        // Get apparent FOV and convert it to degrees
        if (!$target->getElementsByTagName('apparentFOV')->item(0)) {
            $fov = 60.0;
        } else {
            $aeyepiece = $eyepiece->getElementsByTagName('apparentFOV')->item(0);
            $unit = $aeyepiece->getAttribute('unit');
            if ($unit == 'deg') {
                $fov = $aeyepiece->nodeValue;
            } elseif ($unit == 'rad') {
                $fov = Rad2Deg($aeyepiece->nodeValue);
            } elseif ($unit == 'arcmin') {
                $fov = $aeyepiece->nodeValue / 60.0;
            } elseif ($unit == 'arcsec') {
                $fov = $aeyepiece->nodeValue / 3600.0;
            }
        }
        $eyepieceInfoArray['apparentFOV'] = $fov;

        $eyepieceArray[$eyepieceid] = $eyepieceInfoArray;
    }

    // LENSES
    $lenses = $dom->getElementsByTagName('lenses');
    $lens = $lenses->item(0)->getElementsByTagName('lens');

    $lensArray = [];

    foreach ($lens as $lens) {
        $lensInfoArray = [];
        $lensid = $lens->getAttribute('id');

        $lensInfoArray['name'] = htmlentities(
            $lens->getElementsByTagName('model')->item(0)->nodeValue,
            ENT_COMPAT,
            'UTF-8',
            0
        );
        $lensInfoArray['factor'] = $lens->getElementsByTagName('factor')
            ->item(0)->nodeValue;

        $lensArray[$lensid] = $lensInfoArray;
    }

    // FILTERS
    $filters = $dom->getElementsByTagName('filters');
    $filter = $filters->item(0)->getElementsByTagName('filter');

    $filterArray = [];

    foreach ($filter as $filter) {
        $filterInfoArray = [];
        $filterid = $filter->getAttribute('id');

        $filterInfoArray['name'] = htmlentities(
            $filter->getElementsByTagName('model')->item(0)->nodeValue,
            ENT_COMPAT,
            'UTF-8',
            0
        );
        $type = $filter->getElementsByTagName('type')->item(0)->nodeValue;

        if ($type == 'other') {
            $typeInfo = 0;
        } elseif ($type == 'broad band') {
            $typeInfo = 1;
        } elseif ($type == 'narrow band') {
            $typeInfo = 2;
        } elseif ($type == 'O-III') {
            $typeInfo = 3;
        } elseif ($type == 'H-beta') {
            $typeInfo = 4;
        } elseif ($type == 'H-alpha') {
            $typeInfo = 5;
        } elseif ($type == 'color') {
            $typeInfo = 6;
        } elseif ($type == 'neutral') {
            $typeInfo = 7;
        } elseif ($type == 'corrective') {
            $typeInfo = 8;
        }

        $filterInfoArray['type'] = $typeInfo;

        if ($filter->getElementsByTagName('wratten')->item(0)) {
            $filterInfoArray['wratten'] = $filter
                ->getElementsByTagName('wratten')->item(0)->nodeValue;
        } else {
            $filterInfoArray['wratten'] = '';
        }

        if ($filter->getElementsByTagName('schott')->item(0)) {
            $filterInfoArray['schott'] = $filter
                ->getElementsByTagName('schott')->item(0)->nodeValue;
        } else {
            $filterInfoArray['schott'] = '';
        }

        if ($filter->getElementsByTagName('color')->item(0)) {
            $color = $filter->getElementsByTagName('color')->item(0)->nodeValue;

            if ($color == 'light red') {
                $filterInfoArray['color'] = 1;
            } elseif ($color == 'red') {
                $filterInfoArray['color'] = 2;
            } elseif ($color == 'deep red') {
                $filterInfoArray['color'] = 3;
            } elseif ($color == 'orange') {
                $filterInfoArray['color'] = 4;
            } elseif ($color == 'light yellow') {
                $filterInfoArray['color'] = 5;
            } elseif ($color == 'deep yellow') {
                $filterInfoArray['color'] = 6;
            } elseif ($color == 'yellow') {
                $filterInfoArray['color'] = 7;
            } elseif ($color == 'yellow-green') {
                $filterInfoArray['color'] = 8;
            } elseif ($color == 'light green') {
                $filterInfoArray['color'] = 9;
            } elseif ($color == 'green') {
                $filterInfoArray['color'] = 10;
            } elseif ($color == 'medium blue') {
                $filterInfoArray['color'] = 11;
            } elseif ($color == 'pale blue') {
                $filterInfoArray['color'] = 12;
            } elseif ($color == 'blue') {
                $filterInfoArray['color'] = 13;
            } elseif ($color == 'deep blue') {
                $filterInfoArray['color'] = 14;
            } elseif ($color == 'voilet') {
                $filterInfoArray['color'] = 15;
            } else {
                $filterInfoArray['color'] = 0;
            }
        } else {
            $filterInfoArray['color'] = 0;
        }

        $filterArray[$filterid] = $filterInfoArray;
    }

    // Add the sessions
    foreach ($sessionArray as $key => $value) {
        if (count(
            $objDatabase->selectRecordArray(
                'SELECT * from sessions where begindate = "'
                . $sessionArray[$key]['begindate']
                . '" and enddate = "' . $sessionArray[$key]['enddate'] . '";'
            )
        ) == 0
        ) {
            $sessionid = 0;
        } else {
            $sessionid = $objDatabase->selectRecordArray(
                'SELECT * from sessions where begindate = "'
                . $sessionArray[$key]['begindate']
                . '" and enddate = "' . $sessionArray[$key]['enddate'] . '";'
            );
            $sessionid = $sessionid['id'];
        }
    }

    $beginday = substr($sessionArray[$key]['begindate'], 8, 2);
    $beginmonth = substr($sessionArray[$key]['begindate'], 5, 2);
    $beginyear = substr($sessionArray[$key]['begindate'], 0, 4);
    $beginhours = substr($sessionArray[$key]['begindate'], 11, 2);
    $beginminutes = substr($sessionArray[$key]['begindate'], 14, 2);

    $endday = substr($sessionArray[$key]['enddate'], 8, 2);
    $endmonth = substr($sessionArray[$key]['enddate'], 5, 2);
    $endyear = substr($sessionArray[$key]['enddate'], 0, 4);
    $endhours = substr($sessionArray[$key]['enddate'], 11, 2);
    $endminutes = substr($sessionArray[$key]['enddate'], 14, 2);

    $location = $sessionArray[$key]['site'];

    // Check if the site already exists in DeepskyLog
    $site = $siteArray[$sessionArray[$key]['site']]['name'];

    $sa = $siteArray[$sessionArray[$key]['site']];
    if (count(
        $objDatabase->selectRecordArray(
            'SELECT * from locations where observer = "'
            . $_SESSION['deepskylog_id'] . '" and name = "'
            . $site . '";'
        )
    ) > 0
    ) {
        // Update the coordinates
        $run = $objDatabase->selectRecordset(
            'SELECT id FROM locations WHERE observer = "'
            . $_SESSION['deepskylog_id'] . '" and name = "'
            . $site . '";'
        );
        $get = $run->fetch(PDO::FETCH_OBJ);

        $locId = $get->id;

        $objLocation->setLocationProperty(
            $locId,
            'longitude',
            $sa['longitude']
        );
        $objLocation->setLocationProperty(
            $locId,
            'latitude',
            $sa['latitude']
        );
        $objLocation->setLocationProperty(
            $locId,
            'timezone',
            $sa['timezone']
        );
        $objLocation->setLocationProperty(
            $locId,
            'country',
            $sa['country']
        );
    } else {
        // Add the new site!
        $locId = $objLocation->addLocation(
            $sa['name'],
            $sa['longitude'],
            $sa['latitude'],
            '',
            $sa['country'],
            $sa['timezone'],
            0
        );
        $objDatabase->execSQL(
            'update locations set observer = "'
            . $_SESSION['deepskylog_id'] . '" where id = "'
            . $locId . '";'
        );
        $objDatabase->execSQL(
            'update locations set checked = "0" where id = "'
            . $locId . '";'
        );
    }

    $location = $locId;

    if (array_key_exists('weather', $sessionArray[$key])) {
        $weather = $sessionArray[$key]['weather'];
    } else {
        $weather = '';
    }
    if (array_key_exists('equipment', $sessionArray[$key])) {
        $equipment = $sessionArray[$key]['equipment'];
    } else {
        $equipment = '';
    }
    if (array_key_exists('comments', $sessionArray[$key])) {
        $comments = $sessionArray[$key]['comments'];
    } else {
        $comments = '';
    }

    // $language
    $language = $sessionArray[$key]['lang'];

    // If the observers exist, add them to the session
    $observers = [];
    if (array_key_exists('coObservers', $sessionArray[$key])) {
        $coObservers = $sessionArray[$key]['coObservers'];
        for ($cnt = 0;$cnt < count($coObservers);$cnt++) {
            $name = $observerArray[$coObservers[$cnt]]['surname'];
            $firstname = $observerArray[$coObservers[$cnt]]['name'];
            $foundUser = $objDatabase->selectRecordArray(
                'SELECT * from observers where name = "'
                . $name . '" and firstname = "' . $firstname . '"'
            );
            if (count($foundUser) > 0) {
                $observers[] = $foundUser['id'];
            }
        }
    }

    if ($sessionid == 0) {
        // Add new session
        $objSession->addSession(
            '',
            $beginday,
            $beginmonth,
            $beginyear,
            $beginhours,
            $beginminutes,
            $endday,
            $endmonth,
            $endyear,
            $endhours,
            $endminutes,
            $location,
            $weather,
            $equipment,
            $comments,
            $language,
            $observers,
            0
        );
    } else {
        // Adapt sessions
        $objSession->updateSession(
            $sessionid,
            '',
            $sessionArray[$key]['begindate'],
            $sessionArray[$key]['enddate'],
            $location,
            $weather,
            $equipment,
            $comments,
            $language
        );
    }
}

// Check if there are observations for the given observer
$searchNode = $dom->getElementsByTagName('observations');
$observation = $searchNode->item(0)->getElementsByTagName('observation');

$added = 0;
$double = 0;
$errors = 0;

foreach ($observation as $observation) {
    $siteValid = true;
    if ($observation->getElementsByTagName('site')->item(0)) {
        $observerid = $observation->getElementsByTagName('observer')
            ->item(0)->nodeValue;

        if ($id == $observerArray[$observerid]['id']) {
            // Check if the site already exists in DeepskyLog
            $sa = $siteArray[$observation->getElementsByTagName('site')
                ->item(0)->nodeValue];
            $site = $sa['name'];

            if (count(
                $objDatabase->selectRecordArray(
                    'SELECT * from locations where observer = "'
                    . $_SESSION['deepskylog_id'] . '" and name = "'
                    . $site . '";'
                )
            ) > 0
            ) {
                // Update the coordinates
                $run = $objDatabase->selectRecordset(
                    'SELECT id FROM locations WHERE observer = "'
                    . $_SESSION['deepskylog_id'] . '" and name = "'
                    . $site . '";'
                );
                $get = $run->fetch(PDO::FETCH_OBJ);

                $locId = $get->id;

                $objLocation->setLocationProperty(
                    $locId,
                    'longitude',
                    $sa['longitude']
                );
                $objLocation->setLocationProperty(
                    $locId,
                    'latitude',
                    $sa['latitude']
                );
                $objLocation->setLocationProperty(
                    $locId,
                    'timezone',
                    $sa['timezone']
                );
            } else {
                // Add the new site!
                $locId = $objLocation->addLocation(
                    $sa['name'],
                    $sa['longitude'],
                    $sa['latitude'],
                    '',
                    $sa['country'],
                    $sa['timezone']
                );
                $objDatabase->execSQL(
                    'update locations set observer = "'
                    . $_SESSION['deepskylog_id']
                    . '" where id = "' . $locId . '";'
                );
            }
        } else {
            $siteValid = false;
        }

        $instId = -1;
        // Check if the instrument already exists in DeepskyLog
        if ($observation->getElementsByTagName('scope')->item(0)) {
            $ia = $scopeArray[$observation->getElementsByTagName('scope')
                ->item(0)->nodeValue];
            $instrument = $ia['name'];

            if (count(
                $objDatabase->selectRecordArray(
                    'SELECT * from instruments where observer = "'
                    . $_SESSION['deepskylog_id'] . '" and name = "'
                    . $instrument . '";'
                )
            ) > 0
            ) {
                // Update
                $instId = $objInstrument->getInstrumentId(
                    $ia['name'],
                    $_SESSION['deepskylog_id']
                );

                $objInstrument->setInstrumentProperty(
                    $instId,
                    'name',
                    $ia['name']
                );
                $objInstrument->setInstrumentProperty(
                    $instId,
                    'diameter',
                    $ia['diameter']
                );
                $objInstrument->setInstrumentProperty(
                    $instId,
                    'fd',
                    $ia['fd']
                );
                $objInstrument->setInstrumentProperty(
                    $instId,
                    'type',
                    $ia['type']
                );
                $objInstrument->setInstrumentProperty(
                    $instId,
                    'fixedMagnification',
                    $ia['fixedMagnification']
                );
            } else {
                // Add the new instrument!
                $instId = $objInstrument->addInstrument(
                    $ia['name'],
                    $ia['diameter'],
                    $ia['fd'],
                    $ia['type'],
                    $ia['fixedMagnification'],
                    $_SESSION['deepskylog_id']
                );
            }
        } else {
            // No scope defined, so this is a naked eye observation
            $instrument = 'Naked eye';

            if (count(
                $objDatabase->selectRecordArray(
                    'SELECT * from instruments where observer = "'
                    . $_SESSION['deepskylog_id'] . '" and name = "'
                    . $instrument . '";'
                )
            ) > 0
            ) {
                $instId = $objInstrument->getInstrumentId(
                    $instrument,
                    $_SESSION['deepskylog_id']
                );
            } else {
                // Add the new instrument!
                $instId = $objInstrument->addInstrument(
                    $instrument,
                    7,
                    1,
                    0,
                    1,
                    $_SESSION['deepskylog_id']
                );
            }
        }

        // Filter is not mandatory
        if ($observation->getElementsByTagName('filter')->item(0)) {
            // Check if the filter already exists in DeepskyLog
            $fa = $filterArray[$observation->getElementsByTagName('filter')
                ->item(0)->nodeValue];
            $filter = $fa['name'];

            if (count(
                $objDatabase->selectRecordArray(
                    'SELECT * from filters where observer = "'
                    . $_SESSION['deepskylog_id'] . '" and name = "'
                    . $filter . '";'
                )
            ) > 0
            ) {
                // Update the filter
                $filtId = $objFilter->getFilterId(
                    $fa['name'],
                    $_SESSION['deepskylog_id']
                );
                $objFilter->setFilterProperty($filtId, 'name', $fa['name']);
                $objFilter->setFilterProperty($filtId, 'type', $fa['type']);
                $objFilter->setFilterProperty(
                    $filtId,
                    'color',
                    $fa['color']
                );
                $objFilter->setFilterProperty(
                    $filtId,
                    'wratten',
                    $fa['wratten']
                );
                $objFilter->setFilterProperty(
                    $filtId,
                    'schott',
                    $fa['schott']
                );
            } else {
                // Add the new filter!
                $filtId = $objFilter->addFilter(
                    $fa['name'],
                    $fa['type'],
                    $fa['color'],
                    $fa['wratten'],
                    $fa['schott']
                );
                $objDatabase->execSQL(
                    'update filters set observer = "'
                    . $_SESSION['deepskylog_id']
                    . '" where id = "' . $filtId . '";'
                );
            }
        }

        // Eyepiece is not mandatory
        if ($observation->getElementsByTagName('eyepiece')->item(0)) {
            // Check if the eyepiece already exists in DeepskyLog
            $ea = $eyepieceArray[$observation
                ->getElementsByTagName('eyepiece')->item(0)->nodeValue];
            $eyepiece = $ea['name'];

            if (count(
                $objDatabase->selectRecordArray(
                    'SELECT * from eyepieces where observer = "'
                    . $_SESSION['deepskylog_id']
                    . '" and name = "' . $ea['name'] . '";'
                )
            ) > 0
            ) {
                // Update the eyepiece
                $eyepId = $objEyepiece->getEyepieceId(
                    $ea['name'],
                    $_SESSION['deepskylog_id']
                );
                $objEyepiece->setEyepieceProperty(
                    $eyepId,
                    'name',
                    $ea['name']
                );
                $objEyepiece->setEyepieceProperty(
                    $eyepId,
                    'focalLength',
                    $ea['focalLength']
                );
                $objEyepiece->setEyepieceProperty(
                    $eyepId,
                    'apparentFOV',
                    $ea['apparentFOV']
                );
                $objEyepiece->setEyepieceProperty(
                    $eyepId,
                    'maxFocalLength',
                    $ea['maxFocalLength']
                );
            } else {
                // Add the new eyepiece!
                $eyepId = $objEyepiece->addEyepiece(
                    $ea['name'],
                    $ea['focalLength'],
                    $ea['apparentFOV']
                );
                $objDatabase->execSQL(
                    'update eyepieces set observer = "'
                    . $_SESSION['deepskylog_id']
                    . '" where id = "' . $eyepId . '";'
                );
                $objEyepiece->setEyepieceProperty(
                    $eyepId,
                    'maxFocalLength',
                    $ea['maxFocalLength']
                );
            }
        }

        // Lens is not mandatory
        if ($observation->getElementsByTagName('lens')->item(0)) {
            // Check if the eyepiece already exists in DeepskyLog
            $la = $lensArray[$observation->getElementsByTagName('lens')
                ->item(0)->nodeValue];
            $lens = $la['name'];

            if (count(
                $objDatabase->selectRecordArray(
                    'SELECT * from lenses where observer = "'
                    . $_SESSION['deepskylog_id']
                    . '" and name = "' . $lens . '";'
                )
            ) > 0
            ) {
                // Update the lens
                $lensId = $objLens->getLensId(
                    $la['name'],
                    $_SESSION['deepskylog_id']
                );
                $objLens->setLensProperty($lensId, 'name', $la['name']);
                $objLens->setLensProperty($lensId, 'factor', $la['factor']);
            } else {
                // Add the new lens!
                $lensId = $objLens->addLens($la['name'], $la['factor']);
                $objDatabase->execSQL(
                    'update lenses set observer = "'
                    . $_SESSION['deepskylog_id']
                    . '" where id = "' . $lensId . '";'
                );
            }
        }

        // Object!!!

        // If the target is not known, we skip this observation.
        if (!array_key_exists(
            $observation->getElementsByTagName('target')->item(0)->nodeValue,
            $targetArray
        )
        ) {
            continue;
        }
        $ta = $targetArray[$observation->getElementsByTagName('target')
            ->item(0)->nodeValue];
        $target = $ta['name'];

        if ($siteValid) {
            if ($ta['known'] == 1) {
                $pattern = '/([A-Za-z]+)([\d\D\w]*)/';
                $targetName = preg_replace($pattern, '${1} ${2}', $target);
                $targetName = str_replace('  ', ' ', $targetName);
                $objeId = -1;
                // Check if the object with the given name exists. If this is
                // the case, set the objeId, else check the alternative names
                $targetName = $objCatalog->checkObject($targetName);
                if (count(
                    $objDatabase->selectRecordArray(
                        'SELECT objectnames.objectname FROM objectnames '
                        . 'WHERE (objectnames.altname = "'
                        . $targetName . '");'
                    )
                ) > 0
                ) {
                    $objeId = $objObject->getDsObjectName($targetName);
                } else {
                    // Object with the given name does not exist...
                    // Check if the name is an alternative name
                    for ($i = 0;$i < sizeof($ta['aliases']); $i++) {
                        $targetName = preg_replace(
                            $pattern,
                            '${1} ${2}',
                            $ta['aliases']['alias' . $i]
                        );
                        $targetName = str_replace('  ', ' ', $targetName);
                        $targetName = $objCatalog->checkObject($targetName);
                        if (count(
                            $objDatabase->selectRecordArray(
                                'SELECT objectnames.objectname FROM '
                                . 'objectnames WHERE '
                                . '(objectnames.altname = "'
                                . $targetName . '")'
                            )
                        ) > 0
                        ) {
                            $objeId = $objObject->getDsObjectName(
                                $targetName
                            );
                        }
                    }
                    if ($objeId == -1) {
                        // If the object has no coordinates,
                        // we can not add a new object.
                        if ($ta['type'] == 'TARGET') {
                            $errors++;
                            continue;
                        }
                        // Object does not exist (name or alternative name)
                        // Check for the type and coordinates. If there is
                        // already an object at the same coordinates with the
                        // same type, add the alternative name
                        $sql = 'SELECT name FROM objects WHERE ra > '
                                . ($ta['ra'] - 0.0001)
                                . ' and ra < ' . ($ta['ra'] + 0.0001)
                                . ' and decl > ' . ($ta['dec'] - 0.0001)
                                . ' and decl < ' . ($ta['dec'] + 0.0001)
                                . ' and type = "' . $ta['type'] . '"';
                        if (count(
                            $objDatabase->selectRecordArray($sql)
                        ) > 0
                        ) {
                            $run = $objDatabase->selectRecordset($sql);
                            $get = $run->fetch(PDO::FETCH_OBJ);

                            $objeId = $get->name;

                        // Also add alternative name to the existing
                        // object.
                        // $names = explode(' ', $objeId);
                        // $aliasNames = explode(' ', $targetName);

                        // $objObject->newAltName(
                        //     $names[0] . ' ' . $names[1],
                        //     $aliasNames[0],
                        //     $aliasNames[1]
                        // );
                        } else {
                            // else, add new object
                            continue;
                            $targetName = preg_replace(
                                $pattern,
                                '${1} ${2}',
                                $target
                            );
                            $targetName = str_replace(
                                '  ',
                                ' ',
                                $targetName
                            );
                            $targetName = $objCatalog->checkObject(
                                $targetName
                            );
                            $names = explode(' ', $targetName);
                            $objObject->addDSObject(
                                $names[0] . ' ' . $names[1],
                                $names[0],
                                $names[1],
                                $ta['type'],
                                $ta['constellation'],
                                $ta['ra'],
                                $ta['dec'],
                                $ta['mag'],
                                $ta['subr'],
                                $ta['diam1'],
                                $ta['diam2'],
                                $ta['pa'],
                                $ta['datasource']
                            );
                            for ($i = 0; $i < sizeof($ta['aliases']); $i++) {
                                $aliasName = preg_replace(
                                    $pattern,
                                    '${1} ${2}',
                                    $ta['aliases']['alias' . $i]
                                );
                                $aliasNames = explode(' ', $aliasName);
                                $objObject->newAltName(
                                    $names[0] . ' ' . $names[1],
                                    $aliasNames[0],
                                    $aliasNames[1]
                                );
                            }
                            $objeId = $objObject->getDsObjectName(
                                $targetName
                            );
                            $body = _('DeepskyLog - New Object ')
                                    . ' <a href="http://www.deepskylog.org/'
                                    . 'index.php?indexAction=detail_object'
                                    . '&object='
                                    . urlencode($targetName) . '">'
                                    . $targetName
                                    . '</a> '
                                    . _(' added during XML import')
                                    . ' '
                                    . _('by observer ')
                                    . ' <a href="http://www.deepskylog.org/'
                                    . 'index.php?indexAction=detail_observer'
                                    . '&user='
                                    . urlencode($loggedUser) . '">'
                                    . $objObserver->getObserverProperty(
                                        $loggedUser,
                                        'firstname'
                                    )
                                    . ' '
                                    . $objObserver->getObserverProperty(
                                        $loggedUser,
                                        'name'
                                    )
                                    . '</a>.<br /><br />';
                            if (isset($developversion)
                                && ($developversion == 1)
                            ) {
                                $entryMessage .= 'On the live server, '
                                        . 'a mail would be sent with the'
                                        . ' subject: '
                                        . _('DeepskyLog - New Object ')
                                        . ' ' . $targetName
                                        . _(' added during XML import')
                                        . '.<br />';
                            } else {
                                $objMessage->sendEmail(
                                    _('DeepskyLog - New Object ') . ' '
                                    . $targetName
                                    . _(' added during XML import'),
                                    $body,
                                    'developers'
                                );
                            }
                        }
                    }
                }

                // Check if the observation already exists!
                $date = new DateTime(
                    $observation->getElementsByTagName('begin')
                        ->item(0)->nodeValue
                );
                $date->setTimezone(new DateTimeZone('UTC'));

                $dateStr = $date->format('Ymd');
                $timeStr = $date->format('Hi');

                if ($instId > 1) {
                    // Check if the observation does already exist
                    $obsId = $objDatabase->selectRecordArray(
                        'SELECT id from observations WHERE objectname = "'
                        . $objeId . '" and date = "' . $dateStr
                        . '" and instrumentid = "' . $instId
                        . '" and locationId = "' . $locId
                        . '" and observerid = "'
                        . $_SESSION['deepskylog_id'] . '";'
                    );

                    if (count($obsId) > 0) {
                        $double++;
                    // We do NOT adapt the observation that is already
                    // in DeepskyLog!
                    } else {
                        // New observation
                        $resultNode = $observation
                            ->getElementsByTagName('result')->item(0);
                        $res = $resultNode
                            ->getElementsByTagName('description')->item(0);
                        if ($res) {
                            $description = ($res->nodeValue);
                        } else {
                            $description = '';
                        }
                        // Seeing is not mandatory
                        $see = $observation->getElementsByTagName('seeing')
                            ->item(0);
                        if ($see) {
                            $seeing = $see->nodeValue;
                        } else {
                            $seeing = '-1';
                        }

                        // Limiting magnitude is not mandatory
                        $faint = $observation
                            ->getElementsByTagName('faintestStar')->item(0);
                        if ($faint) {
                            $limmag = $faint->nodeValue;
                        } else {
                            $limmag = '';
                        }

                        if ($resultNode->hasAttribute('lang')) {
                            $language = $resultNode->getAttribute('lang');
                            if ($language == '') {
                                $language = 'en';
                            }
                        } else {
                            $language = 'en';
                        }

                        // Rating is not mandatory
                        $rate = $resultNode->getElementsByTagName('rating')
                            ->item(0);
                        if ($rate) {
                            $visibility = $rate->nodeValue;
                        } else {
                            $visibility = 0;
                        }
                        if ($visibility == 99) {
                            $visibility = 0;
                        }

                        $eyepiece = $observation
                            ->getElementsByTagName('eyepiece')->item(0);
                        if ($eyepiece) {
                            $ei = $eyepId;
                        } else {
                            $ei = 0;
                        }

                        $filter = $observation
                            ->getElementsByTagName('filter')->item(0);
                        if ($filter) {
                            $fi = $filtId;
                        } else {
                            $fi = 0;
                        }

                        $lens = $observation
                            ->getElementsByTagName('lens')->item(0);
                        if ($lens) {
                            $li = $lensId;
                        } else {
                            $li = 0;
                        }
                        $obsId = $objObservation->addDSObservation2(
                            $objeId,
                            $_SESSION['deepskylog_id'],
                            $instId,
                            $locId,
                            $dateStr,
                            $timeStr,
                            $description,
                            $seeing,
                            $limmag,
                            $visibility,
                            $language,
                            $ei,
                            $fi,
                            $li
                        );
                        $obsId = $objDatabase->selectSingleValue(
                            'SELECT id FROM observations '
                            . 'ORDER BY id DESC LIMIT 1',
                            'id'
                        );

                        // Add the observation to the session
                        $objSession->addObservationToSessions($obsId);

                        // Magnification is not mandatory
                        $magn = $observation
                            ->getElementsByTagName('magnification')->item(0);
                        if ($magn) {
                            $objObservation->setDsObservationProperty(
                                $obsId,
                                'magnification',
                                $magn->nodeValue
                            );
                        }
                        // Sqm is not mandatory
                        $sqmv = $observation
                            ->getElementsByTagName('sky-quality')->item(0);
                        if ($sqmv) {
                            // Get sqm value and convert it
                            $unit = $sqmv->getAttribute('unit');

                            if ($unit == 'mags-per-squarearcmin') {
                                $sqm = $sqmv->nodeValue + 8.89;
                            } else {
                                $sqm = $sqmv->nodeValue;
                            }

                            $objObservation->setDsObservationProperty(
                                $obsId,
                                'SQM',
                                $sqm
                            );
                        }

                        // colorContrasts is not mandatory
                        if ($resultNode->hasAttribute('colorContrasts')) {
                            $cc = $resultNode->getAttribute(
                                'colorContrasts'
                            );
                            if ($cc == 'true') {
                                $colorContrast = 1;
                            } else {
                                $colorContrast = 0;
                            }
                        } else {
                            $colorContrast = -1;
                        }
                        $objObservation->setDsObservationProperty(
                            $obsId,
                            'colorContrasts',
                            $colorContrast
                        );

                        // extended is not mandatory
                        if ($resultNode->hasAttribute('extended')) {
                            $ext = $resultNode->getAttribute('extended');
                            if ($ext == 'true') {
                                $extended = 1;
                            } else {
                                $extended = 0;
                            }
                        } else {
                            $extended = -1;
                        }
                        $objObservation->setDsObservationProperty(
                            $obsId,
                            'extended',
                            $extended
                        );

                        // mottled is not mandatory
                        if ($resultNode->hasAttribute('mottled')) {
                            $mot = $resultNode->getAttribute('mottled');
                            if ($mot == 'true') {
                                $mottled = 1;
                            } else {
                                $mottled = 0;
                            }
                        } else {
                            $mottled = -1;
                        }
                        $objObservation->setDsObservationProperty(
                            $obsId,
                            'mottled',
                            $mottled
                        );

                        // resolved is not mandatory
                        if ($resultNode->hasAttribute('resolved')) {
                            $res = $resultNode->getAttribute('resolved');
                            if ($res == 'true') {
                                $resolved = 1;
                            } else {
                                $resolved = 0;
                            }
                        } else {
                            $resolved = -1;
                        }
                        $objObservation->setDsObservationProperty(
                            $obsId,
                            'resolved',
                            $resolved
                        );

                        // stellar is not mandatory
                        if ($resultNode->hasAttribute('stellar')) {
                            $stel = $resultNode->getAttribute('stellar');
                            if ($stel == 'true') {
                                $stellar = 1;
                            } else {
                                $stellar = 0;
                            }
                        } else {
                            $stellar = -1;
                        }
                        $objObservation->setDsObservationProperty(
                            $obsId,
                            'stellar',
                            $stellar
                        );

                        // unusualShape is not mandatory
                        if ($resultNode->hasAttribute('unusualShape')) {
                            $un = $resultNode->getAttribute('unusualShape');
                            if ($un == 'true') {
                                $unusualShape = 1;
                            } else {
                                $unusualShape = 0;
                            }
                        } else {
                            $unusualShape = -1;
                        }
                        $objObservation->setDsObservationProperty(
                            $obsId,
                            'unusualShape',
                            $unusualShape
                        );

                        // partlyUnresolved is not mandatory
                        if ($resultNode->hasAttribute('partlyUnresolved')) {
                            $pun = $resultNode->getAttribute(
                                'partlyUnresolved'
                            );
                            if ($pun == 'true') {
                                $partlyUnresolved = 1;
                            } else {
                                $partlyUnresolved = 0;
                            }
                        } else {
                            $partlyUnresolved = -1;
                        }
                        $objObservation->setDsObservationProperty(
                            $obsId,
                            'partlyUnresolved',
                            $partlyUnresolved
                        );

                        // equalBrightness is not mandatory
                        if ($resultNode->hasAttribute('equalBrightness')) {
                            $eb = $resultNode->getAttribute(
                                'equalBrightness'
                            );
                            if ($eb == 'true') {
                                $equalBrightness = 1;
                            } else {
                                $equalBrightness = 0;
                            }
                        } else {
                            $equalBrightness = -1;
                        }
                        $objObservation->setDsObservationProperty(
                            $obsId,
                            'equalBrightness',
                            $equalBrightness
                        );

                        // niceSurrounding is not mandatory
                        if ($resultNode->hasAttribute('niceSurrounding')) {
                            $ns = $resultNode->getAttribute(
                                'niceSurrounding'
                            );
                            if ($ns == 'true') {
                                $niceSurrounding = 1;
                            } else {
                                $niceSurrounding = 0;
                            }
                        } else {
                            $niceSurrounding = -1;
                        }
                        $objObservation->setDsObservationProperty(
                            $obsId,
                            'nicefield',
                            $niceSurrounding
                        );

                        // colorMain is not mandatory
                        $colorMain = $resultNode
                            ->getElementsByTagName('colorMain')->item(0);
                        if ($colorMain) {
                            $color1 = $colorMain->nodeValue;

                            if ($color1 == 'White' || $color1 == 'white') {
                                $col1 = 1;
                            }
                            if ($color1 == 'Red' || $color1 == 'red') {
                                $col1 = 2;
                            }
                            if ($color1 == 'Orange' || $color1 == 'orange') {
                                $col1 = 3;
                            }
                            if ($color1 == 'Yellow' || $color1 == 'yellow') {
                                $col1 = 4;
                            }
                            if ($color1 == 'Green' || $color1 == 'green') {
                                $col1 = 5;
                            }
                            if ($color1 == 'Blue' || $color1 == 'blue') {
                                $col1 = 6;
                            }
                            $objObservation->setDsObservationProperty(
                                $obsId,
                                'component1',
                                $col1
                            );
                        }

                        // colorCompanion is not mandatory
                        $colorc = $resultNode
                            ->getElementsByTagName('colorCompanion')
                            ->item(0);
                        if ($colorc) {
                            $color2 = $colorc->nodeValue;

                            if ($color2 == 'White' || $color2 == 'white') {
                                $col2 = 1;
                            }
                            if ($color2 == 'Red' || $color2 == 'red') {
                                $col2 = 2;
                            }
                            if ($color2 == 'Orange' || $color2 == 'orange') {
                                $col2 = 3;
                            }
                            if ($color2 == 'Yellow' || $color2 == 'yellow') {
                                $col2 = 4;
                            }
                            if ($color2 == 'Green' || $color2 == 'green') {
                                $col2 = 5;
                            }
                            if ($color2 == 'Blue' || $color2 == 'blue') {
                                $col2 = 6;
                            }
                            $objObservation->setDsObservationProperty(
                                $obsId,
                                'component2',
                                $col2
                            );
                        }

                        // Character is not mandatory
                        $chara = $resultNode
                            ->getElementsByTagName('character')->item(0);
                        if ($chara) {
                            $objObservation->setDsObservationProperty(
                                $obsId,
                                'clusterType',
                                $chara->nodeValue
                            );
                        }

                        // smallDiameter is not mandatory
                        $sd = $resultNode
                            ->getElementsByTagName('smallDiameter')->item(0);
                        if ($sd) {
                            $unit = $sd->getAttribute('unit');
                            if ($unit == 'deg') {
                                $smallDiameter = $sd->nodeValue * 3600.0;
                            } elseif ($unit == 'rad') {
                                $smallDiameter = Rad2Deg($sd->nodeValue)
                                        * 3600.0;
                            } elseif ($unit == 'arcmin') {
                                $smallDiameter = $sd->nodeValue * 60.0;
                            } elseif ($unit == 'arcsec') {
                                $smallDiameter = $sd->nodeValue;
                            }
                            $objObservation->setDsObservationProperty(
                                $obsId,
                                'smallDiameter',
                                $smallDiameter
                            );
                        }
                        // largeDiameter is not mandatory
                        $ld = $resultNode
                            ->getElementsByTagName('largeDiameter')->item(0);
                        if ($ld) {
                            $unit = $ld->getAttribute('unit');
                            if ($unit == 'deg') {
                                $largeDiameter = $ld->nodeValue * 3600.0;
                            } elseif ($unit == 'rad') {
                                $largeDiameter = Rad2Deg($ld->nodeValue)
                                        * 3600.0;
                            } elseif ($unit == 'arcmin') {
                                $largeDiameter = $ld->nodeValue * 60.0;
                            } elseif ($unit == 'arcsec') {
                                $largeDiameter = $ld->nodeValue;
                            }
                            $objObservation->setDsObservationProperty(
                                $obsId,
                                'largeDiameter',
                                $largeDiameter
                            );
                        }

                        $magni = $observation
                            ->getElementsByTagName('magnification')->item(0);
                        if ($magni) {
                            $objObservation->setDsObservationProperty(
                                $obsId,
                                'magnification',
                                $magni->nodeValue
                            );
                        }
                        $added++;
                    }
                }
            }
        }
    }
}
$_GET['indexAction'] = 'default_action';

$entryMessage = sprintf(
    _('Observations added: %s'),
    $added
) . '; ' . sprintf(_('observations rejected with problems: %s'), $errors)
    . '; ' . sprintf(
        _('observations dropped because already present: %s'),
        $double
    ) . '.<br />';
//} else {
//    $entryMessage .= _('Invalid XML file!');
//    $_GET['indexAction'] = 'add_xml';

//    return;
//}

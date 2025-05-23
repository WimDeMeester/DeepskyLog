<?php

// data_get_objects
// get objects for queries, lists, nearby objects...
global $inIndex;

if ((!isset($inIndex)) || (!$inIndex)) {
    include "../../redirect.php";
} else {
    data_get_objects();
}
function data_get_objects()
{
    global $showPartOfs, $listname, $loggedUser, $objAtlas, $objObserver, $objObservation, $objCatalog, $objList, $objObject, $objUtil;
    $showPartOfs = $objUtil->checkGetKey('showPartOfs', $objUtil->checkSessionKey('QobjPO', 0));
    // ========================================= filter objects from observation query
    if ($objUtil->checkGetKey('source') == 'observation_query') {
        $validQobj = false;
        if (array_key_exists('QobjParams', $_SESSION) && array_key_exists('source', $_SESSION ['QobjParams']) && ($_SESSION ['QobjParams'] ['source'] == 'observation_query')) {
            $validQobj = true;
        }
        if ($validQobj) {
            foreach ($_SERVER['QobjParams'] as $key => $value) {
                if ((!array_key_exists($key, $_SESSION['QobsParams']) || ($value != $_SESSION['QobsParams'][$key])) && ($key != 'source')) {
                    $validQobj = false;
                    break;
                }
            }
        }
        if ($validQobj) {
            foreach ($_SESSION['QobsParams'] as $key => $value) {
                if (!array_key_exists($key, $_SESSION ['QobjParams']) || ($value != $_SESSION ['QobjParams'] [$key])) {
                    $validQobj = false;
                    break;
                }
            }
        }
        if ($showPartOfs != $objUtil->checkSessionKey('QobjPO', 0)) {
            $validQobj = false;
        }
        if (!$validQobj) {
            $obj = $objObject->getSeenObjectDetails($objObservation->getObjectsFromObservations($_SESSION ['Qobs'], $showPartOfs), 'A');
            $_SESSION ['QobjParams'] = array_merge(array(
                    'source' => 'observation_query'
            ), $_SESSION ['QobsParams']);
            $_SESSION ['QobjPO'] = $showPartOfs;
            $_SESSION ['Qobj'] = $obj;
        }
    }	// ========================================= get objects from list
    elseif ($objUtil->checkGetKey('source') == 'tolist') {
        $validQobj = false;
        if (array_key_exists('QobjParams', $_SESSION) && array_key_exists('source', $_SESSION ['QobjParams']) && ($_SESSION ['QobjParams'] ['source'] == 'tolist') && array_key_exists('list', $_SESSION ['QobjParams']) && ($_SESSION ['QobjParams'] ['list'] == $listname)) {
            $validQobj = true;
        }
        if (!$validQobj) {
            $_SESSION ['QobjParams'] = array(
                    'source' => 'tolist',
                    'list' => $listname
            );
            $_SESSION ['Qobj'] = $objList->getObjectsFromList($_SESSION ['listname'], $objUtil->checkGetKey('public'));
        }
    }	// ========================================= get nearby objects for selected object
    elseif ($objUtil->checkGetKey('source') == 'objects_nearby') {
        $validQobj = false;
        if (array_key_exists('QobjParams', $_SESSION) && array_key_exists('source', $_SESSION ['QobjParams']) && ($_SESSION ['QobjParams'] ['source'] == 'objects_nearby') && array_key_exists('object', $_GET) && array_key_exists('object', $_SESSION ['QobjParams']) && ($_SESSION ['QobjParams'] ['object'] == $_GET ['object']) && array_key_exists('zoom', $_GET) && array_key_exists('zoom', $_SESSION ['QobjParams']) && ($_SESSION ['QobjParams'] ['zoom'] == $_GET ['zoom'])) {
            $validQobj = true;
        }
        if (!$validQobj) {
            $_SESSION ['QobjParams'] = array(
                    'source' => 'objects_nearby',
                    'object' => $_GET ['object'],
                    'zoom' => $_GET ['zoom']
            );
            $_SESSION ['Qobj'] = $objObject->getSeenObjectDetails($objObject->getNearbyObjects($_GET ['object'], $_GET ['zoom']));
        }
    }	// ========================================= get objects for objects query page
    elseif ($objUtil->checkGetKey('source') == 'setup_objects_query') {
        $exact = 0;
        $name = $objUtil->checkGetKey('catalog');
        $catalog = $objUtil->checkGetKey('catalog');
        $catNumber = $objUtil->checkGetKey('catNumber');
        $atlas = $objUtil->checkGetKey('atlas', (($loggedUser) ? $objAtlas->atlasCodes [$objObserver->getObserverProperty($loggedUser, 'standardAtlasCode', 'urano')] : ''));
        $atlasPageNumber = $objUtil->checkGetKey('atlasPageNumber', '');
        $inList = $objUtil->checkGetKey('inList');
        $notInList = $objUtil->checkGetKey('notInList');
        $size_min_units = $objUtil->checkGetKey('size_min_units');
        $size_max_units = $objUtil->checkGetKey('size_max_units');
        if (array_key_exists('catNumber', $_GET) && $_GET ['catNumber']) {
            $name = ucwords(trim($name . " " . trim($_GET ['catNumber'])));
            $exact = "1";
        }
        // ATLAS PAGE
        $pageError = false;
        if (array_key_exists('atlasPageNumber', $_GET) && $_GET ['atlasPageNumber']) {
            if (!is_numeric($_GET ['atlasPageNumber']) || ($_GET ['atlasPageNumber'] < 1) || ($_GET ['atlasPageNumber'] > 5000)) {
                $pageError = true;
            } else {
                $atlasPageNumber = $_GET ['atlasPageNumber'];
            }
        }
        $con = $objUtil->checkGetKey('con'); // CONSTELLATION
        $conto = $objUtil->checkGetKey('conto', $con); // CONSTELLATION
        $type = $objUtil->checkGetKey('type'); // TYPE
        $descriptioncontains = $objUtil->checkGetKey('descriptioncontains');
        $minDecl = '';
        $minDeclDegreesError = false;
        $minDeclMinutesError = false;
        $minDeclSecondsError = false;
        if (($minDeclDegrees = $objUtil->checkGetKey('minDeclDegrees')) != '') { 		// MINIMUM DECLINATION
            if ((!is_numeric($minDeclDegrees)) || ($minDeclDegrees <= -90) || ($minDeclDegrees >= 90)) {
                $minDeclDegreesError = true;
            }
            $minDeclMinutes = $objUtil->checkGetKey('minDeclMinutes', 0);
            if ((!is_numeric($minDeclMinutes)) || ($minDeclMinutes < 0) || ($minDeclMinutes >= 60)) {
                $minDeclMinutesError = true;
            }
            $minDeclSeconds = $objUtil->checkGetKey('minDeclSeconds', 0);
            if ((!is_numeric($minDeclSeconds)) || ($minDeclSeconds < 0) || ($minDeclSeconds >= 60)) {
                $minDeclSecondsError = true;
            }
            if (!($errorQuery = ($minDeclDegreesError || $minDeclMinutesError || $minDeclSecondsError))) {
                if (substr(trim($_GET ['minDeclDegrees']), 0, 1) == "-") {
                    $minDecl = $minDeclDegrees - ($minDeclMinutes / 60) - ($minDeclSeconds / 3600);
                } else {
                    $minDecl = $minDeclDegrees + ($minDeclMinutes / 60) + ($minDeclSeconds / 3600);
                }
            }
        }
        $maxDecl = '';
        $maxDeclDegreesError = false;
        $maxDeclMinutesError = false;
        $maxDeclSecondsError = false;
        if (($maxDeclDegrees = $objUtil->checkGetKey('maxDeclDegrees')) != '') { 		// MAXIMUM DECLINATION
            if ((!is_numeric($maxDeclDegrees)) || ($maxDeclDegrees <= -90) || ($maxDeclDegrees >= 90)) {
                $maxDeclDegreesError = true;
            }
            $maxDeclMinutes = $objUtil->checkGetKey('maxDeclMinutes', 0);
            if ((!is_numeric($maxDeclMinutes)) || ($maxDeclMinutes < 0) || ($maxDeclMinutes >= 60)) {
                $maxDeclMinutesError = true;
            }
            $maxDeclSeconds = $objUtil->checkGetKey('maxDeclSeconds', 0);
            if ((!is_numeric($maxDeclSeconds)) || ($maxDeclSeconds < 0) || ($maxDeclSeconds >= 60)) {
                $maxDeclSecondsError = true;
            }
            if (!($errorQuery = ($maxDeclDegreesError || $maxDeclMinutesError || $maxDeclSecondsError))) {
                if (substr(trim($_GET ['maxDeclDegrees']), 0, 1) == "-") {
                    $maxDecl = $maxDeclDegrees - ($maxDeclMinutes / 60) - ($maxDeclSeconds / 3600);
                } else {
                    $maxDecl = $maxDeclDegrees + ($maxDeclMinutes / 60) + ($maxDeclSeconds / 3600);
                }
            }
        }
        // MIN RA
        $minRA = '';
        $minRAHoursError = false;
        $minRAMinutesError = false;
        $minRASecondsError = false;
        if (($minRAHours = $objUtil->checkGetKey('minRAHours')) != '') {
            if ((!is_numeric($_GET ['minRAHours'])) || ($_GET ['minRAHours'] < 0) || ($_GET ['minRAHours'] > 24)) {
                $minRAHoursError = true;
            }
            if (array_key_exists('minRAMinutes', $_GET) && $_GET ['minRAMinutes'] != '') {
                $minRAMinutes = $_GET ['minRAMinutes'];
                if ((!is_numeric($_GET ['minRAMinutes'])) || ($_GET ['minRAMinutes'] < 0) || ($_GET ['minRAMinutes'] >= 60)) {
                    $minRAMinutesError = true;
                }
            } else {
                $minRAMinutes = 0;
                $_GET ['minRAMinutes'] = 0;
            }
            if (array_key_exists('minRASeconds', $_GET) && $_GET ['minRASeconds'] != '') {
                if ((!is_numeric($_GET ['minRASeconds'])) || ($_GET ['minRASeconds'] < 0) || ($_GET ['minRASeconds'] >= 60)) {
                    $minRASecondsError = true;
                } else {
                    $minRASeconds = $_GET ['minRASeconds'];
                }
            } else {
                $minRASeconds = 0;
                $_GET ['minRASeconds'] = 0;
            }
            if (!($minRAHoursError || $minRAMinutesError || $minRASecondsError)) {
                $minRA = $minRAHours + ($minRAMinutes / 60) + ($minRASeconds / 3600);
            }
        }
        // MAX RA
        $maxRA = '';
        $maxRAHoursError = false;
        $maxRAMinutesError = false;
        $maxRASecondsError = false;
        if (($maxRAHours = $objUtil->checkGetKey('maxRAHours')) != '') {
            if ((!is_numeric($_GET ['maxRAHours'])) || ($_GET ['maxRAHours'] < 0) || ($_GET ['maxRAHours'] > 24)) {
                $maxRAHoursError = true;
            }
            if (array_key_exists('maxRAMinutes', $_GET) && $_GET ['maxRAMinutes'] != '') {
                $maxRAMinutes = $_GET ['maxRAMinutes'];
                if ((!is_numeric($_GET ['maxRAMinutes'])) || ($_GET ['maxRAMinutes'] < 0) || ($_GET ['maxRAMinutes'] >= 60)) {
                    $maxRAMinutesError = true;
                }
            } else {
                $maxRAMinutes = 0;
                $_GET ['maxRAMinutes'] = 0;
            }
            if (array_key_exists('maxRASeconds', $_GET) && $_GET ['maxRASeconds'] != '') {
                if ((!is_numeric($_GET ['maxRASeconds'])) || ($_GET ['maxRASeconds'] < 0) || ($_GET ['maxRASeconds'] >= 60)) {
                    $maxRASecondsError = true;
                } else {
                    $maxRASeconds = $_GET ['maxRASeconds'];
                }
            } else {
                $maxRASeconds = 0;
                $_GET ['maxRASeconds'] = 0;
            }
            if (!($maxRAHoursError || $maxRAMinutesError || $maxRASecondsError)) {
                $maxRA = $maxRAHours + ($maxRAMinutes / 60) + ($maxRASeconds / 3600);
            }
        }
        // MAGNITUDE BRIGHTER THAN
        $maxMag = '';
        if (array_key_exists('maxMag', $_GET) && $_GET ['maxMag'] != '') {
            $maxMag = $_GET ['maxMag'];
            if ((!is_numeric($_GET ['maxMag'])) || ($_GET ['maxMag'] <= -2) || ($_GET ['maxMag'] >= 30)) {
                $maxMagError = true;
            }
        }
        // MAGNITUDE LESSER THAN
        $minMag = '';
        if (array_key_exists('minMag', $_GET) && $_GET ['minMag'] != '') {
            $minMag = $_GET ['minMag'];
            if ((!is_numeric($_GET ['minMag'])) || ($_GET ['minMag'] <= -2) || ($_GET ['minMag'] >= 30)) {
                $minMagError = true;
            }
        }
        // SB BRIGHTER THAN
        $maxSB = '';
        if (array_key_exists('maxSB', $_GET) && $_GET ['maxSB'] != '') {
            $maxSB = $_GET ['maxSB'];
            if ((!is_numeric($_GET ['maxSB'])) || ($_GET ['maxSB'] <= -2) || ($_GET ['maxSB'] >= 30)) {
                $maxSBError = true;
            }
        }
        // SB LESSER THAN
        $minSB = '';
        if (array_key_exists('minSB', $_GET) && $_GET ['minSB'] != '') {
            $minSB = $_GET ['minSB'];
            if ((!is_numeric($_GET ['minSB'])) || ($_GET ['minSB'] <= -2) || ($_GET ['minSB'] >= 30)) {
                $minSBError = true;
            }
        }
        // MINIMUM SIZE
        $minSizeC = '';
        if (array_key_exists('minSize', $_GET) && ($_GET ['minSize'] != '')) {
            if ((!is_numeric($_GET ['minSize'])) || ($_GET ['minSize'] < 0)) {
                $minSizeError = true;
            }
            if (array_key_exists('size_min_units', $_GET) && ($_GET ['size_min_units'] == "sec")) {
                $size_min_units = 'sec';
                $minSize = $_GET ['minSize'];
                $minSizeC = $_GET ['minSize'];
            } else {
                $size_min_units = 'min';
                $minSize = $_GET ['minSize'];
                $minSizeC = $_GET ['minSize'] * 60;
            }
        }
        // MAXIMUM SIZE
        $maxSizeC = '';
        if (array_key_exists('maxSize', $_GET) && $_GET ['maxSize'] != '') {
            if ((!is_numeric($_GET ['maxSize'])) || ($_GET ['maxSize'] < 0)) {
                $maxSizeError = true;
            }
            if (array_key_exists('size_max_units', $_GET) && ($_GET ['size_max_units'] == "sec")) {
                $size_max_units = 'sec';
                $maxSize = $_GET ['maxSize'];
                $maxSizeC = $_GET ['maxSize'];
            } else {
                $size_max_units = 'min';
                $maxSize = $_GET ['maxSize'];
                $maxSizeC = $_GET ['maxSize'] * 60;
            }
        }
        // MIN CONTRAST
        $minContrast = '';
        if (array_key_exists('minContrast', $_GET) && $_GET ['minContrast'] != '') {
            $minContrast = $_GET ['minContrast'];
            if (!is_numeric($_GET ['minContrast'])) {
                $minContrastError = true;
            }
        }
        // MAX CONTRAST
        $maxContrast = '';
        if (array_key_exists('maxContrast', $_GET) && $_GET ['maxContrast'] != '') {
            $maxContrast = $_GET ['maxContrast'];
            if (!is_numeric($_GET ['maxContrast'])) {
                $maxContrastError = true;
            }
        }
        // DESCRIPTION CONTAINS
        $descriptioncontains = $objUtil->checkGetKey('descriptioncontains');
        if ($minDecl && $maxDecl && ($minDecl < $MaxDecl)) {
            $minDeclError = true;
            $maxDeclError = true;
        }
        if ($minRA && $maxRA && ($minRA < $maxRA)) {
            $minRAError = true;
            $maxRAError = true;
        }
        $minMagError = false;
        $maxMagError = false;
        if ($maxMag && $minMag && ($maxMag < $minMag)) {
            $maxMagError = true;
            $minMagError = true;
        }
        $minSBError = false;
        $maxSBError = false;
        if ($minSB && $maxSB && ($maxSB < $minSB)) {
            $minSBError = true;
            $maxSBError = true;
        }
        $minSizeError = false;
        $maxSizeError = false;
        if ($minSizeC && $maxSizeC && ($minSizeC > $maxSizeC)) {
            $minSizeError = true;
            $maxSizeError = true;
        }
        $minContrastError = false;
        $maxContrastError = false;
        if ($minContrast && $maxContrast && ($minContrast > $maxContrast)) {
            $minContrastError = true;
            $maxContrastError = true;
        }
        $listError = false;
        if ($inList && $notInList && ($inList == $notInList)) {
            $listError = true;
        }
        // Disable possibility to search for objects with a contrast reserve alone!!!!
        if ((( int ) !((array_key_exists('con', $_GET) && ($_GET ['con'] != "")) || (array_key_exists('type', $_GET) && ($_GET ['type'] != "")) || (array_key_exists('catalog', $_GET) && ($_GET ['catalog'] != "")) || (array_key_exists('catPageNumber', $_GET) && ($_GET ['catPageNumber'] != "")) || (array_key_exists('minMag', $_GET) && ($_GET ['minMag'] != "")) || (array_key_exists('maxMag', $_GET) && ($_GET ['maxMag'] != "")) || (array_key_exists('maxSB', $_GET) && ($_GET ['maxSB'] != "")) || (array_key_exists('minSB', $_GET) && ($_GET ['minSB'] != "")) || (array_key_exists('minRAhours', $_GET) && ($_GET ['minRAhours'] != "")) || (array_key_exists('minDeclDegrees', $_GET) && ($_GET ['minDeclDegrees'] != "")) || (array_key_exists('maxRAhours', $_GET) && ($_GET ['maxRAhours'] != "")) || (array_key_exists('maxDeclDegrees', $_GET) && ($_GET ['maxDeclDegrees'] != "")) || (array_key_exists('minSize', $_GET) && ($_GET ['minSize'] != "")) || (array_key_exists('maxSize', $_GET) && ($_GET ["maxSize"] != "")))) && ((array_key_exists('maxContrast', $_GET) && ($_GET ['maxContrast'] != "")) || (array_key_exists('minContrast', $_GET) && ($_GET ['minContrast'] != "")))) {
            $maxContrastError = true;
            $minContrastError = true;
        }
        reset($_GET);
        $excl = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 5) == 'excl_') {
                $excl [] = substr($key, 5);
            }
        }
        $excludeexceptseen = $objUtil->checkGetKey('excludeexceptseen', 'off');
        if (!($pageError || $minDeclDegreesError || $minDeclMinutesError || $minDeclSecondsError || $maxDeclDegreesError || $maxDeclMinutesError || $maxDeclSecondsError || $minRAHoursError || $minRAMinutesError || $minRASecondsError || $maxRAHoursError || $maxRAMinutesError || $maxRASecondsError || $minMagError || $maxMagError || $minSBError || $maxSBError || $minSizeError || $maxSizeError || $minContrastError || $maxContrastError || $listError)) {
            if (array_key_exists('seen', $_GET) && $_GET ['seen']) {
                $seenPar = $_GET ['seen'];
            } else {
                $seenPar = "A";
            }
            $query = array(
                    "name" => $name,
                    "type" => $type,
                    "con" => $con,
                    "conto" => $conto,
                    "minmag" => $minMag,
                    "maxmag" => $maxMag,
                    "minsubr" => $minSB,
                    "maxsubr" => $maxSB,
                    "minra" => $minRA,
                    "maxra" => $maxRA,
                    "mindecl" => $minDecl,
                    "maxdecl" => $maxDecl,
                    "mindiam1" => $minSizeC,
                    "maxdiam1" => $maxSizeC,
                    "minContrast" => $minContrast,
                    "maxContrast" => $maxContrast,
                    "inList" => $inList,
                    "notInList" => $notInList,
                    "atlas" => $atlas,
                    "atlasPageNumber" => $atlasPageNumber,
                    "excl" => $excl,
                    "exclexceptseen" => $excludeexceptseen,
                    "descriptioncontains" => $descriptioncontains,
                    "catalog" => $objUtil->checkGetKey('catalog'),
                    "catNumber" => $objUtil->checkGetKey('catNumber'),
                    "seen" => $seenPar
            );
            $validQobj = false;
            if (array_key_exists('QobjParams', $_SESSION) && (count($_SESSION ['QobjParams']) > 1) && array_key_exists('Qobj', $_SESSION) && (count($_SESSION ['Qobj']) > 0)) {
                $validQobj = true;
            }
            if ($validQobj) {
                foreach ($_SESSION['QobjParams'] as $key => $value) {
                    if ((!array_key_exists($key, $query)) || ($value != $query [$key])) {
                        $validQobj = false;
                        break;
                    }
                }
            }
            if ($validQobj) {
                foreach ($query as $key => $value) {
                    if ((!array_key_exists($key, $_SESSION ['QobjParams'])) || ($value != $_SESSION ['QobjParams'] [$key])) {
                        $validQobj = false;
                        break;
                    }
                }
            }
            if ($showPartOfs != $objUtil->checkSessionKey('QobjPO', 0)) {
                $validQobj = false;
            }
            if (!$validQobj) {
                $_SESSION ['QobjParams'] = $query;
                $_SESSION ['QobjPO'] = $showPartOfs;
                $_SESSION ['Qobj'] = $objObject->getObjectFromQuery($query, $exact, $seenPar, $showPartOfs);
            }
        } else {
            $_SESSION ['QobjParams'] = array();
            $_SESSION ['Qobj'] = array();
        }
    } elseif ($objUtil->checkGetKey('source') == 'quickpick') { 	// ========================== from quickpick page
        $validQobj = false;
        if (array_key_exists('QobjParams', $_SESSION) && array_key_exists('source', $_SESSION ['QobjParams']) && ($_SESSION ['QobjParams'] ['source'] == 'quickpick') && array_key_exists('object', $_SESSION ['QobjParams']) && ($_SESSION ['QobjParams'] ['object'] == $objUtil->checkGetKey('object'))) {
            $validQobj = true;
        }
        if ($showPartOfs != $objUtil->checkSessionKey('QobjPO', 0)) {
            $validQobj = false;
        }
        if (!$validQobj) {
            if (!$objUtil->checkGetKey('object')) { // $_SESSION['QobjParams']=array();
                $_SESSION ['QobjPO'] = $showPartOfs;
                $_SESSION ['Qobj'] = array();
            } else {
                $_SESSION ['QobjParams'] = array(
                        'source' => 'quickpick',
                        'object' => $objUtil->checkGetKey('object')
                );
                $_SESSION ['QobjPO'] = $showPartOfs;
                $_SESSION ['Qobj'] = $objObject->getObjectFromQuery(array(
                        'name' => $objUtil->checkGetKey('object')
                ), 1, "A", $showPartOfs);
            }
        }
    } elseif ($objUtil->checkGetKey('source') == 'add_object10') { 	// ========================== from quickpick page
        $validQobj = false;
        $catandindex = $objCatalog->checkObject($objUtil->checkRequestKey('catalog') . ' ' . $objUtil->checkRequestKey('number'));
        $firstspace = strpos($catandindex, ' ', 0);
        if ($firstspace !== false) {
            $thenewcatalog0 = trim(substr($catandindex, 0, $firstspace));
            $theindex = trim(substr($catandindex, $firstspace + 1));
        } else {
            $thenewcatalog0 = $catandindex;
            $theindex = '';
        }
        $_REQUEST ['catalog'] = $thenewcatalog0;
        $_REQUEST ['number'] = $theindex;
        $_GET ['object'] = "%" . $_REQUEST ['catalog'] . "%" . str_replace(" ", "%", $_REQUEST ['number']) . "%";
        if (array_key_exists('QobjParams', $_SESSION) && array_key_exists('source', $_SESSION ['QobjParams']) && ($_SESSION ['QobjParams'] ['source'] == 'add_object') && array_key_exists('object', $_SESSION ['QobjParams']) && ($_SESSION ['QobjParams'] ['object'] == $objUtil->checkGetKey('object'))) {
            $validQobj = true;
        }
        if ($showPartOfs != $objUtil->checkSessionKey('QobjPO', 0)) {
            $validQobj = false;
        }
        if (!$validQobj) {
            if (!$objUtil->checkGetKey('object')) {
                $_SESSION ['QobjParams'] = array();
                $_SESSION ['QobjPO'] = $showPartOfs;
                $_SESSION ['Qobj'] = array();
            } else {
                $_SESSION ['QobjParams'] = array(
                        'source' => 'add_object',
                        'object' => $objUtil->checkGetKey('object')
                );
                $_SESSION ['QobjPO'] = $showPartOfs;
                $_SESSION ['Qobj'] = $objObject->getObjectFromQuery(array(
                        'name' => $objUtil->checkGetKey('object')
                ), 1, "A", $showPartOfs);
            }
        }
    } elseif ($objUtil->checkGetKey('source') == 'add_object20') { 	// ========================== from quickpick page
        $catandindex = $objCatalog->checkObject($objUtil->checkRequestKey('catalog') . ' ' . $objUtil->checkRequestKey('number'));
        $firstspace = strpos($catandindex, ' ', 0);
        if ($firstspace !== false) {
            $thenewcatalog0 = trim(substr($catandindex, 0, $firstspace));
            $theindex = trim(substr($catandindex, $firstspace + 1));
        } else {
            $thenewcatalog0 = $catandindex;
            $theindex = '';
        }
        $_REQUEST ['catalog'] = $thenewcatalog0;
        $_REQUEST ['number'] = $theindex;
        $_SESSION ['QobjParams'] = array(
                'source' => 'add_object20'
        );
        $ra = (abs($objUtil->checkRequestKey('RAhours')) + ($objUtil->checkRequestKey('RAminutes') / 60) + ($objUtil->checkRequestKey('RAseconds') / 3600));
        $decl = ((substr(trim($objUtil->checkRequestKey('DeclDegrees')), 0, 1) == '-') ? -1 : 1) * (abs($objUtil->checkRequestKey('DeclDegrees')) + ($objUtil->checkRequestKey('DeclMinutes') / 60) + ($objUtil->checkRequestKey('DeclSeconds') / 3600));
        $_SESSION ['Qobj'] = $objObject->getSeenObjectDetails($objObject->getNearbyObjects("", 60, $ra, $decl));
    } 	// ========================================= no search specified
    else {
        $_SESSION ['QobjParams'] = array();
        $_SESSION ['Qobj'] = array();
    }
}

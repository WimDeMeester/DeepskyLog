<?php

// view_object.php
// view all information of one object
if ((!isset($inIndex)) || (!$inIndex)) {
    include '../../redirect.php';
} elseif (!($object = $objUtil->checkGetKey('object'))) {
    throw new Exception('To implement');
} else {
    view_object();
}
function showButtons($theLocation, $viewobjectdetails, $viewobjectephemerides, $viewobjectobjectsnearby, $viewobjectobservations)
{
    global $baseURL, $object, $objLocation, $objObject, $objPresentations, $objUtil;
    $object_ss = stripslashes($object);
    $seen = $objObject->getSeen($object);
    $content1 = '<a href="'.$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectextrainfo=hidden'.'" >-</a>';
    $content1 .= '&nbsp;&nbsp;&nbsp;&nbsp;';
    $content1 .= '<input type="button" class="btn btn-success" value=">"
                   title="'._('Only object details')
        .'&nbsp;-&nbsp;'.$object_ss.'&nbsp;-&nbsp;'._('Seen').'&nbsp;:&nbsp;'.$seen."\"
	               onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectdetails=show&amp;viewobjectephemerides=hidden&amp;viewobjectobjectsnearby=hidden&amp;viewobjectobservations=hidden'."';\"/>";
    if ($viewobjectdetails == 'hidden') {
        $content1 .= '<input type="button" class="btn" value="+ '
            ._('Object details').'"
	               title="'._('Object details').'&nbsp;-&nbsp;'.$object_ss.'&nbsp;-&nbsp;'._('Seen').'&nbsp;:&nbsp;'.$seen."\"
	               onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectdetails=show'."';\"/>";
    } else {
        $content1 .= '<input type="button" class="btn" value="- '
            ._('Object details').'"
	               title="'._('Object details').'&nbsp;-&nbsp;'.$object_ss.'&nbsp;-&nbsp;'._('Seen').'&nbsp;:&nbsp;'.$seen."\"
	               onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectdetails=hidden'."';\"/>";
    }
    if ($theLocation) {
        $content1 .= '&nbsp;'.'&nbsp;'.'&nbsp;';
        $content1 .= '<input type="button" class="btn btn-success" value=">"
                       title="'._('Only object ephemerides')
            .'&nbsp;-&nbsp;'.$object_ss.'&nbsp;-&nbsp;'._('Seen').'&nbsp;:&nbsp;'.$seen."\"
		               onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectdetails=hidden&amp;viewobjectephemerides=show&amp;viewobjectobjectsnearby=hidden&amp;viewobjectobservations=hidden'."';\"/>";
        if ($viewobjectephemerides == 'hidden') {
            $content1 .= '<input type="button" class="btn" value="+ '
                ._('Object ephemerides').'"
                       title="'
                .sprintf(
                    _('Ephemerides for %s in %s'),
                    $object_ss,
                    $objLocation->getLocationPropertyFromId($theLocation, 'name')
                )
                ."\"
		               onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectephemerides=show'."';\"/>";
        } else {
            $content1 .= '<input type="button" class="btn" value="- '
                ._('Object ephemerides').'"
                       title="'
                .sprintf(
                    _('Ephemerides for %s in %s'),
                    $object_ss,
                    $objLocation->getLocationPropertyFromId($theLocation, 'name')
                )."\"
		               onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectephemerides=hidden'."';\"/>";
        }
    }
    $content1 .= '&nbsp;'.'&nbsp;'.'&nbsp;';
    $content1 .= '<input type="button" class="btn btn-success" value=">"
                   title="'._('Only nearby objects')
        .'&nbsp;-&nbsp;'.$object_ss.'&nbsp;-&nbsp;'._('Seen').'&nbsp;:&nbsp;'.$seen."\"
	               onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectdetails=hidden&amp;viewobjectephemerides=hidden&amp;viewobjectobjectsnearby=show&amp;viewobjectobservations=hidden'."';\"/>";
    if ($viewobjectobjectsnearby == 'hidden') {
        $content1 .= '<input type="button" class="btn" value="+ '
            ._('Nearby objects')."\" onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectobjectsnearby=show'."';\"/>";
    } else {
        $content1 .= '<input type="button" class="btn" value="- '
            ._('Nearby objects')."\" onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectobjectsnearby=hidden'."';\"/>";
    }
    $content1 .= '&nbsp;'.'&nbsp;'.'&nbsp;';
    $content1 .= '<input type="button" class="btn btn-success" value=">"
                   title="'._('Only object observations')
        .'&nbsp;-&nbsp;'.$object_ss.'&nbsp;-&nbsp;'._('Seen').'&nbsp;:&nbsp;'.$seen."\"
	               onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectdetails=hidden&amp;viewobjectephemerides=hidden&amp;viewobjectobjectsnearby=hidden&amp;viewobjectobservations=show'."';\"/>";
    if ($viewobjectobservations == 'hidden') {
        $content1 .= '<input type="button" class="btn" value="+ '._('Object observations')."\" onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectobservations=show'."';\"/>";
    } else {
        $content1 .= '<input type="button" class="btn" value="- '._('Object observations')."\" onclick=\"location='".$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectobservations=hidden'."';\"/>";
    }
    $content2 = '<a href="'.$baseURL.'index.php?indexAction=atlaspage&amp;object='.urlencode($object).'"><input type="button" class="btn pull-right btn-success" value="'._('Interactive Atlas').'"/></a>';

    echo $content1;
    echo $content2;
    echo '<hr />';
}
function showObjectDetails($object_ss)
{
    global $baseURL, $object, $objObject, $objPresentations, $objUtil,$objDatabase;
    $seen = $objObject->getDSOseenLink($object);
    echo '<h4>'.'<a href="'.$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectdetails=hidden'.'" title="'
        ._('Hide the object details').'">-</a> '._('Object details').'&nbsp;-&nbsp;'.$object_ss.'&nbsp;-&nbsp;'._('Seen').'&nbsp;:&nbsp;'.$seen.'</h4>';
    if (array_key_exists('admin', $_SESSION) && $_SESSION['admin'] == 'yes') {
        $obsCnt = $objDatabase->selectSingleValue('SELECT COUNT(observations.id) As ObsCnt FROM observations WHERE objectname = "'.$object_ss.'"', 'ObsCnt');

        echo '<div class="pull-right">';
        if ($obsCnt == 0) {
            echo '<button class="btn btn-danger pull-right" data-toggle="modal" data-target="#removeObject">'._('Remove Object').'</button>';

            echo '<div class="modal fade" id="removeObject" tabindex="-1">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="myModalLabel">'._('Remove Object').'</h4>
                                    </div>
                                    <div class="modal-body">'.

                                        sprintf(_('Do you really want to delete %s?'), '<strong>'.$object_ss.'</strong><br />').

                                        sprintf(_(
                                            'There are no observations of %s, so removing %s should be relatively safe.'),
                                                    '<strong>'.$object_ss.'</strong>',
                                                    '<strong>'.$object_ss.'</strong>').
                                    '<form action="'.$baseURL.'index.php?indexAction=deleteObject" method="post">
                                        			 <input type="hidden" name="indexAction" value="deleteObject" />
                                        			 <input type="hidden" name="object" value="'.$object_ss.'" />
                                        			</div>
                                        			<div class="modal-footer">
                                        			<button type="button" class="btn btn-success" data-dismiss="modal">'._('Keep Object').'</button>
                                        			<input class="btn btn-danger" type="submit" name="objectToDelete" value="'._('Remove Object').'!" />
                                         </form>
									</div>
								 </div><!-- /.modal-content -->
								</div><!-- /.modal-dialog -->
							 </div><!-- /.modal -->';

                            } else {
            echo '&nbsp;&nbsp;&nbsp;<button class="btn btn-danger pull-right" data-toggle="modal" data-target="#forceRemoveObject">'._('Force Remove Object').'</button>';

            echo '<div class="modal fade" id="forceRemoveObject" tabindex="-1">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
										<h4 class="modal-title" id="myModalLabel">'._('Force Remove Object').'</h4>
									</div>
									<div class="modal-body">'.
                                    sprintf('Do you really want to delete %s?', '<strong>'.$object_ss.'</strong><br />').
                                    sprintf(', which has %s observations?', '<strong>'.
                                     $obsCnt.'</strong>').'
									 <br />'._('All the observations will also be removed!').'
				            <form action="'.$baseURL.'index.php?indexAction=deleteObject" method="post">
				             <input type="hidden" name="indexAction" value="deleteObject" />
										 <input type="hidden" name="object" value="'.$object_ss.'" />
				            </div>
				            <div class="modal-footer">
				            <button type="button" class="btn btn-success" data-dismiss="modal">'._('Keep Object').'</button>
				            <input class="btn btn-danger" type="submit" name="objectToDelete" value="'._('Force Remove Object').'!" />
						   </form>
				          </div>
				         </div><!-- /.modal-content -->
				        </div><!-- /.modal-dialog -->
				       </div><!-- /.modal -->';
        }
        echo '&nbsp;<button class="btn btn-warning">'._('Save').'</button>&nbsp;</div>';

        echo '<br />';
    }
    echo '<hr />';
    $objObject->showObject($object);
}
function showObjectsNearby()
{
    global $baseURL, $FF, $step, $objObject, $objPresentations, $objUtil;
    $link = $baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj';

    $content1 = '<h4>';
    $content1 .= '<a href="'.$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectobjectsnearby=hidden'.'" title="'
        ._('Hide the nearby objects').'">-</a> ';
    $content1 .= $_GET['object'];
    if (count($_SESSION['Qobj']) > 2) {
        $content1 .= ' '._(' and ').' '.(count($_SESSION['Qobj']) - 1).' '._('nearby objects');
    } elseif (count($_SESSION['Qobj']) > 1) {
        $content1 .= ' '._(' and ').' '.(count($_SESSION['Qobj']) - 1).' '._('nearby object');
    } else {
        $content1 .= ' '._(' - there are no other objects within the specified distance');
    }
    $content1 .= '</h4>';
    echo $content1;
    $content1 = '<span class="pull-right"><form class="form-inline" action="'.$link.'" method="get"><div>';
    $content1 .= _('up to about ').':&nbsp;';
    $content1 .= '<select class="form-control" name="zoom" onchange="submit();">';
    if ($objUtil->checkGetKey('zoom', 30) == '180') {
        $content1 .= ('<option selected="selected" value="180">3x3&deg;</option>');
    } else {
        $content1 .= ('<option value="180">3x3&deg;</option>');
    }
    if ($objUtil->checkGetKey('zoom', 30) == '120') {
        $content1 .= ('<option selected="selected" value="120">2x2&deg;</option>');
    } else {
        $content1 .= ('<option value="120">2x2&deg;</option>');
    }
    if ($objUtil->checkGetKey('zoom', 30) == '60') {
        $content1 .= ('<option selected="selected" value="60">1x1&deg;</option>');
    } else {
        $content1 .= ('<option value="60">1x1&deg;</option>');
    }
    if ($objUtil->checkGetKey('zoom', 30) == '30') {
        $content1 .= ("<option selected=\"selected\" value=\"30\">30x30'</option>");
    } else {
        $content1 .= ("<option value=\"30\">30x30'</option>");
    }
    if ($objUtil->checkGetKey('zoom', 30) == '15') {
        $content1 .= ("<option selected=\"selected\" value=\"15\">15x15'</option>");
    } else {
        $content1 .= ("<option value=\"15\">15x15'</option>");
    }
    if ($objUtil->checkGetKey('zoom', 30) == '10') {
        $content1 .= ("<option selected=\"selected\" value=\"10\">10x10'</option>");
    } else {
        $content1 .= ("<option value=\"10\">10x10'</option>");
    }
    if ($objUtil->checkGetKey('zoom', 30) == '5') {
        $content1 .= ("<option selected=\"selected\" value=\"5\">5x5'</option>");
    } else {
        $content1 .= ("<option value=\"5\">5x5'</option>");
    }
    $content1 .= '</select>';
    $content1 .= '<input type="hidden" name="object" value="'.$_GET['object'].'" /> ';
    $content1 .= '<input type="hidden" name="indexAction" value="detail_object" /> ';
    $content1 .= '</div></form></span>';
    echo $content1;
    echo '<br /><hr />';
    $objObject->showObjects($link, $_GET['object'], 0, '', 'view_object');
    echo '<hr />';
}
function showObjectEphemerides($theLocation)
{
    global $baseURL, $object, $theMonth, $theDay, $objLocation, $objObject, $objPresentations, $objUtil;
    $longitude = 1.0 * $objLocation->getLocationPropertyFromId($theLocation, 'longitude');
    $latitude = 1.0 * $objLocation->getLocationPropertyFromId($theLocation, 'latitude');
    $timezone = $objLocation->getLocationPropertyFromId($theLocation, 'timezone');
    $dateTimeZone = new DateTimeZone($timezone);

    $theMonth = $_SESSION['globalMonth'];
    $theDay = $_SESSION['globalDay'];

    echo '<div id="ephemeridesdiv">';
    for ($i = 1; $i < 13; ++$i) {
        $datestr = sprintf('%02d', $i).'/'.sprintf('%02d', 1).'/'.$_SESSION['globalYear'];
        $dateTime = new DateTime($datestr, $dateTimeZone);
        $timedifference = $dateTimeZone->getOffset($dateTime);
        if (strncmp($timezone, 'Etc/GMT', 7) == 0) {
            $timedifference = -$timedifference;
        }
        date_default_timezone_set('UTC');
        $theTimeDifference1[$i] = $timedifference;
        $theEphemerides1[$i] = $objObject->getEphemerides($object, 1, $i, 2010);
        $theNightEphemerides1[$i] = date_sun_info(strtotime('2010'.'-'.$i.'-'.'1'), $latitude, $longitude);
        $datestr = sprintf('%02d', $i).'/'.sprintf('%02d', 1).'/'.$_SESSION['globalYear'];
        $dateTime = new DateTime($datestr, $dateTimeZone);
        $timedifference = $dateTimeZone->getOffset($dateTime);
        if (strncmp($timezone, 'Etc/GMT', 7) == 0) {
            $timedifference = -$timedifference;
        }
        date_default_timezone_set('UTC');
        $theTimeDifference15[$i] = $timedifference;
        $theEphemerides15[$i] = $objObject->getEphemerides($object, 15, $i, 2010);
        $theNightEphemerides15[$i] = date_sun_info(strtotime('2010'.'-'.$i.'-'.'15'), $latitude, $longitude);
    }
    echo '<h4>'.'<a href="'.$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectephemerides=hidden'.'" title="'._('Hide the ephemerides').'">-</a> '
        .sprintf(
            _('Ephemerides for %s in %s'),
            stripslashes($object),
            $objLocation->getLocationPropertyFromId($theLocation, 'name')
        ).'</h4>';
    echo '<hr />';
    echo '<table class="table table-condensed">';
    echo '<tr class="type10">';
    echo '<th class="right">'._('Month').' > </th>';
    for ($i = 1; $i < 13; ++$i) {
        $background1 = '';
        $background15 = '';
        if ((($i == $theMonth) && ($theDay < 8)) || ((($i - 1) == $theMonth) && ($theDay > 22))) {
            $background1 = 'class="current"';
        }

        if (($i == $theMonth) && ($theDay >= 8) && ($theDay <= 22)) {
            $background15 = 'class="current"';
        }

        echo '<th '.$background1.'>&nbsp;</th><th class="centered" '.$background15.'>'.$i.'</th>';
    }
    $background1 = '';
    if ((12 == $theMonth) && ($theDay > 22)) {
        $background1 = ' style="background-color:#FFAAAA" color: red';
    }
    echo '<th'.$background1.'>&nbsp;</th>';
    echo '</tr>';
    echo '<tr class="type20">';
    echo '<td class="centered">'._('Max Alt').'</td>';
    for ($i = 1; $i < 13; ++$i) {
        $colorclass = '';
        if ($i == 1) {
            if (($theEphemerides1[$i]['altitude'] != '-') && ($theEphemerides15[$i]['altitude'] != '-') && (($theEphemerides1[$i]['altitude'] == $theEphemerides15[$i]['altitude']) || ($theEphemerides1[$i]['altitude'] == $theEphemerides15[12]['altitude']))) {
                $colorclass = 'ephemeridesgreen';
            }
        } elseif (($theEphemerides1[$i]['altitude'] != '-') && ($theEphemerides15[$i]['altitude'] != '-') && (($theEphemerides1[$i]['altitude'] == $theEphemerides15[$i]['altitude']) || ($theEphemerides1[$i]['altitude'] == $theEphemerides15[$i - 1]['altitude']))) {
            $colorclass = 'ephemeridesgreen';
        }
        echo '<td class="centered '.$colorclass.'">'.$theEphemerides1[$i]['altitude'].'</td>';
        $colorclass = '';
        if ($i == 12) {
            if (($theEphemerides1[$i]['altitude'] != '-') && ($theEphemerides15[$i]['altitude'] != '-') && (($theEphemerides15[$i]['altitude'] == $theEphemerides1[$i]['altitude']) || ($theEphemerides15[$i]['altitude'] == $theEphemerides1[1]['altitude']))) {
                $colorclass = 'ephemeridesgreen';
            }
        } elseif (($theEphemerides15[$i]['altitude'] != '-') && ($theEphemerides15[$i]['altitude'] != '-') && (($theEphemerides15[$i]['altitude'] == $theEphemerides1[$i]['altitude']) || ($theEphemerides15[$i]['altitude'] == $theEphemerides1[$i + 1]['altitude']))) {
            $colorclass = 'ephemeridesgreen';
        }
        echo '<td class="centered '.$colorclass.'">'.$theEphemerides15[$i]['altitude'].'</td>';
    }
    $colorclass = '';
    if (($theEphemerides1[1]['altitude'] != '-') && ($theEphemerides15[1]['altitude'] != '-') && (($theEphemerides1[1]['altitude'] == $theEphemerides15[1]['altitude']) || ($theEphemerides1[1]['altitude'] == $theEphemerides15[12]['altitude']))) {
        $colorclass = 'ephemeridesgreen';
    }
    echo '<td class="centered '.$colorclass.'">'.$theEphemerides1[1]['altitude'].'</td>';
    echo '</tr>';
    echo '<tr class="type10">';
    echo '<td class="centered">'._('Transit').'</td>';
    for ($i = 1; $i < 13; ++$i) {
        $colorclass = '';
        if ((date('H:i', $theNightEphemerides1[$i]['astronomical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinuteBetweenOthers($theEphemerides1[$i]['transit'], date('H:i', $theNightEphemerides1[$i]['astronomical_twilight_end'] + $theTimeDifference1[$i]), date('H:i', $theNightEphemerides1[$i]['astronomical_twilight_begin'] + $theTimeDifference1[$i]))) {
            $colorclass = 'ephemeridesgreen';
        } elseif ((date('H:i', $theNightEphemerides1[$i]['nautical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinuteBetweenOthers($theEphemerides1[$i]['transit'], date('H:i', $theNightEphemerides1[$i]['nautical_twilight_end'] + $theTimeDifference1[$i]), date('H:i', $theNightEphemerides1[$i]['nautical_twilight_begin'] + $theTimeDifference1[$i]))) {
            $colorclass = 'ephemeridesyellow';
        }
        echo '<td class="centered '.$colorclass.'">'.$theEphemerides1[$i]['transit'].'</td>';
        $colorclass = '';
        if ((date('H:i', $theNightEphemerides15[$i]['nautical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinuteBetweenOthers($theEphemerides15[$i]['transit'], date('H:i', $theNightEphemerides15[$i]['astronomical_twilight_end'] + $theTimeDifference15[$i]), date('H:i', $theNightEphemerides15[$i]['astronomical_twilight_begin'] + $theTimeDifference15[$i]))) {
            $colorclass = 'ephemeridesgreen';
        } elseif ((date('H:i', $theNightEphemerides15[$i]['nautical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinuteBetweenOthers($theEphemerides15[$i]['transit'], date('H:i', $theNightEphemerides15[$i]['nautical_twilight_end'] + $theTimeDifference15[$i]), date('H:i', $theNightEphemerides15[$i]['nautical_twilight_begin'] + $theTimeDifference15[$i]))) {
            $colorclass = 'ephemeridesyellow';
        }
        echo '<td class="centered '.$colorclass.'">'.$theEphemerides15[$i]['transit'].'</td>';
    }
    $colorclass = '';
    if ((date('H:i', $theNightEphemerides1[1]['astronomical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinuteBetweenOthers($theEphemerides1[1]['transit'], date('H:i', $theNightEphemerides1[1]['astronomical_twilight_end'] + $theTimeDifference1[1]), date('H:i', $theNightEphemerides1[1]['astronomical_twilight_begin'] + $theTimeDifference1[1]))) {
        $colorclass = 'ephemeridesgreen';
    } elseif ((date('H:i', $theNightEphemerides1[1]['nautical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinuteBetweenOthers($theEphemerides1[1]['transit'], date('H:i', $theNightEphemerides1[1]['nautical_twilight_end'] + $theTimeDifference1[1]), date('H:i', $theNightEphemerides1[1]['nautical_twilight_begin'] + $theTimeDifference1[1]))) {
        $colorclass = 'ephemeridesyellow';
    }
    echo '<td class="centered '.$colorclass.'">'.$theEphemerides1[1]['transit'].'</td>';
    echo '</tr>';
    echo '<tr class="type20">';
    echo '<td class="centered">'._('Astronomical night').'</td>';
    for ($i = 1; $i < 13; ++$i) {
        echo '<td class="centered">'.((date('H:i', $theNightEphemerides1[$i]['astronomical_twilight_end']) != '00:00') ? date('H:i', $theNightEphemerides1[$i]['astronomical_twilight_end'] + $theTimeDifference1[$i]).'<br />-<br />'.date('H:i', $theNightEphemerides1[$i]['astronomical_twilight_begin'] + $theTimeDifference1[$i]) : '-').'</td>';
        echo '<td class="centered">'.((date('H:i', $theNightEphemerides15[$i]['astronomical_twilight_end']) != '00:00') ? date('H:i', $theNightEphemerides15[$i]['astronomical_twilight_end'] + $theTimeDifference15[$i]).'<br />-<br />'.date('H:i', $theNightEphemerides15[$i]['astronomical_twilight_begin'] + $theTimeDifference15[$i]) : '-').'</td>';
    }
    echo '<td class="centered">'.((date('H:i', $theNightEphemerides1[1]['astronomical_twilight_end']) != '00:00') ? date('H:i', $theNightEphemerides1[1]['astronomical_twilight_end'] + $theTimeDifference1[1]).'<br />-<br />'.date('H:i', $theNightEphemerides1[1]['astronomical_twilight_begin'] + $theTimeDifference1[1]) : '-').'</td>';
    echo '</tr>';
    echo '<tr class="type10">';
    echo '<td class="centered">'._('Nautical night').'</td>';
    for ($i = 1; $i < 13; ++$i) {
        echo '<td class="centered">'.((date('H:i', $theNightEphemerides1[$i]['nautical_twilight_end']) != '00:00') ? date('H:i', $theNightEphemerides1[$i]['nautical_twilight_end'] + $theTimeDifference1[$i]).'<br />-<br />'.date('H:i', $theNightEphemerides1[$i]['nautical_twilight_begin'] + $theTimeDifference1[$i]) : '-').'</td>';
        echo '<td class="centered">'.((date('H:i', $theNightEphemerides15[$i]['nautical_twilight_end']) != '00:00') ? date('H:i', $theNightEphemerides15[$i]['nautical_twilight_end'] + $theTimeDifference15[$i]).'<br />-<br />'.date('H:i', $theNightEphemerides15[$i]['nautical_twilight_begin'] + $theTimeDifference15[$i]) : '-').'</td>';
    }
    echo '<td class="centered">'.((date('H:i', $theNightEphemerides1[1]['nautical_twilight_end']) != '00:00') ? date('H:i', $theNightEphemerides1[1]['nautical_twilight_end'] + $theTimeDifference1[1]).'<br />-<br />'.date('H:i', $theNightEphemerides1[1]['nautical_twilight_begin'] + $theTimeDifference1[1]) : '-').'</td>';
    echo '</tr>';
    echo '<tr class="type20">';
    echo '<td class="centered">'._('Object rise<br />-<br />set').'</td>';
    for ($i = 1; $i < 13; ++$i) {
        $colorclass = '';
        if ($theEphemerides1[$i]['rise'] == '-') {
            if ((date('H:i', $theNightEphemerides1[$i]['astronomical_twilight_end']) != '00:00')) {
                $colorclass = 'ephemeridesgreen';
            } elseif ((date('H:i', $theNightEphemerides1[$i]['nautical_twilight_end']) != '00:00')) {
                $colorclass = 'ephemeridesyellow';
            }
        }
        if ((date('H:i', $theNightEphemerides1[$i]['astronomical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinutePeriodOverlap($theEphemerides1[$i]['rise'], $theEphemerides1[$i]['set'], date('H:i', $theNightEphemerides1[$i]['astronomical_twilight_end'] + $theTimeDifference1[$i]), date('H:i', $theNightEphemerides1[$i]['astronomical_twilight_begin'] + $theTimeDifference1[$i]))) {
            $colorclass = 'ephemeridesgreen';
        } elseif ((date('H:i', $theNightEphemerides1[$i]['nautical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinutePeriodOverlap($theEphemerides1[$i]['rise'], $theEphemerides1[$i]['set'], date('H:i', $theNightEphemerides1[$i]['nautical_twilight_end'] + $theTimeDifference1[$i]), date('H:i', $theNightEphemerides1[$i]['nautical_twilight_begin'] + $theTimeDifference1[$i]))) {
            $colorclass = 'ephemeridesyellow';
        }
        echo '<td class="centered '.$colorclass.'">'.($theEphemerides1[$i]['rise'] == '-' ? '-' : $theEphemerides1[$i]['rise'].'<br />-<br />'.$theEphemerides1[$i]['set']).'</td>';
        $colorclass = '';
        if ($theEphemerides15[$i]['rise'] == '-') {
            if ((date('H:i', $theNightEphemerides15[$i]['astronomical_twilight_end']) != '00:00')) {
                $colorclass = 'ephemeridesgreen';
            } elseif ((date('H:i', $theNightEphemerides15[$i]['nautical_twilight_end']) != '00:00')) {
                $colorclass = 'ephemeridesyellow';
            }
        } elseif ((date('H:i', $theNightEphemerides15[$i]['astronomical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinutePeriodOverlap($theEphemerides15[$i]['rise'], $theEphemerides15[$i]['set'], date('H:i', $theNightEphemerides15[$i]['astronomical_twilight_end'] + $theTimeDifference15[$i]), date('H:i', $theNightEphemerides15[$i]['astronomical_twilight_begin'] + $theTimeDifference15[$i]))) {
            $colorclass = 'ephemeridesgreen';
        } elseif ((date('H:i', $theNightEphemerides15[$i]['nautical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinutePeriodOverlap($theEphemerides15[$i]['rise'], $theEphemerides15[$i]['set'], date('H:i', $theNightEphemerides15[$i]['nautical_twilight_end'] + $theTimeDifference15[$i]), date('H:i', $theNightEphemerides15[$i]['nautical_twilight_begin'] + $theTimeDifference15[$i]))) {
            $colorclass = 'ephemeridesyellow';
        }
        echo '<td class="centered '.$colorclass.'">'.($theEphemerides15[$i]['rise'] == '-' ? '-' : $theEphemerides15[$i]['rise'].'<br />-<br />'.$theEphemerides15[$i]['set']).'</td>';
    }
    $colorclass = '';
    if ($theEphemerides1[1]['rise'] == '-') {
        if ((date('H:i', $theNightEphemerides1[1]['astronomical_twilight_end']) != '00:00')) {
            $colorclass = 'ephemeridesgreen';
        } elseif ((date('H:i', $theNightEphemerides1[1]['nautical_twilight_end']) != '00:00')) {
            $colorclass = 'ephemeridesyellow';
        }
    } elseif ((date('H:i', $theNightEphemerides1[1]['astronomical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinutePeriodOverlap($theEphemerides1[1]['rise'], $theEphemerides1[1]['set'], date('H:i', $theNightEphemerides1[1]['astronomical_twilight_end'] + $theTimeDifference1[1]), date('H:i', $theNightEphemerides1[1]['astronomical_twilight_begin'] + $theTimeDifference1[1]))) {
        $colorclass = 'ephemeridesgreen';
    } elseif ((date('H:i', $theNightEphemerides1[1]['nautical_twilight_end']) != '00:00') && $objUtil->checkNightHourMinutePeriodOverlap($theEphemerides1[1]['rise'], $theEphemerides1[1]['set'], date('H:i', $theNightEphemerides1[1]['nautical_twilight_end'] + $theTimeDifference1[1]), date('H:i', $theNightEphemerides1[1]['nautical_twilight_begin'] + $theTimeDifference1[1]))) {
        $colorclass = 'ephemeridesyellow';
    }
    echo '<td class="centered '.$colorclass.'">'.($theEphemerides1[1]['rise'] == '-' ? '-' : $theEphemerides1[1]['rise'].'<br />-<br />'.$theEphemerides1[1]['set']).'</td>';
    echo '</tr>';
    echo '</table>';
    echo '<hr />';
    echo '</div>';
}
function showObjectImage($imagesize)
{
    global $object, $objPresentations, $objUtil;
    echo '<h4>'._('DSS image').' - '.$object.'&nbsp;('.$imagesize."'&nbsp;x&nbsp;".$imagesize."')</h4>";
    $imagelink = 'http://archive.stsci.edu/cgi-bin/dss_search?'.'v=poss2ukstu_red&amp;r='.urlencode($objUtil->checkRequestKey('raDSS')).'.0&amp;d='.urlencode($objUtil->checkRequestKey('declDSS')).'&amp;e=J2000&amp;h='.$imagesize.'.0&amp;w='.$imagesize.'&amp;f=gif&amp;c=none&amp;fov=NONE&amp;v3=';
    echo '<p class="centered DSSImage" >
	       <a href="'.$imagelink.'" data-lightbox="image-1" data-title="">
	       <img class="DSSImage" src="'.$imagelink.'" alt="'.$object.'" ></img>
	       </a></p>';
    echo '<p>&copy;&nbsp;<a href="http://archive.stsci.edu/dss/index.html">STScI Digitized Sky Survey</a></p>';
    echo '<hr />';
}
function showObjectObservations()
{
    global $baseURL, $FF, $object, $loggedUser, $min, $step, $objObservation, $objPresentations, $objUtil;

    $link = $baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj';
    $link2 = $link;
    $link3 = $link;
    $content3 = '<h4>';
    $min = 0;
    $max = 0;
    $pageleft = '';
    $pageright = '';
    $pagemax = '';
    if (count($_SESSION['Qobs']) == 0) {
        echo '<h4>'._('Sorry, no observations found!').(($objUtil->checkGetKey('myLanguages')) ? (' ('._('selected languages').')') : (' ('._('all languages').')')).'</h4>';
        if ($objUtil->checkGetKey('myLanguages')) {
            echo '<p>'.'<a href="'.$link2.'">'._('Look again, using all languages.').'</a>&nbsp;</p>';
        }
        echo '<p>'.'<a href="'.$baseURL.'index.php?indexAction=query_observations">'._('Set up a detailed search.').'</a>'.'</p>';
    } else {
        $theDate = date('Ymd', strtotime('-1 year'));
        $content1 = '<h4>'.'<a href="'.$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectobservations=hidden'.'" title="'
            ._('Hide the observations').'">-</a> ';
        if (array_key_exists('minyear', $_GET) && ($_GET['minyear'] == substr($theDate, 0, 4)) && array_key_exists('minmonth', $_GET) && ($_GET['minmonth'] == substr($theDate, 4, 2)) && array_key_exists('minday', $_GET) && ($_GET['minday'] == substr($theDate, 6, 2))) {
            $content1 .= _("Overview of last year's observations");
        } elseif ($object) {
            $content1 .= sprintf(_('Overview of all observations of %s'), $object);
        } else {
            $content1 .= _('Overview selected observations');
        }
        $content1 .= '</h4>';
        echo $content1;
    }
    if ($objUtil->checkGetKey('myLanguages')) {
        $content3 .= ' ('._('selected languages only').')';
        $link .= '&amp;myLanguages=true';
        $link2 .= '&amp;myLanguages=true';
    } else {
        $content3 .= ' ('._('all languages').')';
    }
    $content3 .= '</h4>';
    echo $content3;
    $content5 = '<span class="pull-right">';
    if (($objUtil->checkSessionKey('lco', '') != 'L')) {
        $content5 .= '&nbsp;&nbsp;<a class="btn btn-success" href="'.$link.'&amp;lco=L" title="'.
            _('Overview with the basic information on one line per observation').'">'._('List').'</a>';
    }
    if (($objUtil->checkSessionKey('lco', '') != 'C')) {
        $content5 .= '&nbsp;&nbsp;<a class="btn btn-success" href="'.$link.'&amp;lco=C" title="'.
            _('Overview with an information line and a description per observation').'">'._('Compact').'</a>';
    }
    if ($loggedUser && ($objUtil->checkSessionKey('lco', '') != 'O')) {
        $content5 .= '&nbsp;&nbsp;<a class="btn btn-success" href="'.$link.'&amp;lco=O" title="'.
            _('Overview with an information line, a description and your last observation').'">'._('Compare').'</a>';
    }
    if ($loggedUser && $objUtil->checkSessionKey('lco', '') == 'L') {
        $toAdd = '&nbsp;&nbsp;'.'<a class="btn btn-success" href="'.$link.'&amp;noOwnColor=no">'
            ._('Highlight own observations').'</a>';
        if ($objUtil->checkGetKey('noOwnColor')) {
            if ($objUtil->checkGetKey('noOwnColor') == 'no') {
                $toAdd = '&nbsp;&nbsp;'.'<a class="btn btn-success" href="'.$link.'&amp;noOwnColor=yes">'
                    ._("Don't highlight own observations").'</a>';
            }
        }
        $content5 .= $toAdd;
    }
    $content5 .= '</span>';
    if ($objUtil->checkGetKey('myLanguages')) {
        $content6 = '<a class="btn btn-success" href="'.$link3.'">'._('Show all languages').'</a>';
    } elseif ($loggedUser) {
        $content6 = '<a class="btn btn-success" href="'.$link3.'&amp;myLanguages=true">'._('Only show the observations from my profile languages').'</a>';
    } else {
        $content6 = '<a class="btn btn-success" href="'.$link3.'&amp;myLanguages=true">'._('Only show observations in English').'</a>';
    }
    echo $content5;
    echo $content6;
    echo '<hr />';

    $objObservation->showListObservation($link, $_SESSION['lco']);
    echo '<hr />';
    if ($_SESSION['lco'] == 'O') {
        echo _('(*) All Observations(AO), My observations(MO), my Last observations(LO) of this object');
        echo '<br /><br />';
    }
    $content1 = '<a class="btn btn-primary" href="'.$baseURL.'index.php?indexAction=query_objects&amp;source=observation_query">'._('Filter objects').'</a>  ';
    $content1 .= $objPresentations->promptWithLinkText(
        _('Please enter a title'),
        _('DeepskyLog observations'),
        $baseURL.'observations.pdf.php?SID=Qobs',
        _('pdf')
    );
    $content1 .= '  ';
    $content1 .= '<a class="btn btn-primary" href="'.$baseURL.'observations.csv.php" rel="external"><span class="glyphicon glyphicon-download"></span> '._('CSV').'</a>  ';
    $content1 .= '<a class="btn btn-primary" href="'.$baseURL.'observations.xml.php" rel="external"><span class="glyphicon glyphicon-download"></span> '.'&lt;OAL&gt;'.'</a>';
    echo $content1;
    echo '<hr />';
}
function showAdminObjectFunctions()
{
    global $baseURL, $object, $DSOcatalogs, $objObject;
    echo '<hr />';
    echo '<form class="form-inline" action="'.$baseURL.'index.php" method="get"><div>';
    echo '<input type="hidden" name="object" value="'.$object.'" />';
    echo '<input type="hidden" name="indexAction" value="detail_object" />';
    echo '<select class="form-control" name="newaction">';
    echo '<option value="">&nbsp;</option>';
    echo '<option value="NewName">'._('NN-New name').'</option>';
    echo '<option value="NewAltName">'._('AN-New Alt Name').'</option>';
    echo '<option value="RemoveAltNameName">'._('RAN-Remove Alt name').'</option>';
    echo '<option value="NewPartOf">'._('PO-New Part Of').'</option>';
    echo '<option value="RemovePartOf">'._('RPO-Remove Part Of').'</option>';
    echo '<option value="RemoveAndReplaceObjectBy">'._('RRO-Remove the object and replace observations of it by object').'</option>';
    echo '<option value="ObjectSetRA">'._('RA-Set RA (Hour Format: 1.50 for 1h30m)').'</option>';
    echo '<option value="ObjectSetDECL">'._("DE-Set Decl (Degree Format: 25.50 for 25°30')").'</option>';
    echo '<option value="ObjectSetCon">'._('CON-Set Constellation (Three letter capital abbrevation!)').'</option>';
    echo '<option value="ObjectSetType">'._('TYP-Set Type (Five capital abbrevation!)').'</option>';
    echo '<option value="ObjectSetMag">'._('MA-Set Magnitude').'</option>';
    echo '<option value="ObjectSetSUBR">'._('SB-Set Surface Brightness').'</option>';
    echo '<option value="ObjectSetDiam1">'._('D1-Set Diameter 1 (arcseconds!)').'</option>';
    echo '<option value="ObjectSetDiam2">'._('D2-Set Diameter 2 (arcseconds!)').'</option>';
    echo '<option value="ObjectSetPA">'._('PA-Set Position Angle').'</option>';
    echo '<option value="ObjectSetDESC">'._('DESC-Edit Description').'</option>';
    echo '</select>';
    echo '<select class="form-control" name="newcatalog">';
    echo '<option value="">&nbsp;</option>';
    foreach ($DSOcatalogs as $key => $value) {
        echo "<option value=\"$value\">".$value.'</option>';
    }
    echo '</select>';
    echo '<input type="text" class="form-control" maxlength="255" name="newnumber" size="40" value=""/>';
    echo '<input type="submit" name="gonew" class="btn btn-success pull-right" value="Go"/><br /><br />';
    echo '<a class="btn btn-success pull-right" href="'.$baseURL.'index.php?indexAction=manage_csv_object">'._('Manage objects from CSV file').'</a><br />';
    echo '</div></form>';
}
function view_object()
{
    global $baseURL, $FF, $link, $link2, $loggedUser, $object, $step, $objLocation, $objObject, $objObservation, $objObserver, $objPresentations, $objUtil;
    echo '<script type="text/javascript" src="'.$baseURL.'lib/javascript/presentation.js"></script>';
    if (array_key_exists('viewobjectextrainfo', $_GET)) {
        $viewobjectextrainfo = $_GET['viewobjectextrainfo'];
    } elseif (array_key_exists('viewobjectextrainfo', $_COOKIE)) {
        $viewobjectextrainfo = $_COOKIE['viewobjectextrainfo'];
    } else {
        $viewobjectextrainfo = 'hidden';
    }
    if (array_key_exists('viewobjectdetails', $_GET)) {
        $viewobjectdetails = $_GET['viewobjectdetails'];
    } elseif (array_key_exists('viewobjectdetails', $_COOKIE)) {
        $viewobjectdetails = $_COOKIE['viewobjectdetails'];
    } else {
        $viewobjectdetails = 'show';
    }
    if (array_key_exists('viewobjectephemerides', $_GET)) {
        $viewobjectephemerides = $_GET['viewobjectephemerides'];
    } elseif (array_key_exists('viewobjectephemerides', $_COOKIE)) {
        $viewobjectephemerides = $_COOKIE['viewobjectephemerides'];
    } else {
        $viewobjectephemerides = 'hidden';
    }
    if (array_key_exists('viewobjectobjectsnearby', $_GET)) {
        $viewobjectobjectsnearby = $_GET['viewobjectobjectsnearby'];
    } elseif (array_key_exists('viewobjectobjectsnearby', $_COOKIE)) {
        $viewobjectobjectsnearby = $_COOKIE['viewobjectobjectsnearby'];
    } else {
        $viewobjectobjectsnearby = 'show';
    }
    if (array_key_exists('viewobjectobservations', $_GET)) {
        $viewobjectobservations = $_GET['viewobjectobservations'];
    } elseif (array_key_exists('viewobjectobservations', $_COOKIE)) {
        $viewobjectobservations = $_COOKIE['viewobjectobservations'];
    } else {
        $viewobjectobservations = 'hidden';
    }
    if (!($theLocation = ($loggedUser ? $objObserver->getObserverProperty($loggedUser, 'stdLocation') : ''))) {
        $viewobjectephemerides = 'hidden';
    }

    echo '<div id="main">';
    // Check if the user is an administrator.
    if (array_key_exists('admin', $_SESSION) && $_SESSION['admin'] == 'yes') {
        showObjectDetails(stripslashes($object));
        showObjectsNearby();
        showAdminObjectFunctions();
    } else {
        if ($viewobjectextrainfo == 'show') {
            showButtons($theLocation, $viewobjectdetails, $viewobjectephemerides, $viewobjectobjectsnearby, $viewobjectobservations);
            echo $objPresentations->getDSSDeepskyLiveLinks1($object);
            echo $objPresentations->getDSSDeepskyLiveLinks2($object);
            echo '<hr />';
        }
        if ($viewobjectextrainfo == 'hidden') {
            $content = '<a href="'.$baseURL.'index.php?indexAction=detail_object&amp;object='.urlencode($_GET['object']).'&amp;zoom='.$objUtil->checkGetKey('zoom', 30).'&amp;SID=Qobj&amp;viewobjectextrainfo=show'.'" >+&nbsp;'
                ._('Show or remove extra info...').'</a>';
            echo $content;
            echo $objPresentations->getDSSDeepskyLiveLinks1($object);
            echo $objPresentations->getDSSDeepskyLiveLinks2($object);
            echo '<hr />';
        }
        if ($viewobjectdetails == 'show') {
            showObjectDetails(stripslashes($object));
        }
        if ($viewobjectephemerides == 'show') {
            showObjectEphemerides($theLocation);
        }
        if ($viewobjectobjectsnearby == 'show') {
            showObjectsNearby();
        }
        if ($imagesize = $objUtil->checkRequestKey('imagesize')) {
            showObjectImage($imagesize);
        }
        if ($viewobjectobservations == 'show') {
            showObjectObservations();
        }
    }
    echo '</div>';
}

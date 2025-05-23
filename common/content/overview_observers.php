<?php

// overview_observers.php
// generates an overview of all observers (admin only)
if ((!isset($inIndex)) || (!$inIndex)) {
    include "../../redirect.php";
} elseif (!$loggedUser) {
    throw new Exception(_("You need to be logged in to change your locations or equipment."));
} elseif ($_SESSION ['admin'] != "yes") {
    throw new Exception(_("You need to be logged in as an administrator to execute these operations."));
} else {
    overview_observers();
}
function overview_observers()
{
    global $baseURL, $step, $objObserver, $objPresentations, $objUtil;
    set_time_limit(60);
    $sort = $objUtil->checkGetKey('sort', 'observers.registrationDate DESC');
    $observersArr = $objObserver->getSortedObserversAdmin($sort);
    $_SESSION ['observersArr'] = $observersArr;
    $_SESSION ['observersArrSort'] = $sort;
    echo "<div id=\"main\">";
    echo "<h4>" . _("Observers overview") . "</h4>";
    echo "<hr />";
    echo "<table class=\"table sort-table table-condensed table-striped table-hover tablesorter custom-popup\">";
    echo "<thead><tr>";
    echo "<th>id</th>";
    echo "<th>" . _("Last Name") . "</th>";
    echo "<th>" . _("First Name") . "</th>";
    echo "<th>Email</th>";
    echo "<th>Reg. Date</th>";
    echo "<th>" . _("Role") . "</th>";
    echo "<th class=\"filter-false columnSelector-disable\" data-sorter=\"false\"></th>";
    echo "<th>" . "Observations" . "</th>";
    echo "<th>" . "comet Observations" . "</th>";
    echo "<th>" . _("Number of instruments") . "</th>";
    echo "<th>" . "list Count" . "</th>";
    echo "<th>" . "max Max" . "</th>";
    echo "</tr></thead>";
    foreach ($observersArr as $key => $value) {
        echo "<tr>";
        echo "<td><a href=\"" . $baseURL . "index.php?indexAction=detail_observer&amp;user=" . urlencode($value ['id']) . "\">" . $value ['id'] . "</a> </td>";
        echo "<td>" . $value ['name'] . "</td>";
        echo "<td>" . $value ['firstname'] . "</td>";
        echo "<td> <a href=\"mailto:" . $value ['email'] . "\"> " . $value ['email'] . " </a> </td>";
        echo "<td>" . $value ['registrationDate'] . " </td>";
        $role = $objObserver->getObserverProperty($value ['id'], 'role', 2);
        if ($role == ROLEADMIN) {
            echo "<td> " . _("Admin") . "</td><td></td>";
        } elseif ($role == ROLEUSER) {
            echo "<td> " . _("User") . "</td>";
            if ($value ['maxMax']) {
                echo "<td class=\"centered\">niet verwijderbaar</td>";
            } else {
                echo "<td class=\"centered\"><a href=\"" . $baseURL . "index.php?indexAction=validate_delete_observer&amp;validateDelete=" . urlencode($value['id']) . "\">" . "Verwijder" . "</a></td>";
            }
        } elseif ($role == ROLECOMETADMIN) {
            echo "<td> " . _("Comet admin") . "</td><td></td>";
        } elseif ($role == ROLEWAITLIST) {
            echo "<td> " . _("Waitlist") . "</td><td class=\"centered\"><a href=\"" . $baseURL . "index.php?indexAction=validate_observer&amp;validate=" . urlencode($value['id']) . "\">" . _("Validate") . "</a> / <a href=\"" . $baseURL . "index.php?indexAction=validate_delete_observer&amp;validateDelete=" . urlencode($value ['id']) . "\">" . "Verwijder" . "</a></td>";
        }
        echo "<td>" . $value ['obsCount'] . " </td>";
        echo "<td>" . $value ['cometobsCount'] . " </td>";
        echo "<td>" . $value ['instrumentCount'] . " </td>";
        echo "<td>" . $value ['listCount'] . " </td>";
        echo "<td>" . $value ['maxMax'] . " </td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<hr />";
    echo "</div>";
    $objUtil->addPager("", 100);
}

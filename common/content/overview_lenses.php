<?php

// overview_lenses.php
// generates an overview of all lenses (admin only)
if ((!isset($inIndex)) || (!$inIndex)) {
    include "../../redirect.php";
} elseif (!$loggedUser) {
    throw new Exception(_("You need to be logged in to change your locations or equipment."));
} elseif ($_SESSION ['admin'] != "yes") {
    throw new Exception(_("You need to be logged in as an administrator to execute these operations."));
} else {
    overview_lenses();
}
function overview_lenses()
{
    global $baseURL, $step, $min, $objLens, $objPresentations, $objUtil;
    set_time_limit(60);
    $sort = $objUtil->checkGetKey('sort', 'name');
    if (!$min) {
        $min = $objUtil->checkGetKey('min', 0);
    }
    $lns = $objLens->getSortedLenses($sort, '%');
    echo "<div id=\"main\">";
    echo "<h4>" . _("Overview Lenses") . "</h4>";
    echo "<hr />";
    echo "<table class=\"table sort-table table-condensed table-striped table-hover tablesorter custom-popup\">";
    echo "<thead><tr>";
    echo "<th>" . _("Name") . "</th>";
    echo "<th>" . _("Factor") . "</th>";
    echo "<th>" . _("Observer") . "</th>";
    echo "<th class=\"filter-false columnSelector-disable\" data-sorter=\"false\"></th>";
    echo "</tr></thead>";
    $count = 0;
    foreach ($lns as $key => $value) {
        $name = stripslashes($objLens->getLensPropertyFromId($value, 'name'));
        $factor = $objLens->getLensPropertyFromId($value, 'factor');
        $observer = $objLens->getLensPropertyFromId($value, 'observer');
        if ($value != "1") {
            echo "<tr>";
            echo "<td><a href=\"" . $baseURL . "index.php?indexAction=adapt_lens&amp;lens=" . urlencode($value) . "\">" . $name . "</a></td>";
            echo "<td>";
            echo $factor;
            echo "</td>";
            echo "<td>";
            echo $observer;
            echo "</td>";
            echo "<td>";
            if (!($objLens->getLensUsedFromId($value))) {
                echo("<a href=\"" . $baseURL . "index.php?indexAction=validate_delete_lens&amp;lensid=" . urlencode($value) . "\">" . _("Delete") . "</a>");
            }
            echo "</td>";
            echo "</tr>";
        }
        $count++;
    }
    echo "</table>";
    echo "<hr />";
    echo "</div>";

    $objUtil->addPager("", $count);
}

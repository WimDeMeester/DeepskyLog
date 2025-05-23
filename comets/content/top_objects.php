<?php

// top_objects.php
// generates an overview of all observed objects and their rank
global $inIndex, $loggedUser, $objUtil;
if ((!isset($inIndex)) || (!$inIndex)) {
    include "../../redirect.php";
} else {
    top_objects();
}
function top_objects()
{
    global $baseURL, $step, $objCometObject, $objCometObservation, $objPresentations, $objUtil;
    echo "<div id=\"main\">";
    $rank = $objCometObservation->getPopularObservations();
    $link = $baseURL . "index.php?indexAction=comets_rank_objects";
    echo "<h4>" . _("Most popular objects") . "</h4>";

    echo "<hr />";
    echo "<table class=\"table sort-tablecometobjects table-condensed table-striped table-hover tablesorter custom-popup\">";
    echo "<thead><tr>";
    echo "<th class=\"filter-false columnSelector-disable\">" . _("Rank") . "</th>";
    echo "<th>" . _("Object") . "</th>";
    echo "<th>" . _("Number of observations") . "</th>";
    echo "</tr></thead>";
    $count = 0;
    foreach ($rank as $key => $value) {
        echo "<tr>
				<td>" . ($count + 1) . "</td>
				<td> <a href=\"" . $baseURL . "index.php?indexAction=comets_detail_object&amp;object=" . urlencode($key) . "\">" . $objCometObject->getName($key) . "</a> </td>";
        echo "<td> $value </td>";
        echo "</tr>";
        $count++;
    }
    echo "</table>";

    $objUtil->addPager("cometobjects", $count);

    echo "<hr />";
    echo "</div>";
}

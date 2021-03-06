<?php
// quickpick.php
// allows the user to quiclky enter the name of an object and search it, its observations or make a new observation
global $loggedUser, $inIndex, $objUtil;

if ((! isset ( $inIndex )) || (! $inIndex))
	include "../../redirect.php";
else
	menu_quickpick ();
function menu_quickpick() {
	global $baseURL, $menuSearch, $loggedUser, $loggedUser;
	echo "<li><form role=\"form\" action=\"" . $baseURL . "index.php\" method=\"get\">";
	reset ( $_GET );
	$link = "";
	foreach ($_GET as $key=>$value)
		if ($key != "menuSearch")
			$link .= "&amp;" . $key . "=" . urlencode ( $value );
	reset ( $_GET );
	echo "<h4>";
	echo _("Quick Search") . "</h4>";
	echo "<input type=\"hidden\" name=\"indexAction\" value=\"quickpick\" />";
	echo "<input type=\"hidden\" name=\"titleobjectaction\" value=\"" . _("Search") . "\" />";
	echo "<input type=\"hidden\" name=\"source\"      value=\"quickpick\" />";
	echo "<input type=\"hidden\" name=\"myLanguages\" value=\"true\" />";
    echo "<input type=\"search\" class=\"form-control\" placeholder=\"" 
        . _("Enter object name") 
        . "\" id=\"quickpickobject\" name=\"object\" title=\"" . _("Give the name of an object (e.g.: 'NGC 7000', 'Mel 20' or even 'Ring Nebula'), or use the wildcard character % (e.g. M %).") . "\" value=\"" . ((array_key_exists ( 'object', $_GET ) && ($_GET ['object'] != '* ')) ? $_GET ['object'] : "") . "\" />";
	echo "<br /><br />";
	echo "<div class=\"form group\"><input class=\"btn btn-default btn-block btn-sm\" type=\"submit\" name=\"searchObjectQuickPickQuickPick\" value=\"" . _("Search Object") . "\" /></div>";
	echo "<div class=\"form group\"><input class=\"btn btn-default btn-block btn-sm\" type=\"submit\" name=\"searchObservationsQuickPick\" value=\"" . _("Search Observations") . "\" /></div>";
	if ($loggedUser) {
		echo "<div class=\"form group\"><input class=\"btn btn-default btn-block btn-sm\" type=\"submit\" name=\"newObservationQuickPick\" value=\"" . _("New Observation") . "\" /></div>";
	}
	echo "</form></li>";
}
?>

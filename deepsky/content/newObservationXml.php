<?php
/**
 * GUI to add new observations from xml file to the database
 * 
 * @category Deepsky/import
 * @package  DeepskyLog
 * @author   DeepskyLog Developers <developers@deepskylog.be>
 * @license  GPL2 <https://opensource.org/licenses/gpl-2.0.php>
 * @link     http://www.deepslylog.org
 */
if ((!isset($inIndex)) || (!$inIndex)) {
    include "../../redirect.php";
} else {
    newObservationXml();
}

/**
 * Show the page where a openAstronomyLog XML file can be imported.
 * 
 * @return Nothing
 */
function newObservationXml()
{
    global $baseURL, $objPresentations;
    echo "<div id=\"main\">";
    echo "<h4>" . LangXMLTitle . "</h4>"; 
    echo "<hr />";
    print LangXMLMessage1 . "<br />";
    print LangXMLMessage2 . "<br />";
    print LangXMLMessage3 . "<br />";
    echo "<hr />";
    echo "<form action=\"" . $baseURL . "index.php?indexAction=addXmlObservations\" enctype=\"multipart/form-data\" method=\"post\"><div>";
    echo "<input type=\"file\" name=\"xml\" /><br />";
    echo "<input class=\"btn btn-success\" type=\"submit\" name=\"change\" value=\"" . LangXMLButton . "\" />";
    echo "</div></form>";
    echo "<hr />";
    echo "</div>";
}
?>
<?php 
// setup_objects_query.php
// interface to query comets

global $inIndex,$loggedUser,$objUtil;
if((!isset($inIndex))||(!$inIndex)) include "../../redirect.php";
else setup_objects_query();

function setup_objects_query()
{ global $baseURL,
         $objPresentations;
	$_SESSION['result'] = "";
	echo "<div id=\"main\">";
	echo "<form action=\"".$baseURL."index.php\" method=\"get\"><div>";
	echo "<input type=\"hidden\" name=\"indexAction\" value=\"comets_result_query_objects\" />";
	echo "<h4>".LangQueryObjectsTitle."</h4>";
	echo "<input type=\"submit\" class=\"btn btn-success pull-right\" name=\"query\" value=\"" . LangQueryObjectsButton1 . "\" />";
	echo "<br /><hr />";
	// OBJECT NAME 
	$content="<input type=\"text\" class=\"form-control\" maxlength=\"40\" name=\"name\" size=\"40\" value=\"\" />";
	echo "<strong>" . LangQueryObjectsField1 . "</strong>";
	echo $content;;
	$content="<input type=\"text\" class=\"form-control\" maxlength=\"40\" name=\"icqname\" size=\"40\" value=\"\" />";
	echo "<strong>" . LangNewObjectIcqname . "</strong>";
	echo $content;
	echo "<hr />";
	echo "</div></form>";
	echo "</div>";
}
?>
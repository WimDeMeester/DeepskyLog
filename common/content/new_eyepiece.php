<?php  // new_eyepiece.php - allows the user to add a new eyepiece

$mfl = -1;
if(array_key_exists('maxFocalLength',$_GET) && $_GET['maxFocalLength']) 
  $mfl = $_GET['maxFocalLength'];
if(array_key_exists('eyepieceid',$_GET) && $_GET['eyepieceid'])
  $mfl = stripslashes($objEyepiece->getEyepiecePropertyFromId($_GET['eyepieceid'],'maxFocalLength'));
if($mfl<0)
  $mfl="";

$sort=$objUtil->checkGetKey('sort','focalLength');
if(!$min) $min=$objUtil->checkGetKey('min',0);
// the code below looks very strange but it works
if((isset($_GET['previous'])))
  $orig_previous = $_GET['previous'];
else
  $orig_previous = "";
$eyeps = $objEyepiece->getSortedEyepieces($sort, $loggedUser);
if((isset($_GET['sort'])) && $_GET['previous'] == $_GET['sort']) // reverse sort when pushed twice
{ if ($_GET['sort'] == "name")
    $eyeps = array_reverse($eyeps, true);
  else
  { krsort($eyeps);
    reset($eyeps);
  }
  $previous = ""; // reset previous field to sort on
}
else
  $previous = $sort;
$step = 25;
echo "<div id=\"main\">";
echo "<h2>".LangOverviewEyepieceTitle."</h2>";
$link = $baseURL."index.php?indexAction=add_eyepiece&amp;sort=".$sort."&amp;previous=".$orig_previous;
list($min, $max) = $objUtil->printNewListHeader($eyeps, $link, $min, $step, "");
echo "<table width=\"100%\">";
echo "<tr class=\"type3\">";
echo "<td><a href=\"".$baseURL."index.php?indexAction=add_eyepiece&amp;sort=name&amp;previous=$previous\">".LangViewEyepieceName."</a></td>";
echo "<td align=\"center\"><a href=\"".$baseURL."index.php?indexAction=add_eyepiece&amp;sort=focalLength&amp;previous=$previous\">".LangViewEyepieceFocalLength."</a></td>";
echo "<td align=\"center\"><a href=\"".$baseURL."index.php?indexAction=add_eyepiece&amp;sort=maxFocalLength&amp;previous=$previous\">".LangViewEyepieceMaxFocalLength."</a></td>";
echo "<td align=\"center\"><a href=\"".$baseURL."index.php?indexAction=add_eyepiece&amp;sort=apparentFOV&amp;previous=$previous\">".LangViewEyepieceApparentFieldOfView."</a></td>";
echo "<td></td>";
echo "</tr>";
$count = 0;
if ($eyeps != null)
{ while(list($key,$value) = each($eyeps))
  { $eyepiece=$objEyepiece->getEyepiecePropertiesFromId($value);
    echo "<tr class=\"type".(2-($count%2))."\">";
		echo "<td><a href=\"".$baseURL."index.php?indexAction=adapt_eyepiece&amp;eyepiece=".urlencode($value)."\">".stripslashes($eyepiece['name'])."</a></td>";
		echo "<td align=\"center\">".$eyepiece['focalLength']."</td>";
		echo "<td align=\"center\">".(($eyepiece['maxFocalLength']!=-1)?$eyepiece['maxFocalLength']:"-")."</td>";
		echo "<td align=\"center\">".$eyepiece['apparentFOV']."</td>";
		echo "<td>";
    if(!($objEyepiece->getEyepieceUsedFromId($value)))
      echo("<a href=\"".$baseURL."index.php?indexAction=validate_delete_eyepiece&amp;eyepieceid=" . urlencode($value) . "\">" . LangRemove . "</a>");
    echo "</td></tr>";
    $count++;
  }
}
echo "</table>";
list($min, $max) = $objUtil->printNewListHeader($eyeps, $link, $min, $step, "");
echo "</div>";
echo "<hr />";
$eyeps = $objEyepiece->getSortedEyepieces('focalLength');
echo "<h2>".LangAddEyepieceTitle."</h2>";
echo "<ol>";
echo "<li value=\"1\">";
echo LangAddEyepieceExisting;
echo "<table width=\"100%\">";
echo "<tr>";
echo "<td width=\"25%\">";
echo "<form name=\"overviewform\">";
echo "<select onchange=\"location = this.options[this.selectedIndex].value;\" name=\"catalog\">";
while(list($key, $value)=each($eyeps))
  echo "<option value=\"".$baseURL."index.php?indexAction=add_eyepiece&amp;eyepieceid=".urlencode($value)."\" >".$objEyepiece->getEyepiecePropertyFromId($value,'name')."</option>"; 
echo "</select>";
echo "</form>";
echo "</td>";
echo "</tr>";
echo "</table>";
echo "</li>";
echo "</ol>";
echo "<p>";
echo LangAddSiteFieldOr;
echo "</p>";
echo "<ol>";
echo "<li value=\"2\">".LangAddEyepieceManually."</li>";
echo "</ol>";
echo "<form action=\"".$baseURL."index.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"indexAction\" value=\"validate_eyepiece\" />";
echo "<table>";
tableFieldnameFieldExplanation(LangAddEyepieceField1,
                               "<input type=\"text\" class=\"inputfield requiredField\" maxlength=\"64\" name=\"eyepiecename\" size=\"30\" value=\"".stripslashes($objUtil->checkGetKey('eyepiecename')).stripslashes($objEyepiece->getEyepiecePropertyFromId($objUtil->checkGetKey('eyepieceid'),'name'))."\" />",
                               LangAddEyepieceField1Expl);
tableFieldnameFieldExplanation(LangAddEyepieceField2,
                               "<input type=\"text\" class=\"inputfield requiredField\" maxlength=\"5\" name=\"focalLength\" size=\"5\" value=\"".stripslashes($objEyepiece->getEyepiecePropertyFromId($objUtil->checkGetKey('eyepieceid'),'focalLength',$objUtil->checkGetKey('focalLength')))."\" />",
                               LangAddEyepieceField2Expl);
tableFieldnameFieldExplanation(LangAddEyepieceField4,
                               "<input type=\"text\" class=\"inputfield\" maxlength=\"5\" name=\"maxFocalLength\" size=\"5\" value=\"".$mfl."\" />",
                               LangAddEyepieceField4Expl);
tableFieldnameFieldExplanation(LangAddEyepieceField3,
                               "<input type=\"text\" class=\"inputfield\" maxlength=\"5\" name=\"apparentFOV\" size=\"5\" value=\"".stripslashes($objEyepiece->getEyepiecePropertyFromId($objUtil->checkGetKey('eyepieceid'),'apparentFOV',$objUtil->checkGetKey('apparentFOV')))."\" />",
                               LangAddEyepieceField3Expl);
echo "</table>";
echo "<hr />";
echo "<input type=\"submit\" name=\"add\" value=\"".LangAddEyepieceButton."\" />";
echo "</form>";
echo "</div>";
?>

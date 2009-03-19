<?php // getLocation.php - prints the locations looked up into the database 
if((!isset($inIndex))||(!$inIndex)) include "../../redirect.php";
elseif(!$loggedUser) throw new Exception(LangException002);
elseif(!($locationname=$objUtil->checkPostKey('location'))) throw new Exception(LangException013);
elseif(!($countryname=$objUtil->checkPostKey('country'))) throw new Exception(LangException014);
elseif(!($objUtil->checkUserID($objLocation->getLocationPropertyFromId($locationid,'observer','')))) throw new Exception(LangExcpetion012);
else
{
$latitudestr = $objLocation->getLocationPropertyFromId($locationid,'latitude');
echo "<div id=\"main\">";
echo "<h2>".LangGetLocation1."</h2>";
$count=0;
$result=$objLocation->getLocationsFromDatabase($location_name,$countryname);
if(($result)&&($location_name))
{ echo "<div class=\"results\">".LangGetLocation2."<a href=\"".$baseURL."index.php?indexAction=search_sites\">".LangGetLocation2a."</div>";
  echo "<p>";
	echo "<table>";
  echo "<tr class=\"type3\">";
  echo "<td>".LangGetLocation3."</td>";
  echo "<td>".LangGetLocation4."</td>";
  echo "<td>".LangGetLocation5."</td>";
  echo "<td>".LangGetLocation6."</td>";
  echo "<td>".LangGetLocation7."</td>";
  echo "</tr>";
  while(list($key, $value)=each($result))
  { $vars = explode("\t", $value);
    echo "<tr class=\"type".(2-($count%2))."\">";
    echo "<td>";
    echo "<a href=\"".$baseURL."index.php?indexAction=add_site&amp;sitename=$vars[0]&amp;longitude=$vars[1]&amp;latitude=$vars[2]&amp;region=$vars[4]&amp;country=$vars[3]\">$vars[0]</a> </td><td>".$objPresentations->decToString($vars[1], 1) ."</td><td>".$objPresentations->decToString($vars[2], 1) ."</td><td> $vars[4] </td><td> $vars[3]";
    echo "</td>";
    echo "</tr>";
    $count++;
  }
  echo "</table>";
	echo "</p>";
}
else
{ echo "<p>".LangGetLocation8."</p>";
  echo "<p><a href=\"".$baseURL."index.php?indexAction=search_sites\">".LangGetLocation9."</a>";
  echo LangGetLocation10;
  echo "<a href=\"".$baseURL."index.php?indexAction=add_site\">".LangGetLocation11."</a>";
}
echo "</div>";
}
?>

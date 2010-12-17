<?php 
// search.php
// menu which allows the user to search the observation database 

global $inIndex,$loggedUser,$objUtil;

if((!isset($inIndex))||(!$inIndex)) include "../../redirect.php";
else menu_search();

function menu_search()
{ global $loggedUser,$menuView,$baseURL;
	$theDate      = date('Ymd', strtotime('-1 year')) ;
	$lastMinYear  = substr($theDate,0,4);
	$lastMinMonth = substr($theDate,4,2);
	$lastMinDay   = substr($theDate,6,2);
	$link="";
	reset($_GET);
	while(list($key,$value)=each($_GET))
	  if($key!="menuView")
	    $link.="&amp;".$key."=".urlencode($value);
	echo "<div class=\"menuDiv\">";
	echo "<p   class=\"menuHead\">";
	if($menuView=="collapsed")
	  echo "<a href=\"".$baseURL."index.php?menuView=expanded".$link."\" title=\"".LangMenuExpand."\">+</a> ";
	else
	  echo "<a href=\"".$baseURL."index.php?menuView=collapsed".$link."\" title=\"".LangMenuCollapse."\">-</a> ";
	echo LangSearchMenuTitle."</p>";
	if($menuView=="collapsed")
	{ echo "<select name=\"view\" class=\"menuField menuDropdown\" onchange=\"{location=this.options[this.selectedIndex].value;}\">";
	  echo "<option value=\"".$baseURL."index.php\">"."&nbsp;"."</option>";
	  if($loggedUser)
	    echo "<option value=\"".$baseURL."index.php?indexAction=result_selected_observations&amp;myLanguages=true&amp;catalog=%&amp;minyear=$lastMinYear&amp;minmonth=$lastMinMonth&amp;minday=$lastMinDay&amp;newobservations=true\">".LangSearchMenuItem9."</option>";
	  if($loggedUser
	  &&($loggedUser!="admin")) {                                                           // admin doesn't have own observations
	    echo "<option value=\"".$baseURL."index.php?indexAction=result_selected_observations&amp;observer=".urlencode($loggedUser)."\">".LangSearchMenuItem1."</option>";
	    echo "<option value=\"".$baseURL."index.php?indexAction=result_selected_sessions&amp;observer=".urlencode($loggedUser)."\">".LangSearchMenuItem11."</option>";
	  }
	  echo "<option value=\"".$baseURL."index.php?indexAction=result_selected_observations&amp;myLanguages=true&amp;catalog=%&amp;minyear=$lastMinYear&amp;minmonth=$lastMinMonth&amp;minday=$lastMinDay\">".LangSearchMenuItem8."</option>";
	  echo "<option value=\"".$baseURL."index.php?indexAction=result_selected_sessions\">".LangSearchMenuItem12."</option>";
	  echo "<option value=\"".$baseURL."index.php?indexAction=rank_observers\">".LangSearchMenuItem6."</option>";
	  echo "<option value=\"".$baseURL."index.php?indexAction=rank_objects\">".LangSearchMenuItem7."</option>";
	  echo "<option value=\"".$baseURL."index.php?indexAction=result_selected_observations&amp;catalog=%\">".LangSearchMenuItem2."</option>";
	  echo "<option value=\"".$baseURL."index.php?indexAction=view_catalogs\">".LangSearchMenuItem10."</option>";
	  echo "</select>";
	}
	else
	{ if($loggedUser)
	    echo "<a class=\"menuLine\" href=\"".$baseURL."index.php?indexAction=result_selected_observations&amp;myLanguages=true&amp;catalog=%&amp;minyear=$lastMinYear&amp;minmonth=$lastMinMonth&amp;minday=$lastMinDay&amp;newobservations=true\">".LangSearchMenuItem9."</a><br />";
	  if(($loggedUser)
	  && ($loggedUser!="admin")) {          // admin doesn't have own observations
	    echo "<a class=\"menuLine\" href=\"".$baseURL."index.php?indexAction=result_selected_observations&amp;observer=".urlencode($loggedUser)."\">".LangSearchMenuItem1."</a><br />";
	    echo "<a class=\"menuLine\" href=\"".$baseURL."index.php?indexAction=result_selected_sessions&amp;observer=".urlencode($loggedUser)."\">".LangSearchMenuItem11."</a><br />";
	  }
	  echo "<a class=\"menuLine\" href=\"".$baseURL."index.php?indexAction=result_selected_observations&amp;myLanguages=true&amp;catalog=%&amp;minyear=$lastMinYear&amp;minmonth=$lastMinMonth&amp;minday=$lastMinDay\">".LangSearchMenuItem8."</a><br />";
	  echo "<a class=\"menuLine\" href=\"".$baseURL."index.php?indexAction=result_selected_sessions\">".LangSearchMenuItem12."</a><br />";
	  echo "<a class=\"menuLine\" href=\"".$baseURL."index.php?indexAction=rank_observers\">".LangSearchMenuItem6."</a><br />";
	  echo "<a class=\"menuLine\" href=\"".$baseURL."index.php?indexAction=rank_objects\">".LangSearchMenuItem7."</a><br />";
	  echo "<a class=\"menuLine\" href=\"".$baseURL."index.php?indexAction=result_selected_observations&amp;catalog=%\">".LangSearchMenuItem2."</a><br />";
	  echo "<a class=\"menuLine\" href=\"".$baseURL."index.php?indexAction=view_catalogs\">".LangSearchMenuItem10."</a><br />";
	}
	echo "</div>";
}
?>
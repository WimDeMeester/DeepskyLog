<?php
// tolist.php
// manages and shows lists
echo "<script type=\"text/javascript\" src=\"".$baseURL."lib/javascript/presentation.js\"></script>";

echo "<form action=\"".$baseURL."index.php?indexAction=listaction\">";
echo "<input type=\"hidden\" name=\"indexAction\" value=\"listaction\" />";
echo "<table>";
echo "<tr>";
echo "<td align=\"right\">".LangToListAddNew."</td>";
echo "<td>"."<input style=\"width:20em;\" type=\"text\" class=\"inputfield\" name=\"addlistname\" size=\"40\" value=\"\" />"."</td>";
echo "<td><input type=\"checkbox\" name=\"PublicList\" value=\"" . LangToListPublic . "\" />".LangToListPublic . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
echo "<input style=\"width:10em;\"  type=\"submit\" name=\"addList\" value=\"" . LangToListAdd . "\" />";
if($myList)
  echo "<input style=\"width:10em;\" type=\"submit\" name=\"renameList\" value=\"" . LangToListRename . "\" />";
echo "</td>";
echo"</tr>";
echo "</table>"; 
echo "</form>";
echo "<hr />";
if($listname)
{ $link = $baseURL."index.php?indexAction=listaction&amp;sort=".$objUtil->checkGetKey('sort','objectpositioninlist');
  list($min, $max,$content)=$objUtil->printNewListHeader3($_SESSION['Qobj'], $link, $min, 25, "");	
  $objPresentations->line(array("<h5>".LangSelectedObjectsTitle." ".$listname_ss. "</h5>".
                                ((!$myList)?
                                "(".LangToListListBy.$objObserver->getObserverProperty(($listowner=$objList->getListOwner()),'firstname').' '.$objObserver->getObserverProperty($listowner,'name').")":
                                "<a href=\"".$baseURL."index.php?indexAction=import_csv_list\">" .  LangToListImport . "</a>"),
                                $content),
                          "LR", array(60,40),50);
  echo "<div id=\"container2\" style=\"position:relative;\">";
  if(count($_SESSION['Qobj'])>0)
	{ // OUTPUT RESULT
    $link = "".$baseURL."index.php?indexAction=listaction";
	  echo "<hr />";
	  $_GET['min']=$min;
	  $_GET['max']=$max;
	  if($FF)
	  { echo "<script type=\"text/javascript\">";
      echo "theResizeElement='obj_list';";
      echo "theResizeSize=90;";
      echo "</script>";
  	}
	  $objObject->showObjects($link, $min, $max,'',1);
	  echo "<hr />";
    if($myList)
    { echo "<form action=\"".$baseURL."index.php?indexAction=listaction\">";
      echo "<input type=\"hidden\" name=\"indexAction\" value=\"listaction\" />";
		  echo "<input style=\"width:12em;\" type=\"submit\" name=\"emptyList\" value=\"" . LangToListEmpty . "\" />";
      echo "<input style=\"width:12em;\" type=\"submit\" name=\"removeList\" value=\"" . LangToListMyListsRemove . "\" />";
      echo "</form>";
    }
    $objPresentations->promptWithLink(LangListQueryObjectsMessage14,$listname_ss,$baseURL."objects.pdf?SID=Qobj",LangExecuteQueryObjectsMessage4);
	  echo "&nbsp;-&nbsp;";
    $objPresentations->promptWithLink(LangListQueryObjectsMessage14,$listname_ss,$baseURL."objectnames.pdf?SID=Qobj",LangExecuteQueryObjectsMessage4b);
	  echo "&nbsp;-&nbsp;";
    $objPresentations->promptWithLink(LangListQueryObjectsMessage14,$listname_ss,$baseURL."objectsDetails.pdf?SID=Qobj&amp;sort=" . $_SESSION['QobjSort'],LangExecuteQueryObjectsMessage4c);
	  echo "&nbsp;-&nbsp;";
    echo "<a href=\"objects.argo?SID=Qobj\" target=\"new_window\">".LangExecuteQueryObjectsMessage8."</a> &nbsp;-&nbsp;";
    echo "<a href=\"objects.csv?SID=Qobj\" target=\"new_window\">".LangExecuteQueryObjectsMessage6."</a>";
  }
	else
	{ echo LangToListEmptyList;
	}
  echo "</div>";
}


?>

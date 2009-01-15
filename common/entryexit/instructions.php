<?php

//============================================================================== COMMON INSTRUCTIONS
while(list($key,$value)=each($modules))
  if($objUtil->checkGetKey('indexAction')=='module'.$value)
  { $_SESSION['module']=$value;
    setcookie("module",$value,time()+(365*24*60*60),"/");
  }
  
if(($objUtil->checkSessionKey('admin')=='yes')&&($objUtil->checkGetKey('indexAction')=="change_role"))
  require_once $instDir."/common/control/change_role.php";
if(($objUtil->checkSessionKey('admin')=='yes')&&($objUtil->checkGetKey('indexAction')=="validate_observer"))
  require_once $instDir."/common/control/validate_observer.php";
if($objUtil->checkGetKey('indexAction')=="validate_delete_eyepiece")
{ $objEyepiece->validateDeleteEyepiece();
  $_GET['indexAction']='add_eyepiece';
  unset($_GET['validate_delete_eyepiece']);
}
if($objUtil->checkGetKey('indexAction')=="validate_delete_filter")
  require_once $instDir."/common/control/validate_delete_filter.php";
if($objUtil->checkGetKey('indexAction')=="validate_delete_instrument")
  require_once $instDir."/common/control/validate_delete_instrument.php";
if($objUtil->checkGetKey('indexAction')=="validate_delete_lens")
  require_once $instDir."/common/control/validate_delete_lens.php";
if($objUtil->checkGetKey('indexAction')=="validate_delete_location")
  require_once $instDir."/common/control/validate_delete_location.php";

if($objUtil->checkGetKey('indexAction')=="common_control_validate_account")
  require_once $instDir."/common/control/validate_account.php";
if($objUtil->checkGetKey('indexAction')=="validate_eyepiece")
{ $entryMessage.=$objEyepiece->validateSaveEyepiece();
  $_GET['indexAction']='add_eyepiece';
	unset($_GET['validate_eyepiece']);
}
if($objUtil->checkGetKey('indexAction')=="validate_filter")
  require_once $instDir."/common/control/validate_filter.php";  
if($objUtil->checkGetKey('indexAction')=="validate_instrument")
  require_once $instDir."/common/control/validate_instrument.php";
if($objUtil->checkGetKey('indexAction')=="validate_lens")
  require_once $instDir."/common/control/validate_lens.php";
if($objUtil->checkGetKey('indexAction')=="validate_site")
  require_once $instDir."/common/control/validate_site.php";
if($objUtil->checkGetKey('indexAction')=="logout")
  require_once $instDir."/common/control/logout.php";


  
  
//============================================================================== DEEEPSKY INSTRUCTIONS
$object=$objUtil->checkPostKey('object',$objUtil->checkGetKey('object'));
if(($objUtil->checkGetKey('indexAction')=='quickpick') // From quickpick
&&($objUtil->checkGetKey('object'))
&&($objObject->getExactDsObject($_GET['object']))
&&(array_key_exists('newObservation',$_GET)))
{ $_POST['year']=$GLOBALS['objUtil']->checkPostKey('year',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsYear')); 
  $_POST['month']=$GLOBALS['objUtil']->checkPostKey('month',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsMonth')); 
  $_POST['day']=$GLOBALS['objUtil']->checkPostKey('day',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsDay'));
  $_POST['instrument']=$GLOBALS['objUtil']->checkPostKey('instrument',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsInstrument')); 
  $_POST['site']=$GLOBALS['objUtil']->checkPostKey('site',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsLocation')); 
  $_POST['limit']=$GLOBALS['objUtil']->checkPostKey('limit',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsLimit')); 
  $_POST['sqm']=$GLOBALS['objUtil']->checkPostKey('sqm',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsSQM')); 
  $_POST['seeing']=$GLOBALS['objUtil']->checkPostKey('seeing',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsSeeing')); 
  $_POST['description_language']=$GLOBALS['objUtil']->checkPostKey('description_language',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsLanguage'));
	$_POST['timestamp']=time();
	$_SESSION['addObs']=$_POST['timestamp'];
} 
if($objUtil->checkGetKey('indexAction')=="add_observation")
{ if(array_key_exists('number',$_POST)&&(!$_POST['number']))
    $_GET['indexAction']="query_objects";
  elseif(array_key_exists('number',$_POST)&&(!($_GET['object']=$GLOBALS['objObject']->getExactDsObject('',$GLOBALS['objUtil']->checkPostKey('catalog'), $GLOBALS['objUtil']->checkPostKey('number')))))
  { $entryMessage.="No corresponding object found for ".$GLOBALS['objUtil']->checkPostKey('catalog')." ".$GLOBALS['objUtil']->checkPostKey('number');
    $_GET['indexAction']="query_objects";
   }
  else
	{ $_POST['year']=$GLOBALS['objUtil']->checkPostKey('year',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsYear')); 
    $_POST['month']=$GLOBALS['objUtil']->checkPostKey('month',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsMonth')); 
    $_POST['day']=$GLOBALS['objUtil']->checkPostKey('day',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsDay'));
    $_POST['instrument']=$GLOBALS['objUtil']->checkPostKey('instrument',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsInstrument')); 
    $_POST['site']=$GLOBALS['objUtil']->checkPostKey('site',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsLocation')); 
    $_POST['limit']=$GLOBALS['objUtil']->checkPostKey('limit',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsLimit')); 
    $_POST['sqm']=$GLOBALS['objUtil']->checkPostKey('sqm',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsSQM')); 
    $_POST['seeing']=$GLOBALS['objUtil']->checkPostKey('seeing',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsSeeing')); 
    $_POST['description_language']=$GLOBALS['objUtil']->checkPostKey('description_language',$GLOBALS['objUtil']->checkArrayKey($_SESSION,'newObsLanguage'));
		$_POST['timestamp']=time();
		$_SESSION['addObs']=$_POST['timestamp'];
	} 
}
if((array_key_exists('indexAction',$_POST)&&$_POST['indexAction']=="validate_observation")
&& ($objUtil->checkPostKey('clear_observation')==LangViewObservationButton2))
{ $temp=$_POST['object'];
  foreach($_POST as $foo=>$bar)
    $_POST[$foo]="";
  $_POST['object']=$temp;
	$_POST['timestamp']=time();
	$_SESSION['addObs']=$_POST['timestamp'];
  $_GET['indexAction']="add_observation";
}
if(array_key_exists('indexAction',$_POST)&&$_POST['indexAction']=="validate_observation")
  include_once "deepsky/control/validate_observation.php";
if(array_key_exists('indexAction',$_GET)&&$_GET['indexAction']=="validate_change_observation")
  include_once "deepsky/control/validate_change_observation.php";
if(array_key_exists('indexAction',$_GET)&&$_GET['indexAction']=="validate_object")
  include_once "deepsky/control/validate_object.php";
if(array_key_exists('indexAction',$_GET)&&$_GET['indexAction']=="validate_delete_observation")
  include_once "deepsky/control/validate_delete_observation.php";
if(array_key_exists('indexAction',$_GET)&&$_GET['indexAction']=="manage_csv_objects")
  include_once "deepsky/control/manage_csv_objects.php";
if(array_key_exists('indexAction',$_GET)&&$_GET['indexAction']=="add_csv_observations")
  include_once "deepsky/control/add_csv_observations.php";
if(array_key_exists('indexAction',$_GET)&&$_GET['indexAction']=="add_csv_listdata")
  include_once "deepsky/control/add_csv_listdata.php";
  
  
// ============================================================================ LIST COMMANDS
if($objUtil->checkGetKey('emptyList')&&$myList)
{ $objList->emptyList($listname);
  $entryMessage.=LangToListEmptied.$listname_ss.".";
	unset($_SESSION['QobjParams']);
  unset($_GET['emptyList']);
}
if($objUtil->checkGetKey('ObjectDownInList')&&$myList)
{ $objList->ObjectDownInList($_GET['ObjectDownInList']);
	unset($_SESSION['QobjParams']);
  $entryMessage.=LangToListMoved1.$_GET['ObjectDownInList'].LangToListMoved3."<a href=\"".$baseURL."index.php?indexAction=listaction&amp;manage=manage\">".$listname_ss."</a>.";
  unset($_GET['ObjectDownInList']);
}
if($objUtil->checkGetKey('ObjectUpInList')&&$myList)
{ $objList->ObjectUpInList($_GET['ObjectUpInList']);
	unset($_SESSION['QobjParams']);
  $entryMessage.=LangToListMoved1.$_GET['ObjectUpInList'].LangToListMoved2."<a href=\"".$baseURL."index.php?indexAction=listaction&amp;manage=manage\">".$listname_ss."</a>.";
  unset($_GET['ObjectUpInList']);
}

if($objUtil->checkGetKey('ObjectToPlaceInList')&&$myList)
{ $objList->ObjectFromToInList($_GET['ObjectFromPlaceInList'],$_GET['ObjectToPlaceInList']);
	unset($_SESSION['QobjParams']);
  $entryMessage.=LangToListMoved7.$_GET['ObjectToPlaceInList'].".";
  unset($_GET['ObjectToPlaceInList']);
}
if($objUtil->checkGetKey('removePageObjectsFromList')&&$myList)
{ if(count($_SESSION['Qobj'])>0)
	{ if(array_key_exists('min',$_GET) && $_GET['min'])
     $min=$_GET['min'];
    else
     $min=0;
		$count=$min;
	  while(($count<($min+25))&&($count<count($_SESSION['Qobj'])))
	  {$objList->removeObjectFromList($_SESSION['Qobj'][$count]['objectname'],$_SESSION['Qobj'][$count]['showname']);
		  $count++;
    }
	  unset($_SESSION['QobjParams']);
    $entryMessage.=LangToListPageRemoved;
	}
  unset($_GET['removePageObjectsFromList']);
}
if($objUtil->checkGetKey('addList')&&($listnameToAdd=$objUtil->checkGetKey('addlistname')))
{ unset($_SESSION['QobjParams']);
  if(array_key_exists("PublicList",$_GET))
    if(substr($listnameToAdd,0,7)!="Public:")
      $listnameToAdd="Public: ".$listnameToAdd;  
  if($objList->checkList($listnameToAdd)!=0)
    $entryMessage.=LangToListList.stripslashes($listnameToAdd).LangToListExists;
  else
  { $objList->addList($listnameToAdd);
    $_SESSION['listname'] = $listnameToAdd;
    $listname=$_SESSION['listname'];
    $listname_ss=stripslashes($listname);
    $myList=true;
    $entryMessage.=LangToListList.$listname_ss.LangToListAdded;
  }                    	
  unset($_GET['addList']);
}
if($objUtil->checkGetKey('renameList')&&($listnameToAdd=$objUtil->checkGetKey('addlistname')))
{ unset($_SESSION['QobjParams']);
  $listnameTo=$_GET['addlistname'];
  if(array_key_exists("PublicList",$_GET))
	  if(substr($listnameTo,0,7)!="Public:")
	    $listnameTo="Public: ".$listnameTo;  
  if($objList->checkList($listnameTo)!=0)
     $entryMessage.=LangToListList.stripslashes($listnameTo).LangToListExists;
  else
  { $objList->renameList($listname, $listnameTo);
    $_SESSION['listname']=$listnameTo;
    $listname=$_SESSION['listname'];
    $listname_ss=stripslashes($listname);
    $myList=true;
    $entryMessage.=LangToListList.$listname_ss.LangToListAdded; 
  }
  unset($_GET['renameList']);
}
if($objUtil->checkGetKey('removeList')&&$myList)
{ unset($_SESSION['QobjParams']);
	$objList->removeList($listname);
	$entryMessage.=LangToListRemoved.stripslashes($_SESSION['listname']).".";
	unset($_GET['removeList']);
	$_SESSION['listname']="----------";
	$listname='';
  $listname_ss='';
  $myList=False;
  unset($_GET['removeList']);
}
if($objUtil->checkGetKey('activateList')&&$objUtil->checkGetKey('listname'))
{ $_SESSION['listname']=$_GET['listname'];
  $listname=$_SESSION['listname'];
  $listname_ss=stripslashes($listname);
  $myList=False;
  if(array_key_exists('listname',$_SESSION)&&$objList->checkList($_SESSION['listname'])==2)
    $myList=True;
  if($_GET['listname']<>"----------")
    $entryMessage.=LangToListList.$listname_ss.LangToListActivation1;
  unset($_GET['activateList']);
}
if($objUtil->checkGetKey('addObjectToList')&&$listname&&$myList)
{ $objList->addObjectToList($_GET['addObjectToList'],$GLOBALS['objUtil']->checkGetKey('showname',$_GET['addObjectToList']));
  $entryMessage.=LangListQueryObjectsMessage8."<a href=\"".$baseURL."index.php?indexAction=detail_object&amp;object=".urlencode($_GET['addObjectToList']) . "\">".$_GET['showname']."</a>".LangListQueryObjectsMessage6."<a href=\"".$baseURL."index.php?indexAction=listaction&amp;manage=manage\">".$listname_ss."</a>.";
  unset($_GET['addObjectToList']);
}
if(array_key_exists('addObservationToList',$_GET) && $_GET['addObservationToList'] && $myList)
{ $objList->addObservationToList($_GET['addObservationToList']);
  $entryMessage.=LangListQueryObjectsMessage16.LangListQueryObjectsMessage6."<a href=\"".$baseURL."index.php?indexAction=listaction&amp;manage=manage\">".$listname_ss."</a>.";
  unset($_GET['addObservationToList']);
}
if(array_key_exists('removeObjectFromList',$_GET) && $_GET['removeObjectFromList'] && $myList)
{ $objList->removeObjectFromList($_GET['removeObjectFromList']);
  $entryMessage.=LangListQueryObjectsMessage8."<a href=\"".$baseURL."index.php?indexAction=detail_object&amp;object=".urlencode($_GET['removeObjectFromList'])."\">".$_GET['removeObjectFromList']."</a>".LangListQueryObjectsMessage7."<a href=\"".$baseURL."index.php?indexAction=listaction&amp;manage=manage\">".$listname_ss."</a>.";
  unset($_GET['removeObjectFromList']);
}

if(array_key_exists('addAllObjectsFromPageToList',$_GET) && $_GET['addAllObjectsFromPageToList'] && $myList)
{ $count=$min;
	while(($count<($min+25))&&($count<count($_SESSION['Qobj'])))
	{ $objList->addObjectToList($_SESSION['Qobj'][$count]['objectname'],$_SESSION['Qobj'][$count]['showname']);
		$count++;
  }
	$entryMessage = LangListQueryObjectsMessage9 . "<a href=\"".$baseURL."index.php?indexAction=listaction&amp;manage=manage\">".$listname_ss."</a>.";
  unset($_GET['addAllObjectsFromPageToList']);
}
if(array_key_exists('addAllObjectsFromQueryToList',$_GET)&&$_GET['addAllObjectsFromQueryToList']&&$myList)
{ $count=0;
	while($count<count($_SESSION['Qobj']))
	{ $objList->addObjectToList($_SESSION['Qobj'][$count]['objectname'],$_SESSION['Qobj'][$count]['showname']);
		$count++;
  }
	$entryMessage = LangListQueryObjectsMessage9 . "<a href=\"".$baseURL."index.php?indexAction=listaction&amp;manage=manage\">" .  $_SESSION['listname'] . "</a>.";
  unset($_GET['addAllObjectsFromQueryToList']);
}
if(array_key_exists('editListObjectDescription',$_GET)&&$_GET['editListObjectDescription']
&& array_key_exists('object',$_GET)&&$_GET['object']&&array_key_exists('description',$_GET) && $myList)
{ $objList->setListObjectDescription($_GET['object'],$_GET['description']);
  unset($_GET['addAllObjectsFromPageToList']);
}


// =========================================================================== COMET COMMANDS
if(array_key_exists('indexAction',$_GET)&&$_GET['indexAction']=="comets_validate_observation")
  include_once 'comets/control/validate_observation.php';
if(array_key_exists('indexAction',$_GET)&&$_GET['indexAction']=="comets_validate_object")
  include_once 'comets/control/validate_object.php';
if(array_key_exists('indexAction',$_GET)&&$_GET['indexAction']=="comets_validate_change_object")
  include_once 'comets/control/validate_change_object.php';
  
   

// ============================================================================ ADMIN COMMANDS
if(array_key_exists('admin', $_SESSION)&&$_SESSION['admin']=="yes")
{ if(array_key_exists("newaction",$_GET))
	{ if($_GET['newaction']=="NewName")
	  { $objObject->newName($_GET['object'], $_GET['newcatalog'],$_GET['newnumber']);
		  $_GET['object'] = trim($_GET['newcatalog'] . " " . ucwords(trim($_GET['newnumber'])));
    }	
  	if($_GET['newaction']=="NewAltName")
	    $objObject->newAltName($_GET['object'], $_GET['newcatalog'],$_GET['newnumber']);
  	if($_GET['newaction']=="RemoveAltNameName")
	    $objObject->removeAltName($_GET['object'], $_GET['newcatalog'],$_GET['newnumber']);
  	if($_GET['newaction']=="NewPartOf")
	    $objObject->newPartOf($_GET['object'], $_GET['newcatalog'],$_GET['newnumber']);
  	if($_GET['newaction']=="RemovePartOf")
	    $objObject->removePartOf($_GET['object'], $_GET['newcatalog'],$_GET['newnumber']);
  	if($_GET['newaction']=="RemoveAndReplaceObjectBy")
	  { $objObject->removeAndReplaceObjectBy($_GET['object'], $_GET['newcatalog'],$_GET['newnumber']);
		  $_GET['object'] = trim($_GET['newcatalog'] . " " . ucwords(trim($_GET['newnumber'])));
	  }			
  	if($_GET['newaction']=="LangObjectSetRA")
	    $objObject->setRA($_GET['object'], $_GET['newnumber']);
  	if($_GET['newaction']=="LangObjectSetDECL")
	    $objObject->setDeclination($_GET['object'], $_GET['newnumber']);
  	if($_GET['newaction']=="LangObjectSetCon")
	    $objObject->setConstellation($_GET['object'], $_GET['newnumber']);
  	if($_GET['newaction']=="LangObjectSetType")
	    $objObject->setDsObjectType($_GET['object'], $_GET['newnumber']);
  	if($_GET['newaction']=="LangObjectSetMag")
	    $objObject->setMagnitude($_GET['object'], $_GET['newnumber']);
   	if($_GET['newaction']=="LangObjectSetSUBR")
	    $objObject->setSurfaceBrightness($_GET['object'], $_GET['newnumber']);
   	if($_GET['newaction']=="LangObjectSetDiam1")
		  $objObject->setDiam1($_GET['object'], $_GET['newnumber']);
   	if($_GET['newaction']=="LangObjectSetDiam2")
		  $objObject->setDiam2($_GET['object'], $_GET['newnumber']);
   	if($_GET['newaction']=="LangObjectSetPA")
		  $objObject->setPositionAngle($_GET['object'], $_GET['newnumber']);
	}
}
?>

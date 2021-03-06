<?php
// change_account.php
// allows the user to view and change his account's details

if((!isset($inIndex))||(!$inIndex)) include "../../redirect.php";
elseif(!($loggedUser)) throw new Exception(_("You need to be logged in as an administrator to execute these operations."));
elseif(!($objUtil->checkAdminOrUserID($loggedUser))) throw new Exception(_("You need to be logged in to execute these operations."));
else change_account();

function change_account()
{ global $baseURL,$instDir,$languageMenu,$loggedUser,
         $objAtlas,$objInstrument,$objLanguage,$objLocation,$objObserver,$objPresentations,$objUtil;
  $sites = $objLocation->getSortedLocations("name", $loggedUser);
	$tempLocationList="<select name=\"site\" style=\"width: 50%\" class=\"inputfield form-control\">";
	$tempLocationList.= "<option value=\"0\">-----</option>";
	// If there are locations with the same name, the province should also be shown
	$previous = "fskfskf";
	for($i=0;$i<count($sites);$i++)
	{ $adapt[$i] = 0;
	  if($objLocation->getLocationPropertyFromId($sites[$i],'name')==$previous)
	  { $adapt[$i]=1;
	    $adapt[$i-1]=1;
	  }
	 $previous=$objLocation->getLocationPropertyFromId($sites[$i],'name');
	}
	for($i=0;$i<count($sites);$i++)
	{ $sitename = $objLocation->getLocationPropertyFromId($sites[$i],'name');
	  $tempLocationList.="<option ".(($objObserver->getObserverProperty($loggedUser,'stdlocation')==$sites[$i])?" selected=\"selected\"":"")." value=\"".$sites[$i]."\">".$sitename."</option>";
	}
	$tempLocationList.="</select>";
	$tempInstrumentList="<select name=\"instrument\" style=\"width: 50%\" class=\"inputfield form-control\">";
	$tempInstrumentList.= "<option value=\"0\">-----</option>";
	$instr=$objInstrument->getSortedInstruments("name",$loggedUser);
	$noStd=false;
	foreach ($instr as $key=>$value)
	{ $instrumentname=$objInstrument->getInstrumentPropertyFromId($value,'name');
	  if($instrumentname=="Naked eye")
	    $instrumentname=_("Naked Eye");
	  if($objObserver->getObserverProperty($loggedUser,'stdtelescope')=="0")
	    $noStd = 1;
		if($objObserver->getObserverProperty($loggedUser,'stdtelescope')==$value)
	    $tempInstrumentList.="<option selected=\"selected\" value=\"".$value."\">".$instrumentname."</option>";
	  else
	    $tempInstrumentList.="<option ".(($noStd&&($value=="1"))?" selected=\"selected\"":"")." value=\"".$value."\">".$instrumentname."</option>";
	}
	$tempInstrumentList.="</select>";

	$theAtlasKey=$objObserver->getObserverProperty($loggedUser,'standardAtlasCode','urano');
	$tempAtlasList="<select name=\"atlas\" style=\"width: 50%\" class=\"inputfield form-control\">";
	foreach ($objAtlas->atlasCodes as $key=>$value)
	  $tempAtlasList.="<option ".(($key==$theAtlasKey)?"selected=\"selected\"":"")." value=\"$key\">" . $value . "</option>";
	$tempAtlasList.="</select>";

	$tempLangList="<select name=\"language\" style=\"width: 50%\" class=\"inputfield form-control\">";
	$languages=$objLanguage->getLanguages();
	foreach ($languages as $key=>$value)
	  $tempLangList.="<option value=\"".$key."\"".(($objObserver->getObserverProperty($loggedUser,'language')==$key)?" selected=\"selected\"":"").">".$value."</option>";
	$tempLangList.="</select>";

	$allLanguages=$objLanguage->getAllLanguages($objObserver->getObserverProperty($loggedUser,'language'));
	$tempAllLangList="<select name=\"description_language\" style=\"width: 50%\" class=\"inputfield form-control\">";
	foreach ($allLanguages as $key=>$value)
	  $tempAllLangList.="<option value=\"".$key."\"".(($objObserver->getObserverProperty($loggedUser,'observationlanguage') == $key)?" selected=\"selected\"":"").">".$value."</option>";
	$tempAllLangList.="</select>";
	$_SESSION['alllanguages']=$allLanguages;
	$usedLanguages=$objObserver->getUsedLanguages($loggedUser);

	// =================================================================================================PAGE OUTPUT

	echo "<div>";
	echo "<form class=\"form-horizontal\" role=\"form\" action=\"".$baseURL."index.php\" enctype=\"multipart/form-data\" method=\"post\"><div>";
	echo "<input type=\"hidden\" name=\"indexAction\" value=\"validate_account\" />";

	echo "<h4>" . sprintf(_("Settings for %s"), 
                $objObserver->getObserverProperty($objUtil->checkSessionKey('deepskylog_id'),'firstname') . " " .
                $objObserver->getObserverProperty($objUtil->checkSessionKey('deepskylog_id'),'name')) . "</h4>";
	$content="<input class=\"pull-right btn btn-success\" type=\"submit\" name=\"change\" value=\""._("Change")."\" />";
	echo $content;

	echo "<br />";

  // We make some tabs.
  echo "<ul id=\"tabs\" class=\"nav nav-tabs\" data-tabs=\"tabs\">
          <li class=\"active\"><a href=\"#info\" data-toggle=\"tab\">" . _("Personal") . "</a></li>
          <li><a href=\"#observingDetails\" data-toggle=\"tab\">" . _("Observing") . "</a></li>
          <li><a href=\"#atlases\" data-toggle=\"tab\">" . _("Atlases") . "</a></li>
          <li><a href=\"#languages\" data-toggle=\"tab\">" . _("Languages") . "</a></li>
        </ul>";

  echo "<div id=\"my-tab-content\" class=\"tab-content\">";
  echo "<div class=\"tab-pane active\" id=\"info\">";

  echo "<br />";

  $upload_dir = 'common/observer_pics';
	$dir = opendir($instDir.$upload_dir);

  echo "<label class=\"control-label\">" . _("Change profile picture") . "</label>
        <input id=\"images\" name=\"image\" type=\"file\" data-show-remove=\"false\" accept=\"image/*\" class=\"file-loading\">";

  // Check existence of avatar for the observer
  $imaLocation = $baseURL."/images/noAvatar.jpg";
  $oldFile = '';
  while (FALSE!==($file=readdir($dir)))
  { if(("."==$file)||(".."==$file))                                            // skip current directory and directory above
    continue;
  	if(fnmatch($loggedUser.".gif",$file)||fnmatch($loggedUser.".jpg",$file)||fnmatch($loggedUser.".png",$file))
  	{
      $oldFile = $upload_dir."/".$file;
  	  $imaLocation = $baseURL.$upload_dir."/".$file;
  	}
  }
  echo "<input id=\"oldFile\" name=\"oldFile\" value=\"" . $oldFile . "\" type=\"hidden\">";

  // The javascript for the fileinput plugins
  echo "<script type=\"text/javascript\">";
  echo "$(document).on(\"ready\", function() {
  			$(\"#images\").fileinput({
  					initialPreview: [
  						// Show the correct file.
  						'<img src=\"" . $imaLocation . "\" class=\"file-preview-image\">'
  					],
            maxFileCount: 1,
            validateInitialCount: true,
  					overwriteInitial: true,
            maxImageWidth: 500,
            resizeImage: true,
            autoReplace: true,
            showRemove: false,
            showUpload: false,
            removeLabel: '',
            removeIcon: '',
            removeTitle: '',
            layoutTemplates: {actionDelete: ''},
            allowedFileTypes: [\"image\"],
  					initialCaption: \"Profile picture\",
  			});
  		});";
  echo "</script>";


  echo "<br /><br />";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Username") . "</label>";
	echo "<div class=\"col-sm-3\">
			    <input type=\"text\" required disabled class=\"inputfield form-control requiredField\" maxlength=\"64\" name=\"deepskylog_id\" size=\"30\" value=\"".$objUtil->checkSessionKey('deepskylog_id')."\" />
        </div>";
  echo "<div class=\"col-sm-3\">
          <button type=\"button\" class=\"btn btn-danger\" data-toggle=\"modal\" data-target=\"#changePassword\">" . _("Change password") . "</button>
        </div>";
	echo "<p class=\"form-control-static\">" .
        _("This is the name you will use to log in") . "</p></div>";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Email address") . "</label>";
	echo "<div class=\"col-sm-6\">
			<input type=\"email\" required class=\"inputfield form-control requiredField\" maxlength=\"80\" name=\"email\" size=\"30\" value=\"".$objObserver->getObserverProperty($objUtil->checkSessionKey('deepskylog_id'),'email')."\" />";
	echo "</div><p class=\"form-control-static\">" .
        _("Your email address will remain confidential") . "</p></div>";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("First name") . "</label>";
	echo "<div class=\"col-sm-6\">
			<input type=\"text\" required class=\"inputfield form-control requiredField\" maxlength=\"64\" name=\"firstname\" size=\"30\" value=\"".$objObserver->getObserverProperty($objUtil->checkSessionKey('deepskylog_id'),'firstname')."\" />";
	echo "</div></div>";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Last Name") . "</label>";
	echo "<div class=\"col-sm-6\">
			<input type=\"text\" required class=\"inputfield form-control requiredField\" maxlength=\"64\" name=\"name\" size=\"30\" value=\"".$objObserver->getObserverProperty($objUtil->checkSessionKey('deepskylog_id'),'name')."\" />";
	echo "</div></div>";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Send emails") . "</label>";
	echo "<div class=\"col-sm-6\">
			<input type=\"checkbox\" class=\"inputfield\" name=\"send_mail\"".(($objObserver->getObserverProperty($loggedUser,'sendMail'))?"checked":"")." />";
	echo "</div><p class=\"form-control-static\">"
        . _("Send messages as email.") . "</p></div>";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Use local time") . "</label>";
	echo "<div class=\"col-sm-6\">
			<input type=\"checkbox\" class=\"inputfield\" name=\"local_time\"".(($objObserver->getObserverProperty($loggedUser,'UT'))?"":"checked")." />";
	echo "</div><p class=\"form-control-static\">" .
        _("Use local time to enter and search observations") . "</p></div>";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("ICQ observercode") . "</label>";
	echo "<div class=\"col-sm-6 form-inline\">
			<input type=\"text\" class=\"inputfield form-control\" maxlength=\"5\" name=\"icq_name\" size=\"5\" value=\"".$objObserver->getObserverProperty($loggedUser,'icqname')."\" />";
	echo "</div><p class=\"form-control-static\">" . sprintf(
        _("%s code for comet observations"),
        "<a href=\"http://cfa-www.harvard.edu/icq/icq.html\" rel=\"external\">ICQ</a>"
    ) . "</p></div>";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("fstOffset") . "</label>";
	echo "<div class=\"col-sm-6 form-inline\">" .
	    "<input type=\"number\" min=\"-5.0\" max=\"5.0\" step=\"0.1\" class=\"inputfield centered form-control\" maxlength=\"4\" name=\"fstOffset\" size=\"4\" value=\"".$objObserver->getObserverProperty($objUtil->checkSessionKey('deepskylog_id'),'fstOffset')."\" />";
	echo "</div><p class=\"form-control-static\">" .
        _("Offset between measured SQM value and the faintest visible star.") . "</p></div>";

  // The copyright / license settings.
  $copyright = $objObserver->getObserverProperty($objUtil->checkSessionKey('deepskylog_id'),'copyright');
  $ownLicense = true;

  // javascript to disable the copyright field when one of the CC options is selected.
  echo '<script>
          function enableDisableCopyright() {
            var selectBox = document.getElementById("cclicense");
            var selectedValue = selectBox.options[selectBox.selectedIndex].value;
            if (selectedValue == 7) {
              document.getElementById("copyright").disabled=false;
            } else {
              document.getElementById("copyright").disabled=true;
            }
          }
        </script>';

  echo '<div class="form-group">
          <label class="col-sm-2 control-label">' . _("License for drawings") . '</label>
          <div class="col-sm-6">
            <select name="cclicense" id="cclicense" onchange="enableDisableCopyright();" class="inputfield">';
  echo '<option value="0"';
  if (strcmp($copyright, "Attribution CC BY") == 0) {
    $ownLicense = false;
    echo ' selected="selected"';
    $copyrightStr = "";
  }
  echo '>Attribution CC BY</option>';

  echo '<option value="1"';
  if (strcmp($copyright, "Attribution-ShareAlike CC BY-SA") == 0) {
    $ownLicense = false;
    echo ' selected="selected"';
    $copyrightStr = "";
  }
  echo '>Attribution-ShareAlike CC BY-SA</option>';

  echo '<option value="2"';
  if (strcmp($copyright, "Attribution-NoDerivs CC BY-ND") == 0) {
    $ownLicense = false;
    echo ' selected="selected"';
    $copyrightStr = "";
  }
  echo '>Attribution-NoDerivs CC BY-ND</option>';

  echo '<option value="3"';
  if (strcmp($copyright, "Attribution-NonCommercial CC BY-NC") == 0) {
    $ownLicense = false;
    echo ' selected="selected"';
    $copyrightStr = "";
  }
  echo '>Attribution-NonCommercial CC BY-NC</option>';

  echo '<option value="4"';
  if (strcmp($copyright, "Attribution-NonCommercial-ShareAlike CC BY-NC-SA") == 0) {
    $ownLicense = false;
    echo ' selected="selected"';
    $copyrightStr = "";
  }
  echo '>Attribution-NonCommercial-ShareAlike CC BY-NC-SA</option>';

  echo '<option value="5"';
  if (strcmp($copyright, "Attribution-NonCommercial-NoDerivs CC BY-NC-ND") == 0) {
    $ownLicense = false;
    echo ' selected="selected"';
    $copyrightStr = "";
  }
  echo '>Attribution-NonCommercial-NoDerivs CC BY-NC-ND</option>';

  echo '<option value="6"';
  if (strcmp($copyright, "") == 0) {
    $ownLicense = false;
    echo ' selected="selected"';
    $copyrightStr = "";
  }
  echo '>' . _("No license (Not recommended!)") . '</option>';

  echo '<option value="7"';
  if ( $ownLicense ) {
    echo ' selected="selected"';
    $copyrightStr = $copyright;
  }
  echo '>' . _("Enter your own copyright text") . '</option>';

  echo '    </select>
          </div>
          <p class="form-control-static">' .
          _('It is important to select the <strong>correct license for your drawings</strong>! For help, see the <a href="http://creativecommons.org/choose/">Creative Commons license-choosing tool</a>.') . '
          </p>
        </div>';
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Copyright notice") . "</label>";
	echo "<div class=\"col-sm-6\">" .
         "<input type=\"text\" id=\"copyright\" class=\"inputfield form-control\" maxlength=\"128\" name=\"copyright\" size=\"40\" value=\"". $copyrightStr ."\" />";
	echo "</div></div>";

	echo "<p>&nbsp;</p>";

  echo "<input class=\"btn btn-success\" type=\"submit\" name=\"change\" value=\""._("Change")."\" />";

  echo "</div>";

  echo "<div class=\"tab-pane\" id=\"observingDetails\">";

  echo "<br />";
  echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Default observing site") . "</label>";
	echo "<div class=\"col-sm-6\">" . $tempLocationList;
	echo "</div><p class=\"form-control-static\">" .
			"<a href=\"".$baseURL."index.php?indexAction=add_location\">"._("Add new observing site")."</a>" . "</p></div>";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Default instrument") . "</label>";
	echo "<div class=\"col-sm-6\">" . $tempInstrumentList;
	echo "</div><p class=\"form-control-static\">" .
			 "<a href=\"".$baseURL."index.php?indexAction=add_instrument\">"._("Add instrument")."</a>" . "</p></div>";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Default atlas") . "</label>";
	echo "<div class=\"col-sm-6\">" . $tempAtlasList;
	echo "</div></div>";

	$showInches = $objObserver->getObserverProperty ( $loggedUser, "showInches" );
	$inchSelected = ($showInches == '1')?"selected":"";
	$mmSelected = ($showInches == '0')?"selected":"";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Preferred unit for instrument diameter") . "</label>";
	echo "<div class=\"col-sm-6\">";
	echo "<select name=\"showInches\" class=\"form-control\" >";
	echo "<option ".$inchSelected." value='1'>inch</option>";
	echo "<option ".$mmSelected." value='0'>mm</option>";
	echo "</select>";
	echo "</div></div>";

    echo "<input class=\"btn btn-success\" type=\"submit\" name=\"change\" value=\""._("Change")."\" />";

  echo "</div>";

  echo "<div class=\"tab-pane\" id=\"atlases\">";
  echo "<br />";
  echo _("Atlas standard object FoVs:");
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Overview / Lookup / Detail") . "</label>";
	echo "<div class=\"col-sm-2 form\">" .
			   "<input type=\"number\" min=\"1\" max=\"3600\" class=\"inputfield centered form-control\" name=\"overviewFoV\" value=\"".$objObserver->getObserverProperty($loggedUser,'overviewFoV')."\" /></div>".
	       "<div class=\"col-sm-2 form\"><input type=\"number\" min=\"1\" max=\"3600\" class=\"inputfield centered form-control\" name=\"lookupFoV\" value=\"".$objObserver->getObserverProperty($loggedUser,'lookupFoV')."\" /></div>".
	       "<div class=\"col-sm-2 form\"><input type=\"number\" min=\"1\" max=\"3600\" class=\"inputfield centered form-control\" name=\"detailFoV\" value=\"".$objObserver->getObserverProperty($loggedUser,'detailFoV')."\" /></div>";
	echo "</div>";

	echo _("Atlas standard object magnitudes:");
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Overview / Lookup / Detail") . "</label>";
	echo "<div class=\"col-sm-2 form\">" .
        " <input type=\"number\" min=\"1.00\" max=\"20.0\" step=\"0.1\" class=\"inputfield centered form-control\" name=\"overviewdsos\" value=\"".$objObserver->getObserverProperty($loggedUser,'overviewdsos')."\" /></div>".
	      "<div class=\"col-sm-2 form\"><input type=\"number\" min=\"1.00\" max=\"20.0\" step=\"0.1\" class=\"inputfield centered form-control\" name=\"lookupdsos\" value=\"".$objObserver->getObserverProperty($loggedUser,'lookupdsos')."\" /></div>".
	      "<div class=\"col-sm-2 form\"><input type=\"number\" min=\"1.00\" max=\"20.0\" step=\"0.1\" class=\"inputfield centered form-control\" name=\"detaildsos\" value=\"".$objObserver->getObserverProperty($loggedUser,'detaildsos')."\" />";
	echo "</div></div>";

	echo _("Atlas standard star magnitudes:");
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Overview / Lookup / Detail") . "</label>";
	echo "<div class=\"col-sm-2 form\">" .
        "<input type=\"number\" min=\"1.00\" max=\"20.0\" step=\"0.1\" class=\"inputfield centered form-control\" name=\"overviewstars\" value=\"".$objObserver->getObserverProperty($loggedUser,'overviewstars')."\" /></div>".
        "<div class=\"col-sm-2 form\"><input type=\"number\" min=\"1.00\" max=\"20.0\" step=\"0.1\" class=\"inputfield centered form-control\" name=\"lookupstars\" value=\"".$objObserver->getObserverProperty($loggedUser,'lookupstars')."\" /></div>".
        "<div class=\"col-sm-2 form\"><input type=\"number\" min=\"1.00\" max=\"20.0\" step=\"0.1\" class=\"inputfield centered form-control\" name=\"detailstars\" value=\"".$objObserver->getObserverProperty($loggedUser,'detailstars')."\" />";
	echo "</div></div>";

	echo _("Standard size of photos:");
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("photo 1 / photo 2") . "</label>";
	echo "<div class=\"col-sm-2 form\">" .
			  "<input type=\"number\" min=\"1\" max=\"3600\" class=\"inputfield centered form-control\" name=\"photosize1\" value=\"".$objObserver->getObserverProperty($loggedUser,'photosize1')."\" /></div>".
	      "<div class=\"col-sm-2 form\"><input type=\"number\" min=\"1\" max=\"3600\" class=\"inputfield centered form-control\" name=\"photosize2\" value=\"".$objObserver->getObserverProperty($loggedUser,'photosize2')."\" />";
	echo "</div></div>";

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Font size printed atlas pages (6..9)") . "</label>";
	echo "<div class=\"col-sm-2 form\">" .
         "<input type=\"number\" min=\"6\" max=\"9\" class=\"inputfield centered form-control\" maxlength=\"1\" name=\"atlaspagefont\" size=\"5\" value=\"".$objObserver->getObserverProperty($loggedUser,'atlaspagefont')."\" />";
	echo "</div></div>";

  echo "<input class=\"btn btn-success\" type=\"submit\" name=\"change\" value=\""._("Change")."\" />";

  echo "</div>";

  echo "<div class=\"tab-pane\" id=\"languages\">";
  echo "<br />";
  if($languageMenu==1) {
	  echo "<div class=\"form-group\">";
	  echo "<label class=\"col-sm-2 control-label\">" . _("Default language") . "</label>";
	  echo "<div class=\"col-sm-6\">" .
           $tempLangList;
	  echo "</div><p class=\"form-control-static\">" . _("The language for DeepskyLog") . "</p></div>";
	}
	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Standard language for observations") . "</label>";
	echo "<div class=\"col-sm-6\">" .
			$tempAllLangList;
	echo "</div><p class=\"form-control-static\">" . _("The standard language to enter the observations") . "</p></div>";
	reset($allLanguages);

	echo "<div class=\"form-group\">";
	echo "<label class=\"col-sm-2 control-label\">" . _("Define which languages you want to see in the descriptions") . "</label>";
	echo "<div class=\"col-sm-6\">";
	echo "<table class=\"table table-condensed borderless\">";

	$j = 0;
	echo "<tr>";
	foreach ($allLanguages as $key=>$value)
	{ echo "<td><label class=\"checkbox-inline\"><input type=\"checkbox\" ".(in_array($key,$usedLanguages)?"checked=\"checked\"":"")." name=\"".$key."\" value=\"".$key."\" />".$value."</label></td>";
	  if (($j + 1) % 3 == 0) {
	  	echo "</tr><tr>";
	  }
	  $j++;
	}
	for ($i = $j % 3;$i < 3;$i++) {
		echo "<td></td>";
	}
	echo "</tr></table></div></div>";

  echo "<input class=\"btn btn-success\" type=\"submit\" name=\"change\" value=\""._("Change")."\" />";

  echo "</div>";

  echo "</div>";

  echo "</div></form>";

  echo "<div class=\"modal fade\" id=\"changePassword\">
        <div class=\"modal-dialog\">
         <div class=\"modal-content\">
          <div class=\"modal-header\">
           <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
           <h4 class=\"modal-title\">" . _("Change password") . "</h4>
          </div>
          <div class=\"modal-body\">
           <!-- Ask for the name of the list. -->
           <form action=\"".$baseURL."index.php?indexAction=changepassword\" method=\"post\">
             <input type=\"hidden\" name=\"userid\" value=\"" . $loggedUser . "\" />" .
             _("Current password") . "
             <input type=\"password\" name=\"currentPassword\" class=\"strength\" required autofocus data-show-meter=\"false\">" .
             _("New password") . "
             <input type=\"password\" name=\"newPassword\" class=\"strength\" required>" .
             _("Confirm password") . "
             <input type=\"password\" name=\"confirmPassword\" class=\"strength\" required data-show-meter=\"false\">
             <br /><br />
            </div>
            <div class=\"modal-footer\">
            <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>
            <input class=\"btn btn-danger\" type=\"submit\" name=\"changePassword\" value=\"" . _("Change password") . "\" />
		      </form>
          </div>
         </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
       </div><!-- /.modal -->";


}
?>

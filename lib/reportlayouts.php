<?php
class reportLayouts
{ public function loadLayout($formName,$layoutName)
  { global $objDatabase;
  }
  public function saveLayoutField($reportname,$reportlayout,$fieldname,$fieldline,$fieldposition,$fieldwidth,$fieldheight,$fieldstyle)
  { global $loggedUser, $objDatabase;
	  if($reportname&&$reportlayout&&$fieldname)
      if($thepk=$objDatabase->selectSingleValue("SELECT reportlayoutpk FROM reportlayouts WHERE observerid='".$loggedUser."' AND reportname='".$reportname."' AND reportlayout='".$reportlayout."' and fieldname='".$fieldname."';","reportlayoutpk",''))
        $objDatabase->execSQL("UPDATE reportlayouts 
                               SET    fieldline=".$fieldline.",
                                      fieldposition=".$fieldposition.",
                                      fieldwidth=".$fieldwidth.",
                                      fieldheight=".$fieldheight.",
                                      fieldstyle='".$fieldstyle."'
                               WHERE  reportlayoutpk=".$thepk.";");
      else
        $objDatabase->execSQL("INSERT INTO reportlayouts (observerid, reportname,reportlayout,fieldname,fieldline,fieldposition,fieldwidth,fieldheight,fieldstyle)
                               VALUES('".$loggedUser."','".$reportname."','".$reportlayout."','".$fieldname."',".$fieldline.",".$fieldposition.",".$fieldwidth.",".$fieldheight.",'".$fieldstyle."');");   
  }
  public function getLayoutListDefault($reportName)
  { global $loggedUser, $objDatabase;
	  if($reportName)
      return $objDatabase->selectSingleArray("SELECT DISTINCT reportlayout FROM reportlayouts WHERE observerid='"."defaultuser"."' AND reportname='".$reportName."' ORDER BY reportlayout;","reportlayout");
    else
      return array();
  }
    public function getLayoutListObserver($reportName)
  { global $loggedUser, $objDatabase;
	  if($reportName)
      return $objDatabase->selectSingleArray("SELECT reportlayout FROM reportlayouts WHERE observerid='".$loggedUser."' AND reportname='".$reportName."';","reportlayout");
    else
      return array();
  }
}
$objReportLayout = new reportLayouts
?>
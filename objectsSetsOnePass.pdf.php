<?php
// objectsSetsOnePass
// generate the objects sets in one pass

$inIndex = true;
require_once 'common/entryexit/preludes.php';

header("Content-type: application/pdf");
header("Content-Length: ".strlen($_SESSION['allonepass'.$_GET['item']]));
header("Content-Disposition: attachment; filename=".str_replace(' ','_',html_entity_decode($_SESSION['Qobj'][$_GET['item']]['showname'].".pdf")));

objectsSetsOnePass_pdf();

function objectsSetsOnePass_pdf()
{ echo $_SESSION['allonepass'.$_GET['item']];
  $_SESSION['allonepass'.$_GET['item']]="";
  unset($_SESSION['allonepass'.$_GET['item']]);
}
?>
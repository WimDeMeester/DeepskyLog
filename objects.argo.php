<?php 
// argo.php
// download a argo file of objects

header ("Content-Type: application/vnd.ms-excel");
header ("Content-Disposition: attachment; filename=\"objects.argo\"");

$inIndex = true;
require_once 'common/entryexit/preludes.php';

objects_argo();

function objects_argo()
{ global $objUtil;
  if(array_key_exists('SID', $_GET)&&$_GET['SID']&&array_key_exists($_GET['SID'],$_SESSION)&&$_SESSION[$_GET['SID']])
    $objUtil->argoObjects($_SESSION[$_GET['SID']]);
}
?>

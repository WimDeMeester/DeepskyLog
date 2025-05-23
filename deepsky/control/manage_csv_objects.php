<?php
// manage_csv_objects.php
// manage objects from a csv file to the database

global $inIndex,$loggedUser;

if((!isset($inIndex))||(!$inIndex)) include "../../redirect.php";
elseif(!$loggedUser) throw new Exception(_("You need to be logged in to change your locations or equipment."));
elseif($_SESSION['admin']!="yes") throw new Exception(_("You need to be logged in as an administrator to execute these operations."));
else manage_csv_objects();

function manage_csv_objects()
{ global $objObject, $loggedUser;
	if($_FILES['csv']['tmp_name'] != "")
	  $csvfile = $_FILES['csv']['tmp_name'];
	$data_array=file($csvfile);
	for($i=0;$i<count($data_array);$i++ )
	  $parts_array[$i]=explode(";",$data_array[$i]);
	for($i=0;$i<count($parts_array);$i++)
	{ $instruction[$i]=trim($parts_array[$i][0]);
	  $object[$i]=trim($parts_array[$i][1]);
	  $cat[$i]=trim($parts_array[$i][2]);
	  $catindex_data[$i]=trim($parts_array[$i][3]);
		if(array_key_exists(4, $parts_array[$i]))
	    $data4[$i] = trim($parts_array[$i][4]);
		if(array_key_exists(5, $parts_array[$i]))
	    $data5[$i] = trim($parts_array[$i][5]);
		if(array_key_exists(6, $parts_array[$i]))
	    $data6[$i] = trim($parts_array[$i][6]);
		if(array_key_exists(7, $parts_array[$i]))
	    $data7[$i] = trim($parts_array[$i][7]);
		if(array_key_exists(8, $parts_array[$i]))
	    $data8[$i] = trim($parts_array[$i][8]);
		if(array_key_exists(9, $parts_array[$i]))
	    $data9[$i] = trim($parts_array[$i][9]);
		if(array_key_exists(10, $parts_array[$i]))
	    $data10[$i] = trim($parts_array[$i][10]);
		if(array_key_exists(11, $parts_array[$i]))
	    $data11[$i] = trim($parts_array[$i][11]);
		if(array_key_exists(12, $parts_array[$i]))
	    $data12[$i] = trim($parts_array[$i][12]);
		if(array_key_exists(13, $parts_array[$i]))
	    $data13[$i] = trim($parts_array[$i][13]);
	}
	if(!is_array($object))
	  throw new Exception(_("You didn't provide a valid CSV file!"));
	else
	{ $object=array_values($object);
		$objectsMissing = array();
	  $j=0;                                                                         // Test if the objects, locations and instruments are available in the database
	  for($i=0;$i<count($parts_array);$i++)
	  { $_GET['object']=$object[$i];
			if ($instruction[$i] == "NO")
	  	  $objObject->addDSObject($object[$i], $cat[$i], $catindex_data[$i], "", "", 0, 0, "99.9", "99.9", "0", "0", "999", $loggedUser);
	    elseif ($instruction[$i] == "NOC")
	  	  $objObject->addDSObject($object[$i], $cat[$i], $catindex_data[$i], $data4[$i], $data5[$i], $data6[$i], $data7[$i], $data8[$i], $data9[$i], $data10[$i], $data11[$i], $data12[$i], $loggedUser);
	  	elseif ($instruction[$i] == "AN")
	  		$objObject->newAltName($object[$i], $cat[$i], $catindex_data[$i]);
	  	elseif ($instruction[$i] == "NN")
	  	{ $objObject->newName($object[$i], $cat[$i],$catindex_data[$i]);
	    }
	  	elseif ($instruction[$i] == "RAN")
	  	  $objObject->removeAltName($object[$i], $cat[$i], $catindex_data[$i]);
	  	elseif ($instruction[$i] == "PO")
	  	  $objObject->newPartOf($object[$i], $cat[$i], $catindex_data[$i]);
	  	elseif ($instruction[$i] == "RPO")
	  	  $objObject->removePartOf($object[$i], $cat[$i], $catindex_data[$i]);
	  	elseif ($instruction[$i] == "RRO")
	    { $objObject->removeAndReplaceObjectBy($object[$i], $cat[$i], $catindex_data[$i]);
	  	}
	  	elseif ($instruction[$i] == "RA")
	  	{	$objObject->setDsoProperty($object[$i],'ra', $catindex_data[$i]);
	  	  $objObject->setDsObjectAtlasPages($object[$i]);
	  	}
	  	elseif ($instruction[$i] == "DE")
	  	{ $objObject->setDsoProperty($object[$i],'decl', $catindex_data[$i]);
	  	  $objObject->setDsObjectAtlasPages($object[$i]);
	  	}
        elseif ($instruction[$i] == "CON") {
	  	  $objObject->setDsoProperty($object[$i],'con', $catindex_data[$i]);
        } elseif ($instruction[$i] == "TYP")
	  	  $objObject->setDsoProperty($object[$i],'type', $catindex_data[$i]);
	  	elseif ($instruction[$i] == "MG")
	  	{ $objObject->setDsoProperty($object[$i],'mag', $catindex_data[$i]);
	  	  $objObject->setDsObjectSBObj($object[$i]);
	  	}
	  	elseif ($instruction[$i] == "SB")
	  	  $objObject->setDsoProperty($object[$i],'subr', $catindex_data[$i]);
	  	elseif ($instruction[$i] == "D1")
	  	{ $objObject->setDsoProperty($object[$i],'diam1', $catindex_data[$i]);
	  	  $objObject->setDsObjectSBObj($object[$i]);
	  	}
	  	elseif ($instruction[$i] == "D2")
	  	{ $objObject->setDsoProperty($object[$i],'diam2', $catindex_data[$i]);
	  	  $objObject->setDsObjectSBObj($object[$i]);
	  	}
	  	elseif ($instruction[$i] == "PA")
	  		$objObject->setDsoProperty($object[$i],'pa', $catindex_data[$i]);
	  	elseif ($instruction[$i] == "DESC")
	  		$objObject->setDsoProperty($object[$i],'description', $catindex_data[$i]);
	  }
	}
}
?>

<?php
/*
==================================================================

	Copyright (c) 2014 Marc Augier

	The full license can be read in "LICENSE".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 3
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: m.augier@me.com
================================================================
*/

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <meta charset="utf-8">
<title>todo</title>
<style type="text/css">
body {
border:0; margin:10px; padding:0; 
background:#F2F5FE;
font:110%/160% "verdana",sans-serif; color:#192666; _text-align:center;
}

a.prio {
color:red;
text-decoration: none;
}

a.date {
color:green;
text-decoration: none;
}

a.project {
color:blue;
text-decoration: none;
}

a.context {
color:orange;
text-decoration: none;
}

a.prio {
color:red;
text-decoration: none;
}
</style>
</head>
<body>
<?php
/*

*/

$filename = "todo.txt";

echo "<h1>todo.txt</h1>\n\n";

if (is_readable($filename)) {
$handle = fopen($filename, "r");
if ($handle) {
	$i = 0;
    while (($buffer = fgets($handle, 4096)) !== false) {
    	$buffer = trim($buffer);
    	$i++;
    	// Chercher priorité
    	if (substr($buffer,0,1) == "(") {
    		$prio[] = substr($buffer,1,1);
        	$buffer = trim(substr($buffer,3));
        } else {
    		$prio[] = "Z";
        }
        // Chercher date
    	if (substr($buffer,0,3) == "201") {
	   		$date[] = substr($buffer,0,10);
    	   	$buffer = trim(substr($buffer,11));
        } else {
	   		$date[] = "";
    	}
        // Chercher +
        $tmp1 = "";
        while ($tmp = getLabel("+", $buffer)) {
			$tmp1 .= $tmp." ";
   			$buffer = str_replace($tmp,"",$buffer);
        }
   		$project[] = $tmp1;
        // Chercher @
        $tmp1 = "";
        while ($tmp = getLabel("@", $buffer)) {
			$tmp1 .= $tmp." ";
   			$buffer = str_replace($tmp,"",$buffer); 
        }
   		$context[] = $tmp1;
        // Chercher due:
        // Chercher p:
		$line[] = $buffer;
    }
    $lastI = $i;
    echo "$lastI entries in your todo list<br/>";

	$cmd = $_GET['filter'];	
	switch ($cmd) {
		case "prio":
		    array_multisort ($prio, SORT_ASC, $date, $project, $context, $line );
		break;
		case "date":
		    array_multisort ($date, SORT_REGULAR, $prio, $project, $context, $line );
		break;
		default:
			$tmp = $cmd{0};
			switch ($tmp) {
			case "+":
			    array_multisort ($project, SORT_REGULAR, $prio, $date, $context, $line );
			break;
			case "@":
			    array_multisort ($context, SORT_REGULAR, $prio, $date, $project,  $line );
			break;
			default:
			}
	}

    for ($i=0;$i<$lastI;$i++) {
    	echo setLink("prio","prio",$prio[$i])." ".setLink("date","date",$date[$i])." ".setLink("project", $project[$i], $project[$i])." ".setLink("context", $context[$i], $context[$i])." ".$line[$i]."<br/>";
    }
    
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}
} else {
    echo 'The file is not readable';
}

/*

	Functions

*/
function setLink($id, $cmd, $label)
{
	return "<a href='?filter=$cmd' class='$id'>$label</a>";
}

function getLabel($tmp, $buffer)
{
	if ($pos1 = strpos($buffer, $tmp)) {
    	if ($pos2 = strpos($buffer, " ", $pos1)) {
        	$project = substr($buffer,$pos1, $pos2-$pos1);
        } else {
        	$project = substr($buffer,$pos1);
        }
	}
	return $project;
}

?>
</body>
</html>

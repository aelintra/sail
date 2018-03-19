<?php
//
// Developed by CoCo
// Copyright (C) 2012 CoCoSoft
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//

// take pre 5.0 sark rules and format to suit

$OUT = NULL;
$file = "/etc/shorewall/sark_rules";

	if (!file_exists($file)) {
		die ("No sark rules found");
	}

	$rec = file($file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) or die('Could not read file!');
	foreach ($rec as $row) {
		if (preg_match(" /^#|^\s*$/ ", $row)) {
			continue;
		}
		if (preg_match(" /#/ ", $row)) {
			$splitComments = explode("#",$row,2);
			$cols = explode(" ",$splitComments[0]);
		}
		else {
			$cols = explode(" ",$row);
		}
		if (empty($cols[5])) {
			$cols[5] = '-';
		}
		if (empty($cols[6])) {
			$cols[6] = '-';
		}
		$nl = implode(" ",$cols);
		$nl = trim($nl);
		$OUT .= $nl;
		
		if (!empty($splitComments[1])) {			
			$OUT .= ' # ' . trim($splitComments[1]);
		} 
		$OUT .= "\n";
		unset ($cols);	
		unset ($splitComments);	
	}
	
	$fh = fopen($file, 'w') or die('Could not open file!');
	fwrite($fh, $OUT) 
		or die('Could not write to file');
	fclose($fh);	
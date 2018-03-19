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


Class sarkwallboard {
	

	protected $myPanel;
	protected $dbh;
	protected $helper;

	
public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
			
	echo '<form id="sarkwallboardForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Call Activity';
			
/* 
 * start page output
 */
	echo '<br/>' . PHP_EOL;
	echo '<div class="titlebar">' . PHP_EOL;
	echo '<div class="buttons">' . PHP_EOL;
	echo '</div>' . PHP_EOL;
	
	$this->myPanel->Heading();

	echo '</div>' . PHP_EOL;


/*
 *  Print short System status box
 */
	$this->myPanel->printSysNotes(true);
	
/*
 *  Start the iFrame
 */
	echo '<div class="statustabedit">' . PHP_EOL;
	echo '<iframe id="statusframe" src="iframeChannels.php" ></iframe>' . PHP_EOL;
	echo '</div>' . PHP_EOL;

/*
 * done
 */

    echo '</div>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
    
	
	return;
	
}
	

}

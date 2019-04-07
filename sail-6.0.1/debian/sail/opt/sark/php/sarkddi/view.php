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


Class sarkddi {
	
	protected $message; 
	protected $head = "DiD's";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $span = 1;
	protected $smartlink;
	protected $myBooleans = array(
		'active',
		'moh',		
		'swoclip'	
	);

public function showForm() {
		
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	
//	$this->myPanel->pagename = 'Inbound Routing';
	
	if (isset($_POST['new']) || isset ($_GET['new'])  ) { 
		$this->showNew();
		return;		
	}
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}	
	
	if (isset($_POST['save']) || isset($_POST['endsave'])) {
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}					
	}
	
	if (isset($_POST['update']) || isset($_POST['endupdate'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showEdit();
			return;
		}					
	}

	if (isset($_POST['commit']) || isset($_POST['commitClick'])) { 
		$this->helper->sysCommit();
		$this->message = " - Committed!";	
	}
		
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	

private function showMain() {
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
/* 
 * start page output
 */
 
/*
	if ( $_SESSION['user']['pkey'] == 'admin' ) {
		echo '<a  href="/php/downloadpdf.php?pdf=ddi"><img id="pdfprint" src="/sark-common/buttons/print.png" border=0 title = "Click to Download PDF" ></a>' . PHP_EOL;									
	}
*/

	$buttonArray['new'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkddiForm",false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);
//	$this->myPanel->subjectBar("DDI's");

	echo '<form id="sarkddiForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->beginResponsiveTable('trunktable',' w3-tiny');	
	
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	
	$this->myPanel->aHeaderFor('DiD/CLI'); 
//	$this->myPanel->aHeaderFor('carriertype',false,'w3-hide-small w3-hide-medium');	
	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('trunkname',false,'w3-hide-small ');	
	$this->myPanel->aHeaderFor('openroute',false,'w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('closeroute',false,'w3-hide-small w3-hide-medium');
//	$this->myPanel->aHeaderFor('tag',false,'w3-hide-small w3-hide-medium');
//	$this->myPanel->aHeaderFor('swoclip',false,'w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('Active?',false,'w3-hide-small');		
	$this->myPanel->aHeaderFor('ed',false,'editcol');
	$this->myPanel->aHeaderFor('del',false,'delcol');		
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/
			
	$sql = "select li.pkey,cluster,trunkname,openroute,closeroute,peername,routeclassopen,routeclassclosed,swoclip,tag,active,ca.technology,ca.carriertype " . 
			"from lineio li inner join carrier ca  on li.carrier=ca.pkey";				
	$rows = $this->helper->getTable("lineio", $sql,true,false,'li.pkey');
	foreach ($rows as $row ) {
		if ($row['carriertype'] != 'DiD' && $row['carriertype'] != 'CLID' && $row['carriertype'] != 'Class' ) {
			continue;
		}

		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
	
		echo '</td>' . PHP_EOL;
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;
//		echo '<td class="read_only w3-hide-small  w3-hide-medium">' . $row['carriertype'] . '</td>' . PHP_EOL;		
		echo '<td class="w3-hide-small  w3-hide-medium">' . $row['cluster'] . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small  ">' . $row['trunkname'] . '</td>' . PHP_EOL;
		
				
		$searchkey = $row['peername'];

/*
 * asterisk will only return a maximum 15 chars in the peername so
 * we truncate the searchkey to 15
 */ 
		if (strlen($row['peername']) > 15) {
			$searchkey = substr($row['peername'], 0, 15);
		}		
		
		$this->helper->pkey = $row['openroute'];
		echo '<td class="w3-hide-small w3-hide-medium">' . $this->helper->displayRouteClass($row['routeclassopen']) . '</td>' . PHP_EOL;
		$this->helper->pkey = $row['closeroute'];
		echo '<td class="w3-hide-small w3-hide-medium">' . $this->helper->displayRouteClass($row['routeclassclosed']) . '</td>' . PHP_EOL;
//		echo '<td class="w3-hide-small  w3-hide-medium">' . $row['tag'] . '</td>' . PHP_EOL;	
//		echo '<td class="w3-hide-small  w3-hide-medium">' . $row['swoclip'] . '</td>' . PHP_EOL;	
		echo '<td class="w3-hide-small">' . $row['active'] . '</td>' . PHP_EOL;
		$get = '?edit=yes&amp;pkey=';
		$get .= urlencode($row['pkey']);	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);	
		echo '</tr>'. PHP_EOL;

	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();
	
}

private function showNew() {
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkddiForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("New DiD");

	echo '<form id="sarkddiForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
		
	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = 'default';
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';

		
//	$this->myPanel->aLabelFor('Route Type');
//	$this->myPanel->popUp('chooser', array('Choose a route type','DiD','CLID'));
	$this->myPanel->displayPopupFor('chooserDiD','Choose a route type',Array('Choose a route type','DiD','CLID')); 


/*
 * Trunk variables - they will be hidden/revealed according to the chooser dropdown
 */	
	echo '<div id="divtrunkname">' . PHP_EOL;
	$this->myPanel->displayInputFor('trunkname','text');
	echo '</div>' . PHP_EOL;

	echo '<div id="divdidnumber">' . PHP_EOL;
//	$this->myPanel->aLabelFor('remotenum');
	$this->myPanel->displayInputFor('didnumber','text');
//	echo '<input type="text" name="didnumber" id="didnumber" size="25"  />' . PHP_EOL;
	echo '</div>' . PHP_EOL;
	
	echo '<div id="divclinumber">' . PHP_EOL;
//	$this->myPanel->aLabelFor('clidstart');
//	echo '<input type="text" name="clinumber" id="clinumber" size="25"  />' . PHP_EOL;
	$this->myPanel->displayInputFor('clinumber','text');
	echo '</div>' . PHP_EOL;	
/*	
	echo '<div id="divsmartlink">' . PHP_EOL;		
	$this->myPanel->aLabelFor('smartlink');
	$this->myPanel->popUp('smartlink', array('NO','YES'));
	echo '</div>' . PHP_EOL;	
*/							
	echo '<input type="hidden" id="carrier" name="carrier" value="" />' . PHP_EOL; 
	echo '</div>';
	
	$endButtonArray['cancel'] = "cancel";
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;	
	$this->myPanel->responsiveClose();
}

private function saveNew() {
// save the data away

	$tuple = array();
	$this->myPanel->xlateBooleans($this->myBooleans);
/*
 * call the correct routine to prepare the record array
 */ 	
	switch ($_POST['carrier']) {
		case "DiD":
			$this->saveDiD($tuple);
			break;	
		case "CLID":
			$this->saveCLI($tuple);
			break;	
		default: 
			return;						
	}
/*
 * call the creator routine and process any returned error
 */ 
 	if ($this->invalidForm != True) {
/*
 * save a couple of I/Os if smartlink isn't being used
 */ 
		if ($this->smartlink) {
			$res = $this->dbh->query("SELECT EXTLEN FROM globals WHERE pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
			$extlen = $res['EXTLEN'];
			$user = $this->dbh->query("SELECT cluster FROM user WHERE pkey = '" . $_SESSION['user']['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);
		}
		
		$countkey = $tuple['pkey'];
		for ($i = 0; $i < $this->span; $i++) {	
			if ($this->smartlink) {
				$extkey = substr($tuple['pkey'],$extlen * -1);
				$extfetch = $this->dbh->query("SELECT pkey,cluster FROM ipphone WHERE pkey = '" . $extkey . "'")->fetch(PDO::FETCH_ASSOC);
				$tuple['openroute'] = 'Operator';
				$tuple['routeclassopen'] = 100;
				$tuple['closeroute'] = 'Operator';
				$tuple['routeclassclosed'] = 100;				
				if (isset($extfetch['pkey'])) {
					if ( $user['cluster'] ==  $extfetch['cluster'] || $_SESSION['user']['pkey'] == 'admin' ) {
						$tuple['openroute'] = $extkey;
						$tuple['routeclassopen'] = $this->helper->setRouteClass($tuple['openroute']);
						$tuple['closeroute'] = $extkey;
						$tuple['routeclassclosed'] = $this->helper->setRouteClass($tuple['closeroute']);
					}
				}				
			}										
			$ret = $this->helper->createTuple("lineio",$tuple);
			if ($ret != 'OK') {
				break;
			}
/*
 * increment the DiD number without losing any leading zeros;
 */			
			$countkey++;
			$tuple['pkey'] = str_pad($countkey,strlen($tuple['pkey']),0,STR_PAD_LEFT);
		}	
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = " - Saved";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!(3)</B>";	
			$this->error_hash[trunk] = $ret;	
		}
	}	
}


private function saveDiD(&$tuple) {
	$this->validator = new FormValidator();
	
//	$this->validator->addValidation("trunkname","req","No trunk name");
	$this->validator->addValidation("didnumber","req","No did number");
//	$this->validator->addValidation("didnumber","regexp=/^_.*\//","DiD cannot have both class and span (i.e. contain both _ and /"); 
	
	
	if (preg_match(' /^_.*\// ',$_POST['didnumber'])) { 
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!(1)</B>";	
			$this->error_hash['DiD'] = "$didnumber DiD cannot have both class and span (i.e. contain both _ and /";
	}
	$didnumber = strip_tags($_POST['didnumber']);
	$lines = explode("/",$didnumber);
	$tuple['pkey'] = $lines[0];
	if (isset($lines[1])) {
		if (is_numeric($lines[0])) {
			if (is_numeric($lines[1])) {
				$this->span = $lines[1];
				if ($this->span > 100) {
					$this->invalidForm = True;
					$this->message = "<B>  --  Validation Errors!(1)</B>";	
					$this->error_hash['DiD'] = "$didnumber - Cannot create more than 100 DiD Numbers";
				}
			}
			else {
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!(1)</B>";	
				$this->error_hash['DiD'] = "$didnumber - Span (/nnn) must be numeric for DiD range";
			}							
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!(1)</B>";	
			$this->error_hash['DiD'] = "$didnumber - DiD must be numeric for DiD range";
		}			
	}	
		
	if ($this->validator->ValidateForm()) {

		if (isset($_POST['cluster'])) {
			$tuple['cluster']		= $_POST['cluster'];
		}			
		$tuple['trunkname'] 	= strip_tags($_POST['trunkname']);
		$tuple['carrier']		= $_POST['carrier'];	
		$tuple['technology']	= $_POST['carrier'];
		if (strip_tags($_POST['smartlink']) == "YES") {
			$this->smartlink = True;
		}							
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!(2)</B>";		
    }
    unset ($this->validator);    
}

private function saveCLI(&$tuple) {

	$this->validator = new FormValidator();
	$this->validator->addValidation("clinumber","req","No cli number");
	$this->validator->addValidation("trunkname","req","No trunkname");
	
	if ($this->validator->ValidateForm()) {
		$tuple['pkey'] 			= strip_tags($_POST['clinumber']);
		if (isset($_POST['cluster'])) {
			$tuple['cluster']		= $_POST['cluster'];
		}		
		$tuple['trunkname'] 	= strip_tags($_POST['trunkname']);
		$tuple['carrier']		= $_POST['carrier'];	
		$tuple['technology']	= $_POST['carrier'];		
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);    
}

private function showEdit() {
/*
 * General update page.  Jquery controls tabs
 */
  
	$pkey = $_GET['pkey']; 
	$tuple = $this->dbh->query("select li.*,ca.carriertype from lineio li inner join Carrier ca on li.carrier = ca.pkey where li.pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);
	
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkddiForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar($tuple['technology'] . " " . $tuple['pkey']);

	echo '<form id="sarkddiForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

/*
 * trunk container
 */ 	
 	$this->myPanel->displayBooleanFor('active',$tuple['active']);
 	$this->myPanel->displayInputFor('trunkname','text',$tuple['trunkname']); 
 	$this->myPanel->displayInputFor('description','text',$tuple['description']); 
	
	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $tuple['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';

	$this->myPanel->subjectBar("Routing");
	if ( $tuple['technology'] != 'CLID' ) {	
//		$this->myPanel->aLabelFor('swoclip');
//		$this->myPanel->selected = $tuple['swoclip'];
//		$this->myPanel->popUp('swoclip', array('NO','YES'));
		$this->myPanel->displayBooleanFor('swoclip',$tuple['swoclip']);
	}	

//	if ( $tuple['routeable'] == 'YES' )  {	
		echo '<div class="w3-margin-bottom">';
		$this->myPanel->aLabelFor('Open Inbound Route');
		echo '</div>'; 	
		$this->myPanel->selected = $tuple['openroute'];
		$this->myPanel->sysSelect('openroute',false,false,true) . PHP_EOL;
		$this->myPanel->aHelpBoxFor('openroute');

		echo '<div class="w3-margin-bottom">';
		$this->myPanel->aLabelFor('Closed Inbound Route');
		echo '</div>';
		$this->myPanel->selected = $tuple['closeroute'];
		$this->myPanel->sysSelect('closeroute',false,false,true) . PHP_EOL;
		$this->myPanel->aHelpBoxFor('closeroute');	
//	} 

    $this->myPanel->subjectBar("Line Settings");
    $this->myPanel->displayInputFor('tag','text',$tuple['tag']); 
	$this->myPanel->displayInputFor('inprefix','text',$tuple['inprefix']);   
	$this->myPanel->displayBooleanFor('moh',$tuple['moh']);	
	$this->myPanel->displayInputFor('disapass','text',$tuple['disapass']);
	$this->myPanel->displayInputFor('alertinfo','text',$tuple['alertinfo']);

	echo '</div>';	    			  	 	
	
	echo '<input type="hidden" name="pkey" id="pkey" size="20"  value="' . $tuple['pkey'] . '"  />' . PHP_EOL; 

	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";	
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;
	$this->myPanel->responsiveClose();
}



private function saveEdit() {
/*
 * save data from an update(edit)
 */ 
 		$this->myPanel->xlateBooleans($this->myBooleans);
/*
 * 	call the tuple builder to create a table row array 
 */  	
		$this->helper->buildTupleArray($_POST,$tuple);

/*
 * update routeclass
 */ 
	if (array_key_exists('openroute',$tuple)) {
		 $tuple['routeclassopen'] = $this->helper->setRouteClass($tuple['openroute']);
	}
	if (array_key_exists('closeroute',$tuple)) {
		 $tuple['routeclassclosed'] = $this->helper->setRouteClass($tuple['closeroute']);
	}
/*
 * call the setter
 */ 
	$ret = $this->helper->setTuple("lineio",$tuple);
/*
 * flag errors
 */ 	
	if ($ret == 'OK') {
//		$this->helper->commitOn();	
		$this->message = "Saved!";
	}
	else {
		$this->invalidForm = True;
		$this->message = "<B>  --  Validation Errors!</B>";	
		$this->error_hash[trunk] = $ret;	
	}	
}


}

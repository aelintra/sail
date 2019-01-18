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
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $span = 1;
	protected $smartlink;
	
public function showForm() {
		
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	
	echo '<form id="sarkddiForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Inbound Routing';
	
	if (isset($_POST['new_x']) || isset ($_GET['new'])  ) { 
		$this->showNew();
		return;		
	}
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}	
	
	if (isset($_POST['save_x'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}					
	}
	
	if (isset($_POST['update_x'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showEdit();
			return;
		}					
	}

	if (isset($_POST['commit_x']) || isset($_POST['commitClick_x'])) { 
		$this->helper->sysCommit();
		$this->message = "Updates have been Committed";	
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
 
	echo '<div class="titlebar">' . PHP_EOL;
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
	$this->myPanel->commitButton();
	if ( $_SESSION['user']['pkey'] == 'admin' ) {
		echo '<a  href="/php/downloadpdf.php?pdf=ddi"><img id="pdfprint" src="/sark-common/buttons/print.png" border=0 title = "Click to Download PDF" ></a>' . PHP_EOL;									
	}
	echo '</div>';	
	
	$this->myPanel->Heading();
	echo '</div>';	
		
	echo '<div class="datadivwide">';

	echo '<table class="display" id="trunktable">' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('DiD/CLI'); 
	$this->myPanel->aHeaderFor('carriertype');	
	$this->myPanel->aHeaderFor('cluster');
	$this->myPanel->aHeaderFor('trunkname');	
	$this->myPanel->aHeaderFor('openroute');
	$this->myPanel->aHeaderFor('closeroute');
	$this->myPanel->aHeaderFor('tag');
	$this->myPanel->aHeaderFor('swoclip');
	$this->myPanel->aHeaderFor('Act');		
	$this->myPanel->aHeaderFor('ed');
	$this->myPanel->aHeaderFor('del');	

	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/
/*	
	$sql = "select li.pkey,cluster,trunkname,openroute,closeroute,peername,routeclassopen,routeclassclosed,swoclip,active,ca.technology,ca.carriertype " . 
			"from lineio li inner join carrier ca  on li.carrier=ca.pkey WHERE " . 
			"ca.carriertype = 'DiD' OR ca.carriertype = 'CLID' or ca.carriertype = 'Class'";
*/			
	$sql = "select li.pkey,cluster,trunkname,openroute,closeroute,peername,routeclassopen,routeclassclosed,swoclip,tag,active,ca.technology,ca.carriertype " . 
			"from lineio li inner join carrier ca  on li.carrier=ca.pkey";				
	$rows = $this->helper->getTable("lineio", $sql);
	foreach ($rows as $row ) {
		if ($row['carriertype'] != 'DiD' && $row['carriertype'] != 'CLID' && $row['carriertype'] != 'Class' ) {
			continue;
		}

		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;
		echo '<td class="read_only">' . $row['carriertype'] . '</td>' . PHP_EOL;		
		echo '<td >' . $row['cluster'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['trunkname'] . '</td>' . PHP_EOL;
		
				
		$searchkey = $row['peername'];

/*
 * asterisk will only return a maximum 15 chars in the peername so
 * we truncate the searchkey to 15
 */ 
		if (strlen($row['peername']) > 15) {
			$searchkey = substr($row['peername'], 0, 15);
		}		
		
		$this->helper->pkey = $row['openroute'];
		echo '<td >' . $this->helper->displayRouteClass($row['routeclassopen']) . '</td>' . PHP_EOL;
		$this->helper->pkey = $row['closeroute'];
		echo '<td >' . $this->helper->displayRouteClass($row['routeclassclosed']) . '</td>' . PHP_EOL;
		echo '<td >' . $row['tag'] . '</td>' . PHP_EOL;	
		echo '<td class="icons">' . $row['swoclip'] . '</td>' . PHP_EOL;	
		echo '<td class="icons">' . $row['active'] . '</td>' . PHP_EOL;
		$get = '?edit=yes&amp;pkey=';
		$get .= urlencode($row['pkey']);
	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$get = '?id=' . $row['pkey'];
		
		$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;

	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';
	
}

private function showNew() {
	
	$pika = false;
	
	$this->myPanel->msg .= "Add a DDI/CLI Route "; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}  
	echo '<div class="titlebar">' . PHP_EOL;
	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	$this->myPanel->Button("save");
	echo '</div>';			
	
	$this->myPanel->Heading();
//	if (isset($this->message)) {

	if (!empty($this->error_hash)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
	echo '</div>';
/*
 * trunk container
 */ 	
	echo '<div class="editinsert">';

/*
 * trunk control dropdown
 */ 
		

	$this->myPanel->aLabelFor('cluster','cluster');
	$this->myPanel->selected = 'default';
	$this->myPanel->displayCluster();

		
	$this->myPanel->aLabelFor('Route Type');
	$this->myPanel->popUp('chooser', array('Choose a route type','DiD','CLID'));

/*
 * Trunk variables - they will be hidden/revealed according to the chooser dropdown
 */	
	echo '<div id="divtrunkname">' . PHP_EOL;
	$this->myPanel->aLabelFor('trunkname');
	echo '<input type="text" name="trunkname" id="trunkname" size="10" placeholder="Name"  />' . PHP_EOL;
	echo '</div>' . PHP_EOL;

	echo '<div id="divdidnumber">' . PHP_EOL;
	$this->myPanel->aLabelFor('remotenum');
	echo '<input type="text" name="didnumber" id="didnumber" size="25"  />' . PHP_EOL;
	echo '</div>' . PHP_EOL;
	
	echo '<div id="divclinumber">' . PHP_EOL;
	$this->myPanel->aLabelFor('clidstart');
	echo '<input type="text" name="clinumber" id="clinumber" size="25"  />' . PHP_EOL;
	echo '</div>' . PHP_EOL;	
	
	echo '<div id="divsmartlink">' . PHP_EOL;		
	$this->myPanel->aLabelFor('smartlink');
	$this->myPanel->popUp('smartlink', array('NO','YES'));
	echo '</div>' . PHP_EOL;	
							
	echo '<input type="hidden" id="carrier" name="carrier" value="" />' . PHP_EOL; 

/*
 * trunk container end
 */ 
	echo '</div>' . PHP_EOL;
	

}

private function saveNew() {
// save the data away

	$tuple = array();
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
			$this->message = "Saved new DDI/CLI";
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
				$this->error_hash['DiD'] = "$didnumber - Span (/nnn) must be numeric for DDI range";
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
	
	$printline = $tuple['technology'] . "/" . $tuple['pkey'];
	$this->myPanel->msg .= $printline; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	} 	
	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	$this->myPanel->override="update";
	$this->myPanel->Button("save");
	echo '</div>';	
	
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
		
	echo '<div class="datadivtabedit">';
	
	echo '<div id="pagetabs" class="mytabs">' . PHP_EOL;
	echo '<ul>' . PHP_EOL;
	echo '<li><a href="#route">Routing</a></li>' . PHP_EOL;
	echo '<li><a href="#line" >Line</a></li>' . PHP_EOL;
    echo '</ul>' . PHP_EOL;	

#
#   TAB Route
#
    echo '<div id="route" >';
    
 	$this->myPanel->aLabelFor('trunkname');
	echo '<input type="text" name="trunkname" id="trunkname" size="20"  value="' . $tuple['trunkname'] . '"  />' . PHP_EOL;   
    
    $this->myPanel->aLabelFor('active');
    $this->myPanel->selected = $tuple['active'];
	$this->myPanel->popUp('active', array('NO','YES'));
	
    $this->myPanel->aLabelFor('cluster','cluster');
	$this->myPanel->selected = $tuple['cluster'];
	$this->myPanel->displayCluster();
/*	
    $this->myPanel->aLabelFor('faxonoff');
    $this->myPanel->selected = $tuple['faxdetect'];
	$this->myPanel->popUp('faxdetect', array('NO','YES'));
 */		
/*	
	if ( $tuple['technology'] != 'DiD' && $tuple['technology'] != 'Class' )  {
		$this->myPanel->aLabelFor('match');
		echo '<input type="text" name="match" id="match" size="3"  value="' . $tuple['match'] . '"  />' . PHP_EOL;
	}
*/
	if ( $tuple['technology'] != 'CLID' ) {	
		$this->myPanel->aLabelFor('swoclip');
		$this->myPanel->selected = $tuple['swoclip'];
		$this->myPanel->popUp('swoclip', array('NO','YES'));
	}	
/*		
    if ( $tuple['technology'] != 'DiD' && $tuple['technology'] != 'Class' && $tuple['pkey'] != 'Analog-In' )  {
		$this->myPanel->aLabelFor('transform');
		echo '<input type="text" name="transform" id="transform" size="10"  value="' . $tuple['transform'] . '"  />' . PHP_EOL;
	}
*/	
	if ( $tuple['routeable'] = 'YES' || $tuple['carriertype'] != 'group' )  {
		$this->myPanel->aLabelFor('openroute');
		$this->myPanel->selected = $tuple['openroute'];
		$this->myPanel->sysSelect('openroute',false,false,true) . PHP_EOL;
		
		$this->myPanel->aLabelFor('closeroute',false,false,true);
		$this->myPanel->selected = $tuple['closeroute'];
		$this->myPanel->sysSelect('closeroute') . PHP_EOL;	
	}    

	echo '</div>' . PHP_EOL;
	
#
#   TAB Line
#
    echo '<div id="line" >';
    
    $this->myPanel->aLabelFor('tag');
	echo '<input type="text" name="tag" id="tag" size="20"  value="' . $tuple['tag'] . '"  />' . PHP_EOL;
    
    $this->myPanel->aLabelFor('inprefix');
	echo '<input type="text" name="inprefix" id="tag" size="1"  value="' . $tuple['inprefix'] . '"  />' . PHP_EOL;   
	
	$this->myPanel->aLabelFor('moh');
    $this->myPanel->selected = $tuple['moh'];
	$this->myPanel->popUp('moh', array('NO','YES'));	
	
    $this->myPanel->aLabelFor('disapass');
	echo '<input type="text" name="disapass" id="tag" size="4"  value="' . $tuple['disapass'] . '"  />' . PHP_EOL; 
	
	$this->myPanel->aLabelFor('alertinfo');
	echo '<input type="text" name="alertinfo" id="alertinfo" size="20"  value="' . $tuple['alertinfo'] . '"  />' . PHP_EOL;
				  	 	
    echo '</div>' . PHP_EOL;	

	echo '<input type="hidden" name="pkey" id="pkey" size="20"  value="' . $tuple['pkey'] . '"  />' . PHP_EOL; 
	
	echo '</div>' . PHP_EOL;

}

private function saveEdit() {
/*
 * save data from an update(edit)
 */ 
 	
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

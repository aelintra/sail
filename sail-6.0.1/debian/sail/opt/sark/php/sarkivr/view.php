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


Class sarkivr {
	
	protected $message; 
	protected $head="IVRs";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $greetings = array();
	protected $soundir = '/usr/share/asterisk/sounds/'; // set for Debian/Ubuntu	

function __construct() {

	if ($handle = opendir($this->soundir)) {
		while (false !== ($entry = readdir($handle))) {
			if (preg_match("/^usergreeting(\d*)/",$entry,$matches)) {
				array_push($this->greetings, $matches[1]);
			}
		}
		closedir($handle);
		asort($this->greetings);
	}

}

public function showForm() {
//print_r($_POST);	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
			

	$this->myPanel->pagename = 'IVRs';

	if ( isset($_POST['new']) || isset($_GET['new'] )) { 
		$this->showNew();
		return;
	}
	
	if (isset($_POST['update']) || isset($_POST['endupdate'])) { 
		$this->showEdit();	
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

	if (isset($_POST['commit']) || isset($_POST['commitClick'])) { 
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
  
	$buttonArray['new'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkivrForm",false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);
//	$this->myPanel->subjectBar("DDI's");

	echo '<form id="sarkivrForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->beginResponsiveTable('ivrtable',' w3-tiny');	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('ivrname');
	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-small w3-hide-medium');	
	$this->myPanel->aHeaderFor('greeting',false,'w3-hide-small');	
	$this->myPanel->aHeaderFor('description',false,'w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('timeout',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('listenforext',false,'w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('ed',false,'editcol');
	$this->myPanel->aHeaderFor('del',false,'delcol');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("ivrmenu");
	foreach ($rows as $row ) { 
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;			
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['cluster']  . '</td>' . PHP_EOL;		 
		echo '<td class="w3-hide-small">' . $row['greetnum']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['description']  . '</td>' . PHP_EOL;	
		echo '<td class="w3-hide-small">' . $row['timeout']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['listenforext']  . '</td>' . PHP_EOL;	
		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();
		
}

private function showNew() {
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkivrForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("New IVR");

	echo '<form id="sarkivrForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
		
    
	$this->myPanel->displayInputFor('ivrname','text',null,'pkey');
//    $this->myPanel->aLabelFor('ivrname'); 		
//	echo '<input type="text" name="pkey" size="20" id="pkey" placeholder="enter IVR name"/>' . PHP_EOL;			


	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $tuple['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';

	$this->myPanel->displayInputFor('description','text');
//	$this->myPanel->aLabelFor('description');
//	echo '<input type="test" name="description" id="description" size="30" placeholder="enter description"  />' . PHP_EOL;	


	echo '</div>';
	$endButtonArray['cancel'] = true;
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;	
		
	$this->myPanel->responsiveClose();
}

private function saveNew() {
// save the data away
	$tuple = array();
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in IVR name");
    
    //Now, validate the form
    if ($this->validator->ValidateForm()) {

/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);			   
		$ret = $this->helper->createTuple("ivrmenu",$tuple);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = "Saved new IVR " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['ivrinsert'] = $ret;	
		}		
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
	
}

private function showEdit($pkey=false) {
	
/*
 * build navigation arrays (emulate perl's qw, using explode)  
 */
    $tableoffkey = explode(' ','1-OFF.jpg 2-OFF.jpg 3-OFF.jpg 4-OFF.jpg 5-OFF.jpg 6-OFF.jpg 7-OFF.jpg 8-OFF.jpg 9-OFF.jpg 10-OFF.jpg 11-OFF.jpg 12-OFF.jpg'); 
	$tableonkey = explode(' ','1-on.jpg 2-on.jpg 3-on.jpg 4-on.jpg 5-on.jpg 6-on.jpg 7-on.jpg 8-on.jpg 9-on.jpg 10-on.jpg 11-on.jpg 12-on.jpg'); 
    $tabnavkey = explode(' ','1 2 3 4 5 6 7 8 9 10 0 11'); 	
	$printkey = explode (' ','0 1 2 3 4 5 6 7 8 9 * #');
	
/*
 * get a list of greeting numbers
 */

/*
	$greetings = array();
	$root = $this->soundir;
	$dir = "";
*/
/*
	$user =  $_SESSION['user']['pkey'];
	if ($_SESSION['user']['pkey'] != 'admin') {
		$res = $this->dbh->query("SELECT cluster FROM user where pkey = '" . $_SESSION['user']['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);
		if 	(array_key_exists('cluster',$res)) {
			$dir = $res['cluster'] . "/";
		}
	}
*/
/*	
	$search = $root . "/" . $dir;
	if ($handle = opendir($search)) {
		while (false !== ($entry = readdir($handle))) {
			if (preg_match("/^usergreeting(\d*)/",$entry,$matches)) {
				array_push($greetings, $matches[1]);
			}
		}
		closedir($handle);
		asort($greetings);
	}
*/								   	

/*
 * pkey could be POST or GET, depending upon the iteration
 */	
	if (!$pkey) {
		if (isset ($_GET['pkey'])) {
			$pkey = $_GET['pkey']; 
		}
	
		if (isset ($_POST['pkey'])) {		
			$pkey = $_POST['pkey']; 
			$this->saveEdit();
		}
	}
	
	$ivrmenu = $this->dbh->query("SELECT * FROM ivrmenu WHERE pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkivrForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Edit IVR " . $pkey);

	echo '<form id="sarkivrForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

    
//    $this->myPanel->aLabelFor('ivrname'); 		
	echo '<input type="hidden" name="newkey" size="20" id="newkey" value="' . $pkey . '"  />' . PHP_EOL;

	$this->myPanel->internalEditBoxStart();

	if (!empty($this->greetings)) {
		$this->myPanel->aLabelFor('greeting'); 	
		echo '<br/><br/>';
		$this->myPanel->selected = $ivrmenu['greetnum'];
		$this->myPanel->popUp('greetnum',$this->greetings);
	}
	echo '<br/><br/>';
	
//	echo '<div class="w3-container">';
//	echo '<div class="w3-margin">';
	$this->myPanel->aLabelFor('Action on IVR Timeout');
	echo '<br/><br/>';
//	echo '</div>'; 
	$this->myPanel->selected = $ivrmenu['timeout'];
	$this->myPanel->sysSelect('timeout',false,false,true) . PHP_EOL;
	echo '<br/><br/>';

	$this->myPanel->displayBooleanFor('listenforext',$ivrmenu['listenforext']);
	$this->myPanel->displayInputFor('description','text',$ivrmenu['description']);
	
//	echo '</div>';
	echo '</div>';
	echo '<br/>';
    $this->myPanel->subjectBar("IVR Key Settings");

    $this->myPanel->aHelpBoxFor('ivrHelp');

    $key=0;
    $limit = 12;
    while ($key < $limit) {

		echo '<br/>' . PHP_EOL;
//		echo '<div class = "touchkeys"  id="key'.$key.'">' . PHP_EOL;

		$opName = "option".$key;

		$phoneKeyStyle = null;
    
    	if  ($ivrmenu[$opName] != 'None') {
    		$state = 'YES';
    	}
    	else {
    		$state = 'NO';
    		$phoneKeyStyle = 'style="display:none"';
    	}

    	$this->myPanel->internalEditBoxStart();

//    	$this->myPanel->aLabelFor('KEY ' . $printkey[$key]);

		$this->myPanel->displayBooleanFor('PhoneKey' . $printkey[$key],$state,'ivrBoolean' .$key);

		echo '<div class="w3-container ivrBoolean' . $key . '" ' . $phoneKeyStyle . '>';

//		$this->myPanel->aLabelFor('Action on Keypress');
//		echo '<br/><br/>'  . PHP_EOL;


		echo '<div class="w3-margin">';
		$this->myPanel->aLabelFor('Action on Keypress');
		echo '</div>'; 

		$this->myPanel->selected = $ivrmenu[$opName];
		$this->myPanel->sysSelect($opName,true) . PHP_EOL;

		$tagindex = "tag" . $key;
		$alertindex = "alert" . $key;
		echo '<br/><br/>' . PHP_EOL;
		$this->myPanel->displayInputFor($tagindex,'text',$ivrmenu[$tagindex] );
		$this->myPanel->displayInputFor($alertindex,'text',$ivrmenu[$alertindex]);
		$key++;
		echo '</div>' . PHP_EOL;
		echo '</div>' . PHP_EOL;
	
    }
        
#
#       TAB DIVEND
#    

#
#  end of TABS DIV
#
   			  	 	
	echo '</div>' . PHP_EOL;
	echo '<input type="hidden" name="pkey" id="pkey" size="20"  value="' . $pkey . '"  />' . PHP_EOL; 

	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";	
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;	
	$this->myPanel->responsiveClose();
}


private function saveEdit() {
// save the data away


	$tuple = array();
	
	$stripslash = array(
		'alert1' => true,
		'alert2' => true,
		'alert3' => true,
		'alert4' => true,
		'alert5' => true,
		'alert6' => true,
		'alert7' => true,
		'alert8' => true,
		'alert9' => true,
		'alert10' => true,
		'alert11' => true,
		'alert12' => true
	);
			
	$this->validator = new FormValidator();

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */ 
		$custom = array (
			'newkey' => True,
			'PhoneKey0' => True,
			'PhoneKey1' => True,
			'PhoneKey2' => True,
			'PhoneKey3' => True,
			'PhoneKey4' => True,
			'PhoneKey5' => True,
			'PhoneKey6' => True,
			'PhoneKey7' => True,
			'PhoneKey8' => True,
			'PhoneKey9' => True,
			'PhoneKey*' => True,
			'PhoneKey#' => True
        );

		$this->helper->buildTupleArray($_POST,$tuple,$custom,$stripslash);
		
//		$tuple[$key] = preg_replace ( "/\\\/", '', $tuple[$key]);

/*
 * handle the internal routing
 */ 

       $key=0;
       while ($key < 12) {
       		if (! isset($_POST['PhoneKey' . $key])) {
            	$tuple['option'.$key] = 'None';
            }
        	if  (isset ($tuple['option'.$key])) {
        		$tuple['routeclass'.$key] = $this->helper->setRouteClass($tuple['option'.$key]);
            }
            if (! isset($_POST['PhoneKey' . $key])) {
            	$tuple['option'.$key] = 'None';
            }
            $key++;
       }
       if (isset ($tuple['timeout'])) {
			$tuple['timeoutrouteclass'] = $this->helper->setRouteClass($tuple['timeout']);
       }


/*
 * update the SQL database
 */
		if (isset($_POST['newkey'])) {
			$newkey =  trim(strip_tags($_POST['newkey']));
		}
		
		if ($newkey && $newkey != $tuple['pkey']) {	
			$ret = $this->helper->setTuple("ivrmenu",$tuple,$newkey);
		}
		else {
			$ret = $this->helper->setTuple("ivrmenu",$tuple);
		}			 
		
//		$ret = $this->helper->setTuple("ivrmenu",$tuple);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = " Updated ";
		}
		else {
			$this->invalidForm = True;
			$this->message = "Validation Errors!";	
			$this->error_hash['extensave'] = $ret;	
		}			
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
}

private function xRef($pkey) {
/*
 * Build Xrefs
 */
	$xref = '';
	$tref = '';
    
	$sql = $this->dbh->prepare("SELECT * FROM lineio WHERE openroute LIKE ? OR closeroute LIKE ? ORDER BY pkey");
	$sql->execute(array($pkey,$pkey));		
	$result = $sql->fetchall();	
	foreach ($result as $row) {
		if ( $row['openroute'] == $pkey || $row['closeroute'] == $pkey ) {
                $tref .= "DDI/Class <a href='javascript:window.top.location.href=" . '"/php/sarkddi/main.php?edit=yes&pkey=' . $row['pkey'] . '"' . "' >" . $row['pkey'] . ' </a> references this extension <br>' . PHP_EOL;
        }
	}
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No Trunks reference this extension<br/>" . PHP_EOL;
    }  
    
 	$sql = $this->dbh->prepare("SELECT * FROM speed WHERE outcome LIKE ? OR out LIKE ? ORDER BY pkey");
	$sql->execute(array($pkey,'%' . $pkey . '%'));	
 	$result = $sql->fetchall();	
	foreach ($result as $row) {
		if ($row['pkey'] != 'RINGALL') {
			$tref .= "Callgroup <a href='javascript:window.top.location.href=" . '"/php/sarkcallgroup/main.php?edit=yes&pkey=' . $row['pkey'] . '"' . "' >" . $row['pkey'] . ' </a> references this extension <br>' . PHP_EOL;

		}
	}
	
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No callgroups reference this extension<br/>" . PHP_EOL;
    }       

	$sql = "SELECT * FROM ivrmenu ORDER BY pkey";
	foreach ($this->dbh->query($sql) as $row) {
		if ($row['timeout'] == $pkey) {
			$tref .= "IVR <a href='javascript:window.top.location.href=" . '"/php/sarkivr/main.php?edit=yes&pkey=' . $row['pkey'] . '"' . "' >" . $row['pkey'] . ' </a> references this extension <br>' . PHP_EOL;
		}
		else {
			for ($i = 1; $i <= 11; $i++) {
				if ($row["option" . $i] == $pkey) {
					$tref .= "IVR <a href='javascript:window.top.location.href=" . '"/php/sarkivr/main.php?edit=yes&pkey=' . $row['pkey'] . '"' . "' >" . $row['pkey'] . ' </a> references this extension <br>' . PHP_EOL;
					break 1;
				}
			}
		}
	}
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No IVRs reference this extension<br/>" . PHP_EOL;
    } 
    return $xref;  		   				
}
}

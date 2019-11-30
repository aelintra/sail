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
	

	
	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-small w3-hide-medium');		
	$this->myPanel->aHeaderFor('idd');
	$this->myPanel->aHeaderFor('ivrname');
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
		echo '<tr id="' . $row['id'] . '">'. PHP_EOL; 
		echo '<input type="hidden" name="id" value="' . $row['id'] . '"  />' . PHP_EOL;
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['cluster']  . '</td>' . PHP_EOL;
		echo '<td>' . substr($row['pkey'],2) . '</td>' . PHP_EOL;			
		echo '<td>' . $row['name']  . '</td>' . PHP_EOL;		 
		echo '<td class="w3-hide-small">' . $row['greetnum']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['description']  . '</td>' . PHP_EOL;	
		echo '<td class="w3-hide-small">' . $row['timeout']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['listenforext']  . '</td>' . PHP_EOL;		
		$get = '?edit=yes&amp;id=' . $row['id']; 	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$this->myPanel->ajaxdeleteClick($get);
		echo '</td>' . PHP_EOL;
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

	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = 'default';
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';
	$this->myPanel->displayInputFor('ivrname','text',null,'name');
	$this->myPanel->displayInputFor('idd','number',null,'pkey');
	$this->myPanel->displayInputFor('description','text');


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

//	$_POST['directdial'] = $this->helper->getNextFreeQIvr('ivrmenu',$_POST['cluster'],'startivr');

	$this->validator = new FormValidator();
    $this->validator->addValidation("name","req","Please supply IVR name");
    $this->validator->addValidation("pkey","req","Please supply IVR direct dial");    
    $this->validator->addValidation("pkey","num","IVR direct dial must be numeric");    
    $this->validator->addValidation("pkey","maxlen=4","IVR direct dial must be 3 or 4 digits");     
	$this->validator->addValidation("pkey","minlen=3","IVR direct dial must be 3 or 4 digits");     


    //Now, validate the form
    if ($this->validator->ValidateForm()) {
    
// create full pkey
    	$res = $this->dbh->query("SELECT id FROM cluster WHERE pkey = '" . $_POST['cluster'] . "'")->fetch(PDO::FETCH_ASSOC);
		$_POST['pkey'] = $res['id'] . $_POST['pkey']; 
		$res=NULL;
		
// check for dups
	
    	$retc = $this->helper->checkXref($_POST['pkey'],$_POST['cluster']);
    	if ($retc) {
    		$this->invalidForm = True;
    		$this->error_hash['extinsert'] = "Duplicate found in table $retc - choose a different extension number";
    		return;    	
    	}        
       
/*
 * 	call the tuple builder to create a table row array 
 */ 
	 
		$this->helper->buildTupleArray($_POST,$tuple);	
		$sql = "SELECT id FROM cluster WHERE pkey='" . $tuple['cluster'] .  "'";
		$qRes = $this->dbh->query($sql);
		$work = $qRes->fetch();
		$qRes = NULL;
		$tuple['directdial'] = $work['id'] . $tuple['directdial'];						   
		$ret = $this->helper->createTuple("ivrmenu",$tuple,true,$tuple['cluster']);
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

private function showEdit() {
	
/*
 * build navigation arrays (emulate perl's qw, using explode)  
 */

	$printkey = explode (' ','0 1 2 3 4 5 6 7 8 9 * #');
	
 	

/*
 * pkey could be POST or GET, depending upon the iteration
 */	
	if (isset ($_GET['id'])) {
		$id = $_GET['id']; 
	}
	
	if (isset ($_POST['id'])) {		
		$id = $_POST['id']; 
		$this->saveEdit();
	}
	$clusterGreetings = array();
	

	$ivrmenu = $this->dbh->query("SELECT * FROM ivrmenu WHERE id = '" . $id . "'")->fetch(PDO::FETCH_ASSOC);

	$greetings = $this->helper->getTable("greeting", "SELECT pkey FROM greeting WHERE cluster = '" . $ivrmenu['cluster'] . "'");

	

	foreach ($greetings as $greeting) {
		$clusterGreetings[] = substr($greeting['pkey'],12);
	}
	asort($clusterGreetings);
	$clusterGreetings[] = 'None';	

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkivrForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Edit IVR " . substr($ivrmenu['pkey'],2));

	echo '<form id="sarkivrForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

    
//    $this->myPanel->aLabelFor('ivrname'); 		
//	echo '<input type="hidden" name="newkey" size="20" id="newkey" value="' . $pkey . '"  />' . PHP_EOL;

	$this->myPanel->internalEditBoxStart();

	echo '<div id="clustershow">';
	$this->myPanel->displayInputFor('cluster','text',$ivrmenu['cluster'],'cluster');
	echo '</div>';
	$this->myPanel->displayInputFor('ivrname','text',$ivrmenu['name'],'name');
/*	
	echo '<div id="pkeyshow">';
	$this->myPanel->displayInputFor('idd','number',substr($ivrmenu['pkey'],2),'pkey');;
	echo '</div>';
*/	
	
	$this->myPanel->aLabelFor('greeting'); 	
	echo '<br/><br/>';
	$this->myPanel->selected = $ivrmenu['greetnum'];
	$this->myPanel->popUp('greetnum',$clusterGreetings);
	
	echo '<br/><br/>';
	
//	echo '<div class="w3-container">';
//	echo '<div class="w3-margin">';
	$this->myPanel->aLabelFor('Action on IVR Timeout');
	echo '<br/><br/>';
//	echo '</div>'; 
	$this->myPanel->selected = $ivrmenu['timeout'];
	$this->myPanel->sysSelect('timeout',false,true,true,$ivrmenu['cluster']) . PHP_EOL;
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
		$this->myPanel->sysSelect($opName,true,false,false,$ivrmenu['cluster']) . PHP_EOL;

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
	echo '<input type="hidden" name="id" value="' . $id . '"  />' . PHP_EOL; 

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
		$sql = "SELECT id FROM cluster WHERE pkey='" . $tuple['cluster'] .  "'";
		$qRes = $this->dbh->query($sql);
		$work = $qRes->fetch();
		$qRes = NULL;
		$tuple['directdial'] = $work['id'] . $tuple['directdial'];		
		
//		$tuple[$key] = preg_replace ( "/\\\/", '', $tuple[$key]);

/*
 * handle the internal routing
 */ 

		$key=0;
		while ($key < 10) {
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

// handle * and #
		if (! isset($_POST['PhoneKey*'])) {
       		$tuple['option10'] = 'None';
		}
		else {
       		$tuple['routeclass10'] = $this->helper->setRouteClass($tuple['option10']);
		}
		if (! isset($_POST['PhoneKey#'])) {
       		$tuple['option11'] = 'None';
		}
		else {
       		$tuple['routeclass11'] = $this->helper->setRouteClass($tuple['option11']);
		} 

		if (isset ($tuple['timeout'])) {
			$tuple['timeoutrouteclass'] = $this->helper->setRouteClass($tuple['timeout']);
		}       

/*
 * update the SQL database
 */

		$ret = $this->helper->setTupleById("ivrmenu",$tuple);
			 
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

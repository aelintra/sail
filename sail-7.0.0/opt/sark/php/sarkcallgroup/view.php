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

Class sarkcallgroup {
	
	protected $message; 
	protected $head = "Ring Groups"; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();			

public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;

	
	$this->myPanel->pagename = 'Ring Groups';

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
		$this->showEdit($_POST['pkey'],$_POST['cluster']);
		return;				
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
	$buttonArray = array();
	$buttonArray['new'] = true;

/*	
	if ( $_SESSION['user']['pkey'] == 'admin' ) {
		echo '<a  href="/php/downloadpdf.php?pdf=groups"><img id="pdfprint" src="/sark-common/buttons/print.png" border=0 title = "Click to Download PDF" ></a>' . PHP_EOL;	
	}
	echo '</div>';	
*/
	$this->myPanel->actionBar($buttonArray,"sarkcallgroupForm",false,true);
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup();

	echo '<form id="sarkcallgroupForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->beginResponsiveTable('callgrouptable',' w3-tiny');
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-medium w3-hide-small');
	$this->myPanel->aHeaderFor('callgroup'); 	
	$this->myPanel->aHeaderFor('description');
	$this->myPanel->aHeaderFor('grouptype',false,'w3-hide-medium w3-hide-small');
	$this->myPanel->aHeaderFor('alphatag',false,'w3-hide-medium w3-hide-small');
	$this->myPanel->aHeaderFor('groupstring',false,'w3-hide-small');	
	$this->myPanel->aHeaderFor('outcome',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('devicerec',false,'w3-hide-medium w3-hide-small');
	$this->myPanel->aHeaderFor('ed');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;

/*** table rows ****/

	$rows = $this->helper->getTable("speed",NULL,true,false,'cluster');
	foreach ($rows as $row ) {
		if ( $row['pkey'] != 'RINGALL') {
			echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL;

			echo '<input type="hidden" name="pkey" id="pkey" value="' . $row['pkey'] . '"  />' . PHP_EOL;

//			if ($row['cluster'] != 'default') {
				$shortkey = substr($row['pkey'],2);
/*
			}
			else {
				$shortkey = $row['pkey'];
			}	
*/		 
			echo '<td class="w3-hide-medium w3-hide-small">' . $row['cluster'] . '</td>' . PHP_EOL;
			echo '<td class="read_only">' . $shortkey . '</td>' . PHP_EOL;					
			echo '<td >' . $row['longdesc'] . '</td>' . PHP_EOL;
			echo '<td class="w3-hide-medium w3-hide-small">' . $row['grouptype'] . '</td>' . PHP_EOL;
			echo '<td class="w3-hide-medium w3-hide-small">' . $row['calleridname'] . '</td>' . PHP_EOL;
			echo '<td class="w3-hide-small">' . $row['out'] . '</td>' . PHP_EOL;
			
			$this->helper->pkey = $row['outcome'];
			$displayout = $this->helper->displayRouteClass($row['outcomerouteclass']);
			echo '<td class="w3-hide-small">' . $displayout . '</td>' . PHP_EOL;
			echo '<td class="w3-hide-medium w3-hide-small">' . $row['devicerec'] . '</td>' . PHP_EOL;	
			
			$get = '?edit=yes&amp;pkey=';
			$get .= $row['pkey'];
			$get .= '&amp;cluster=';
			$get .= $row['cluster'];	
			$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);		
			$get = '?id=' . $row['pkey'];		
			$this->myPanel->ajaxdeleteClick($get);			echo '</td>' . PHP_EOL;
			echo '</tr>'. PHP_EOL;
		}
	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();	
}
private function showNew() {

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkcallgroupForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);
	

	echo '<form id="sarkcallgroupForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';	

	
	$res = $this->dbh->query("SELECT INTRINGDELAY FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$intringdelay = $res['INTRINGDELAY'];
	
	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("New Ring Group");
	$this->myPanel->displayInputFor('callgroup','text',null,'pkey');	
	

	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = 'default';
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';
	$this->myPanel->displayInputFor('description','text',null,'longdesc');
	$this->myPanel->radioSlide('grouptype','Ring',array('Ring','Hunt','Page'));
	echo '<div class="w3-margin-bottom">';
	$this->myPanel->displayInputFor('groupstring','text',null,'out');
/*	
	$this->myPanel->aLabelFor('outcome');
	echo '</div>'; 
	$this->myPanel->sysSelect('outcome') . PHP_EOL;
	$this->myPanel->aHelpBoxFor('outcome');
*/	
	$this->myPanel->radioSlide('devicerec','default',array('default','None','OTR','OTRR','Inbound'));
	$this->myPanel->displayInputFor('ringdelay','number',$intringdelay);
	$this->myPanel->displayInputFor('alphatag','text',null,'calleridname');
	$this->myPanel->displayInputFor('alertinfo','text',null,'speedalert');
	

	echo '</div>';
	$endButtonArray['cancel'] = true;
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);
	echo '</form>' . PHP_EOL; // close the form 
    $this->myPanel->responsiveClose();	
}

private function saveNew() {
// save the data away
// 



	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Call Group name");
    $this->validator->addValidation("pkey","alnum","Call Group name must be alphanumeric(no spaces)");    
    $this->validator->addValidation("longdesc","alnum_s","Description must be alphanumeric (no special characters)"); 
    $this->validator->addValidation("out","regexp=/^[@A-Za-z0-9-_\/\s]{2,1024}$/","Target must be number or number/channel strings separated by whitespace");     

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 *	prepend the cluster id to the key
 */
    	$sql = $this->dbh->prepare("SELECT id FROM cluster WHERE pkey = ?");
		$sql->execute(array($_POST['cluster']));
		$resid = $sql->fetch();
		$sql=NULL;
		
		$_POST['pkey'] = $resid['id'] . $_POST['pkey'];

    	$retc = $this->helper->checkXref($_POST['pkey'],$_POST['cluster']);
	    if ($retc) {
    		$this->invalidForm = True;
    		$this->error_hash['insert'] = "Duplicate found in table $retc - choose a different extension number";
    		return;    	
    	}

/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);	
/*
 * calculate the route class if outcome is set
 */   
		if (isset($_POST['outcome']) ) {	  
			$tuple['outcomerouteclass'] = $this->helper->setRouteClass($_POST['outcome']);
		} 					
		if ($this->helper->loopcheck($tuple['pkey'] , $tuple['out'])) {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash[routename] = "Loop detected in target list!";
		}
		else {		 		   
			$ret = $this->helper->createTuple("speed",$tuple);
			if ($ret == 'OK') {
//				$this->helper->commitOn();	
				$this->message = "Saved new Ring Group " . substr($tuple['pkey'],2) . "!";
			}
			else {
				$this->invalidForm = True;
				$this->message = "Insert Errors!";	
				$this->error_hash['speedinsert'] = $ret;	
			}
		}		
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "Validation Errors!";		
    }
//    unset ($this->validator);
}

private function showEdit($key=False,$cluster=false) {
	
	if ($key != False) {
		$pkey=$key;
	}
	else {
		$pkey = $_GET['pkey']; 
	}
	if (!$cluster) {
		$cluster = $_GET['cluster'];
	}
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkcallgroupForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	echo '<form id="sarkcallgroupForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->internalEditBoxStart();
/*
	if ($cluster == 'default') {
		$this->myPanel->subjectBar("Ring Group " . $pkey);
	}
	else {
*/
		$this->myPanel->subjectBar("Ring Group " . substr($pkey,2));
//	} 

	$res = $this->dbh->query("SELECT INTRINGDELAY FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$intringdelay = $res['INTRINGDELAY'];	
	$res = $this->dbh->query("SELECT * FROM speed WHERE pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);
	
	echo '<div id="clustershow">';
    $this->myPanel->displayInputFor('cluster','text',$res['cluster'],'cluster');
    echo '</div>';

//	$this->myPanel->displayInputFor('callgroup','text',$res['pkey'],'pkey');	
	$this->myPanel->radioSlide('grouptype',$res['grouptype'],array('Ring','Hunt','Page'));
	$dialparams = 'ciIkt';
	if (isset($res['dialparamsring']) ) {
		$dialparams = $res['dialparamsring'];

	}

	$this->myPanel->displayInputFor('groupstring','text',$res['out'],'out');
	echo '<div class="w3-margin-bottom">';
	$this->myPanel->aLabelFor('outcome');
	echo '</div>';
	$this->myPanel->selected = $res['outcome'];
	$this->myPanel->sysSelect('outcome',false,false,false,$res['cluster']) . PHP_EOL;
	$this->myPanel->aHelpBoxFor('outcome');
	$this->myPanel->displayInputFor('description','text',$res['longdesc'],'longdesc');

	$this->myPanel->displayInputFor('divert','text',$res['divert']);

	echo '<div id="divringname">' . PHP_EOL;
	$this->myPanel->displayInputFor('dialparams','text',$dialparams,"dialparamsring");
	echo '</div>' . PHP_EOL;

	

	$dialparams = 'cIkt';
	if (isset($res['dialparamshunt']) ) {
		$dialparams = $res['dialparamshunt'];
	}
		
	
	echo '<div id="divhuntname">' . PHP_EOL;
	$this->myPanel->displayInputFor('dialparams','text',$dialparams,"dialparamshunt");	
	echo '</div>' . PHP_EOL;	


	$this->myPanel->radioSlide('devicerec','default',array('default','None','OTR','OTRR','Inbound'));
	$this->myPanel->displayInputFor('ringdelay','number',$res['ringdelay']);
	$this->myPanel->displayInputFor('alphatag','text',$res['calleridname'],'calleridname');
	$this->myPanel->displayInputFor('alertinfo','text',$res['speedalert'],'speedalert');
	

	echo '</div>';
	echo '<input type="hidden" name="pkey" id="pkey" size="20"  value="' . $res['pkey'] . '"  />' . PHP_EOL; 

	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";	
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;
	  
    $this->myPanel->responsiveClose(); 	
}

private function saveEdit() {
// save the data away
	$tuple = array();

	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Call Group name");
    $this->validator->addValidation("pkey","alnum","Call Group name must be alphanumeric(no spaces)"); 
    $this->validator->addValidation("divert","alnum","Divert must be alphanumeric(no spaces)");   
//	$this->validator->addValidation("ringdelay","num","Ringtime must be numeric"); 
    $this->validator->addValidation("longdesc","alnum_s","Description must be alphanumeric (no special characters)"); 
    $this->validator->addValidation("out","regexp=/^[@A-Za-z0-9-_\/\s]{2,1024}$/","Target must be number or number/channel strings separated by whitespace");     

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * calculate the route class if outcome has changed
 */   
		if (isset($_POST['outcome']) ) {	  
			$tuple['outcomerouteclass'] = $this->helper->setRouteClass($_POST['outcome']);
		} 				
/*
 * 	call the tuple builder to create a table row array 
 */ 
		$this->helper->buildTupleArray($_POST,$tuple);

		if (array_key_exists('outcome',$tuple)) {
			$tuple['outcomerouteclass'] = $this->helper->setRouteClass($tuple['outcome']);
		}
/*
 * loopcheck
 */ 		
		if ($this->helper->loopcheck($tuple['pkey'], $tuple['out'])) {
			$this->invalidForm = True;
			$this->message = "Validation Errors!";	
			$this->error_hash[routename] = "Loop detected in target list!";
		}
		else {
/*
 * call the setter
 */ 
			$ret = $this->helper->setTuple("speed",$tuple);
/*
 * flag errors
 */ 	
			if ($ret == 'OK') {
//				$this->helper->commitOn();	
				$this->message = "Updated Ring Group " . substr($tuple['pkey'],2) . "!";
			}
			else {
				$this->invalidForm = True;
				$this->message = "Validation Errors!";	
				$this->error_hash[speed] = $ret;	
			}
		}	
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "Validation Errors!";		
    }
    unset ($this->validator);
}

}

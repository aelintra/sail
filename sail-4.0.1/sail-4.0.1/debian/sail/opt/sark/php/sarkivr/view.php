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
require_once "../srkPageClass";
require_once "../srkDbClass";
require_once "../srkHelperClass";
require_once "../formvalidator.php";


Class ivr {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $distro = array();
	protected $soundir = '/var/lib/asterisk/sounds'; // set for rhel (see below)	

public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	$this->helper->qDistro($this->distro);
	$this->soundir = $this->distro['soundroot'] . 'asterisk/sounds';
			
	echo '<body>';
	echo '<form id="sarkivrForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'IVRs';
	
	if (isset($_POST['new_x'])) { 
		$tuple = array();
		$tuple['pkey'] 	= 'IVR' . rand(1000, 9999);
		$ret = $this->helper->createTuple("ivrmenu",$tuple);
		$this->showEdit($tuple['pkey'] );	
	}
	
	if (isset($_POST['update_x'])) { 
		$this->showEdit();	
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
  
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
	$this->myPanel->commitButton();
	echo '</div>';	
	
	$this->myPanel->Heading();
	
	$tabname = 'ivrtable';
	if ( $_SERVER['REMOTE_USER'] == 'admin' ) {
		$tabname .= 'admin';
	}
	
	echo '<div class="datadivnarrow">';
	
	echo '<table class="display" id="' . $tabname . '"  >' ;	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('ivrname');
	$this->myPanel->aHeaderFor('cluster'); 	
	$this->myPanel->aHeaderFor('greeting');
	$this->myPanel->aHeaderFor('timeout');
	$this->myPanel->aHeaderFor('listenforext');
	$this->myPanel->aHeaderFor('ed');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("ivrmenu");
	foreach ($rows as $row ) { 
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;			
		echo '<td >' . $row['cluster']  . '</td>' . PHP_EOL;		 
		echo '<td >' . $row['greetnum']  . '</td>' . PHP_EOL;	
		echo '<td >' . $row['timeout']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['listenforext']  . '</td>' . PHP_EOL;	
		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';
		
}

private function saveNew() {
// save the data away
	
	$tuple = array();

	$tuple['pkey'] 	= '000-New' . rand(1000, 9999);

	$ret = $this->helper->createTuple("ivrmenu",$tuple);
	if ($ret == 'OK') {
			$this->message = "Created new IVR " . $tuple['pkey'] . "!";
	}
	else {
		$this->invalidForm = True;
		$this->message = "<B>  --  Validation Errors!</B>";	
		$this->error_hash['exteninsert'] = $ret;	
	}
	
}

private function showEdit($pkey=false) {
	
/*
 * build navigation arrays (emulate perl's qw, using explode)  
 */
    $tableoffkey = explode(' ','1-OFF.jpg 2-OFF.jpg 3-OFF.jpg 4-OFF.jpg 5-OFF.jpg 6-OFF.jpg 7-OFF.jpg 8-OFF.jpg 9-OFF.jpg star-OFF.jpg 0-OFF.jpg hash-OFF.jpg'); 
	$tableonkey = explode(' ','1-on.jpg 2-on.jpg 3-on.jpg 4-on.jpg 5-on.jpg 6-on.jpg 7-on.jpg 8-on.jpg 9-on.jpg star-on.jpg 0-on.jpg hash-on.jpg'); 
    $tabnavkey = explode(' ','1 2 3 4 5 6 7 8 9 10 0 11'); 	
	$printkey = explode (' ','0 1 2 3 4 5 6 7 8 9 * #');
	
/*
 * get a list of greeting numbers
 */
	$greetings = array();
	$root = $this->soundir;
	$dir = "";
	$user =  $_SERVER['REMOTE_USER'];
	if ($_SERVER['REMOTE_USER'] != 'admin') {
		$res = $dbh->query("SELECT cluster FROM user where pkey = '" . $_SERVER['REMOTE_USER'] . "'")->fetch(PDO::FETCH_ASSOC);
		if 	(array_key_exists('cluster',$res)) {
			$dir = $res['cluster'] . "/";
		}
	}
	$search = $root . "/" . $dir;
	if ($handle = opendir($search)) {
		while (false !== ($entry = readdir($handle))) {
			if (preg_match("/^usergreeting(\d*)/",$entry,$matches)) {
				array_push($greetings, $matches[1]);
			}
		}
		closedir($handle);
	}
								   	

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
		if (isset ($_POST['newkey'])) {		
			$pkey = $_POST['newkey']; 
		}
	}
	
	$ivrmenu = $this->dbh->query("SELECT * FROM ivrmenu WHERE pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);

	$printline = "IVR " . $pkey;
	$this->myPanel->msg .= $printline; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	} 
	
	$xref = $this->xRef($pkey);

	echo '<div class="buttons">';
	$this->myPanel->Button("back");	
	$this->myPanel->override = "update";
	$this->myPanel->Button("save");
	echo '</div>';	
		
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}

	echo '<div class="datadivtabedit">'; 	
    echo '<div id="pagetabs" " >' . PHP_EOL;
    echo '<ul>' . PHP_EOL;
    echo '<li><a href="#general">IVR</a></li>' . PHP_EOL;
    echo '<li><a href="#xref" >XREF</a></li>' . PHP_EOL;
    echo '</ul>' . PHP_EOL;

#
#   TAB XREF table
#
	echo '<div id="xref"  >' . PHP_EOL;
	echo '<h2>Cross References to this IVR</h2>' . PHP_EOL;
    echo '<p>' . $xref . '</p>' . PHP_EOL;
	echo '</div>' . PHP_EOL;

#
#       TAB DIVEND
#

#
#   TAB general
#
    echo '<div id="general" >' . PHP_EOL;  
    
    
    $this->myPanel->aLabelFor('ivrname'); 		
	echo '<input type="text" name="newkey" size="20" id="newkey" value="' . $pkey . '"  />' . PHP_EOL;	
	if (!empty($greetings)) {
		$this->myPanel->aLabelFor('greeting'); 	
		$this->myPanel->selected = $ivrmenu['greetnum'];
		$this->myPanel->popUp('greetnum',$greetings);
	}

	$key = 0;
	$limit = 12;
    echo '<div ><table >' . PHP_EOL;

    while ($key < $limit) {
    	if ($key == 0 || $key == 3 || $key == 6 || $key == 9 ) {
        	echo '<tr>' . PHP_EOL;
        }
        
        $opName = "option" . $tabnavkey[$key];
        
        $title = 'None';
        if (isset($ivrmenu[$opName])) {
			$title = $ivrmenu[$opName]; 
		}

        if (preg_match("/None/",$ivrmenu[$opName])) {
        	echo '<td><a href="#key' . $tabnavkey[$key] .
        		'" id="inline"><img src="/sark-common/keys/' . $tableonkey[$key] .
                	'" border=0 title = "' . $title . '"></a></td>'. PHP_EOL;
        }
        else {
        	echo '<td><a href="#key' . $tabnavkey[$key] .
        		'" id="inline"><img src="/sark-common/keys/' . $tableoffkey[$key] .
                	'" border=0 title = "' . $title . '"></a></td>'. PHP_EOL;
        }
    	if ($key == 2 || $key == 5 || $key == 8 || $key == 11 )  {
        	echo '</tr>'. PHP_EOL;
        }
    	$key++;
    }

    echo '</table></div>'. PHP_EOL;	
    
    $key=0;
    $limit = 12;
    while ($key < $limit) {

		echo '<br/>' . PHP_EOL;
		echo '<div style="display:none"><div id="key'.$key.'">' . PHP_EOL;

		$opName = "option".$key;
    

		echo '<br/><span style="color: rgb(0, 0, 0); font-weight:bold;font-size:small; ">KEY' . $printkey[$key] . '</span>' . PHP_EOL;
		echo '<br/><span style="color: rgb(0, 0, 0); font-weight:bold;font-size:small; ">Action on Keypress</span>' . PHP_EOL;
		echo '<br/><br/>'  . PHP_EOL;

		$opName = "option".$key;
		$this->myPanel->selected = $ivrmenu[$opName];
		$this->myPanel->sysSelect($opName,True) . PHP_EOL;
		$tagindex = "tag" . $key;
		$alertindex = "alert" . $key;
		echo '<br/>' . PHP_EOL;
		echo '<br/><span style="color: rgb(0, 0, 0); font-weight:bold;font-size:small; ">Tag entry</span>' . PHP_EOL;
		echo '<br/>' . PHP_EOL;
		echo '<input type="text" name="tag' . $key . '" id="tag' . $key . '" size="20" value="' . $ivrmenu[$tagindex] . '"  />' . PHP_EOL;
		echo '<br/>' . PHP_EOL;
		echo '<br/><span style="color: rgb(0, 0, 0); font-weight:bold;font-size:small; ">Alert Info</span>' . PHP_EOL;
		echo '<br/>' . PHP_EOL;
		echo '<input type="text" name="alert' . $key . '" id="alert' . $key . '" size="20" value="' . $ivrmenu[$alertindex] . '"  />' . PHP_EOL;
		print '</div>' . PHP_EOL;
		$key++;
    }
        echo '</div>' . PHP_EOL;
#
#       TAB DIVEND
#    

#
#  end of TABS DIV
#
	echo '</div>' . PHP_EOL;
   
	echo '<input type="hidden" name="pkey" id="pkey" value="' . $ivrmenu['pkey'] . '"  />' . PHP_EOL; 
	echo '</div>' . PHP_EOL;		
}


private function saveEdit() {
// save the data away
//print_r ($_POST) ;

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
			'newkey' => True
        );

		$this->helper->buildTupleArray($_POST,$tuple,$custom,$stripslash);
		
//		$tuple[$key] = preg_replace ( "/\\\/", '', $tuple[$key]);

/*
 * handle the internal routing
 */ 

       $key=0;
       while ($key < 12) {
        	if  (isset ($tuple['option'.$key])) {
        		$tuple['routeclass'.$key] = $this->helper->setRouteClass($tuple['option'.$key]);
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
			$this->message = "<B>  --  Validation Errors!</B>";	
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
   
	$sql = "SELECT * FROM lineio WHERE openroute LIKE '" . $pkey . "' OR closeroute LIKE '" . $pkey . "' ORDER BY pkey";
	foreach ($this->dbh->query($sql) as $row) {
		if ( $row['openroute'] == $pkey || $row['closeroute'] == $pkey ) {
                $tref .= "Trunk " . $row['pkey'] . " references this IVR <br>" . PHP_EOL;
        }
	}
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No Trunks reference this IVR<br/>" . PHP_EOL;
    }  
    
 	$sql = "SELECT * FROM speed WHERE outcome LIKE '" . $pkey . "' OR out LIKE '" . $pkey . "' ORDER BY pkey";
 	foreach ($this->dbh->query($sql) as $row) {
		if ($row['pkey'] != 'RINGALL') {
			$tref .= "callgroup " . $row['pkey'] . " references this IVR <br>" . PHP_EOL;
		}
	}
	
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No callgroups reference this IVR<br/>" . PHP_EOL;
    }       

	$sql = "SELECT * FROM ivrmenu where pkey != '" .$pkey . "' ORDER BY pkey";
	foreach ($this->dbh->query($sql) as $row) {
		if ($row['timeout'] == $pkey) {
			$tref .= "IVR Timeout " . $row['pkey'] . " references this IVR <br>" . PHP_EOL;
		}
		else {
			for ($i = 1; $i <= 11; $i++) {
				if ($row["option" . $i] == $pkey) {
					$tref .=  "IVR " . $row['pkey'] . " references this IVR <br>" . PHP_EOL;
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
    	$xref .= "No IVRs reference this IVR<br/>" . PHP_EOL;
    }  		   		
	return $xref;
}
}

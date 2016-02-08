<?php
//
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

require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkLDAPHelperClass";


Class sarkldap {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $log = NULL;	
	
public function showForm() {
//	print_r($_POST);
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	$this->ldap = new ldaphelper;
	
	if (!$this->ldap->Connect()) {
		$this->message = "ERROR - Could not connect to LDAP";
	}

	echo '<form id="sarkldapForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">' . PHP_EOL;
	
	$this->myPanel->pagename = 'LDAP Directory';
	
	if (isset($_POST['new_x'])) { 
		$this->showNew();
		return;
	}
	
	if (isset($_POST['save_x'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}					
	}
	
	if (!empty($_POST['upimgclick'])) {
		$this->showUpload();
		if (!$this->invalidForm) {
			return;
		} 		
	}		
	
	if (isset($_POST['savevcf_x'])) {
		$this->doUpload();		
	}		
			
	$this->showMain();
	
	$this->dbh = NULL;
	$this->ldap->Close();
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
	echo '<img src="/sark-common/buttons/upload.png" id="upimg" alt="upload" title="Upload a vcard (.vcf) file" />'. PHP_EOL;	
	echo '</div>';	
	
	echo '<input type="file" id="file" name="file" style="display: none;" />'. PHP_EOL;
	echo '<input type="hidden" id="upimgclick" name="upimgclick" />'. PHP_EOL;	
	
	$this->myPanel->Heading();
	$tabname = 'ldaptable';
	
	echo '<div class="datadivnarrow">';
	
	echo '<table class="display" id="' . $tabname . '" >' ;

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('surname'); 	
	$this->myPanel->aHeaderFor('forename');
	$this->myPanel->aHeaderFor('phone');
	$this->myPanel->aHeaderFor('mobile');
	$this->myPanel->aHeaderFor('home');
	$this->myPanel->aHeaderFor('del');

	$search_arg = array("uid","givenname", "sn", "telephoneNumber", "mobile", "homePhone", "cn");
	$result = $this->ldap->Search($search_arg);
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	for ($i=0; $i<$result["count"]; $i++) {
		
		echo '<tr id="' .  $result[$i]["uid"][0] . '">'. PHP_EOL; 		
		echo '<td >' . $result[$i]["sn"][0]  . '</td>' . PHP_EOL;
		if (isset($result[$i]["givenname"][0])) {
			echo '<td >' . $result[$i]["givenname"][0] . '</td>' . PHP_EOL;
		}
		else {
			echo '<td ></td>' . PHP_EOL;
		}					 
		if (isset($result[$i]["telephonenumber"][0])) {
			echo '<td >' . $result[$i]["telephonenumber"][0]  . '</td>' . PHP_EOL;	
		}
		else {
			echo '<td ></td>' . PHP_EOL;
		}
		if (isset($result[$i]["mobile"][0])) {
			echo '<td >' . $result[$i]["mobile"][0]  . '</td>' . PHP_EOL;	
		}
		else {
			echo '<td ></td>' . PHP_EOL;
		}				
		if (isset($result[$i]["homephone"][0])) {
			echo '<td >' .  $result[$i]["homephone"][0]  . '</td>' . PHP_EOL;	
		}
		else {
			echo '<td ></td>' . PHP_EOL;
		}		
		$get = '?id=' . $result[$i]["uid"][0];		
		$this->myPanel->ajaxdeleteClick($get);		 
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
		
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';

}

private function showNew() {
	$this->myPanel->msg .= "Add New LDAP entry "; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}  
	
	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	$this->myPanel->Button("save");
	echo '</div>';			
	
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}	
	
	echo '<div class="editinsert">';
	
	$this->myPanel->aLabelFor('surname');
	echo '<input type="text" name="surname" id="surname" size="30"   />' . PHP_EOL;	
	$this->myPanel->aLabelFor('forename');
	echo '<input type="text" name="givenname" id="givenname" size="30"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('ext');
	echo '<input type="text" name="phone" id="phone" size="18"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('mobile');
	echo '<input type="text" name="mobile" id="mobile" size="18"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('home');
	echo '<input type="text" name="home" id="home" size="18"   />' . PHP_EOL;		

	echo '</div>';
		
}

private function saveNew() {
// save the data away
	$ldapargs = Array();
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("surname","req","Please fill in surname");
    $this->validator->addValidation("phone","num","Phone number must be numeric with no spaces");   
    $this->validator->addValidation("mobile","num","Mobile number must be numeric with no spaces");   

    if ($this->validator->ValidateForm()) {
		$ldapargs["sn"] = $_POST['surname'];
		
		if (isset($_POST['givenname']) && $_POST['givenname'] != "") {			
			$ldapargs["givenname"] = $_POST['givenname'];
		}
		if (isset($_POST['phone']) && $_POST['phone'] != "") {
			$ldapargs["telephonenumber"] = $_POST['phone'];
		}
		if (isset($_POST['mobile']) && $_POST['mobile'] != "") {			
			$ldapargs["mobile"] = $_POST['mobile'];
		}
		if (isset($_POST['home']) && $_POST['home'] != "") {			
			$ldapargs["homephone"] = $_POST['home'];
		}		
		$ldapargs["cn"] = $ldapargs["givenname"] . ' ' . $ldapargs["sn"];
		$ldapargs["objectclass"] = array('top', 'person', 'organizationalPerson', 'inetOrgPerson');
		$this->message = $this->ldap->Add($ldapargs);
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);

}

private function showUpload() {
// save the data away
	$showfile = null;
	$filename = strip_tags($_FILES['file']['name']);
	if (!preg_match (' /.vcf$/ ', $filename) ) {
		$this->message = $filename . "NOT a vcf file!";
		$this->invalidForm = True;
		return;
	}
	$tfile = strip_tags($_FILES['file']['tmp_name']);
	$this->helper->request_syscmd ("/bin/mv $tfile /tmp/$filename");
	$this->message = "File $filename uploaded to temp storage!";	
	$file = file("/tmp/$filename") or die("Could not read file $filename");
	foreach ($file as $rec) {
		$showfile .= $rec;
	}
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	} 
	
	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	$this->myPanel->override = "savevcf";
	$this->myPanel->Button("save");
	echo '</div>';	
		
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
	
	echo '<div class="datadivtabedit">';
	

	echo '<h2>Vcard File~: ' . $filename . '; - To add to directory press SAVE </h2>' . PHP_EOL;
	echo '<textarea class="longdatabox" readonly="readonly" style = "background-color: #E8E8EE; font-size: 14px;" name="astfile" id="astfile">' . $showfile . '</textarea>' . PHP_EOL;
	echo '<div id="reset" >'. PHP_EOL;
	
	echo '<h2>'. PHP_EOL;
	echo '<input id="phoneonly" type="checkbox" name="phoneonly" checked="checked" >'. PHP_EOL;
	echo ' :Only load cards with phone numbers?';																									
	echo '</h2>'. PHP_EOL;

    echo '</div>' . PHP_EOL;
	echo '<input type="hidden" name="fkey" id="fkey" value="' . $filename . '"  />' . PHP_EOL;	
	echo '</div>'; 
			
}
private function doUpload() {
// save the data away
	$this->dbh = DB::getInstance();
	$res = $this->dbh->query("SELECT LDAPBASE,LDAPOU,LDAPUSER,LDAPPASS FROM globals")->fetch(PDO::FETCH_ASSOC);
	$this->user = 'cn=' . $res['LDAPUSER'];
	$this->password = $res['LDAPPASS'];
	$ifile = '/tmp/';
	$phoneonly = null;
	if (isset($_POST['phoneonly'])) {
		$phoneonly = '-p';
	}
	if (isset($_POST['fkey'])) {
		$ifile .= strip_tags($_POST['fkey']);
		if (file_exists($ifile)) {
			$this->helper->request_syscmd (
				'php /opt/sark/generator/vcfconvert.php -f ldap ' . $phoneonly . ' -b "ou=' . $res['LDAPOU'] . ',' . $res['LDAPBASE'] . '" -o /tmp/newldif ' . $ifile
			);		
			$this->helper->request_syscmd (
				'ldapadd -H ldap://localhost -xc  -D "cn=' . $res['LDAPUSER'] . ',' . $res['LDAPBASE'] . '" -f /tmp/newldif -w ' . $res['LDAPPASS']
			);	
			$this->message = "Operation complete";
		}
		else {
			$this->message = "Couldn't find upload file $ifile";
		}
	}	
			
}
}

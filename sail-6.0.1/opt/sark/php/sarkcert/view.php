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


Class sarkcert {

        protected $message;
        protected $head = "Certificates";
        protected $certDir = "/opt/sark/etc/ssl/customer/";
        protected $certFile = "cert";
        protected $keyFile = "key";
        protected $myPanel;
        protected $helper;
        protected $validator;
        protected $invalidForm;
        protected $error_hash = array();

public function showForm() {

        $this->myPanel = new page;
        $this->helper = new helper;

        $this->myPanel->pagename = 'Cert';


        if (isset($_POST['remcert'])) {
                $this->message = $this->remCert();
        }

        if (isset($_POST['addcert'])) {
                $this->message = $this->addCert();
        }

        $this->showMain();

        return;

}

private function showMain() {

        print_r($_POST);
        echo $this->message;
        if (isset($this->message)) {
                $this->myPanel->msg = $this->message;
        }
        $buttonArray=array();
        $this->myPanel->actionBar($buttonArray,"sarkcertForm",false);
        $this->myPanel->Heading($this->head,$this->message);
        $this->myPanel->responsiveSetup(2);

        echo '<form id="sarkcertForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;

/*
 *  Certificates
 */

        if (file_exists('/etc/ssl/customertest')) {
			if (file_exists('/etc/ssl/customertest/cert')) {
				echo '<p>Certificate loaded</p>' . PHP_EOL;
			}

			if (file_exists('/etc/ssl/customertest/key')) {
				echo '<p>Private Key loaded</p>' . PHP_EOL;
			}
			echo '<div class="w3-container w3-padding w3-margin-top">' . PHP_EOL;

			echo '<button class="w3-button w3-blue w3-small w3-round-xxlarge w3-padding w3-right" type="submit" name="remcert" onclick="return confirmOK(\'Delete? - Confirm?\'>Remove Certs</button>';
			echo '</div>' . PHP_EOL;

        }
        else {
			$this->myPanel->internalEditBoxStart();
			$this->myPanel->subjectBar("Certificate");
			echo '<div class="w3-margin-bottom w3-text-blue-grey w3-small">';
			echo "<p>";
			echo "<label> Copy and paste your .crt file contents into the box below </label>";
			echo '</p>';
			echo '</div>';
			echo '<p><textarea class="w3-padding w3-margin-bottom w3-tiny w3-card-4 longdatabox" style="height:200px"';
			echo ' name="cert" id="cert" ></textarea></p>' . PHP_EOL;

			$this->myPanel->internalEditBoxStart();
			$this->myPanel->subjectBar("CSR Key");
			echo '<div class="w3-margin-bottom w3-text-blue-grey w3-small">';
			echo "<p>";
			echo "<label> Copy and paste your CSR .key file contents into the box below </label>";
			echo '</div>';
			echo '<p><textarea class="w3-padding w3-margin-bottom w3-tiny w3-card-4 longdatabox" style="height:200px"';
			echo ' name="csrkey" id="csrkey" ></textarea></p>' . PHP_EOL;
			echo '</div>';

			echo '<div class="w3-container w3-padding w3-margin-top">' . PHP_EOL;
			echo '<button class="w3-button w3-blue w3-small w3-round-xxlarge w3-padding w3-right" type="submit" name="addcert">Install</button>';
			echo '</div>' . PHP_EOL;

			echo '</div>' . PHP_EOL;
        }
        echo '</form>';
        $this->myPanel->responsiveClose();
}

private function addcert()
{
    	if (empty($_POST['cert']) || empty($_POST['csrkey'])) {
    		return "Both Cert and Key MUST be filled out!";
    	} 

    	if (! file_exists($this->certDir)) {
        	`sudo mkdir -p $this->certDir`;
        	`sudo chown www-data:www-data $this->certDir`;
        }
        
        $fh = fopen($this->certDir . $this->certFile, 'w') or die('Could not open cert file!');
        fwrite($fh, $_POST['cert'])
        or die('Could not write to file cert');
        fclose($fh);

        $fh = fopen($this->certDir . $this->keyFile, 'w') or die('Could not open key file!');
        fwrite($fh, $_POST['csrkey'])
        or die('Could not write to file key');
        fclose($fh);

        return("Added Certificates");
}


private function remcert() {
        `sudo rm -rf $this->certDir`;
/*
        `a2dissite sark-certs.conf`;
        `a2ensite default-ssl.conf`;
*/
        return("Deleted Certificate - reboot required");
}
}
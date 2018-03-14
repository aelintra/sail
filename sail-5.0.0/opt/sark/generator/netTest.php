<?php
// +-----------------------------------------------------------------------+
// |  Copyright (c) CoCoSoft 2005-10                                  |
// +-----------------------------------------------------------------------+
// | This file is free software; you can redistribute it and/or modify     |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation; either version 2 of the License, or     |
// | (at your option) any later version.                                   |
// | This file is distributed in the hope that it will be useful           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
// | GNU General Public License for more details.                          |
// +-----------------------------------------------------------------------+
// | Author: CoCoSoft                                                           |
// +-----------------------------------------------------------------------+
//

include("/opt/sark/php/srkNetHelperClass");

$net = new nethelper();

	echo "interfacename " . $net->get_interfaceName() . "\n";
	echo "localIPV4 " . $net->get_localIPV4() . "\n";
	echo "networkIPV4 " . $net->get_networkIPV4() . "\n";
	echo "networkGw " . $net->get_networkGw() . "\n"; 
	echo "networkCIDR " . $net->get_networkCIDR() . "\n";
	echo "networkBrd " . $net->get_networkBrd() . "\n";
	echo "networkMask " . $net->get_netMask() . "\n";
	
/* testing	
	echo "localIPV4 " . $this->localIPV4 . "\n";
    echo "networkIPV4 " . $this->networkIPV4 . "\n";
    echo "networkCIDR " . $this->networkCIDR . "\n";
    echo "networkmask " . $this->netMask . "\n";
*/ 

 

 

?>		

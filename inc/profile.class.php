<?php
/*
 * @version $Id: HEADER 1 2009-09-21 14:58 Tsmr $
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2009 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 --------------------------------------------------------------------------
 
// ----------------------------------------------------------------------
// Original Author of file: CAILLAUD Xavier & COLLET Remi
// Purpose of file: plugin addressing v1.8.0 - GLPI 0.80
// ----------------------------------------------------------------------
 */

if (!defined('GLPI_ROOT')) {
	die("Sorry. You can't access directly to this file");
}

class PluginAddressingProfile extends CommonDBTM {
	
	static function getTypeName() {
      global $LANG;

      return $LANG['plugin_addressing']['profile'][0];
   }
   
   function canCreate() {
      return haveRight('profile', 'w');
   }

   function canView() {
      return haveRight('profile', 'r');
   }
   
	//if profile deleted
	function cleanProfiles($ID) {

		$query = "DELETE 
				FROM `".$this->getTable()."`
				WHERE `profiles_id` = '$ID' ";
		
		$DB->query($query);
	}
	
	function getFromDBByProfile($profiles_id) {
		global $DB;
		
		$query = "SELECT * FROM `".$this->getTable()."`
					WHERE `profiles_id` = '" . $profiles_id . "' ";
		if ($result = $DB->query($query)) {
			if ($DB->numrows($result) != 1) {
				return false;
			}
			$this->fields = $DB->fetch_assoc($result);
			if (is_array($this->fields) && count($this->fields)) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}
	
	static function createFirstAccess($ID) {
      
      $myProf = new self();
      if (!$myProf->getFromDBByProfile($ID)) {

         $myProf->add(array(
            'profiles_id' => $ID,
            'addressing' => 'w'));
            
      }
   }
	
	function createAccess($ID) {

      $this->add(array(
      'profiles_id' => $ID));
   }
	
	static function changeProfile() {
      
      $prof = new self();
      if ($prof->getFromDBByProfile($_SESSION['glpiactiveprofile']['id']))
         $_SESSION["glpi_plugin_addressing_profile"]=$prof->fields;
      else
         unset($_SESSION["glpi_plugin_addressing_profile"]);
   }

	//profiles modification
	function showForm ($ID, $options=array()) {
		global $LANG;

		if (!haveRight("profile","r")) return false;

		$prof = new Profile();
		if ($ID) {
			$this->getFromDBByProfile($ID);
			$prof->getFromDB($ID);
		}

      $this->showFormHeader($options);

		echo "<tr class='tab_bg_2'>";
		
		echo "<th colspan='2'>".$LANG['plugin_addressing']['profile'][0]." ".$prof->fields["name"]."</th>";
		
		echo "<td>".$LANG['plugin_addressing']['profile'][3].":</td><td>";
		Profile::dropdownNoneReadWrite("addressing",$this->fields["addressing"],1,1,1);
		echo "</td>";
		
		echo "</tr>";
      
      echo "<input type='hidden' name='id' value=".$this->fields["id"].">";
      
		$options['candel'] = false;
      $this->showFormButtons($options);
	}
}

?>
<?php

/*
   ----------------------------------------------------------------------
   FusionInventory
   Copyright (C) 2010-2011 by the FusionInventory Development Team.

   http://www.fusioninventory.org/   http://forge.fusioninventory.org/
   ----------------------------------------------------------------------

   LICENSE

   This file is part of FusionInventory.

   FusionInventory is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 2 of the License, or
   any later version.

   FusionInventory is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with FusionInventory.  If not, see <http://www.gnu.org/licenses/>.

   ------------------------------------------------------------------------
   Original Author of file: David DURIEUX
   Co-authors of file:
   Purpose of file:
   ----------------------------------------------------------------------
 */

function plugin_fusinvinventory_getAddSearchOptions($itemtype) {
   global $LANG;

   $sopt = array();
   if ($itemtype == 'Computer') {

         $sopt[5150]['table']     = 'glpi_plugin_fusinvinventory_libserialization';
         $sopt[5150]['field']     = 'last_fusioninventory_update';
         $sopt[5150]['linkfield'] = '';
         $sopt[5150]['name']      = $LANG['plugin_fusioninventory']['title'][1]." - ".
            $LANG['plugin_fusinvinventory']['computer'][0];
         $sopt[5150]['datatype']  = 'datetime';
         $sopt[5150]['itemlink_type'] = 'PluginFusinvinventoryLib';

         $sopt[5151]['table']     = 'glpi_plugin_fusinvinventory_antivirus';
         $sopt[5151]['field']     = 'name';
         $sopt[5151]['linkfield'] = '';
         $sopt[5151]['name']      = 'Antivirus name';
         $sopt[5151]['datatype']  = 'text';

         $sopt[5152]['table']     = 'glpi_plugin_fusinvinventory_antivirus';
         $sopt[5152]['field']     = 'version';
         $sopt[5152]['linkfield'] = '';
         $sopt[5152]['name']      = 'Antivirus version';
         $sopt[5152]['datatype']  = 'text';

         $sopt[5153]['table']     = 'glpi_plugin_fusinvinventory_antivirus';
         $sopt[5153]['field']     = 'is_active';
         $sopt[5153]['linkfield'] = '';
         $sopt[5153]['name']      = 'Antivirus activé';
         $sopt[5153]['datatype']  = 'bool';

         $sopt[5154]['table']     = 'glpi_plugin_fusinvinventory_antivirus';
         $sopt[5154]['field']     = 'uptodate';
         $sopt[5154]['linkfield'] = '';
         $sopt[5154]['name']      = 'Antivirus à jour';
         $sopt[5154]['datatype']  = 'bool';
   }
   return $sopt;
}

function plugin_fusinvinventory_install() {

   include (GLPI_ROOT . "/plugins/fusinvinventory/install/install.php");
   pluginFusinvinventoryInstall();

   return true;
}



// Uninstall process for plugin : need to return true if succeeded
function plugin_fusinvinventory_uninstall() {
   include (GLPI_ROOT . "/plugins/fusinvinventory/install/install.php");
   pluginFusinvinventoryUninstall();
}



/**
* Check if Fusinvinventory need to be updated
*
* @param
*
* @return 0 (no need update) OR 1 (need update)
**/
function plugin_fusinvinventory_needUpdate() {
   include (GLPI_ROOT . "/plugins/fusinvinventory/install/update.php");
   $version_detected = pluginFusinvinventoryGetCurrentVersion(PLUGIN_FUSINVINVENTORY_VERSION);
   if ((isset($version_detected)) 
      AND ($version_detected != PLUGIN_FUSINVINVENTORY_VERSION)
      AND $version_detected!='0') {
      return 1;
   } else {
      return 0;
   }
}



// Define headings added by the plugin //
function plugin_get_headings_fusinvinventory($item,$withtemplate) {
   global $LANG;

   switch (get_class($item)) {
      case 'Computer' :
         $array = array ();
         if ($_GET['id'] > 0
                AND $withtemplate!='1') {
            $array[1] = $LANG['plugin_fusioninventory']['title'][1]." ".
               $LANG['plugin_fusioninventory']['xml'][0];
            $array[2] = $LANG['plugin_fusinvinventory']['antivirus'][0];
            $array[3] = $LANG['plugin_fusinvinventory']['menu'][4];
            $array[4] = $LANG['plugin_fusinvinventory']['bios'][0];
         }
         return $array;
         break;
   }


}



// Define headings actions added by the plugin
function plugin_headings_actions_fusinvinventory($item) {

   switch (get_class($item)) {
      case 'Computer' :
         $array = array ();
         $array[1] = "plugin_headings_fusinvinventory_xml";
         $array[2] = "plugin_headings_fusinvinventory_antivirus";
         $array[3] = "plugin_headings_fusinvinventory_integrity";
         $array[4] = "plugin_headings_fusinvinventory_bios";
         return $array;
         break;
      case 'PluginFusioninventoryCredentialIp':
         return array(1 => "plugin_headings_fusinvinventory_credentialip");
   }

}


function plugin_headings_fusinvinventory_xml($item) {
   global $LANG,$CFG_GLPI;

   $id = $item->getField('id');

   $folder = substr($id, 0, -1);
   if (empty($folder)) {
      $folder = '0';
   }
   if (file_exists(GLPI_PLUGIN_DOC_DIR."/fusinvinventory/".$folder."/".$id)) {
      $xml = file_get_contents(GLPI_PLUGIN_DOC_DIR."/fusinvinventory/".$folder."/".$id);
      $xml = str_replace("<", "&lt;", $xml);
      $xml = str_replace(">", "&gt;", $xml);
      $xml = str_replace("\n", "<br/>", $xml);
      echo "<table class='tab_cadre_fixe' cellpadding='1'>";
      echo "<tr>";
      echo "<th>".$LANG['plugin_fusioninventory']['title'][1]." ".
         $LANG['plugin_fusioninventory']['xml'][0];
      echo " (".$LANG['plugin_fusinvinventory']['computer'][0]."&nbsp;: " . 
         Html::convDateTime(date("Y-m-d H:i:s", 
                      filemtime(GLPI_PLUGIN_DOC_DIR."/fusinvinventory/".$folder."/".$id))).")";
      echo "</th>";
      echo "</tr>";

      echo "<tr class='tab_bg_1'>";
      echo "<td width='130' align='center'>";
      echo "<a href='".$CFG_GLPI['root_doc']."/plugins/fusioninventory/front/send_xml.php?pluginname=fusinvinventory&file=".$folder."/".$id."'>".$LANG['document'][15]."</a>";
      echo "</td>";
      echo "</tr>";
      
      echo "<tr class='tab_bg_1'>";
      echo "<td>";
      echo "<pre width='130'>".$xml."</pre>";
      echo "</td>";
      echo "</tr>";
      echo "</table>";
   }
}



function plugin_headings_fusinvinventory_antivirus($item) {
   $antirivus = new PluginFusinvinventoryAntivirus();
   $antirivus->showForm($item->getField('id'));
}



function plugin_headings_fusinvinventory_integrity($item) {
   $pluginFusinvinventoryLibintegrity = new PluginFusinvinventoryLibintegrity();
   $pluginFusinvinventoryLibintegrity->showForm($item->getField('id'));
}


function plugin_headings_fusinvinventory_bios($item) {
   global $DB,$LANG;
   
   if (get_class($item) == 'Computer') {
      $bios=array(
         'BMANUFACTURER'=>'',
         'BVERSION'=>'',
         'BDATE'=>'');

      $pluginFusinvinventoryLib = new PluginFusinvinventoryLib();
      $query = "SELECT internal_id FROM ".$pluginFusinvinventoryLib->getTable()." 
         WHERE computers_id='".$item->fields['id']."'";
      $result = $DB->query($query);
      if($result AND $DB->numrows($result) > 0) {
         $intid = $DB->result($result,0,'internal_id');
         $infosSections = $pluginFusinvinventoryLib->_getInfoSections($intid);
         if($infosSections) {
            foreach($infosSections['sections'] as $key => $val) {
               if(preg_match('/^BIOS/',$key)) {
                  foreach(unserialize($val) as $k => $info) {
                     if(isset($bios[$k])) {
                        $bios[$k] = $info;
                     }
                  }
               }
            }
         }
         // Display form
         echo '<div align="center">';
         echo '<table class="tab_cadre_fixe" style="margin: 0; margin-top: 5px;">';
         echo '<tr>';
         echo '<th colspan="2">'.$LANG['plugin_fusinvinventory']['bios'][0].'</th>';
         echo '</tr>';

         echo '<tr class="tab_bg_1">';
         echo '<td>'.$LANG['common'][27].'&nbsp;:</td>';
         echo '<td>'.$bios['BDATE'].'</td>';
         echo '</tr>';

         echo '<tr class="tab_bg_1">';
         echo '<td>'.$LANG['rulesengine'][78].'&nbsp;:</td>';
         echo '<td>'.$bios['BVERSION'].'</td>';
         echo '</tr>';

         echo '<tr class="tab_bg_1">';
         echo '<td>'.$LANG['common'][5].'&nbsp;:</td>';
         echo '<td>'.$bios['BMANUFACTURER'].'</td>';
         echo '</tr>';
         echo '</table>';
         echo '</div>';
      }
   }
}



function plugin_headings_fusinvinventory($type,$id,$withtemplate=0) {

}

function plugin_fusinvinventory_addLeftJoin($itemtype, $ref_table, $new_table, $linkfield, 
                                            &$already_link_tables) {
   
   if ($itemtype == 'Computer') {
      return " LEFT JOIN `$new_table` ON (`$ref_table`.`id` = `$new_table`.`computers_id`) ";
   }

   return "";
}

function plugin_pre_item_purge_fusinvinventory($item) {
   
   switch (get_class($item)) {

      case 'Computer' :
         $PluginFusinvinventoryLib = new PluginFusinvinventoryLib();
         $PluginFusinvinventoryLib->removeExternalid($item->getField('id'));
         // Remove antivirus if set
         PluginFusinvinventoryAntivirus::cleanComputer($item->getField('id'));
         break;

   }

}

function plugin_fusinvinventory_registerMethods() {
   global $WEBSERVICES_METHOD;
   
   $WEBSERVICES_METHOD['fusioninventory.test'] = array('PluginFusinvinventoryWebservice', 
                                                       'methodTest');
}

?>
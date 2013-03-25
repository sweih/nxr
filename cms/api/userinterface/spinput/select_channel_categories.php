<?php
    /**********************************************************************
     *    N/X - Web Content Management System
     *    Copyright 2003 Sven Weih
     *
     *    This file is part of N/X.
     *    The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
     *    It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
     *
     *    N/X is free software; you can redistribute it and/or modify
     *    it under the terms of the GNU General Public License as published by
     *    the Free Software Foundation; either version 2 of the License, or
     *    (at your option) any later version.
     *
     *    N/X is distributed in the hope that it will be useful,
     *    but WITHOUT ANY WARRANTY; without even the implied warranty of
     *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *    GNU General Public License for more details.
     *
     *    You should have received a copy of the GNU General Public License
     *    along with N/X; if not, write to the Free Software
     *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
     **********************************************************************/


    /**
     * Draws a selector for channels and categories.
     * @package DatabaseConnectedInput
     */
    class SelectChannelCategory extends DBO {

        /**
         * standard constructor
         * @param string Where Condition to filter
         */
        function SelectChannelCategory($row_identifier = "1") {
            global $lang, $chcat, $page_state, $page_action;
            DBO::DBO($lang->get("sel_channel_cat", "Select Channel and Category"), "centerstage", "CHID", $row_identifier, '', 'NUMBER', 'MANDATORY');


            if ($page_state != "processing") {
              $tmp = getDBCell("centerstage", "CH_CAT_ID", $row_identifier);
              if ($tmp != 0 && $tmp != "" && $tmp != "0") {
                  $this->value = $tmp;
              }
            }

            $this->populateSelectbox();
            $this->v_wuiobject = new Dropdown($this->name, $chcat, $this->std_style, $this->value, $this->width);
        }

        /**
         * Store the data
         */
        function process() {
                 // always update
                 if ($this->value == 1) {
                     $chid = 'NULL';
                     $chcatid = 'NULL';
                 } else {
                     $tmp = getDBCell("channel_categories", "CHID", "CH_CAT_ID = ".$this->value);
                     if ($tmp !=0 && $tmp != "") {
                         $chid = $tmp;
                         $chcatid = $this->value;
                     } else {
                         $chid = $this->value;
                         $chcatid = 'NULL';
                     }
                 }
                 addUpdate($this->table, "CHID", $chid, $this->row_identifier, "NUMBER");
                 addUpdate($this->table, "CH_CAT_ID", $chcatid, $this->row_identifier, "NUMBER");
        }

        /**
         * Populate selectbox
         */
         function populateSelectbox() {
           global $chcat, $lang;
           if (! isset($chcat)) {
             $chcat = array();
             $channels = createNameValueArrayEx("channels", "NAME", "CHID", "1 ORDER BY NAME ASC");
             if (!is_array($channels)) $channels=array();
             $chcat[] = array($lang->get("all", "All"), '1');
             foreach ($channels as $channel){
               $chcat[] = $channel;
               $categories = createNameValueArrayEx("channel_categories", "NAME", "CH_CAT_ID", "CHID = ".$channel[1]." ORDER BY NAME");
               if (count($categories)>0) {
                 foreach ($categories as $category) {
                   $chcat[] = array($channel[0]." - ".$category[0], $category[1]);
                 }
               }
             }
           }
         }
    }
?>
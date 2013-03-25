<?php
     /**********************************************************************
     *    N/X - Web Content Management System
     *    Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
     *    www.fzi.de
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
     * Container for displaying a Channel-Configuration object,
     * @package ContentManagement
     */
    class ChannelEnvelope extends AbstractEnvelope {

            
        /**
         * Draw the input boxes needed for editing the contents in the envelope.
         * @param integer id of cluster_content.CLCID
         */
        function getSingleEdit($id) {
            global $specialID, $lang;
            // create Channel/Categories Name-Value-Array
            $values = array();

            array_push($values, array($lang->get("latest", "Change Date, latest first."), 1));
            array_push($values, array($lang->get("oldest", "Change Date, oldest first"), 2));
            array_push($values, array($lang->get("random", "Random Order"), 3));
            array_push($values, array("A - Z", 4));
            array_push($values, array("Z - A", 5));
            array_push($values, array($lang->get("latest_created", "Article Date, latest first"), 6));
            array_push($values, array($lang->get("oldest_created", "Article Date, oldest first"), 7));
			array_push($values, array($lang->get("pos_asc", "Position ascending"), 8));
			array_push($values, array($lang->get("pos_desc", "Position descending"), 9));


            if ($this->editState && $this->editor) {
                 $specialID = $id;
                 $this->add(new SelectChannelCategory("STAGE_ID = $id"));
                 $this->add(new SelectOneInputFixed($lang->get("orderart", "Order of Artciles"), "centerstage", "SORT_ALGORITHM", $values, "STAGE_ID = $id", "type:dropdown"));
                 $this->add(new TextInput($lang->get("num_of_art", "Number of articles ( -1 =infinite)"), "centerstage", "MAXCARD", "STAGE_ID = $id", "type:text,size:3,width:40", "MANDATORY", "NUMBER"));
                 $specialID = "";
            } else {
                 $this->add(new Label("lbl", $lang->get("ch_cat2", "Channel and Category"), "standardwhite"));
                 $chn = getDBCell("centerstage", "CHID", "STAGE_ID = $id");
                 if ($chn == 0) {
                     $chn = $lang->get("all");
                 } else {
                     $chn = getDBCell("channels", "NAME", "CHID = $chn");
                 }
                 $cat = getDBCell("centerstage", "CH_CAT_ID", "STAGE_ID = $id");
                 if ($cat != 0) {
                     $chn.=" - ".getDBCell("channel_categories", "NAME", "CH_CAT_ID = $cat");
                 }
                 $this->add(new Label("lbl", "<b>".$chn."</b>", ""));
                 $this->add(new Label("lbl", $lang->get("orderart"), ""));
                 $this->add(new Label("lbl", "<b>".$values[getDBCell("centerstage", "SORT_ALGORITHM", "STAGE_ID = $id")-1][0]."</b>", "standardwhite"));
                 $this->add(new Label("lbl", $lang->get("num_of_art"), "standardwhite"));
                 $this->add(new Label("lbl", "<b>".getDBCell("centerstage", "MAXCARD", "STAGE_ID = $id")."</b>", "standardwhite"));
            }

        }

        /**
         * Create references for new content.
         * @param integer ID of the content to create.
         */
        function createReferencedItem($id) {
            createCenterstage($id);
        }


        /**
         * Delete the data in the plugins.
         * @param integer ID of the content to delete
         */
        function deleteReferencedItem($id) {
               deleteRow("centerstage", "STAGE_ID = $id");
        }
}
?>
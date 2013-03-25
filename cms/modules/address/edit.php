<?php
    /**********************************************************************
     *    N/X - Web Content Management System
     *    Copyright 2002-2006 Sven Weih
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

  require_once "../../config.inc.php";
  $auth = new auth("ADDRESS");
  $page = new Page("Edit Contacts");
  
  $form = new stdEDForm($lang->get("edcontact", "Edit contact"));
  $cond = $form->setPK("address", "GGUID");
  
  $form->addHeaderLink(crHeaderLink($lang->get("back"), "modules/address/overview.php?sid=".$sid));
  $form->add(new Subtitle("st", $lang->get("personal_information", "Personal Information")));
  if ($page_action != "CREATE") {
  	$form->add(new DisplayedValue($lang->get("nr", "NO"), "address", "GGUID", $cond));  	  	
  }
  $form->add(new TextInput($lang->get("adrletter", "Salutation"), "address", "AddressLetter", $cond, "type:text,size:64,width:200", ""));
  $form->add(new TextInput($lang->get("name", "Name"), "address", "Name", $cond, "type:text,size:64,width:200", "MANDATORY"));
  $form->add(new TextInput($lang->get("firstname", "Firstname"), "address", "Firstname", $cond, "type:text,size:64,width:200", ""));  
  $form->add(new TextInput($lang->get("email", "E-Mail"), "address", "MailAddress", $cond, "type:text,size:128,width:200", ""));
  $form->add(new DateInput($lang->get("birthday", "Birthday"), "address", "Birthday", $cond));
  $form->add(new Spacer(2));
  $form->add(new Subtitle('st',$lang->get("company", "Company")));
  $form->add(new TextInput($lang->get("compname", "Company Name"), "address", "Company", $cond, "type:text,size:64,width:200", ""));
  $form->add(new Spacer(2));
  $form->add(new Subtitle('st', $lang->get("address", "Address")));
  $form->add(new TextInput($lang->get("street1", "Street 1"), "address", "Street1", $cond, "type:text,size:128,width:300", ""));
  $form->add(new TextInput($lang->get("street2", "Street 2"), "address", "Street2", $cond, "type:text,size:128,width:300", ""));
  $form->add(new TextInput($lang->get("zip", "ZIP"), "address", "ZIP", $cond, "type:text,size:32,width:100", ""));
  $form->add(new TextInput($lang->get("city", "City"), "address", "City", $cond, "type:text,size:128,width:200", ""));
  $form->add(new TextInput($lang->get("region", "Region"), "address", "Region", $cond, "type:text,size:128,width:200", ""));
  $form->add(new TextInput($lang->get("country", "Country"), "address", "Country", $cond, "type:text,size:128,width:200", ""));
  $form->add(new Spacer(2));
  $form->add(new Subtitle("st", $lang->get("Contact", "Contact")));
  $form->add(new TextInput($lang->get("phone", "Phone"), "address", "Phone", $cond, "type:text,size:32,width:150", ""));  
  $form->add(new TextInput($lang->get("fax", "Fax"), "address", "Fax", $cond, "type:text,size:32,width:150", ""));  
  $form->add(new TextInput($lang->get("cellphone", "Cell Phone"), "address", "Cellphone", $cond, "type:text,size:32,width:150", ""));  
  $form->add(new Spacer(2));
  $form->add(new Subtitle("st", $lang->get("additional_info", "Additional Information")));
  $form->add(new TextInput($lang->get("website", "Website"), "address", "Website", $cond, "type:text,size:128,width:300", ""));  
  $form->add(new TextInput($lang->get("notes", "Notes"), "address", "Notes", $cond, "type:textarea,size:6,width:300", ""));  
  $form->add(new NonDisplayedValue("address", "LAST_Modified", $cond, getdate()));
  
  $page->add($form);
  $page->draw();
?>
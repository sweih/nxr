<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih, FZI Research Center for Information Technologies
	 *	www.fzi.de
	 *
	 *	This file is part of N/X.
	 *	The initial has been setup as a small diploma thesis (Studienarbeit) at the FZI.
	 *	It was be coached by Prof. Werner Zorn and Dipl.-Inform Thomas Gauweiler.
	 *
	 *	N/X is free software; you can redistribute it and/or modify
	 *	it under the terms of the GNU General Public License as published by
	 *	the Free Software Foundation; either version 2 of the License, or
	 *	(at your option) any later version.
	 *
	 *	N/X is distributed in the hope that it will be useful,
	 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
	 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	 *	GNU General Public License for more details.
	 *
	 *	You should have received a copy of the GNU General Public License
	 *	along with N/X; if not, write to the Free Software
	 *	Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
	 **********************************************************************/
	/**
	 * Creates a save, cancel and save&back button.
	 * @version 1.00
	 */
	class FormButtons extends Container {

		/**
		   * Standard constructor
		   * @param boolean $onlySave Display only the save button
		   */
		function FormButtons($onlySave = false, $resetButton=false, $formname="form1") {
			global $lang;

			$container = new HTMLContainer("con1", "", 2);
			$container->add('<table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td align="right">');
			
			if ($resetButton) {
			  $lb4 = new ButtonInline("rst", $lang->get("reset"), "navelement", "reset");
			  $container->add($lb4->draw(). '&nbsp;&nbsp;');
			}
			
			if (!$onlySave) {
				$lb3 = new ButtonInline("action", $lang->get("back"), "navelement", "submit", getWaitupScreen($formname));
				$container->add($lb3->draw(). drawSpacer(50,1));
				$lb2 = new ButtonInline("action", $lang->get("save_back", "Save and Back"), "navelement", "submit", getWaitupScreen($formname));
				$container->add($lb2->draw(). '&nbsp;&nbsp;');
				
			}
			$lb1 = new ButtonInline("action", $lang->get("save", "Save"), "navelement", "submit", getWaitupScreen($formname));
			$container->add($lb1->draw());

			$actionField = new ActionField();
			$container->add($actionField->get());
			$container->add('&nbsp;&nbsp;&nbsp;</td></tr></table>');
			$this->add($container);
		}
	}
?>
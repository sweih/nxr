<?
	/**********************************************************************
	 *	N/X - Web Content Management System
	 *	Copyright 2002 Sven Weih and Fabian Koenig
	 *	This file is part of N/X.
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
	class CDSInterface {
		var $parent = null;

		var $drawHandlers = null;
		var $pageId;
		var $pageClusterId = 0;
		var $pageClusterNodeId = 0;
		var $variation;

		/**
		 * Standard constructor
		 * @param object References the parent class
		 */
		function CDSInterface(&$parent) {
			$this->parent = &$parent;

			$this->pageId = $this->parent->pageId;
			$this->variation = $this->parent->variation;
			$this->pageClusterId = $this->parent->pageClusterId;
			$this->pageClusterNodeId = $this->parent->pageClusterNodeId;
		}

		/**
		 * Returns the content regarding to its contenthandler.
		 * @param string Name of the contentfield in the cluster
		 * @param integer Variation-Id of the Content, 0 sets current selected
		 * @param array assoc. Array with parameters
		 */
		function get($name, $variation = 0, $params = null) { echo $this->get($name, $variation, $params); }

		/**
		 * Draws the content regarding to its contenthandler.
		 * @param string Name of the contentfield in the cluster
		 * @param integer Variation-Id of the Content, 0 sets current selected
		 * @param array assoc. Array with parameters
		 */
		function draw($name, $variation = 0, $params = null) {
			// todo: change plugin interface...
			echo $this->get($name, $variation, $params); }

		/**
		 * gets contents organized as list
		 */
		function getField($name, $variation, $limit, $params = null) { }

		/**
		 * draws contents organized as list
		 */
		function drawField($name, $variation, $limit, $params = null) { }

		/**
		 * Register a new DrawHandler for use with draw function
		 * @param string Name of the function to call for draw.
		 * @param string Name of the Plugin to register the handler for
		 */
		function registerDrawHandler($functionName, $pluginName) {
			// todo: register Handler
			}
	}
?>
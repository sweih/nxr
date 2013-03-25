/********************************************************************

Name:			Javascript Window-Library.
File:			/js/windows.js
Description:	Common javascript-library for popup-windows
Requires:		-
Parameters:		-

Author:			Sven Weih, 12.07.2001
Changes:	

Copyright (C) 2001 by Sven Weih. All rights reserved.

********************************************************************/

function win(adresse, mwidth, mheight) {
	mywindow = window.open(adresse, "output", 'screenX=5, screenY=5,height=' + mheight + ',width=' + mwidth + ',menubar=no,resizeable=yes', 'replace');

	mywindow.moveTo((screen.width/2) - (mwidth/2), (screen.height/2) - (mheight/2));
	mywindow.focus();
}

function toggle(oid) {
  if (document.getElementById(oid).style.display == 'block') {
    document.getElementById(oid).style.display = 'none';
  } else {
    document.getElementById(oid).style.display = 'block';
  }
}
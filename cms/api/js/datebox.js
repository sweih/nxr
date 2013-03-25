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
 *
 *
 *
 *	example:
 *	<script language="JavaScript">
 *	 	createDateBox("dateboxname", "formname", "current");
 *	</script>
 *
 *	creates a datebox which can be read after posting the form
 *	by accessing start_date. f1 ist the name of the form the date is in,
 *	the third parameter (current) is to determine the date to display.
 *		current		-  today.
 *		end		-  last day in date range.
 *		begin		-  first day in date range.
 *		2004-04-30	-  30th of April 2004
 *	
 * 	Note. start_date is in format YYYY-MM-DD (mySQL-Date-Format)
 * 	You can change the format by editing updateHidden-function.	
 ******************************************************/

timeInput = false;

function createDateBox(selector, formname, mydate, stylesheet) {
	document.write("<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr>");

	document.write("<td class=\"" + stylesheet + "\"><select width=\"60\" style=\"width:65;\"  name=\"" + selector + "_year\" onChange='getDays(this.form." + selector + "_day ,\"" + selector + "\",\"" + formname + "\",\"\");'></select>&nbsp;/&nbsp;</td>");
	document.write("<td class=\"" + stylesheet + "\"><select width=\"45\" style=\"width:45;\"  name=\"" + selector + "_month\" onChange='getDays(this.form." + selector + "_day ,\"" + selector + "\",\"" + formname + "\",\"\");'></select>&nbsp;/&nbsp;</td>");
	document.write("<td class=\"" + stylesheet + "\"><select width=\"45\" style=\"width:45;\"  name=\"" + selector + "_day\" onChange='updateHidden(\"" + formname + "\", \"" + selector + "\")'><option> </option></select>");
	document.write("<input type=\"hidden\" name=\"" + selector + "_date\" value=\"emtpy\"></td>");
	document.write("</tr></table>");

	if (mydate == "current" || mydate == "begin" || mydate == "end") {
		myYear = mydate;

		myMonth = mydate;
		myDay = mydate;
	} else if (mydate.length == 10) {
		myYear = mydate.substring(0, 4);

		myMonth = mydate.substring(5, 7);
		myDay = mydate.substring(8, 10);
	} else {
		myYear = "current";

		myMonth = "current";
		myDay = "current";
	}

	eval("getYears(document." + formname + "." + selector + "_year, myYear);");
	eval("getMonths(document." + formname + "." + selector + "_month, myMonth);");
	eval("getDays(document." + formname + "." + selector + "_day, \"" + selector + "\", \"" + formname + "\", myDay);");
}

function createDateTimeBox(selector, formname, mydate, stylesheet) {
	timeInput = true;

	document.write("<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\"><tr>");
	document.write("<td class=\"" + stylesheet + "\"><select width=\"60\" style=\"width:65;\" name=\"" + selector + "_year\" onChange='getDays(this.form." + selector + "_day ,\"" + selector + "\",\"" + formname + "\",\"\");'></select>&nbsp;/&nbsp;</td>");
	document.write("<td class=\"" + stylesheet + "\"><select width=\"45\" style=\"width:45;\" name=\"" + selector + "_month\" onChange='getDays(this.form." + selector + "_day ,\"" + selector + "\",\"" + formname + "\",\"\");'></select>&nbsp;/&nbsp;</td>");
	document.write("<td class=\"" + stylesheet + "\"><select width=\"45\" style=\"width:45;\" name=\"" + selector + "_day\" onChange='updateHidden(\"" + formname + "\", \"" + selector + "\")'><option> </option></select>&nbsp;&nbsp;&nbsp;</td>");
	document.write("<td class=\"" + stylesheet + "\"><select width=\"45\" style=\"width:45;\" name=\"" + selector + "_hour\" onChange='updateHidden(\"" + formname + "\", \"" + selector + "\")'><option> </option></select>&nbsp;:&nbsp;</td>");
	document.write("<td class=\"" + stylesheet + "\"><select width=\"45\" style=\"width:45;\" name=\"" + selector + "_minute\" onChange='updateHidden(\"" + formname + "\", \"" + selector + "\")'><option> </option></select>");
	document.write("<input type=\"hidden\" name=\"" + selector + "_date\" value=\"emtpy\"></td>");
	document.write("</tr></table>");

	if (mydate == "current" || mydate == "begin" || mydate == "end") {
		myYear = mydate;

		myMonth = mydate;
		myDay = mydate;
		myHour = mydate;
		myMinute = mydate;
	} else if (mydate.length == 19) {
		myYear = mydate.substring(0, 4);

		myMonth = mydate.substring(5, 7);
		myDay = mydate.substring(8, 10);
		myHour = mydate.substring(11, 13);
		myMinute = mydate.substring(14, 16);
	} else {
		myYear = "current";

		myMonth = "current";
		myDay = "current";
		myMinute = "current";
		myHour = "current";
	}

	eval("getYears(document." + formname + "." + selector + "_year, myYear);");
	eval("getMonths(document." + formname + "." + selector + "_month, myMonth);");
	eval("getDays(document." + formname + "." + selector + "_day, \"" + selector + "\", \"" + formname + "\", myDay);");
	eval("getHours(document." + formname + "." + selector + "_hour, myHour);");
	eval("getMinutes(document." + formname + "." + selector + "_minute, myMinute);");
}

function updateHidden(formname, selector) {
	now = new Date();

	yearc = now.getYear();
	year = eval("document.forms." + formname + "." + selector + "_year.selectedIndex") + yearc;
	month = eval("document.forms." + formname + "." + selector + "_month.selectedIndex") + 1;
	day = eval("document.forms." + formname + "." + selector + "_day.selectedIndex") + 1;

	if (month < 10)
		month = "0" + month;

	if (day < 10)
		day = "0" + day;

	mydate = year + "-" + month + "-" + day;

	if (timeInput) {
		hour = eval("document.forms." + formname + "." + selector + "_hour.selectedIndex");

		minute = eval("document.forms." + formname + "." + selector + "_minute.selectedIndex");

		if (hour < 10)
			hour = "0" + hour;

		if (minute < 10)
			minute = "0" + minute;

		mydate = mydate + " " + hour + ":" + minute + ":00";
	}

	eval("document.forms." + formname + "." + selector + "_date.value = '" + mydate + "'");
}

function getDays(reference, selector, formname, selindex) {
	now = new Date();

	day = now.getDate();
	yearc = now.getYear();
	year = eval("document.forms." + formname + "." + selector + "_year.selectedIndex") + yearc;
	month = eval("document.forms." + formname + "." + selector + "_month.selectedIndex") + 1;
	oldDay = eval("document.forms." + formname + "." + selector + "_day.selectedIndex") + 1;
	timeA = new Date(year, month, 1);
	timeDifference = timeA - 86400000;
	timeB = new Date(timeDifference);
	var daysInMonth = timeB.getDate();

	for (var i = 0; i < reference.length; i++) {
		reference.options[i] = null;
	}

	for (var i = 0; i < daysInMonth; i++) {
		if (i < 9) {
			prefix = "0";
		} else {
			prefix = "";
		}

		reference.options[i] = new Option(prefix + (i + 1));
		reference.options[i].value = prefix + (i + 1);
	}

	if (selindex == "" && oldDay != 1) {
		if (oldDay >= daysInMonth)
			oldDay = daysInMonth;

		if (oldDay == 30 && daysInMonth == 31)
			oldDay++;

		reference.options[oldDay - 1].selected = true;
	} else if (selindex == "begin") {
		reference.options[0].selected = true;
	} else if (selindex == "end") {
		reference.options[daysInMonth - 1].selected = true;
	} else if (selindex == "current") {
		reference.options[day - 1].selected = true;
	} else {
		for (i = 0; i < daysInMonth; i++) {
			if (reference.options[i].text == selindex)
				reference.options[i].selected = true;
		}
	}

	updateHidden(formname, selector);
}

function getMonths(reference, selindex) {
	now = new Date();

	month = now.getMonth();
	var months = new Array("01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12");

	for (var i = 0; i < months.length; i++) {
		reference.options[i] = new Option(months[i]);
	}

	if (selindex == "begin") {
		reference.options[0].selected = true;
	} else if (selindex == "end") {
		reference.options[11].selected = true;
	} else if (selindex == "current") {
		reference.options[month].selected = true;
	} else {
		for (i = 0; i < 12; i++) {
			if (reference.options[i].text == selindex)
				reference.options[i].selected = true;
		}
	}
}

function getYears(reference, selindex) {
	now = new Date();

	year = now.getYear();

	if (document.layers)
		year += 1900;

	var years = new Array(year, year + 1, year + 2, year + 3, year + 4, year + 5, year + 6, year + 7, year + 8, year + 9);
	timeC = new Date();
	currYear = timeC.getFullYear();

	for (var i = 0; i < years.length; i++) {
		reference.options[i] = new Option(years[i]);
	}

	if (selindex == "begin" || selindex == "current") {
		reference.options[0].selected = true;
	} else if (selindex == "end") {
		reference.options[years.length - 1].selected = true;
	} else {
		for (i = 0; i < 10; i++) {
			if (reference.options[i].text == selindex)
				reference.options[i].selected = true;
		}
	}
}

function getHours(reference, selindex) {
	now = new Date();

	hour = now.getHours();

	for (i = 0; i < 24; i++) {
		if (i < 10) {
			prefix = "0";
		} else {
			prefix = "";
		}

		reference.options[i] = new Option(prefix + (i));
		reference.options[i].value = prefix + (i);
	}

	if (selindex == "begin") {
		reference.options[0].selected = true;
	} else if (selindex == "end") {
		reference.options[23].selected = true;
	} else if (selindex == "current") {
		reference.options[hour].selected = true;
	} else {
		for (i = 0; i < 24; i++) {
			if (reference.options[i].text == selindex)
				reference.options[i].selected = true;
		}
	}
}

function getMinutes(reference, selindex) {
	now = new Date();

	minutes = now.getMinutes();

	for (var i = 0; i < 60; i++) {
		if (i < 10) {
			prefix = "0";
		} else {
			prefix = "";
		}

		reference.options[i] = new Option(prefix + (i));
		reference.options[i].value = prefix + (i);
	}

	if (selindex == "begin") {
		reference.options[0].selected = true;
	} else if (selindex == "end") {
		reference.options[59].selected = true;
	} else if (selindex == "current") {
		reference.options[minutes].selected = true;
	} else {
		for (i = 0; i < 60; i++) {
			if (reference.options[i].text == selindex)
				reference.options[i].selected = true;
		}
	}
}
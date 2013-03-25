function PartWrapperToggle(elementName) {
	// var HeaderElement = null;
	var BodyElement = null;

	if (document.getElementsByName) {
		alert(elementName);

		// HeaderElement = document.getElementsByName(elementName+"Header");
		BodyElement = document.getElementsByName(elementName + "Body");

		// UpImage = document.getElementsByName(elementName+"Up");
		// DownImage = document.getElementsByName(elementName+"Down");
		if (BodyElement) {
			if (BodyElement[0].style.display == "none") {
				BodyElement[0].style.display = "block";
			// HeaderElement[0].className = "ListNuggetHeader";
			// DownImage[0].style.display = "none";
			// UpImage[0].style.display = "block";
			} else {
				BodyElement[0].style.display = "none";
			// HeaderElement[0].className = "ListNuggetHeaderClosed";
			// UpImage[0].style.display = "none";
			// DownImage[0].style.display = "block";
			}
		}
	}
	
	return false;
}

function PartWrapperSwitch(elementName, num, panels) {
	
	var BodyElement = null;
	var i = 0;

	if (document.getElementsByName) {
		for (i = 1; i < panels; i++) {
			BodyElement = document.getElementsByName(elementName + i + "Body");
			TabHead = document.getElementById(elementName + i + "Head");
			TabLeft = document.getElementById(elementName + i+  "p1");
			TabRight = document.getElementById(elementName + i+  "p2");
			TabText = document.getElementById(elementName + i + "text");
			TabLine = document.getElementById(elementName + i + "line");
			if (BodyElement) {
				if (i == num) {
					BodyElement[0].style.display = "block";
				    if (TabHead) TabHead.className = 'activeTab';
				    if (TabLeft) TabLeft.className = 'active1';
				    if (TabRight) TabRight.className = 'active2';
				    if (TabLine) TabLine.className = 'active3';
				    if (TabText) TabText.className = 'activeTabText';
				} else {
					BodyElement[0].style.display = "none";
					if (TabHead) TabHead.className = 'inactiveTab';
				    if (TabLeft) TabLeft.className = 'inactive1';
				    if (TabRight) TabRight.className = 'inactive2';
				    if (TabLine) TabLine.className = 'inactive3';
				    if (TabText) TabText.className = 'inactiveTabText';
				}
			}
		}
	}
	return false;
}
var sActiveImage = "";
var sActiveLink = "";

function getImage(imagePath) { 
	sActiveImage = imagePath;
}

function getDoc(fileName) {
    sActiveLink = fileName;
}

function ok() {
	if (sActiveImage != "")
	   window.opener.SetUrl(sActiveImage);
	if (sActiveLink != "")
		window.opener.SetUrl(sActiveLink);
	window.close();
	window.opener.focus();
}
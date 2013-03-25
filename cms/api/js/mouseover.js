var pictures = new Array();

function preload(imgArray, name, suffix, number) {
	for (var i = 0; i < number; i++) {
		imgArray[i] = new Image();

		if (typeof (name) == "string")
			imgArray[i].src = name + ((i < 10) ? "0" : "") + i + suffix;
		else
			imgArray[i].src = name[i] + suffix;
	}

	return true;
}
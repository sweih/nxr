/*******
cbe_core.js
CrossBrowserElement v3b16 (Core)
Cross-Browser DHTML for IE 4+, NN 4+, Gecko, and Opera 4+
Download the latest version at cross-browser.com

Copyright (c) 2001 Michael Foster (mfoster@cybrtyme.com)
This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.
This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details, at www.gnu.org.
A copy of the GNU LGPL has been included with this distribution.
*******/

var cbeVersion = "v3b16";      //~~~ v3b16
var cbeBodyId = 'cbeBodyId';
var cbeAll;
var is;

window.onload = function() {
	cbeInitialize();

	if (window.windowOnload) {
		window.windowOnload();
	}
}

window.onunload = function() { // Opera5.12 calls this twice???    //~~~ v3b16
	if (window.windowOnunload) {
		window.windowOnunload();
	}

	for (var i = 0; i < cbeAll.length; i++) {
		if (cbeAll[i]) {
			if (cbeAll[i].ele) {
				if (cbeAll[i].ele.cbe) {
					cbeAll[i].ele.cbe = null;
				}
			}
		}
	}
}

///////////////////////////////////// CrossBrowserElement Object Constructor
function CrossBrowserElement(eleObject, indexNum) {
	if (arguments.length > 2) {
		alert('cbe error: arguments > 2 in CrossBrowserElement()');

		return;
	}

	if (!eleObject) {
		alert('cbe error: arg1 is null in CrossBrowserElement()');

		return;
	}

	//// Core Properties:
	if (indexNum == 0) {
		this.id = cbeBodyId;

		this.parent = null;
	} else {
		this.id = eleObject.id;

		this.parent = cbeGetElementParent(eleObject);
	}

	this.ele = eleObject;
	this.index = indexNum;
	this.w = 0;
	this.h = 0;
	this.x = 0;
	this.y = 0;
	this.path = new Array(); // for animation methods

	//// Core Methods:
	this.sizeTo = cbeSizeTo;
	this.sizeBy = cbeSizeBy;
	this.resizeTo = cbeResizeTo; //~~~ v3b16
	this.resizeBy = cbeResizeBy; //~~~ v3b16
	this.moveTo = cbeMoveTo;
	this.moveBy = cbeMoveBy;
	this.show = cbeShow;
	this.hide = cbeHide;
	this.contains = cbeContains;
	this.scrollLeft = cbeScrollLeft;
	this.scrollTop = cbeScrollTop;
	// this.getImgByName = cbeGetImageByName;       //~~~ v3b16
	this.left = domLeft;
	this.top = domTop;
	this.width = domWidth;
	this.height = domHeight;
	this.offsetLeft = domOffsetLeft;
	this.offsetTop = domOffsetTop;

	this.zIndex = domZIndex;
	this.innerHtml = domInnerHtml;
	this.visibility = domVisibility;
	//  this.display =            //~~~ to be implemented
	this.color = domColor;
	this.background = domBackground;
	this.relativePosition = cbeRelativePosition;

	this.clip = domClip;
	this.clipTop = domClipTop;
	this.clipRight = domClipRight;
	this.clipBottom = domClipBottom;
	this.clipLeft = domClipLeft;
	this.clipWidth = domClipWidth;
	this.clipHeight = domClipHeight;
	this.clipArray = domClipArray;

	// Adapt interface to browser-specifics:
	if (is.ie4up) {                      // IE4+
		this.left = ieLeft;

		this.top = ieTop;
		this.width = ieWidth;
		this.height = ieHeight;
	} else if (is.opera) {               // Opera
		this.left = ieLeft;

		this.top = ieTop;
		this.width = ieWidth;
		this.height = ieHeight;
		this.offsetLeft = domOffsetLeft; //cbeReturnZero;     //~~~ v3b16
		this.offsetTop = domOffsetTop;   //cbeReturnZero;       //~~~ v3b16
		this.background = opBackground;
		this.clip = opClip;
		this.clipTop = cbeReturnZero;
		this.clipRight = cbeReturnZero;
		this.clipBottom = cbeReturnZero;
		this.clipLeft = cbeReturnZero;
		this.clipWidth = opClipWidth;
		this.clipHeight = opClipHeight;
		this.clipArray = nnClipArray;
		this.innerHtml = opInnerHtml;
		this.scrollBy = cbeReturnZero;
		this.clipBy = cbeReturnZero;
		this.autoClip = cbeReturnZero;
	} else if (is.gecko) {               // Gecko
		;
	} else if (is.nav4up) {              // NN4+
		this.left = nnLeft;

		this.top = nnTop;
		this.width = nnWidth;
		this.height = nnHeight;
		this.offsetLeft = nnOffsetLeft;
		this.offsetTop = nnOffsetTop;
		this.visibility = nnVisibility;
		this.zIndex = nnZIndex;
		this.background = nnBackground;
		this.color = nnColor;
		this.clip = nnClip;
		this.clipTop = nnClipTop;
		this.clipRight = nnClipRight;
		this.clipBottom = nnClipBottom;
		this.clipLeft = nnClipLeft;
		this.clipWidth = nnClipWidth;
		this.clipHeight = nnClipHeight;
		this.clipArray = nnClipArray;
		this.innerHtml = nnInnerHtml;
	} else {
		alert("cbe error: Unsupported browser");

		return;
	}

	// Add a property to this Element object, pointing to this CBE object
	this.ele.cbe = this;

	// if not body object initialize width and height, and clip auto:
	if (this.index != 0) {
		if (is.ie)
			this.resizeTo(this.ele.offsetWidth, this.ele.offsetHeight);
		else if (is.gecko)
			this.resizeTo(this.ele.offsetWidth, this.ele.offsetHeight);
		else if (is.nav)
			this.resizeTo(this.ele.clip.width, this.ele.clip.height);
		else if (is.opera)
			this.resizeTo(this.ele.style.pixelWidth, this.ele.style.pixelHeight);
	// this.hide(); //~~~ v3b16
	}
}

///////////////////////////////////// Create the Cross-Browser Object Model
function cbeInitialize() {
	if (!is.ie && !is.nav && !is.gecko && !is.opera)
		return;

	//// If other cbe_xxx.js files have been included
	//// bind required functions and variables to CBE object methods and properties.

	// Event Methods (in cbe_event.js)
	if (window.cbeEventJsLoaded) {
		CrossBrowserElement.prototype.addEventListener = _cbeAddEventListener;

		CrossBrowserElement.prototype.removeEventListener = _cbeRemoveEventListener;
		// Add CrossBrowserEvent object to window
		window.cbeEvent = new CrossBrowserEvent();

		// Hook resize event for NN4 and Opera
		if (is.nav4 || is.opera)
			cbeAddEventListener(window, "resize", cbeDefaultOnResizeListener);
	}

	// Animation Methods (in cbe_anim.js)
	if (window.cbeAnimJsLoaded) {
		CrossBrowserElement.prototype.slideTo = cbeSlideTo;

		CrossBrowserElement.prototype.arcTo = cbeArcTo;
		CrossBrowserElement.prototype.linearPath = cbeLinearPath;
		CrossBrowserElement.prototype.circularPath = cbeCircularPath;
		CrossBrowserElement.prototype.ellipticalPath = cbeEllipticalPath;
		CrossBrowserElement.prototype.followPath = cbeFollowPath;
		CrossBrowserElement.prototype.cyclePaths = cbeCyclePaths;
		CrossBrowserElement.prototype.stopCycle = cbeStopCycle;
		CrossBrowserElement.prototype.slideToY = cbeSlideToY; //
		CrossBrowserElement.prototype.yTarget = 0;            // for slideToY
		CrossBrowserElement.prototype.moving = false;
		CrossBrowserElement.prototype.walk = null;
		CrossBrowserElement.prototype.stop = true;

		if (!CrossBrowserElement.prototype.timeout)
			CrossBrowserElement.prototype.timeout = 10;
	}

	// Slide Methods (in cbe_slide.js)      //~~~ v3b16
	if (window.cbeSlideJsLoaded) {
		// cbeSlide methods
		CrossBrowserElement.prototype.slideBy = cbeSlideBy;

		CrossBrowserElement.prototype.slideTo = cbeSlideTo2;
		CrossBrowserElement.prototype.slide = cbeSlide;
		// cbeSlideCorner methods
		CrossBrowserElement.prototype.slideCornerBy = cbeSlideCornerBy;
		CrossBrowserElement.prototype.slideCornerTo = cbeSlideCornerTo;
		CrossBrowserElement.prototype.slideCorner = cbeSlideCorner;
		// cbeSlideCorner properties
		CrossBrowserElement.prototype.corner = "";
		// cbeEllipse methods
		CrossBrowserElement.prototype.ellipse = cbeEllipse0;
		CrossBrowserElement.prototype.ellipse1 = cbeEllipse1;
		// cbeEllipse properties
		CrossBrowserElement.prototype.radiusInc = 0;
		// cbeParametricEquation methods
		CrossBrowserElement.prototype.parametricEquation = cbeParametricEquation0;
		CrossBrowserElement.prototype.parametricEquation1 = cbeParametricEquation1;
		// cbeParametricEquation properties
		CrossBrowserElement.prototype.centerX = 0;
		CrossBrowserElement.prototype.centerY = 0;
		CrossBrowserElement.prototype.exprX = "";
		CrossBrowserElement.prototype.exprY = "";
		CrossBrowserElement.prototype.t = 0;
		CrossBrowserElement.prototype.tStep = .008;
		// properties used by all the above
		CrossBrowserElement.prototype.xTarget = 0;
		CrossBrowserElement.prototype.yTarget = 0;
		CrossBrowserElement.prototype.slideTime = 1000;
		CrossBrowserElement.prototype.onSlideEnd = null;
		CrossBrowserElement.prototype.xA = 0;
		CrossBrowserElement.prototype.yA = 0;
		CrossBrowserElement.prototype.xD = 0;
		CrossBrowserElement.prototype.yD = 0;
		CrossBrowserElement.prototype.B = 0;
		CrossBrowserElement.prototype.C = 0;
		CrossBrowserElement.prototype.moving = false;
		CrossBrowserElement.prototype.stop = true;
		CrossBrowserElement.prototype.timeout = 35;
	}

	// Clipping Methods (in cbe_clip.js)
	if (window.cbeClipJsLoaded) {
		CrossBrowserElement.prototype.clipBy = cbeClipBy;

		CrossBrowserElement.prototype.autoClip = cbeAutoClip;
		CrossBrowserElement.prototype.scrollBy = cbeScrollBy;
		CrossBrowserElement.prototype.clipSpeed = 50;
		CrossBrowserElement.prototype.clipping = false;

		if (!CrossBrowserElement.prototype.timeout)
			CrossBrowserElement.prototype.timeout = 10;
	}

	//// Add document methods and properties
	document.getImageByName = cbeGetImageByName;
	document.getFormByName = cbeGetFormByName; //~~~ v3b16
	document.preloadImage = cbePreloadImage;   //~~~ v3b16
	document.displayImage = cbeDisplayImage;   //~~~ v3b16
	document.imagesNotLoaded = 0;              //~~~ v3b16
	//// Add window and document methods for W3C DOM support
	if (!document.getElementById)
		document.getElementById = cbeGetElementById;

	if (!document.getElementsByTagName)
		document.getElementsByTagName = cbeGetElementsByTagName;

	if (!document.body) {
		document.body = new Object();

		document.body.id = cbeBodyId;
	}

	if (window.cbeEventJsLoaded) {
		if (!window.addEventListener) {
			window.addEventListener = cbeWindowAddEventListener;

			window.removeEventListener = cbeWindowRemoveEventListener;
		}

		if (!document.addEventListener) {
			document.addEventListener = cbeDocumentAddEventListener;

			document.removeEventListener = cbeDocumentRemoveEventListener;
		}
	}

	//// Build array (cbeAll) of all CrossBrowserElement objects,
	//// including all DIV, SPAN, and LAYER elements with id != "".
	var i, j, ele, divArray;
	window.cbeAll = new Array();
	// document.body object is first
	cbeAll[0] = new CrossBrowserElement(document.body, 0);
	// Get all named DIV elements
	divArray = document.getElementsByTagName('DIV');

	for (i = 0, j = 1; i < divArray.length; i++) {
		ele = divArray[i];

		if (ele.id != "") {
			cbeAll[j] = new CrossBrowserElement(ele, j);

			++j;
		}
	}

	// Get all named SPAN elements
	if (!is.nav4) {
		divArray = document.getElementsByTagName('SPAN');

		for (i = 0; i < divArray.length; i++) {
			ele = divArray[i];

			if (ele.id != "") {
				cbeAll[j] = new CrossBrowserElement(ele, j);

				++j;
			}
		}
	}
} // end cbeInitialize

///////////////////////////////////// document Methods
function cbeGetElementById(eleId) {
	var ele = null;

	if (is.ie5up || is.gecko || is.opera)
		ele = document.getElementById(eleId);
	else if (is.ie4up)
		ele = document.all[eleId];
	else if (is.nav4up)
		ele = nnGetElementById(eleId);

	return ele;
}

function nnGetElementById(searchId)                    // assumes cbom is in place
	{
	for (var i = 0; i < cbeAll.length; i++) {
		if (cbeAll[i].id == searchId)
			return cbeAll[i].ele;
	}

	return null;
}

function cbeGetElementsByTagName(tagName) {
	var eleArray;

	if (is.opera)
		eleArray = document.body.getElementsByTagName(tagName);
	else if (is.ie4)
		eleArray = document.all.tags(tagName);
	else if (is.ie5up || is.gecko || is.nav5up)
		eleArray = document.getElementsByTagName(tagName); else if (is.nav4) {
		eleArray = new Array();

		nnGetAllLayers(window, eleArray, 0);
	}

	return eleArray;
}

function nnGetAllLayers(parent, layerArray, nextIndex) // recursive
	{
	var i, layer;

	// get all named layers
	for (i = 0; i < parent.document.layers.length; i++) {
		layer = parent.document.layers[i];

		layerArray[nextIndex++] = layer;

		if (layer.document.layers.length)
			nextIndex = nnGetAllLayers(layer, layerArray, nextIndex);
	}

	return nextIndex;
}

function cbeGetElementParent(child) {
	var parent = document.body; // assume cbom is in place

	if (is.ie || is.gecko || is.opera) {
		if (child.parentElement)
			parent = child.parentElement;
		else if (child.parentNode)
			parent = child.parentNode;
		else if (child.offsetParent)
			parent = child.offsetParent;
	} else if (is.nav4up) {
		if (child.parentLayer) {
			if (child.parentLayer != window)
				parent = child.parentLayer;
		}
	}

	return parent;
}

function cbeGetImageByName(imgName) // convenient for NN4.x
	{
	var i, j;

	if (document.images[imgName]) {
		return document.images[imgName];
	}

	if (is.nav4) { // assumes cbom is in place
		for (i = 0; i < cbeAll.length; i++) {
			if (cbeAll[i].ele.document) {
				for (j = 0; j < cbeAll[i].ele.document.images.length; j++) {
					if (imgName == cbeAll[i].ele.document.images[j].name) {
						return cbeAll[i].ele.document.images[j];
					}
				}
			}
		}
	}

	return null;
}

function cbeGetFormByName(frmName) // convenient for NN4.x   //~~~ v3b16
	{
	var i, j;

	if (document.forms[frmName]) {
		return document.forms[frmName];
	}

	if (is.nav4) { // assumes cbom is in place
		for (i = 0; i < cbeAll.length; i++) {
			if (cbeAll[i].ele.document) {
				for (j = 0; j < cbeAll[i].ele.document.forms.length; j++) {
					if (frmName == cbeAll[i].ele.document.forms[j].name) {
						return cbeAll[i].ele.document.forms[j];
					}
				}
			}
		}
	}

	return null;
}

function cbePreloadImage(imgName, imgUrl, imgWidth, imgHeight) { //~~~ v3b16
	var imgObj = null;

	switch (arguments.length) {
		case 4:
			imgObj = new Image(imgWidth, imgHeight);

			break;

		case 3:
			imgObj = new Image(imgWidth, imgWidth);

			break;

		case 2:
			imgObj = new Image();

			break;

		default: alert('cbe error: invalid arg count in preloadImage()');
	}

	if (!imgObj) {
		alert('cbe error: null imgObj in preloadImage()');
	} else {
		if (!is.opera) {
			++document.imagesNotLoaded;

			imgObj.onload = cbeImageHandler;
			imgObj.onerror = cbeImageHandler;
			imgObj.onabort = cbeImageHandler;
		}

		imgObj.src = imgUrl; // assign handlers before assigning src
		imgObj.name = imgName;
	}

	return imgObj;
}

function cbeImageHandler() { // Opera5.12 doesn't call this?               //~~~ v3b16
	--document.imagesNotLoaded; }

function cbeDisplayImage(srcImgObj, dstImgName) { //~~~ v3b16
	document.getImageByName(dstImgName).src = srcImgObj.src; }

///////////////////////////////////// CrossBrowserElement Methods
function cbeContains(leftPoint, topPoint, top, right, bottom, left) {
	if (arguments.length == 2)
		top = right = bottom = left = 0;
	else if (arguments.length == 3)
		right = bottom = left = top; else if (arguments.length == 4) {
		left = right;

		bottom = top;
	}

	return (leftPoint >= this.left() + left && leftPoint <= this.left() + this.width() - right && topPoint >= this.top() + top && topPoint <= this.top() + this.height() - bottom);
}

function cbeMoveTo(x_cr, y_mar, outside, endHandler) //~~~ v3b16
	{
	if (isFinite(x_cr)) {
		this.left(x_cr);

		this.top(y_mar);
	} else {
		this.relativePosition(x_cr, y_mar, outside);

		this.left(this.x);
		this.top(this.y);
	}

	if (endHandler) {
		if (typeof (endHandler) == "string")
			eval(endHandler);
		else
			endHandler();
	}
}

function cbeMoveBy(dX, dY, endHandler) //~~~ v3b16
	{
	if (dX)
		this.left(this.left() + dX);

	if (dY)
		this.top(this.top() + dY);

	if (endHandler) {
		if (typeof (endHandler) == "string")
			eval(endHandler);
		else
			endHandler();
	}
}

function domLeft(newLeft) {
	if (arguments.length == 1)
		this.ele.style.left = newLeft + "px";

	return parseInt(this.ele.style.left);
}

function ieLeft(newLeft) {
	if (arguments.length == 1)
		this.ele.style.pixelLeft = newLeft;

	return this.ele.style.pixelLeft;
}

function nnLeft(newLeft) {
	if (arguments.length == 1)
		this.ele.left = newLeft;

	return this.ele.left;
}

function domTop(newTop) {
	if (arguments.length == 1)
		this.ele.style.top = newTop + "px";

	return parseInt(this.ele.style.top);
}

function ieTop(newTop) {
	if (arguments.length == 1)
		this.ele.style.pixelTop = newTop;

	return this.ele.style.pixelTop;
}

function nnTop(newTop) {
	if (arguments.length == 1)
		this.ele.top = newTop;

	return this.ele.top;
}

function domOffsetLeft() { return this.ele.offsetLeft; }

function domOffsetTop() { return this.ele.offsetTop; }

function nnOffsetLeft() { return this.ele.pageX; }

function nnOffsetTop() { return this.ele.pageY; }

function cbeSizeTo(newWidth, newHeight) {
	this.width(newWidth);

	this.height(newHeight);
}

function cbeSizeBy(dW, dH) {
	this.width(this.width() + dW);

	this.height(this.height() + dH);
}

function cbeResizeTo(newWidth, newHeight, endHandler) //~~~ v3b16
	{
	this.sizeTo(newWidth, newHeight);

	this.clip('auto');

	if (endHandler) {
		if (typeof (endHandler) == "string")
			eval(endHandler);
		else
			endHandler();
	}
}

function cbeResizeBy(dW, dH, endHandler) //~~~ v3b16
	{
	this.sizeBy(dW, dH);

	this.clip('auto');

	if (endHandler) {
		if (typeof (endHandler) == "string")
			eval(endHandler);
		else
			endHandler();
	}
}

function domWidth(newWidth) {
	if (arguments.length == 1) {
		this.w = newWidth;

		this.ele.style.width = newWidth + "px";
	} else if (this.index == 0) { // if is document.body.cbe
		this.w = cbeInnerWidth();
	}

	return this.w
}

function ieWidth(newWidth) {
	if (arguments.length == 1) {
		this.w = newWidth;

		this.ele.style.pixelWidth = newWidth;
	} else if (this.index == 0) { // if is document.body.cbe
		this.w = cbeInnerWidth();
	}

	return this.w
}

function nnWidth(newWidth) {
	if (arguments.length == 1) {
		this.w = newWidth;
	} else if (this.index == 0) { // if is document.body.cbe
		this.w = cbeInnerWidth();
	}

	return this.w
}

function domHeight(newHeight) {
	if (arguments.length == 1) {
		this.h = newHeight;

		this.ele.style.height = newHeight + "px";
	} else if (this.index == 0) { // if is document.body.cbe
		this.h = cbeInnerHeight();
	}

	return this.h
}

function ieHeight(newHeight) {
	if (arguments.length == 1) {
		this.h = newHeight;

		this.ele.style.pixelHeight = newHeight;
	} else if (this.index == 0) { // if is document.body.cbe
		this.h = cbeInnerHeight();
	}

	return this.h
}

function nnHeight(newHeight) {
	if (arguments.length == 1) {
		this.h = newHeight;
	} else if (this.index == 0) { // if is document.body.cbe
		this.h = cbeInnerHeight();
	}

	return this.h
}

function cbeScrollLeft() {
	var value = 0;

	if (is.ie4up) {
		value = this.ele.scrollLeft;
	} else if (is.nav4up || is.opera || is.gecko) {
		if (this.index == 0) // if is body object
			value = window.pageXOffset;
		else {
			value = 0; // ???????
		}
	}

	return value;
}

function cbeScrollTop() {
	var value = 0;

	if (is.ie4up) {
		value = this.ele.scrollTop;
	} else if (is.nav4up || is.opera || is.gecko) {
		if (this.index == 0) { // if is body object
			value = window.pageYOffset;
		} else {
			value = 0; // ???????
		}
	}

	return value;
}

function cbeShow() { this.visibility(1); }

function cbeHide() { this.visibility(0); }

function domVisibility(vis) {
	if (arguments.length == 1) {
		switch (typeof (vis)) {
			case 'number':
			case 'boolean':
				if (vis)
					this.ele.style.visibility = is.opera ? 'visible' : 'inherit';
				else
					this.ele.style.visibility = 'hidden';

				break;

			case 'string':
				this.ele.style.visibility = vis;

				break;

			default: alert('invalid argument in domVisibility()');
		}
	} else {
		return (this.ele.style.visibility == 'visible' || this.ele.style.visibility == 'inherit' || this.ele.style.visibility == '');
	}
}

function nnVisibility(vis) {
	if (arguments.length == 1) {
		switch (typeof (vis)) {
			case 'number':
			case 'boolean':
				if (vis)
					this.ele.visibility = 'inherit';
				else
					this.ele.visibility = 'hide';

				break;

			case 'string':
				this.ele.visibility = vis;

				break;

			default: alert('invalid argument in nnVisibility()');
		}
	} else {
		return (this.ele.visibility == 'show' || this.ele.visibility == 'inherit' || this.ele.visibility == '');
	}
}

function domZIndex(newZ) {
	if (arguments.length == 1)
		this.ele.style.zIndex = newZ;

	return this.ele.style.zIndex
}

function nnZIndex(newZ) {
	if (arguments.length == 1)
		this.ele.zIndex = newZ;

	return this.ele.zIndex;
}

function domBackground(newBgColor, newBgImage) {
	if (arguments.length >= 1) {
		if (newBgColor == null || newBgColor == 'transparent')
			newBgColor = 'transparent';

		this.ele.style.backgroundColor = newBgColor;

		if (arguments.length == 2)
			this.ele.style.backgroundImage = "url(" + newBgImage + ")";
	}

	return this.ele.style.backgroundColor;
}

function nnBackground(newBgColor, newBgImage) {
	if (arguments.length >= 1) {
		if (newBgColor == null || newBgColor == 'transparent')
			newBgColor = null;

		this.ele.bgColor = newBgColor;

		if (arguments.length == 2)
			this.ele.background.src = newBgImage || null;
	}

	return this.ele.bgColor;
}

function opBackground(newBgColor, newBgImage) {
	if (arguments.length >= 1) {
		if (newBgColor == null || newBgColor == 'transparent')
			newBgColor = null; // doesn't work

		this.ele.style.background = newBgColor;

		if (arguments.length == 2) {
			this.ele.style.backgroundImage = "url(" + newBgImage + ")";
		}
	}

	return this.ele.style.backgroundColor;
}

function domColor(newColor) {
	if (arguments.length == 1) {
		this.ele.style.color = newColor;
	}

	return this.ele.style.color;
}

function nnColor(newColor) { return; }

function domClip(top, right, bottom, left) {
	if (arguments.length == 4) {
		var clipRect = "rect(" + top + "px " + right + "px " + bottom + "px " + left + "px" + ")";

		this.ele.style.clip = clipRect;
	} else if (arguments.length == 1) {
		this.clip(0, this.width(), this.height(), 0);
	} else
		return this.ele.style.clip;
}

function nnClip(top, right, bottom, left) {
	if (arguments.length == 4) {
		this.ele.clip.top = top;

		this.ele.clip.right = right;
		this.ele.clip.bottom = bottom;
		this.ele.clip.left = left;
	} else if (arguments.length == 1) {
		this.clip(0, this.width(), this.height(), 0);
	} else {
		var clipRect = "rect(" + this.ele.clip.top + "px " + this.ele.clip.right + "px " + this.ele.clip.bottom + "px " + this.ele.clip.left + "px" + ")";

		return clipRect;
	}
}

function opClip(top, right, bottom, left) {
	if (arguments.length == 0)
		return "rect()";
}

function domClipArray() {
	var re = /\(|px,?\s?\)?|\s|,|\)/;

	return this.ele.style.clip.split(re);
}

function nnClipArray() { alert('method not implemented for NN4 nor Opera'); }

function domClipTop() {
	var a = this.clipArray();

	return parseInt(a[1]);
}

function nnClipTop() { return this.ele.clip.top; }

//--
function domClipRight() {
	var a = this.clipArray();

	return parseInt(a[2]);
}

function nnClipRight() { return this.ele.clip.right; }

//--
function domClipBottom() {
	var a = this.clipArray();

	return parseInt(a[3]);
}

function nnClipBottom() { return this.ele.clip.bottom; }

//--
function domClipLeft() {
	var a = this.clipArray();

	return parseInt(a[4]);
}

function nnClipLeft() { return this.ele.clip.left; }

function domClipWidth() {
	var a = this.clipArray();

	return (a[2] - a[4]);
}

function nnClipWidth() {
	if (this.index == 0)
		return cbeInnerWidth();

	return this.ele.clip.width;
}

function opClipWidth() { return this.w; }

//--
function domClipHeight() {
	var a = this.clipArray();

	return (a[3] - a[1]);
}

function nnClipHeight() {
	if (this.index == 0)
		return cbeInnerHeight();

	return this.ele.clip.height;
}

function opClipHeight() { return this.h; }

function cbeReturnZero() { return 0; }

function cbeReturnVoid() { }

function domInnerHtml(newHtml) { this.ele.innerHTML = newHtml; }

function nnInnerHtml(newHtml) {
	if (newHtml == '')
		newHtml = ' ';

	this.ele.document.open();
	this.ele.document.write(newHtml);
	this.ele.document.close();
}

function opInnerHtml(newHtml) { return; // Opera5 does not support this
	}

function cbeRelativePosition(rp, margin, outside) {
	var x = this.left();

	var y = this.top();
	var w = this.clipWidth();
	var h = this.clipHeight();
	var pw = this.parent.cbe.width();
	var ph = this.parent.cbe.height();
	var sx = this.parent.cbe.scrollLeft();
	var sy = this.parent.cbe.scrollTop();
	var right = sx + pw;
	var bottom = sy + ph;
	var cenLeft = sx + Math.floor(pw/2) - Math.floor(w/2);
	var cenTop = sy + Math.floor(ph/2) - Math.floor(h/2);

	if (arguments.length > 1) {
		if (outside)
			margin = -margin;

		sx += margin;
		sy += margin;
		right -= margin;
		bottom -= margin;
	}

	switch (rp.toLowerCase()) {
		case 'center':
			x = cenLeft;

			y = cenTop;
			break;

		case 'centerh':
			x = cenLeft;

			break;

		case 'centerv':
			y = cenTop;

			break;

		case 'n':
			x = cenLeft;

			if (outside)
				y = sy - h;
			else
				y = sy;

			break;

		case 'ne':
			if (outside) {
				x = right;

				y = sy - h;
			} else {
				x = right - w;

				y = sy;
			}

			break;

		case 'e':
			y = cenTop;

			if (outside)
				x = right;
			else
				x = right - w;

			break;

		case 'se':
			if (outside) {
				x = right;

				y = bottom;
			} else {
				x = right - w;

				y = bottom - h
			}

			break;

		case 's':
			x = cenLeft;

			if (outside)
				y = sy - h;
			else
				y = bottom - h;

			break;

		case 'sw':
			if (outside) {
				x = sx - w;

				y = bottom;
			} else {
				x = sx;

				y = bottom - h;
			}

			break;

		case 'w':
			y = cenTop;

			if (outside)
				x = sx - w;
			else
				x = sx;

			break;

		case 'nw':
			if (outside) {
				x = sx - w;

				y = sy - h;
			} else {
				x = sx;

				y = sy;
			}

			break;

		default: alert("invalid 'rp' argument in relativePosition()");
	}

	this.x = x;
	this.y = y;
}

// End of method definitions, global functions follow
function cbeInnerWidth() {
	var w = 0;

	if (is.nav4up) {
		w = window.innerWidth;

		if (document.height > window.innerHeight) // has vert scrollbar
			w -= 16;
	} else if (is.ie4up) {
		w = document.body.clientWidth;
	} else if (is.opera) {
		w = window.innerWidth;
	}

	return w;
}

function cbeInnerHeight() {
	var h = 0;

	if (is.nav4up) {
		h = window.innerHeight;

		if (document.width > window.innerWidth) // has horz scrollbar
			h -= 16;
	} else if (is.ie4up) {
		h = document.body.clientHeight;
	} else if (is.opera) {
		h = window.innerHeight;
	}

	return h;
}

function cbePageXOffset() {
	var offset;

	if (is.nav4up || is.opera) {
		offset = window.pageXOffset;
	} else if (is.ie4up) {
		offset = document.body.scrollLeft;
	}

	return offset;
}

function cbePageYOffset() {
	var offset;

	if (is.nav4up || is.opera) {
		offset = window.pageYOffset;
	} else if (is.ie4up) {
		offset = document.body.scrollTop;
	}

	return offset;
}

///////////////////////////////////// ClientSnifferJr Object Constructor
function ClientSnifferJr() {
	this.ua = navigator.userAgent.toLowerCase();

	this.major = parseInt(navigator.appVersion);
	this.minor = parseFloat(navigator.appVersion);

	// DOM Support
	if (document.addEventListener && document.removeEventListener)
		this.dom2events = true;

	if (document.getElementById)
		this.dom1getbyid = true;

	// Opera
	this.opera = this.ua.indexOf('opera') != -1;

	if (this.opera) {
		this.opera5 = (this.ua.indexOf("opera 5") != -1 || this.ua.indexOf("opera/5") != -1);

		return;
	}

	// MSIE
	this.ie = this.ua.indexOf('msie') != -1;

	if (this.ie) {
		this.ie3 = this.major < 4;

		this.ie4 = (this.major == 4 && this.ua.indexOf('msie 5') == -1 && this.ua.indexOf('msie 6') == -1);
		this.ie4up = this.major >= 4;
		this.ie5 = (this.major == 4 && this.ua.indexOf('msie 5.0') != -1);
		this.ie5up = !this.ie3 && !this.ie4;
		this.ie6 = (this.major == 4 && this.ua.indexOf('msie 6.0') != -1);
		this.ie6up = (!this.ie3 && !this.ie4 && !this.ie5 && this.ua.indexOf("msie 5.5") == -1);
		return;
	}

	// Misc.
	this.hotjava = this.ua.indexOf('hotjava') != -1;
	this.webtv = this.ua.indexOf('webtv') != -1;
	this.aol = this.ua.indexOf('aol') != -1;

	if (this.hotjava || this.webtv || this.aol)
		return;

	// Gecko, NN4+, and NS6
	this.gecko = this.ua.indexOf('gecko') != -1;
	this.nav = (this.ua.indexOf('mozilla') != -1 && this.ua.indexOf('spoofer') == -1 && this.ua.indexOf('compatible') == -1);

	if (this.nav) {
		this.nav4 = this.major == 4;

		this.nav4up = this.major >= 4;
		this.nav5up = this.major >= 5;
		this.nav6 = this.major == 5;
		this.nav6up = this.nav5up;
	}
}

window.is = new ClientSnifferJr();

// End cbe_core.js  
/**
* Description:  Class and class inheritance skeleton code of Object-Oriented JavaScript
* Author:   Richard Li
*
*/

(function(){
/**
 * Top enumeration class of Object-Oriented JavaScript
 *
 *
*/
var baseEnumClass = function() 
{	
	// Public enumeration properties
	this.TITLE = "... framework library";
	this.DESCRIPTION = "JavaScript library for ...";
	this.AUTHOR = "Richard Li";
	this.OK = "OK";
	this.CANCEL = "Cancel";
}

/**
 * Top base class of Object-Oriented JavaScript
 *
 *
*/
var baseClass = function () 
{	
	// Data type
	this.isInt = function (s) {
			return s === +s && isFinite(s) && !(s % 1);
	};
	
	this.isFloat = function (s) {
			return +s === s && (!isFinite(s) || !!(s % 1));
	};
	
	this.isNumeric = function (s) {
			return (typeof s === "number" || typeof s === "string") && s !== "" && !isNaN(s);
	};
	
	this.isString = function (s) {
			return (typeof s === "string");
	};
	
	this.isArray = function (s) {
			return Object.prototype.toString.call( s ) === "[object Array]";
	};
	
	this.isObject = function (s) {
			return Object.prototype.toString.call( s ) === "[object Object]";
	};
	
	// Validation	
	this.validEmail = function (str) {
			var pattern = new RegExp("^([-a-zA-Z0-9_\.])+\@(([-a-zA-Z0-9])+\.)+([a-zA-Z0-9]{2,4})+$", "ig");
			
			if (pattern.test(str)) {
				return true;
			} else {
				return false;
			}
	};
	
	this.validUrl = function (str) {
			var pattern = new RegExp(
								  "^(ftp:\/\/|http:\/\/|https:\/\/|\/\/)" +
								  "((([a-z\d]([a-z\d-]*[a-z\d])*)\.)+[a-z]{2,}|((\d{1,3}\.){3}\d{1,3}))" +
								  "(\:\d+)?(\/[-a-z\d%_.~+=]*)*" +
								  "(\?[;&a-z\d%_.~+=-]*)?" +
								  "(\#[-a-z\d_]*)?$", 
								"i");
			if (pattern.test(str)) {
				return true;
			} else {
				return false;
			}
	};
	
	// Setting keyboard when class instance is created  
	document.onkeydown = function(e) {
			var keycode = e.keyCode ? e.keyCode : e.which;
			
			// Pressing key Ctrl
			if (keycode == 17) {
				firstkey = true;
			}
			
			if (keycode == 88 && firstkey == true) {
				// Processing code for Ctrl - X			
				return firstkey = false;
			}
			
			if (keycode == 120) {
				// Processing code for F9	
				return false;
			}
	};
	
	// Loading JavaScript file 
	this.loadJs = function(jsPath) {
		    var script = document.createElement("script");
		    script.setAttribute("type", "text/javascript");
		    script.setAttribute("src", jsPath);
		    document.getElementsByTagName("head")[0].appendChild(script); 
	};
};

/**
 * More classes with same mechanisms
 *
 *
*/

/**
 * A child class of baseClass and baseEnumClass to add more properties and methods
 *
 *
*/
var appClass = function (classNo)
{	
	"use strict";
	
	// Inheritance of baseClass and baseEnumClass
	baseClass.apply(this, arguments);		// Or baseClass.call(this, arguments.toString());	
	baseEnumClass.apply(this, arguments);	// Or baseEnumClass.call(this, arguments.toString());
	
	// Private properties
	var className = "appClass";
  	
	// Public properties
	this.classNo = classNo;	
	this.className = className;
	
	// Public methods
	// Getting an Unix timestamp
  	this.getTimestamp = function () {
		return Math.round(+new Date() / 1000);
	};
};

// Create namespace object appModule 
this.appModule = new appClass("No. 001");

/**
 * Extend code JavaScript API 
 *
 *
 */
Array.prototype.min = function() { 
	return Math.min.apply(null, this); 
};

Array.prototype.max = function() { 
	return Math.max.apply(null, this); 
};

Array.prototype.unique = function() { 
	var unique= []; 
	for (var i = 0; i < this.length; i++ ) { 
		if (unique.indexOf(this[i]) == -1) { 
			unique.push(this[i]) 
		} 
	}  
	return unique; 
};

})();

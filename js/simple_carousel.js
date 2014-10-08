var js = jQuery.noConflict();

function getLeft(a) {
	// get the left of the content
	//var left = jQuery('#scroll-content').css('left');
	var left = a.css('left');
	if ( left == 'auto' ) {
		left = '0px';
	}
	return trimPx(left);
}

function getWidth(id) {
	// get the width, including padding	
	var width = js(id).width();
	var paddingLeft = trimPx(js(id).css("padding-left"));
	var paddingBottom = trimPx(js(id).css("padding-bottom"));

	return width + paddingLeft + paddingBottom;
}

function trimPx(value) {
	// remove "px" from values
	
	if( value = "undefined" ) return;
	var pos = value.indexOf("px");
	if (pos != 0)
		return parseInt(value.substring(0, pos));
	else
		return 0;
}

var container;
var content;
var hidden;	// # of pixels hidden by the container

function setScrollerDimensions() {
	container = getWidth("#scroll-container");
	content = getWidth("#scroll-content");
	hidden = content - container;
}

function resetScroller() {
	setScrollerDimensions();
	js('#scroll-content').css('left', 0);
}

js(document).ready(function() {

	setScrollerDimensions();

	js('#scroll-controls a.left-arrow').click(function() {
		return false;
	});

	js('#scroll-controls a.right-arrow').click(function() {
		return false;
	});

	js('#scroll-controls a.right-arrow').hover(
		function() {
			
			if (hidden > 0) {
				var content = js(this).parent().siblings('#scroll-content');
				var current = getLeft( content );
				content.animate({ left: "-" + hidden }, Math.abs(current - hidden) * 10);
			}
		},
		function() {
			jQuery(this).parent().siblings('#scroll-content').stop();
		}
	);

	js('#scroll-controls a.left-arrow').hover(
		function() {
			if (hidden > 0) {
				var content = js(this).parent().siblings('#scroll-content') ;
				var current = getLeft( content );
				content.animate({ left: "0" }, Math.abs(current) * 10);
			}
		},
		function() {
			js(this).parent().siblings('#scroll-content').stop();
		}
	);
});
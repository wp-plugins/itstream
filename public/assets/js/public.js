(function ( $ ) {
	"use strict";

	$(function () {

	});

}(jQuery));


function iframeLoaded(iframe_id) {
    var iFrameID = document.getElementById(iframe_id);
    if(iFrameID) {
        // here you can make the height, I delete it first, then I make it again
        iFrameID.height = "";
        iFrameID.height = (iFrameID.contentWindow.document.body.scrollHeight + 40 ) + "px";
    }
}
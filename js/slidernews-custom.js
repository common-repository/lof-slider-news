jQuery(document).ready( function(){
    var buttons = { previous:jQuery('#mlsncontent .button-previous') ,
	    next:jQuery('#mlsncontent .button-next') };
    jQuery('#mlsncontent').lofJSidernews( {
        interval:4000,
		direction:'opacity',
		duration:1000,
        navigatorWidth:  365,
		easing:'easeInOutSine',
        bottons:buttons} );
	});

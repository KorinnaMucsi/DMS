$(document).ready(function()
{	
	ContHeightMain();
});
$(window).resize(function()
{	
	ContHeightMain();
});

function ContHeightMain()
{
	//Beallitja a sotetebb hatteru div meretet, hogy a documentum vegeig tartson - minimum magassag, mivel ha kimegy az ablakbol, akkor azt kell kovetnie a hatternek, nem pedig az ablakmagassagot
	//Ha csak az ablakhoz igazitanank, akkor az moge nem jutna, ami kilog az ablakbol
	//Windows + Android teszt OK
	
	var eH=$('#hdr_div').outerHeight();
	var wH = $(document).outerHeight();
	
	var fH=wH-eH;
	
	$('.main_mainContainerDiv').css({minHeight: fH});
	
}

$( window ).on( "orientationchange", function( event ) 
{
	ContHeightMain();
});
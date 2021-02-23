$(document).ready(function()
{	
	MenusTopPos();	
});

$(window).resize(function()
{	
	MenusTopPos();
});

function MenusTopPos()
{
	/*Dinamikusan allitjuk be a css-t a csuszos menuhoz, mert csak javascriptbol tudjuk a mainContainerDiv top poziciojat*/
	var link = $("#mainContainerDivMenu");
	var offset = link.offset();
	var top = offset.top;
	
	var d_w=$(document).width();
	
	/*Ha 1023px-tol kisebb a felbontas, akkor onReady es onResize-kor 100% szelessegu lesz a becsuszos menu, ellenkezo esetben csak 240px-es*/
	if(d_w<1023)
	{
		$('#cbp-spmenu-s1').css({"width": "100%", "height": "100%", "top": top + 'px', "z-index": "1000", "border-right": "2pt white solid" , "box-shadow": "0 5px 20px white"});
	}
	else
	{
		$('#cbp-spmenu-s1').css({"width": "240px", "height": "100%", "top": top + 'px', "z-index": "1000", "border-right": "2pt white solid" , "box-shadow": "0 5px 20px white"});
	}

	$('#main_menu').css({"cursor":"pointer"});
}

function OpenMainMenu()
{
	document.location.href="main.php?showMainMenu=True";
}

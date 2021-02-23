function ShowDocHistory(ID, doc_id, src)
{
	//Ez a funkcio szesszios valtozot csinal a doc_id-bol, hogy arra lehessen szurni a history query-t a documents_hst.php fajlban,
	//amit megmutatunk a felhasznaloknak, amikor a History linkre kattintanak
	$.ajax(
	{
		type: "POST",
		//Ez a fajl kesziti a szesszios valtozot
		url: "documents_hst_set_docid.php",
		data: 
		{
			doc_id:doc_id
		},
		async: false,
		success:function()
		{
			//Ha lefutott, akkor a kovetkezo linket nyitja meg a bongeszo
			if(src=='MY' || src=='PDT')
			{
				var loc='main.php?showMyDocuments=True&showHst=True&ID=' + ID + '&doc_id=' + doc_id;
			}
			if(src=='ALL')
			{
				var loc='main.php?showAllDocuments=True&showHst=True&ID=' + ID + '&doc_id=' + doc_id;
			}
			if(src=='NV')
			{
				var loc='main.php?showNVDocuments=True&showHst=True&ID=' + ID + '&doc_id=' + doc_id;
			}
			
			window.location=loc;
		}
	});	

}
function ShowPrilog(doc_type_id)
{
	$.ajax(
	{
		type: "POST",
		//Ez a fajl kesziti a szesszios valtozot
		url: "documents_prilog_set_typeid.php",
		data: 
		{
			doc_type_id:doc_type_id
		},
		async: false,
		success:function()
		{
			//Amikor a Prilog? mezobe kattint, akkor megjelenik az also Prilog form, ami a kivalasztott dokumentum-hoz tartozik. Frissiteni kell, hogy 
			//a beallitott szessziot alkalmazni lehessen.
			document.getElementById('main_dataDiv_p').style.display='inline-block';
			$("#jqxgridPrilog").jqxGrid('refresh');
			$("#jqxgridPrilog").jqxGrid('applyfilters');
		}
	});
}
function HidePrilog()
{
	$.ajax(
	{
		type: "POST",
		//Ez a fajl kesziti a szesszios valtozot
		url: "documents_prilog_unset_typeid.php",
		data: 
		{
			doc_type_id:''
		},
		async: false,
		success:function()
		{
			document.getElementById('main_dataDiv_p').style.display='none';
		}
	});
}
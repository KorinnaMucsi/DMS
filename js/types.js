$(document).ready(function () 
{
	$('#dialogDistribLst').on('dialogclose', function(event) 
	{
		job_list=[];
	});
	
	$("#active").on('change',function(event)
	{
		var type_id=$("#type_id").val();
		//alert(type_id);
		
		if (!$(this).is(':checked'))
        {
            //A jelenlegi statusa 1, 0-ra akarjuk allitani
            var future_status=0;
            //A jelenlegi statusza 1
            var orig_status=true;
        }
        else
        {
	        //A jelenlegi statusza 0, 1-re akarjuk allitani
	        var future_status=1;
	        //A jelenlegi statusza 0
	        var orig_status=false;
        }
        //alert(future_status);
		
		//Nem lehet aktivizalni a dokumentumot, amig legalabb egy tetel nincs a disztribucios listajan
		$.ajax(
		{
			type: "POST",
			url: "get_dl_item_no.php",
			data: 
			{
				TYPE_ID:type_id
			},
			success:function(response)
			{
				//alert(response);
				if(response==0)
				{
					alert('You can\'t change the selected document type\'s status to active because it has no distribution list set. Please, define the distribution list first!');
					$("#active").prop("checked", false);
				}
			}			
		});
		
		
		//Nem lehet passzivalni a dokumentum tipust, ha van alatta aktiv dokumentum
		$.ajax(
		{
			type: "POST",
			url: "types_status_chk.php",
			data: 
			{
				TYPE_ID:type_id,
				FUTURE_STATUS:future_status
			},
			dataType: 'json',
			success:function(resp)
			{
				//alert('err_no: ' + resp["ACTDOC_ERR_NO"]);
				//alert(type_id + ', ' + future_status);
				if(resp["ACTDOC_ERR_NO"]==1)
				{
					//Ha a types_status_chk.php fajl visszakuldte, hogy van hiba - response["ACTDOC_ERR_NO"]==1, akkor nem lehet passzivalni a dokumentum tipust
					alert(resp["ACTDOC_ERR_NO_DESCR"]);
					//Visszaallitja az eredeti statuszta a checkboxot
					$("#active").prop("checked", orig_status);
				}
			}			
		});
		
	});
	
	$("#descr").on('change',function(event)
	{
		var type_id=$("#type_id").val();
		var descr=$("#descr").val();

		$.ajax(
		{
			type: "POST",
			url: "get_matching_descr.php",
			data: 
			{
				TYPE_ID:type_id,
				DESCR: descr
			},
			dataType: 'json',
			success:function(response)
			{
				//alert(response);
				if(response["existing_type_id"]!='')
				{
					alert('This description already exists for type: \'' + response["existing_type_id"] + 
					'\'. Please, define a new name or change the name on type \'' + response["existing_type_id"] + '\'!');
					$("#descr").val(response["curr_descr"]);
					$("#descr").focus();
				}
			}			
		});
	});
	
});

var job_list=new Array;

function NewTypeVisible()
{
	//Mezok inicializalasa, ha netan EDIT utan kattintott volna a NEW-ra
	$("#type_id").val('');
	$("#descr").val('');
	$("#sel_doc_descr").val('');
	$("#sel_doc_uploader").val('');
	$("#sel_doc_da").val('');
	$("#sel_doc_ga").val('');
	$("#active").prop("checked", false);
	//Uj bevitelnel nem lathato az azonosito es az aktiv checkbox valamint a labeljeik
	document.getElementById('btnTypes').style.display = 'none';
	document.getElementById('lbl_type').style.display = 'none';
	document.getElementById('type_id').style.display = 'none';
	document.getElementById('lbl_active').style.display = 'none';
	document.getElementById('active').style.display = 'none';
	document.getElementById('form_types').style.display = 'inline-block';
	$("#jqxgridTypes").jqxGrid('updatebounddata');

}
function NewTypeInVisible()
{
	document.getElementById('btnTypes').style.display = 'inline-block';
	document.getElementById('btnTypes').style.margin = '1% 0 1% 0';
	document.getElementById('form_types').style.display = 'none';
	$("#jqxgridTypes").jqxGrid('updatebounddata');
}
function EditType(TYPE_ID)
{
	NewTypeVisible();
	//A modositasnal kell, hogy latszon a tipus, viszont csak read-only-ban es latszodnia kell az Active checkbox-nak is, de azt kell hogy birja a felhasznalo modositani
	document.getElementById('lbl_type').style.display = 'inline-block';
	document.getElementById('type_id').style.display = 'inline-block';
	document.getElementById('lbl_active').style.display = 'inline-block';
	document.getElementById('active').style.display = 'inline-block';

	$('#type_id').prop('readonly', true);

	$.ajax(
	{
		type: "POST",
		//Ez a fajl kesziti a szesszios valtozot
		url: "edit_type.php",
		data: 
		{
			TYPE_ID:TYPE_ID
		},
		dataType: 'json',
		async: false,
		success:function(response)
		{
			$("#type_id").val(response["type_tp"]);
			$("#descr").val(response["descr_tp"]);
			$("#sel_doc_descr").val(response["doc_descr_tp"]);
			$("#sel_doc_uploader").val(response["doc_uploader"]);
			$("#sel_doc_da").val(response["doc_da"]);
			$("#sel_doc_ga").val(response["doc_ga"]);

			if(response["active_tp"]=='No')
			{
				$("#active").prop("checked", false);
				$("#orig_active").prop("checked", false);
			}
			else
			{
				$("#active").prop("checked", true);
				$("#orig_active").prop("checked", true);
			}
		}
	});	
	
}
function EditDistribLst(TYPE_ID)
{
	//Ez az ajax kirajzolja a js formra a HR munakahelyek kombo boxat (hr_cmb valtozoba rakja)
	$.ajax(
	{
		type: "POST",
		url: "hr_functions_to_cmb.php",
		data:
		{
			CURR_FN:''
		},
		success:function(hr_cmb)
		{
			
			//Ez az ajax az osszes tobbi elemet rajzolja ki a js formra, kiszedi a bazisbol a mar meglevo jogosultsagokat a cont. formrol kivalasztott tipusra
			//Kirajzolja a listat, ha van, ha nincs, akkor pedig kiirja, hogy 'No job titles added yet'
			$.ajax(
			{
				type: "POST",
				url: "get_distrib_lst.php",
				data:
				{
					TYPE_ID:TYPE_ID
				},
				dataType: 'json',
				success:function(dl)
				{
					job_list=JSON.parse(dl["return_to_array"]);
					content=	'<div style="display:inline-block;float:left;clear:both;width:100%;text-align:left;margin-bottom:2%;">' +
								'Create the distribution list for document type: <b>' + TYPE_ID + '</b><br><hr>' +
								'<i>Please, select a job title and click on the \'Add\' button to add it to the distribution list</i><br><br>' +
								'<i>Click on the \'OK\' button to finalize the list and save it on the document type</i><br><br>' +
								'<select name="sel_hr_fn" id="sel_hr_fn">' + 
								'<option value="PS" selected="selected" disabled="disabled">----- Please, select a function -----</option>' +
								hr_cmb +
								'</select>' +
								'<button id="dl_add" onclick="AddJobToList();"><img src="img/add.png" width="16px" alt="up" />Add</button><br><br>' +
								'<fieldset style="display:inline-block;float:left;clear:both;width:100%;text-align:left;margin-top:2%;">' +
								'<legend>Distribution List</legend>' +
								'<ul>' +
								'<div id="grpDistribLst">' +
								dl["return_to_form"] +
								//'<li>No job titles added yet</li>' +
								'</div>' +
								'</ul>' +
								'</fieldset>' +
								'</div>';
					//Ez a funkcio hivja elo magat a js ablakot, az elozo ket ajax lepesben beallitott tartalommal			
					ShowDialogBoxDistribLst('Distribution List',content,'OK','Cancel',TYPE_ID);
				}
			});			
			
			
		}
	});		
}
function ShowDialogBoxDistribLst(title, content, btn1text, btn2text, typeid) 
{
	var btn1css;
	var btn2css;

	if (btn1text == '') 
	{
		btn1css = "hidecss";
	} 
	else 
	{
		btn1css = "showcss";
	}

	if (btn2text == '') 
	{
		btn2css = "hidecss";
	} 
	else 
	{
		btn2css = "showcss";
	}
	
	$("#lblMessageDistribLst").html(content);
	
	$("#dialogDistribLst").dialog
	({
		resizable: false,
		title: title,
		modal: true,
		width: 'auto',
		hide: { effect: 'scale', duration: 400 },
		buttons: 
		[
			{
				text: btn1text,
				"class": btn1css,
				click: function() 
				{
					//job_list=[];
					//$("#dialogDistribLst").dialog('close');
					SaveListToDB(job_list, typeid);
				}
			},
			{
				text: btn2text,
				"class": btn2css,
				click: function () 
				{
					job_list=[];
					$("#dialogDistribLst").dialog('close');
				}
			}
		]
	});
}

function AddJobToList()
{
	var distrib_lst=$("#grpDistribLst").html();
	
	//Ha elso munkahelyet adjuk hozza, akkor ez fog minket varni a formon, de mivel ez nem kell, hogy ott mradjon, miutan az elso munkahely megjelent, akkor 
	//uresre kell tennunk a valtozot
	if(distrib_lst=='<li>No job titles added yet</li>')
	{
		distrib_lst='';
	}

	//A kivalasztott munkahely azonositoja a legordulo listabol
	var sel_job_title=$("#sel_hr_fn").val();

	//Ha az van kivalasztva, hogy "Please, select" akkor hibauzenetet kell adni a felhasznalonak, hogy nem valasztott munkahelyet
	if(sel_job_title==null)
	{
		alert('Please, select a job title first!');
	}
	else
	{	
		//A parameter, amely megmutatja, hogy van-e mar a listan egyszer a kivalasztott munkahely
		var wrong_entry=0;
		
		//Vegigmegyunk az eddig kivalasztott munakhelyek listajan
		for(var i=0; i < job_list.length; i++) 
		{
			if (job_list[i] == sel_job_title) 
			{
				wrong_entry = wrong_entry + 1;
			}
		}
		
	    //Ha van duplazas, akkor hibauzenet
	    if(wrong_entry>0)
	    {
	    	alert('This job title was already added to the distribution list!');
		}
	    //Ha nincs duplazas, akkor hozzaadja a listahoz a munkahelyet
		else
		{
		
			//A php fajl segitsegevel a kombobol kivalasztott munkahely ID-jara visszaadjuk a munkahely nevet es a listara dobjuk egy Remove linkkel egyutt, amivel le is tudjuk 
			//venni a listarol, ha veletlenul tettuk ra
			$.ajax(
			{
				type: "POST",
				//Ez a fajl kesziti a szesszios valtozot
				url: "get_job_title_descr.php",
				data: 
				{
					SEL_JOB_TITLE:sel_job_title
				},
				async: false,
				success:function(response)
				{
					var sel_job_title_descr=response;
					job_list.push(sel_job_title);
					distrib_lst=distrib_lst + '<div style="display:inline-block;clear:left;float:left;width:70%;text-align:left;"><li>' + sel_job_title_descr + '</li></div>' +
								'<div style="display:inline-block;clear:right;float:left;width:29%;text-align:right;margin-right:1%;">' +
								'<a id="' + sel_job_title + '" href="#" onclick="Javascript:RemoveJobFromList(' + sel_job_title + ')">Remove</a></div>';
					//Ez a vegso div, ami tartalmazza az osszes listara tett munkahelyet
					$("#grpDistribLst").html(distrib_lst);
				}
			});	
	
		}
	}		
}

//Mi tortenik, amikor a felhasznalo az OK gombra kattint
function SaveListToDB(job_list, type_id)
{
	//Ha egyetlen tetel sincs a listan, akkor hibauzenetet kap
	if(job_list.length==0)
	{
		alert('Please, add at least one job title, or exit with cancel or close the window!');
	}
	//Ha van legalabb egy tetel a disztribucios listan, akkor lementi a bazisba az adott tipus melle
	else
	{
		//var hr_func_lst=job_list.join(", ");
		var query='';
		for(var i=0; i < job_list.length; i++) 
		{
			query=query + "SELECT " + job_list[i] + " AS HR_FUNC_ID ";
			
			if(i<job_list.length-1)
			{
				query=query + " UNION ";
			}
		}	
		
		$.ajax(
		{
			type: "POST",
			//Ez a fajl kesziti a szesszios valtozot
			url: "save_distriblst_on_doc.php",
			data: 
			{
				QUERY:query,
				TYPE_ID:type_id
			},
			async: false,
			success:function(response)
			{
				//alert(response);
				$("#dialogDistribLst").dialog('close');
				location.reload();
			}
		});	
	}
}
function RemoveJobFromList(hr_func_id)
{
	//Beallitjuk a kitorlendo tetel poziciojat. Ha nem talalja meg a listan, akkor nehogy valamit rosszul toroljon ki
	var jt_to_remove=100000;
	
	for(var i=0; i<job_list.length; i++)
	{
		if(job_list[i]==hr_func_id)
		{
			//Megkeressuk, hogy az a tetel, amelyik melletti Remove-ra kattintottunk hanyadik az array-ban, majd az igy megkapott poziciot hasznaljuk a torlesnel
			jt_to_remove=i;
		}
	}	
	//Ezzel kivesszi az arraybol azt a tetelt, amelynek a sorszamat megkaptuk az elozo lepesben - a splice funkcio elso parametere megmutatja honnan toroljunk, a masodik, pedig hany tetelt
	job_list.splice(jt_to_remove,1);
	
	//A torles utan az array-nak megfeleloen ujra kell rajzolni a #grpDistribLst html tartalmat
	//Ehhez elkuldjuk a PHP-nak az array-unkat, aki majd visszakuldi ide a html sorokat a bazisbol kiszedett adatok alapjan es kirajozolja az ablak tartalmat
	
	//alert(job_list);
	$.ajax(
	{
		type: "POST",
		url: "get_dl_form.php",
		data:
		{
			ARR_JOB_LIST:JSON.stringify(job_list)
		},
		async: false,
		success:function(grpDistribLst)
		{
			//alert(grpDistribLst);
			$("#grpDistribLst").html(grpDistribLst);
		}	
	});
}

$(document).ready(function () 
{	
	$("#active_pdt").on('change',function(event)
	{
		var type_id=$("#type_id_pdt").val();
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
		
		
		//Nem lehet passzivalni az appendix tipust, ha van alatta aktiv appendix
		$.ajax(
		{
			type: "POST",
			url: "ptypes_status_chk.php",
			data: 
			{
				TYPE_ID:type_id,
				FUTURE_STATUS:future_status
			},
			dataType: 'json',
			success:function(resp)
			{
				if(resp["ACTDOC_ERR_NO"]==1)
				{
					//Ha a types_status_chk.php fajl visszakuldte, hogy van hiba - response["ACTDOC_ERR_NO"]==1, akkor nem lehet passzivalni a dokumentum tipust
					alert(resp["ACTDOC_ERR_NO_DESCR"]);
					//Visszaallitja az eredeti statuszta a checkboxot
					$("#active_pdt").prop("checked", orig_status);
				}
			}			
		});//$.ajax vege		
	});//$("#active_pdt").on('change',function(event) vege
});//$(document).ready(function () vege

function NewPTypeVisible()
{
	
	//Mezok inicializalasa, ha netan EDIT utan kattintott volna a NEW-ra
	$("#type_id_pdt").val('');
	$("#print").prop("checked", false);
	$("#descr").val('');
	
	//Uj bevitelnel nem lathato az azonosito es az aktiv checkbox valamint a labeljeik
	document.getElementById('btnPTypes').style.display = 'none';
	document.getElementById('form_ptypes').style.display = 'inline-block';
	document.getElementById('lbl_active_pdt').style.display = 'none';
	document.getElementById('active_pdt').style.display = 'none';
	document.getElementById('lbl_active_pdt').style.display = 'none';
	document.getElementById('lbl_type_pdt').style.display = 'none';
	document.getElementById('type_id_pdt').style.display = 'none';
}
function NewPTypeInVisible()
{
	document.getElementById('btnPTypes').style.display = 'inline-block';
	document.getElementById('btnPTypes').style.margin = '1% 0 1% 0';
	document.getElementById('form_ptypes').style.display = 'none';
}
function EditPType(ID)
{
	NewPTypeVisible();
	document.getElementById('lbltp').style.display='none';
	document.getElementById('sel_doc_types').style.display = 'none';
	document.getElementById('lbl_active_pdt').style.display = 'inline-block';
	document.getElementById('active_pdt').style.display = 'inline-block';
	document.getElementById('lbl_type_pdt').style.display = 'inline-block';
	document.getElementById('type_id_pdt').style.display = 'inline-block';

	$('#sel_doc_types').removeAttr('required');
	$('#type_id_pdt').prop('readonly', true);
	
	$.ajax(
	{
		type: "POST",
		//Ez a fajl kesziti a szesszios valtozot
		url: "edit_ptype.php",
		data: 
		{
			ID:ID
		},
		dataType: 'json',
		async: false,
		success:function(response)
		{
			$("#type_id_pdt").val(response["type_tp_pdt"]);
			$("#descr").val(response["descr_tp"]);
			//Printable checkbox erteket itt allitjuk be
			if(response["prnt_tp"]==0)
			{
				$("#print").prop("checked", false);
			}
			else
			{
				$("#print").prop("checked", true);
			}
			
			//Active checkbox erteket itt allitjuk be
			if(response["active_pdt"]==0)
			{
				$("#active_pdt").prop("checked", false);
				$("#orig_active_pdt").prop("checked", false);
			}
			else
			{
				$("#active_pdt").prop("checked", true);
				$("#orig_active_pdt").prop("checked", true);
			}

		}
	});	

}
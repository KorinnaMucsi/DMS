function GApproveDocument(ID, TypeID, DocID)
{
	$.ajax(
	{
		type: "POST",
		url: "approve_ga_permission_ctrl.php",
		data: 
		{
			TYPE:TypeID
		},
		async: false,
		success:function(response)
		{
			//Amennyiben a PHP fajl 0-at kuld vissza a funkcionak, akkor az azt jelenti, hogy nincs approve jogosultsaga az adott dokumentum tipusra (tDocTypes)
			if(response==0)
			{
				alert('You don\'t have permission for this operation!');
			}
			//Amennyiben a PHP fajl 1-et kuld vissza, az azt jelenti, hogy van hozza jogosultsaga
			if(response==1)
			{
				//Miutan leellenoriztuk a jogosultsagot, akkor le kell ellenorizni, hogy a DA mar jovahagyta-e a sajat reszet, mert a GA csak akkor kerulhet ra,
				//ha elozoleg a DA jovahagyas mar rakerult a dokumetumra (beirodott a DA oszlop a tablazatba)
				
				$.ajax(
				{
					type: "POST",
					url: "approve_ga_daapp_ctrl.php",
					data: 
					{
						ID:ID
					},
					async: false,
					success:function(response_da)
					{
						//Amennyiben a PHP fajl 0-at kuld vissza a funkcionak, akkor az azt jelenti, hogy a DA meg nem hagyta jova es igy a GA-nak sem lehet
						if(response_da==0)
						{
							alert('The Document Approver has to approve the document first!');
						}//if(response_da==0) vege
						//Amennyiben a PHP fajl 1-et kuld vissza, az azt jelenti, hogy a DA jovahagyta, mehet a GA jovahagyas is
						if(response_da==1)
						{
							content=	'<div style="width:100%;text-align:center;margin-bottom:15%;">' +
										'<div class="main_radiobtnsl"><input type="radio" id="rG" name="rA" value="1" checked="checked"><img src="img/goodResult.ico" alt="Good result" width="64px"></div>' + 
										'<div class="main_radiobtnsr"><input type="radio" id="rB" name="rA" value="2"><img src="img/badResult.ico" alt="Bad result" width="64px"></div></div>' + 
										'<label for="txtcmmt">Comment:</label>' +
										'<textarea id="txtcmmt" name="txtcmmt"></textarea>';
										
							ShowDialogBoxGA('Document approval (GA)', content, 'OK', 'Cancel', '',null, ID, TypeID, DocID);
						}//if(response_da==1) vege
					}//success:function(response_da) vege
				});//ajax vege
			}//if(response==1) vege
		}//successfunction(response) vege
	});//ajax vege
}//funkcio vege

function GA(Pwd, ID, TypeID, DocID, Result, Cmmt)
{
	//$("body").addClass("main_wait");

	//Funkcio, amely lefut, amikor a felhasznalo az Approve linkre kattintva jova akarja hagyni a feltoltott dokumentumot
	$.ajax(
	{
		type: "POST",
		url: "check_pwd.php",
		//Ezek az ertekek kellenek a jovahagyashoz - a link tartalmazza oket es kuldi a funkcionak (ApproveDocument(ID, TypeID, DocID))
		data: 
		{
			PWD:Pwd
		},
		async: false,
		success:function(response)
		{
			//response=2 azt jelenti, hogy nem vitt be jelszot (check_pwd.php echo)
			if(response==2)
			{
				alert('Please, enter a password!');
				document.getElementById("txtpwd").focus();
			}
			//Amennyiben a PHP fajl 0-at kuld vissza a funkcionak, akkor az azt jelenti, hogy nem egyezik a beutott jelszo, a bazisban levo jelszoval
			if(response==0)
			{
				alert('Wrong password, please try again!');
				document.getElementById("txtpwd").value='';
				document.getElementById("txtpwd").focus();
			}
			//Amennyiben a PHP fajl 1-et kuld vissza, az azt jelenti, hogy egyezik a jelszo a bazisban levo jelszoval es mehet az update
			if(response==1)
			{				
				$("body").addClass("main_wait");
		
				//Funkcio, amely lefut, amikor a felhasznalo az Approve linkre kattintva jova akarja hagyni a feltoltott dokumentumot
				$.ajax(
				{
					type: "POST",
					url: "approve_document_ga.php",
					//Ezek az ertekek kellenek a jovahagyashoz - a link tartalmazza oket es kuldi a funkcionak (ApproveDocument(ID, TypeID, DocID))
					data: 
					{
						ID:ID,
						TYPE:TypeID,
						DOC:DocID,
						DOC_APPROVED:Result,
						A_CMMT: Cmmt				
					},
					async: false,
					success:function()
					{
						$("#dialogPWDDAGA").dialog('close');
						$("#dialogDAGA").dialog('close');
					}
				});
				//Miutan lefutott a PHP kod, frissiteni kell az oldalat, hogy a jovahagyott rekord (dokumentum atkeruljon az also tablazatba es eltunjon a felso tablazatbol)
				$("#dialogPWDDAGA").dialog('close');
				$("#dialogDAGA").dialog('close');
				location.reload();
			}	
		}
	});
}

function AskForPasswordGA(ID, TypeID, DocID, Res, Cmmt)
{
	content_pwd='<div style="width:100%;text-align:left;margin-bottom:5%;">' +	
				'<label for="txtcmmt">Please, enter your password:</label>' +
				'<br><input type="password" name="pwd" id="txtpwd" style="width:10em;text-align:left;margin-top:5%;">' +
				'</div>'
				
	ShowDialogBoxPWDGA('Password', content_pwd, 'OK', 'Cancel', '',null, ID, TypeID, DocID, Res, Cmmt);
}

function ShowDialogBoxGA(title, content, btn1text, btn2text, functionText, parameterList, id, typeid, docid) 
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
	
	$("#lblMessageDAGA").html(content);
	
	$("#dialogDAGA").dialog
	({
		resizable: false,
		title: title,
		modal: true,
		minWidth: 'auto',
		hide: { effect: 'scale', duration: 400 },
		buttons: 
		[
			{
				text: btn1text,
				"class": btn1css,
				click: function() 
				{
					if(document.getElementById("rG").checked)
					{
						result=1;
					}					
					if(document.getElementById("rB").checked)
					{
						result=2;
					}
					
					cmmt=document.getElementById("txtcmmt").value;
					AskForPasswordGA(id, typeid, docid,result,cmmt);
					//GA(id, typeid, docid, result, cmmt);
				}
			},
			{
				text: btn2text,
				"class": btn2css,
				click: function () 
				{
					$("#dialogDAGA").dialog('close');
				}
			}
		]
	});
}

function ShowDialogBoxPWDGA(title, content, btn1text, btn2text, functionText, parameterList, id, typeid, docid, result, cmmt) 
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
	
	$("#lblMessagePWDDAGA").html(content);
	
	$("#dialogPWDDAGA").dialog
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
					pwd=document.getElementById("txtpwd").value;
					GA(pwd, id, typeid, docid, result, cmmt);
				}
			},
			{
				text: btn2text,
				"class": btn2css,
				click: function () 
				{
					$("#dialogPWDDAGA").dialog('close');
				}
			}
		]
	});
}

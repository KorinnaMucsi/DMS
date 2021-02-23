<?php
/*
Created by: Mucsi Korinna
Date of creation: 11.05.2015.
Description: The following file is used to present the menu to the user depending on the selection
*/

	session_start();

	if (empty($_SESSION['dms_username']) || !isset($_SESSION['dms_username']) || $_SESSION['dms_username']=='')
	{
		die(header('Location:login.php'));
	}
	require_once('hdr.php'); //Fejlec informacio

//UNSET ALL THE SESSIONS IF MAIN MENU IS CLICKED -->
//echo $_SESSION['where'];
//MAIN MENU -->
	if(isset($_GET['showMainMenu']) && $_GET['showMainMenu']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		
		require_once('main_menu.php'); //Fomenu kirajzolasa - nagy ikonok a desktopon
	}
//NEW UPLOAD -->	
	
	if(isset($_GET['showNewUpload']) && $_GET['showNewUpload']=='True')
	{
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('new_upload.php');
	}

//APPROVE -->
	
	if(isset($_GET['showApprove']) && $_GET['showApprove']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('approvals_form.php');
	}
	
//DOCUMENTS -->
	
	if(isset($_GET['showDocuments']) && $_GET['showDocuments']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('documents_menu.php');
	}

	
//MY DOCUMENTS -->
	
	if(isset($_GET['showMyDocuments']) && $_GET['showMyDocuments']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('documents_my_form.php');
	}
	
//ALL DOCUMENTS -->
	
	if(isset($_GET['showAllDocuments']) && $_GET['showAllDocuments']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('documents_all_form.php');
	}

	
//UNREAD DOCUMENTS -->
	
	if(isset($_GET['showNVDocuments']) && $_GET['showNVDocuments']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('not_viewed_docs_form.php');
	}


//REPORTS MENU -->
	
	if(isset($_GET['showReports']) && $_GET['showReports']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('reports_menu.php');
	}

//REPORTS DOCUMENTS -->
	
	if(isset($_GET['showRepDocs']) && $_GET['showRepDocs']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('rep_documents.php');
	}
	
//REPORTS - DOCUMENT TYPES
	
	if(isset($_GET['showRepDTP']) && $_GET['showRepDTP']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('rep_dtp.php');
	}
	
//REPORTS USERS - JOB TITLES -->
	
	if(isset($_GET['showRepUsrsJobs']) && $_GET['showRepUsrsJobs']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('rep_usrsjobs.php');
	}
//MAINTENANCE MENU -->
	
	if(isset($_GET['showMaintenance']) && $_GET['showMaintenance']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('maintenance_menu.php');
	}
	
//USERS MAINTENANCE -->
	
	if(isset($_GET['showUsers']) && $_GET['showUsers']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('users_form.php');
	}
	
//WS USERS MAINTENANCE -->
	
	if(isset($_GET['showWSUsers']) && $_GET['showWSUsers']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		
		require_once('ws_users_form.php');
	}

//DOCUMENT TYPES (PRILOG) -->
	
	if(isset($_GET['showPTypes']) && $_GET['showPTypes']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('ptypes_form.php');
	}
	
//DOCUMENT TYPES -->
	
	if(isset($_GET['showTypes']) && $_GET['showTypes']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('types_form.php');
	}
	
//FUNCTIONS (HR) -->
	
	if(isset($_GET['showFns']) && $_GET['showFns']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		require_once('hr_functions_form.php');
	}
	
//WS JOB TITLES MAINTENANCE -->
	
	if(isset($_GET['showWSFns']) && $_GET['showWSFns']=='True')
	{
		unset($_SESSION['upload_success']);
		unset($_SESSION['upload_error']);
		unset($_SESSION['approve_success']);
		unset($_SESSION['approve_error']);
		unset($_SESSION['fn_success']);
		unset($_SESSION['fn_error']);
		unset($_SESSION['fns_success']);
		unset($_SESSION['fns_error']);
		unset($_SESSION['p_success']);
		unset($_SESSION['p_error']);
		unset($_SESSION['t_success']);
		unset($_SESSION['t_error']);
		unset($_SESSION['tp']);
		unset($_SESSION['wsu_success']);
		unset($_SESSION['wsu_error']);
		
		require_once('ws_fns_form.php');
	}

?>
<!DOCTYPE html>
<html>

<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0">
<link href="css/hdr.css" type="text/css" rel="stylesheet">
<link href="css/main.css" type="text/css" rel="stylesheet">
<link href="css/upload.css" type="text/css" rel="stylesheet">
<link href="css/component.css" type="text/css" rel="stylesheet">
<link href="css/default.css" type="text/css" rel="stylesheet">
<link href="css/doc_hst_print.css" type="text/css" rel="stylesheet" media="print">
<link href="css/doc_hst.css" type="text/css" rel="stylesheet" media="screen,projection" >
<link href="css/ptypes.css" type="text/css" rel="stylesheet" media="screen,projection" >
<link href="css/hr_functions.css" type="text/css" rel="stylesheet" media="screen,projection" >
<script src="js/jquery.min.js"></script>
<link rel="stylesheet" href="css/jquery-ui.css">
<script src="js/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/modernizr.custom.js"></script>
<script src="js/login.js" type="text/javascript"></script>
<script src="js/hdr.js" type="text/javascript"></script>
<script src="js/main.js" type="text/javascript"></script>
<script src="js/new_upload.js" type="text/javascript"></script>
<script src="js/download.js" type="text/javascript"></script>
<script src="js/approve.js" type="text/javascript"></script>
<script src="js/approve_ga.js" type="text/javascript"></script>
<script src="js/approved_doc_alert_job.js" type="text/javascript"></script>
<script src="js/documents.js" type="text/javascript"></script>
<script src="js/usrs_maint.js" type="text/javascript"></script>
<script src="js/types.js" type="text/javascript"></script>
<script src="js/ptypes.js" type="text/javascript"></script>
<script src="js/hr_functions.js" type="text/javascript"></script>
<script src="js/ws_users_roles.js" type="text/javascript"></script>
<script src="js/ws_fns.js" type="text/javascript"></script>
<script src="js/reports.js" type="text/javascript"></script>
<link rel="stylesheet" href="jq/jqwidgets/styles/jqx.base.css" type="text/css" />
<link rel="stylesheet" href="jq/jqwidgets/styles/jqx.energyblue.css" type="text/css" />
<script type="text/javascript" src="jq/jqwidgets/jqxcore.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxbuttons.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxscrollbar.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxmenu.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxgrid.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxgrid.selection.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxgrid.filter.js"></script>	
<script type="text/javascript" src="jq/jqwidgets/jqxdata.js"></script>	
<script type="text/javascript" src="jq/jqwidgets/jqxlistbox.js"></script>	
<script type="text/javascript" src="jq/jqwidgets/jqxdropdownlist.js"></script>	
<script type="text/javascript" src="jq/jqwidgets/jqxeditor.js"></script>	
<script type="text/javascript" src="jq/jqwidgets/jqxinput.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxgrid.sort.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxgrid.edit.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxpanel.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxcalendar.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxdatetimeinput.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxcheckbox.js"></script>
<script type="text/javascript" src="jq/jqwidgets/jqxgrid.columnsresize.js"></script>
<script type="text/javascript">
	$(function() 
	{	
		$('#lst_upd').datepicker({dateFormat: "dd.mm.yy" ,showButtonPanel: true});
	});	
</script>
<?php
	//Ha approve-ra kattintott a felhasznalo, akkor a kovetkezo ket Javascript kodcsoport lefut, hogy feltotlse a ket tablazatot (#jqxgrid es #jqxgridApp)
	if(isset($_GET['showApprove']) && $_GET['showApprove']=='True')
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{

				var source =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'YR', type: 'int' },
						{ name: 'NO', type: 'int' },
						{ name: 'TYPE_ID', type: 'string' },
						{ name: 'DOC_ID', type: 'string' },
						{ name: 'DOC_DESCR', type: 'string' },
						{ name: 'DESCR', type: 'string' },
						{ name: 'U_FLNAME_DT', type: 'string'},
						{ name: 'U_LNK_V', type: 'string'},
						{ name: 'U_LNK_DA', type: 'string'},
						{ name: 'U_LNK_GA', type: 'string'}
					], 
		
					url: 'approvals.php',
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgrid").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgrid").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				
				var sourceApp =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'YR', type: 'int' },
						{ name: 'NO', type: 'int' },
						{ name: 'TYPE_ID', type: 'string' },
						{ name: 'DOC_ID', type: 'string' },
						{ name: 'DOC_DESCR', type: 'string' },
						{ name: 'DESCR', type: 'string' },
						{ name: 'U_FLNAME_DT', type: 'string'},
						{ name: 'A_FLNAME_DT', type: 'string'},
						{ name: 'U_LNK_V', type: 'string'}
					], 
		
					url: 'approvals_app.php',
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridApp").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridApp").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};

				var dataAdapter = new $.jqx.dataAdapter(source);
				var dataAdapterApp = new $.jqx.dataAdapter(sourceApp);
		 		
				$("#jqxgrid").jqxGrid(
				{
					width:'100%',
					height:200,
		            source: dataAdapter,
		            columnsresize: true,
					altrows: true,
					//autoheight:true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
/*						{ text: 'Year', datafield: 'YR', filtertype: 'checkedlist', cellsalign: 'right', width: '5%' },
		                { text: 'No.', datafield: 'NO', filtertype: 'checkedlist', cellsalign: 'right', width: '5%' },
   		                { text: 'Doc. type', datafield: 'TYPE_ID', filtertype: 'checkedlist', width: '10%' },
*/		                { text: 'ID', datafield: 'DOC_ID', filtertype: 'checkedlist', width: '5%' },
		                { text: 'Type', datafield: 'DOC_DESCR', filtertype: 'checkedlist', width: '10%' },
		                { text: 'Description', datafield: 'DESCR', filtertype: 'checkedlist', width: '45%' },
				        { text: 'Upload', datafield: 'U_FLNAME_DT', filterable: false, filtertype: 'default', width: '25%'},
		   		        { text: '', datafield: 'U_LNK_V', filterable: false, filtertype: 'default', width: '5%'},
   		   		        { text: 'DA', datafield: 'U_LNK_DA', filterable: false, filtertype: 'default', cellsalign:'center', width: '5%'},
   		   		        { text: 'GA', datafield: 'U_LNK_GA', filterable: false, filtertype: 'default', cellsalign:'center', width: '5%'}
		            ]
				});
				
            	$("#jqxgridApp").jqxGrid(
				{
					width:'100%',
					height:200,
		            source: dataAdapterApp,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
/*						{ text: 'Year', datafield: 'YR', filtertype: 'checkedlist', cellsalign: 'right', width: '5%' },
		                { text: 'No.', datafield: 'NO', filtertype: 'checkedlist', cellsalign: 'right', width: '5%' },
   		                { text: 'Doc. type', datafield: 'TYPE_ID', filtertype: 'checkedlist', width: '10%' },
*/		                { text: 'ID', datafield: 'DOC_ID', filtertype: 'checkedlist', width: '5%' },
		                { text: 'Type', datafield: 'DOC_DESCR', filtertype: 'checkedlist', width: '10%' },
		                { text: 'Description', datafield: 'DESCR', filtertype: 'checkedlist', width: '45%' },
				        { text: 'Approval', datafield: 'A_FLNAME_DT', filterable: false, filtertype: 'default', width: '35%' },
		   		        { text: '', datafield: 'U_LNK_V', filterable: false, filtertype: 'default', width: '5%' }
		            ]
				});
			});
		</script>
<?php
	}
?>

<?php
	//Ha Documents-ra kattintott a felhasznalo, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridDocs)
	if(isset($_GET['showMyDocuments']) && $_GET['showMyDocuments']=='True' && !isset($_GET['showHst']))
	{
		//A parametereket kulon mappaban taroljuk
		require_once('params/params.php');
		//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
		require_once('connectvars/connectvars.php');
		
		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
		$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
		mysqli_set_charset($conn,"utf8");


		//A Query megnezi, hogy van-e barmelyik tipushoz approve jogosultsaga
		$query=	"SELECT SUM(UPLOAD_P) AS UPL, SUM(APPROVE_P) AS APPR, SUM(APPROVE_GA_P) AS APPR_GA " .
				"FROM " .
				"tUsrPermissionsDO UP " .
				"JOIN tUsrs U " .
				"ON UP.USR_ID=U.USR_ID " .
				"WHERE UP.USR_ID='" . $_SESSION['dms_username'] . "'";
				
		$result=mysqli_query($conn, $query);
		
		while($row=mysqli_fetch_array($result))
		{
			$upload_p=$row['UPL'];
			$approve_p=$row['APPR'];
			$approve_ga_p=$row['APPR_GA'];
		}
		
		if(isset($_SESSION['doc_type_id']))
		{
		?>
		<style type="text/css">
			#main_dataDiv_p
			{
				display:inline-block;
			}
		</style>
		<?php			
		}
		else
		{
		?>
		<style type="text/css">
			#main_dataDiv_p
			{
				display:none;
			}
		</style>
		<?php
		}
		
		if($upload_p==0)
		{
			$cols=	//{ text: 'Year', datafield: 'YR', filtertype: 'checkedlist', cellsalign: 'right', width: '5%' },
		            //{ text: 'No.', datafield: 'NO', filtertype: 'checkedlist', cellsalign: 'right', width: '5%' },
   		            //{ text: 'Doc. type', datafield: 'TYPE_ID', filtertype: 'checkedlist', width: '5%' },
					"{ text: 'ID', datafield: 'DOC_ID', filtertype: 'default', width: '5%' }, " . "\n" .
		           	"{ text: 'Type', datafield: 'DOC_DESCR', filtertype: 'checkedlist', width: '10%' }, " . "\n" .
		           	"{ text: 'Description', datafield: 'DESCR', filtertype: 'default', width: '60%' }, " . "\n" .
		   		    "{ text: 'Appendix?', datafield: 'P_NUM', filterable: false, filtertype: 'checkedlist', width: '7%' }, " . "\n" .
		   		    "{ text: 'ACTUAL', datafield: 'ACT', filtertype: 'checkedlist', width: '6.5%' }, " . "\n" .
		   		    "{ text: '', datafield: 'U_LNK_V', filterable: false, filtertype: 'default', width: '5%' }, " . "\n" .
		   		    "{ text: '', datafield: 'HST', filterable: false, filtertype: 'default', width: '5%' } " . "\n";
		}
		if($upload_p!=0)
		{
			$cols=	//{ text: 'Year', datafield: 'YR', filtertype: 'checkedlist', cellsalign: 'right', width: '5%' },
		            //{ text: 'No.', datafield: 'NO', filtertype: 'checkedlist', cellsalign: 'right', width: '5%' },
   		            //{ text: 'Doc. type', datafield: 'TYPE_ID', filtertype: 'checkedlist', width: '5%' },
					"{ text: 'ID', datafield: 'DOC_ID', filtertype: 'default', width: '5%' }, " . "\n" .
		           	"{ text: 'Type', datafield: 'DOC_DESCR', filtertype: 'checkedlist', width: '10%' }, " . "\n" .
		           	"{ text: 'Description', datafield: 'DESCR', filtertype: 'default', width: '50%' }, " . "\n" .
		   		    "{ text: 'Appendix?', datafield: 'P_NUM', filterable: false, filtertype: 'checkedlist', width: '7%' }, " . "\n" .
		   		    "{ text: 'ACTUAL', datafield: 'ACT', filtertype: 'checkedlist', width: '6.5%' }, " . "\n" .
		   		    "{ text: '', datafield: 'U_LNK_V', filterable: false, filtertype: 'default', width: '5%' }, " . "\n" .
		   		    "{ text: '', datafield: 'U_LNK_VP', filterable: false, filtertype: 'default', width: '10%' }, " . "\n" .
		   		    "{ text: '', datafield: 'HST', filterable: false, filtertype: 'default', width: '5%' } " . "\n";
		}
		
		$js= 	"<script type=\"text/javascript\"> " . "\n" .
				"$(document).ready(function () " . "\n" .
				"{ " . "\n" .
/*					"$('#jqxgridDocs').on('cellclick', function (event) " . "\n" .
				 	"{ " . "\n" .
	     				"if(event.args.datafield=='P_NO') " . "\n" .
	     				"{ " . "\n" .
	     					"if(event.args.value=='') " . "\n" .
	     					"{ " . "\n" .
	     						"document.getElementById('main_dataDiv_p').style.display='none';" . "\n" .
								"addfilterp(); " . "\n" .
	     					"} " . "\n" .
	     					"else " . "\n" .
	     					"{ " . "\n" .
	     						"document.getElementById('main_dataDiv_p').style.display='inline-block';" . "\n" .
								"addfilterp(); " . "\n" .
	     					"} " . "\n" .
	     				"} " . "\n" .
	     			"}); " . "\n" .
*/					
					"var sourceDocs = " . "\n" .
					"{ " . "\n" .
						"datatype: \"json\", " . "\n" .
						"datafields: " . "\n" . 
						"[ " . "\n" .
							"{ name: 'YR', type: 'int' }, " . "\n" .
							"{ name: 'NO', type: 'int' }, " . "\n" .
							"{ name: 'TYPE_ID', type: 'string' }, " . "\n" .
							"{ name: 'DOC_ID', type: 'string' }, " . "\n" .
							"{ name: 'DOC_DESCR', type: 'string' }, " . "\n" .
							"{ name: 'DESCR', type: 'string' }, " . "\n" .
							"{ name: 'U_FLNAME_DT', type: 'string'}, " . "\n" .
							"{ name: 'A_FLNAME_DT', type: 'string'}, " . "\n" .
							"{ name: 'U_LNK_V', type: 'string'}, " . "\n" .
							"{ name: 'U_LNK_VP', type: 'string'}, " . "\n" .
							"{ name: 'P_NUM', type: 'string'}, " . "\n" .
							"{ name: 'HST', type: 'string'}, " . "\n" .
							"{ name: 'ACT', type: 'string'} " . "\n" .
						"], " . "\n" .
						"url: 'documents.php', " . "\n" .
						"filter: function() " . "\n" .
						"{ " . "\n" .
							"$(\"#jqxgridDocs\").jqxGrid('updatebounddata', 'filter'); " . "\n" .

						"}, " . "\n" .
						"sort: function() " . "\n" .
						"{ " . "\n" .
							"$(\"#jqxgridDocs\").jqxGrid('updatebounddata', 'sort'); " . "\n" .
						"}, " . "\n" .
						"cache: false " . "\n" .
					"}; " . "\n" .
					"var addfilter = function () " . "\n" .
					"{ " . "\n" .
					    // create a filter group for the FirstName column.
					    "var docFilterGroup = new $.jqx.filter(); " . "\n" .
					    // operator between the filters in the filter group. 1 is for OR. 0 is for AND.
					    "var filter_or_operator = 0; " . "\n" .
					    // create a string filter with 'contains' condition.
					    "var filtervalue = 'Yes'; " . "\n" .
					    "var filtercondition = 'equal'; " . "\n" .
					    "var docFilter = docFilterGroup.createfilter('stringfilter', filtervalue, filtercondition); " . "\n" .
					    // add the filters to the filter group.
					    "docFilterGroup.addfilter(filter_or_operator, docFilter); " . "\n" .
					    // add the filter group to the 'firstname' column in the Grid.
					    "$(\"#jqxgridDocs\").jqxGrid('addfilter', 'ACT', docFilterGroup); " . "\n" .
					    // apply the filters.
					    "$(\"#jqxgridDocs\").jqxGrid('applyfilters'); " . "\n" .
					"} " . "\n" .
					"var dataAdapterDocs = new $.jqx.dataAdapter(sourceDocs); " . "\n" .
	            	"$(\"#jqxgridDocs\").jqxGrid( " . "\n" .
					"{ " . "\n" .
						"width:'100%', " . "\n" .
						"height:200, " . "\n" .
					    "ready: function () " . "\n" .
					    "{ " . "\n" .
					        "addfilter(); " . "\n" .
					    "}, " . "\n" .
					    "autoshowfiltericon: true, " . "\n" .
			            "source: dataAdapterDocs, " . "\n" .
			            "columnsresize: true, " . "\n" .
						"//autoheight:true, " . "\n" .
						"altrows: true, " . "\n" .
						"//showfilterrow: true, " . "\n" .
						"filterable: true, " . "\n" .
						"sortable: true, " . "\n" .
						"theme: 'energyblue', " . "\n" .         
						"columns: " . "\n" . 
						"[ " . "\n" .
						$cols . "\n" .
						"] " . "\n" .
					"}); " . "\n" .
					"$('#jqxgridDocs').bind('cellclick', function (event) " . "\n" .
					"{ " . "\n" .
					    "var args = event.args; " . "\n" .
					    "var value = args.value; " . "\n" .
					    "var row = args.rowindex; " . "\n" .
						//"var selcell=event.args.datafield; " . "\n" .
                   		"var p_type = $(\"#jqxgridDocs\").jqxGrid('getcellvalue', row, 'TYPE_ID'); " . "\n" .
                   		"var p = $(\"#jqxgridDocs\").jqxGrid('getcellvalue', row, 'P_NUM'); " . "\n" .
                   		"if(p!='<span id=\"p_invisible\">.</span>') " . "\n" .
                   		"{ " . "\n" .
                   		"ShowPrilog(p_type); " . "\n" .
                   		"} " . "\n" . 
                   		"if(p=='<span id=\"p_invisible\">.</span>') " . "\n" .
                   		"{ " . "\n" .
                   		"HidePrilog(); " . "\n" .
                   		"} " . "\n" . 		
					"}); " . "\n" .
				"}); " . "\n" .
			"</script> ";
			
			echo $js;
	}
	
	//Ha a NVDocuments-ra kattintott a felhasznalo, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridDocs)
	if(isset($_GET['showNVDocuments']) && $_GET['showNVDocuments']=='True' && !isset($_GET['showHst']))
	{
		//A parametereket kulon mappaban taroljuk
		require_once('params/params.php');
		//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
		require_once('connectvars/connectvars.php');
		
		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
		$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
		mysqli_set_charset($conn,"utf8");


		//A Query megnezi, hogy van-e barmelyik tipushoz approve jogosultsaga
		$query=	"SELECT SUM(UPLOAD_P) AS UPL, SUM(APPROVE_P) AS APPR, SUM(APPROVE_GA_P) AS APPR_GA " .
				"FROM " .
				"tUsrPermissionsDO UP " .
				"JOIN tUsrs U " .
				"ON UP.USR_ID=U.USR_ID " .
				"WHERE UP.USR_ID='" . $_SESSION['dms_username'] . "'";
				
		$result=mysqli_query($conn, $query);
		
		while($row=mysqli_fetch_array($result))
		{
			$upload_p=$row['UPL'];
			$approve_p=$row['APPR'];
			$approve_ga_p=$row['APPR_GA'];
		}
		
		if($upload_p==0)
		{
			$cols=	"{ text: 'ID', datafield: 'DOC_ID', filtertype: 'default', width: '5%' }, " . "\n" .
		           	"{ text: 'Type', datafield: 'DOC_DESCR', filtertype: 'checkedlist', width: '10%' }, " . "\n" .
		           	"{ text: 'Description', datafield: 'DESCR', filtertype: 'default', width: '50%' }, " . "\n" .
		           	"{ text: 'Doc./App.', datafield: 'DTD', filtertype: 'checkedlist', width: '10%' }, " . "\n" .
		   		    "{ text: 'ACTUAL', datafield: 'ACT', filtertype: 'checkedlist', width: '10%' }, " . "\n" .
		   		    "{ text: '', datafield: 'U_LNK_V', filterable: false, filtertype: 'default', width: '7.5%' }, " . "\n" .
		   		    "{ text: '', datafield: 'HST', filterable: false, filtertype: 'default', width: '7.5%' } " . "\n";
		}
		if($upload_p!=0)
		{
			$cols=	"{ text: 'ID', datafield: 'DOC_ID', filtertype: 'default', width: '5%' }, " . "\n" .
		           	"{ text: 'Type', datafield: 'DOC_DESCR', filtertype: 'checkedlist', width: '10%' }, " . "\n" .
		           	"{ text: 'Description', datafield: 'DESCR', filtertype: 'default', width: '45%' }, " . "\n" .
		           	"{ text: 'Doc./App.', datafield: 'DTD', filtertype: 'checkedlist', width: '10%' }, " . "\n" .
		   		    "{ text: 'ACTUAL', datafield: 'ACT', filtertype: 'checkedlist', width: '10%' }, " . "\n" .
		   		    "{ text: '', datafield: 'U_LNK_V', filterable: false, filtertype: 'default', width: '5%' }, " . "\n" .
		   		    "{ text: '', datafield: 'U_LNK_VP', filterable: false, filtertype: 'default', width: '10%' }, " . "\n" .
		   		    "{ text: '', datafield: 'HST', filterable: false, filtertype: 'default', width: '5%' } " . "\n";
		}
		
		$nvdocs_js= "<script type=\"text/javascript\"> " . "\n" .
					"$(document).ready(function () " . "\n" .
					"{ " . "\n" .
						"var sourceNVDocs = " . "\n" .
						"{ " . "\n" .
							"datatype: \"json\", " . "\n" .
							"datafields: " . "\n" . 
							"[ " . "\n" .
								"{ name: 'YR', type: 'int' }, " . "\n" .
								"{ name: 'NO', type: 'int' }, " . "\n" .
								"{ name: 'TYPE_ID', type: 'string' }, " . "\n" .
								"{ name: 'DOC_ID', type: 'string' }, " . "\n" .
								"{ name: 'DOC_DESCR', type: 'string' }, " . "\n" .
								"{ name: 'DESCR', type: 'string' }, " . "\n" .
								"{ name: 'DTD', type: 'string' }, " . "\n" .
								"{ name: 'U_FLNAME_DT', type: 'string'}, " . "\n" .
								"{ name: 'A_FLNAME_DT', type: 'string'}, " . "\n" .
								"{ name: 'U_LNK_V', type: 'string'}, " . "\n" .
								"{ name: 'U_LNK_VP', type: 'string'}, " . "\n" .
								"{ name: 'P_NO', type: 'string'}, " . "\n" .
								"{ name: 'HST', type: 'string'}, " . "\n" .
								"{ name: 'ACT', type: 'string'} " . "\n" .
							"], " . "\n" .
							"url: 'not_viewed_docs_db.php', " . "\n" .
							"filter: function() " . "\n" .
							"{ " . "\n" .
								"$(\"#jqxgridNVDocs\").jqxGrid('updatebounddata', 'filter'); " . "\n" .
	
							"}, " . "\n" .
							"sort: function() " . "\n" .
							"{ " . "\n" .
								"$(\"#jqxgridNVDocs\").jqxGrid('updatebounddata', 'sort'); " . "\n" .
							"}, " . "\n" .
							"cache: false " . "\n" .
						"}; " . "\n" .
						"var dataAdapterDocs = new $.jqx.dataAdapter(sourceNVDocs); " . "\n" .
		            	"$(\"#jqxgridNVDocs\").jqxGrid( " . "\n" .
						"{ " . "\n" .
							"width:'100%', " . "\n" .
							"height:500, " . "\n" .
				            "source: dataAdapterDocs, " . "\n" .
				            "columnsresize: true, " . "\n" .
							"altrows: true, " . "\n" .
							"filterable: true, " . "\n" .
							"sortable: true, " . "\n" .
							"theme: 'energyblue', " . "\n" .         
							"columns: " . "\n" . 
							"[ " . "\n" .
							$cols . "\n" .
							"] " . "\n" .
						"}); " . "\n" .
					"}); " . "\n" .
				"</script> ";
			
			echo $nvdocs_js;
	}

	if(isset($_GET['showMyDocuments']) && $_GET['showMyDocuments']=='True'  && !isset($_GET['showHst']))
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{
					$('#jqxgridDocs').on('cellclick', function (event) 
				 	{  
	     				if(event.args.datafield=='P_NO')
	     				{ 
	     					if(event.args.value=='')
	     					{
								addfilterp();
	     						document.getElementById('main_dataDiv_p').style.display='none';
	     					}
	     					else
	     					{
								addfilterp();
	     						document.getElementById('main_dataDiv_p').style.display='inline-block';
	     					}
	     				}
	     			});
				var sourcePrilog =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'DOC_ID', type: 'string' },
						{ name: 'DESCR', type: 'string' },
						{ name: 'U_LNK_V', type: 'string' },
						{ name: 'HST', type: 'string'},
						{ name: 'ACT', type: 'string'}
					], 
		
					url: "documents_prilog_db.php" ,
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridPrilog").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridPrilog").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				var addfilterp = function ()
				{ 
					// create a filter group for the FirstName column.
					var pdocFilterGroup = new $.jqx.filter(); 
					//operator between the filters in the filter group. 1 is for OR. 0 is for AND.
					var filter_or_operator = 0; 
					// create a string filter with 'contains' condition.
					var filtervalue = 'Yes'; 
					var filtercondition = 'equal'; 
					var pdocFilter = pdocFilterGroup.createfilter('stringfilter', filtervalue, filtercondition);
					// add the filters to the filter group.
					pdocFilterGroup.addfilter(filter_or_operator, pdocFilter);
					// add the filter group to the 'firstname' column in the Grid.
					$("#jqxgridPrilog").jqxGrid('addfilter', 'ACT', pdocFilterGroup);
					// apply the filters.
					$("#jqxgridPrilog").jqxGrid('applyfilters'); 
				}				

				var dataAdapterPrilog = new $.jqx.dataAdapter(sourcePrilog);
		 		
            	$("#jqxgridPrilog").jqxGrid(
				{
					width:'100%',
					height:200,
					ready: function ()
					{ 
						addfilterp();
					}, 
					autoshowfiltericon: true,
		            source: dataAdapterPrilog,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
						{ text: 'ID', datafield: 'DOC_ID', filtertype: 'default', width: '5%' },
			           	{ text: 'Description', datafield: 'DESCR', filtertype: 'default', width: '77%' },
			   		    { text: 'ACTUAL', datafield: 'ACT', filtertype: 'checkedlist', width: '6.5%' },
			   		    { text: '', datafield: 'U_LNK_V', filterable: false, width: '5%' }, 
			   		    { text: '', datafield: 'HST', filterable: false, width: '5%' }
		            ]
				});
			});
		</script>
<?php	
	
	}
	
	//Ha az AllDocuments-ra kattintott a felhasznalo, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridAllDocs)
	if(isset($_GET['showAllDocuments']) && $_GET['showAllDocuments']=='True' && !isset($_GET['showHst']))
	{
		//A parametereket kulon mappaban taroljuk
		require_once('params/params.php');
		//A konnekciohoz szukseges adatokat kulon mappaban taroljuk
		require_once('connectvars/connectvars.php');
		
		$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD) or die ("Error connecting to the database");
		$selected = mysqli_select_db($conn,DB_NAME) or die("Couldn't open database");
		mysqli_set_charset($conn,"utf8");


		//A Query megnezi, hogy van-e barmelyik tipushoz approve jogosultsaga
		$query=	"SELECT SUM(UPLOAD_P) AS UPL, SUM(APPROVE_P) AS APPR, SUM(APPROVE_GA_P) AS APPR_GA " .
				"FROM " .
				"tUsrPermissionsDO UP " .
				"JOIN tUsrs U " .
				"ON UP.USR_ID=U.USR_ID " .
				"WHERE UP.USR_ID='" . $_SESSION['dms_username'] . "'";
				
		$result=mysqli_query($conn, $query);
		
		while($row=mysqli_fetch_array($result))
		{
			$upload_p=$row['UPL'];
			$approve_p=$row['APPR'];
			$approve_ga_p=$row['APPR_GA'];
		}
		
		if($upload_p==0)
		{
			$cols=	"{ text: 'ID', datafield: 'DOC_ID', editable: false, filtertype: 'default', width: '10%' }, " . "\n" .
		           	"{ text: 'Type', datafield: 'DOC_DESCR', editable: false, filtertype: 'checkedlist', width: '10%' }, " . "\n" .
		           	"{ text: 'Description', datafield: 'DESCR', editable: false, filtertype: 'default', width: '50%' }, " . "\n" .
		           	"{ text: 'Doc./App.', datafield: 'DTD', editable: false, filtertype: 'checkedlist', width: '15%' }, " . "\n" .
		   		    "{ text: '', datafield: 'U_LNK_V', editable: false, filterable: false, filtertype: 'default', width: '7.5%' }, " . "\n" .
		   		    "{ text: '', datafield: 'HST', editable: false, filterable: false, filtertype: 'default', width: '7.5%' } " . "\n";
		}
		if($upload_p!=0)
		{
			$cols=	"{ text: 'Active', datafield: 'ACT_STAT', columntype: 'checkbox', editable: true, filtertype: 'checkedlist', width: '5%' }," . "\n" .
					"{ text: 'ID', datafield: 'DOC_ID', editable: false, filtertype: 'default', width: '5%' }, " . "\n" .
		           	"{ text: 'Type', datafield: 'DOC_DESCR', editable: false, filtertype: 'checkedlist', width: '10%' }, " . "\n" .
		           	"{ text: 'Description', datafield: 'DESCR', editable: false, filtertype: 'default', width: '50%' }, " . "\n" .
		           	"{ text: 'Doc./App.', datafield: 'DTD', editable: false, filtertype: 'checkedlist', width: '10%' }, " . "\n" .
		   		    "{ text: '', datafield: 'U_LNK_V', editable: false, filterable: false, filtertype: 'default', width: '5%' }, " . "\n" .
		   		    "{ text: '', datafield: 'U_LNK_VP', editable: false, filterable: false, filtertype: 'default', width: '10%' }, " . "\n" .
		   		    "{ text: '', datafield: 'HST', editable: false, filterable: false, filtertype: 'default', width: '5%' } " . "\n";
		}
		
		$documents_all_js= "<script type=\"text/javascript\"> " . "\n" .
					"$(document).ready(function () " . "\n" .
					"{ " . "\n" .
						"var sourceAllDocs = " . "\n" .
						"{ " . "\n" .
							"datatype: \"json\", " . "\n" .
							"datafields: " . "\n" . 
							"[ " . "\n" .
								"{ name: 'ACT_STAT', type: 'int' }," . "\n" .
								"{ name: 'YR', type: 'int' }, " . "\n" .
								"{ name: 'NO', type: 'int' }, " . "\n" .
								"{ name: 'TYPE_ID', type: 'string' }, " . "\n" .
								"{ name: 'DOC_ID', type: 'string' }, " . "\n" .
								"{ name: 'DOC_DESCR', type: 'string' }, " . "\n" .
								"{ name: 'DESCR', type: 'string' }, " . "\n" .
								"{ name: 'DTD', type: 'string' }, " . "\n" .
								"{ name: 'U_FLNAME_DT', type: 'string'}, " . "\n" .
								"{ name: 'A_FLNAME_DT', type: 'string'}, " . "\n" .
								"{ name: 'U_LNK_V', type: 'string'}, " . "\n" .
								"{ name: 'U_LNK_VP', type: 'string'}, " . "\n" .
								"{ name: 'P_NO', type: 'string'}, " . "\n" .
								"{ name: 'HST', type: 'string'}, " . "\n" .
								"{ name: 'ACT', type: 'string'} " . "\n" .
							"], " . "\n" .
							"url: 'documents_all_db.php', " . "\n" .
							"filter: function() " . "\n" .
							"{ " . "\n" .
								"$(\"#jqxgridAllDocs\").jqxGrid('updatebounddata', 'filter'); " . "\n" .
								//"state = $(\"#jqxgridAllDocs\").jqxGrid('savestate'); " . "\n" .	
							"}, " . "\n" .
							"sort: function() " . "\n" .
							"{ " . "\n" .
								"$(\"#jqxgridAllDocs\").jqxGrid('updatebounddata', 'sort'); " . "\n" .
							"}, " . "\n" .
							"cache: false " . "\n" .
						"}; " . "\n" .
						"var addfilter = function () " . "\n" .
						"{ " . "\n" .
						    // create a filter group for the ACT_STAT column.
						    "var whsFilterGroup = new $.jqx.filter(); " . "\n" .
						    // operator between the filters in the filter group. 1 is for OR. 0 is for AND.
						    "var filter_or_operator = 0; " . "\n" .
						    // create a string filter with 'contains' condition.
						    "var filtervalue = 1; " . "\n" .
						    "var filtercondition = 'equal'; " . "\n" .
						    "var whsFilter = whsFilterGroup.createfilter('numericfilter', filtervalue, filtercondition); " . "\n" .
						    // add the filters to the filter group.
						    "whsFilterGroup.addfilter(filter_or_operator, whsFilter); " . "\n" .
						    // add the filter group to the 'firstname' column in the Grid.
						    "$(\"#jqxgridAllDocs\").jqxGrid('addfilter', 'ACT_STAT', whsFilterGroup); " . "\n" .
						    // apply the filters.
						    "$(\"#jqxgridAllDocs\").jqxGrid('applyfilters'); " . "\n" .
						"} " . "\n" .
						"var dataAdapterAllDocs = new $.jqx.dataAdapter(sourceAllDocs); " . "\n" .
		            	"$(\"#jqxgridAllDocs\").jqxGrid( " . "\n" .
						"{ " . "\n" .
							"width:'100%', " . "\n" .
							"height:500, " . "\n" .
							"ready: function () " . "\n" .
						    "{ " . "\n" .
						        "addfilter(); " . "\n" .
						    "}, " . "\n" .
						    "autoshowfiltericon: true, " . "\n" .
							"selectionmode: 'singlecell'," . "\n" .
							"editable: true," . "\n" .
				            "source: dataAdapterAllDocs, " . "\n" .
				            "columnsresize: true, " . "\n" .
							"altrows: true, " . "\n" .
							"filterable: true, " . "\n" .
							"sortable: true, " . "\n" .
							"theme: 'energyblue', " . "\n" .  
							"columns: " . "\n" . 
							"[ " . "\n" .
							$cols . "\n" .
							"] " . "\n" .
						"}); " . "\n" .
						//Bind kezdete
					    "$(\"#jqxgridAllDocs\").bind('cellendedit', function (event) " . "\n" .
					    "{ " . "\n" .
						    "var args = event.args; " . "\n" .
						    "var value = args.value; " . "\n" .
						    "var row = args.rowindex; " . "\n" .
						    "if(value==1) " . "\n" .
						    "{ " . "\n" .
						    	"value=1; " . "\n" .
						    	"var change_to_value=0;" . "\n" .
						    "} " . "\n" .
						    "else " . "\n" .
						    "{ " . "\n" .
						    	"value=0; " . "\n" .
						    	"var change_to_value=1;" . "\n" .
						    "} " . "\n" .
						   //"alert(value);" . "\n" .
						   //"alert(change_to_value);" . "\n" .
						   //A prilog vagy dokumentum (dtd)?
	                		"var dtd = $(\"#jqxgridAllDocs\").jqxGrid('getcellvalue', row, 'DTD'); " . "\n" .
	                		"var doc_id = $(\"#jqxgridAllDocs\").jqxGrid('getcellvalue', row, 'DOC_ID'); " . "\n" .
	                		"var type_id = $(\"#jqxgridAllDocs\").jqxGrid('getcellvalue', row, 'TYPE_ID'); " . "\n" .
							"$.ajax( " . "\n" .
							"{ " . "\n" .
								"type: \"POST\", " . "\n" .
								//Ez a php fajl fogja aktivalni/passzivalni a dokumentumot - aktivalni csak akkor lehet, ha MANUAL_AP mezo ertekeben 1 van, passzivalni barmikor
								"url: 'doc_appx_ap_upd.php', " . "\n" .
								"cache: false, " . "\n" .
								"data: " . "\n" .
								"{ " . "\n" .
									"DOC_ID:doc_id, " . "\n" .
									"DOC_OR_APPX:dtd, " . "\n" .
									"TYPE_ID:type_id, " . "\n" .
									"AP:value " . "\n" . 
								"}, " . "\n" .
								"success: function(result) " . "\n" .
								"{ " . "\n" .
							       	"if(result) " . "\n" .
							       	"{ " . "\n" .
							       		"alert(result); " . "\n" .
									"} " . "\n" .
									"$(\"#jqxgridAllDocs\").jqxGrid('updatebounddata', 'cells'); " . "\n" .
									//Azzal, hogy a document.location.reload ki lett kommentelve, nem frissul az oldal, nem alkalmazodik ujra a default filter
									//es igy lehet folyamatosan dolgozni, mert a cellak attol meg frissulnek
									//"document.location.reload(true); " . "\n" .
								"} " . "\n" .
							"}); " . "\n" .  //ajax vege    
					    "}); " . 
					    //checkbox bind - onclick vege
					"}); " . "\n" .
				"</script> ";
			
			echo $documents_all_js;
	}
	
	//Ha a New type-ra kattintott a felhasznalo, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridPTypes)
	if(isset($_GET['showPTypes']) && $_GET['showPTypes']=='True')
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{

				var sourcePTypes =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'TYPE_ID', type: 'string' },
						{ name: 'DOC_TYPE_ID', type: 'string' },
						{ name: 'DESCR', type: 'string' },
						{ name: 'PRINTABLE', type: 'string' },
						{ name: 'ACTIVE', type: 'string' }, 
						{ name: 'E_LNK', type: 'string' }
					], 
		
					url: "ptypes_db.php" ,
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridPTypes").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridPTypes").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				

				var dataAdapterPTypes = new $.jqx.dataAdapter(sourcePTypes);
		 		
            	$("#jqxgridPTypes").jqxGrid(
				{
					width:'100%',
					height:'100%',
		            source: dataAdapterPTypes,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
		                { text: 'Type ID (Appendix)', datafield: 'TYPE_ID', filtertype: 'checkedlist', width: '10%' },
		                { text: 'Document Type ID', datafield: 'DOC_TYPE_ID', filtertype: 'checkedlist', width: '10%' },
   		                { text: 'Description', datafield: 'DESCR', filtertype: 'default', width: '50%' },
		                { text: 'Printable?', datafield: 'PRINTABLE', columntype: 'checkbox', filtertype: 'checkedlist', width: '10%' },
		                { text: 'Active', datafield: 'ACTIVE', filtertype: 'checkedlist', cellsalign: 'center', width: '10%' }, 
		                { text: 'Edit', datafield: 'E_LNK', filtertype: 'checkedlist', width: '10%' },
		            ]
				});
			});
		</script>
<?php
	}
	
	//Ha a New type-ra kattintott a felhasznalo, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridTypes)
	if(isset($_GET['showTypes']) && $_GET['showTypes']=='True')
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{

				var sourceTypes =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'TYPE_ID', type: 'string' },
						{ name: 'DESCR', type: 'string' },
						{ name: 'DOC_DESCR', type: 'string' },
						{ name: 'ACTIVE', type: 'string' },
						{ name: 'E_LNK', type: 'string' },
						{ name: 'D_LNK', type: 'string' }
					], 
		
					url: "types_db.php" ,
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridTypes").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridTypes").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				

				var dataAdapterTypes = new $.jqx.dataAdapter(sourceTypes);
		 		
            	$("#jqxgridTypes").jqxGrid(
				{
					width:'99%',
					height:'100%',
		            source: dataAdapterTypes,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
		                { text: 'ID', datafield: 'TYPE_ID', filtertype: 'checkedlist', width: '10%' },
   		                { text: 'Description', datafield: 'DESCR', filtertype: 'default', width: '30%' },
		                { text: 'Type', datafield: 'DOC_DESCR', filtertype: 'default', width: '20%' },
		                { text: 'Active', datafield: 'ACTIVE', filtertype: 'checkedlist', width: '20%' },
		                { text: 'Edit', datafield: 'E_LNK', filterable:false, sortable:false, width: '10%' },
		                { text: 'Distribution list', datafield: 'D_LNK', filterable:false, sortable:false, width: '10%' }
		            ]
				});
			});
		</script>
<?php
	}
	//Ha Documents-ra kattintott a felhasznalo, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridHst)
	if(isset($_GET['showHst']) && $_GET['showHst']=='True')
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{

				var sourceHst =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'USR_INFO', type: 'string' },
						{ name: 'GRP_LST', type: 'string' },
						{ name: 'VIEW_DT', type: 'string' }
					], 
		
					url: "documents_hst.php" ,
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridHst").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridHst").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				

				var dataAdapterHst = new $.jqx.dataAdapter(sourceHst);
		 		
            	$("#jqxgridHst").jqxGrid(
				{
					width:'100%',
					height:'100%',
		            source: dataAdapterHst,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
		                { text: 'User', datafield: 'USR_INFO', filtertype: 'checkedlist', width: '20%' },
   		                { text: 'Document viewed at', datafield: 'VIEW_DT', filtertype: 'default', width: '20%'},
		                { text: 'Group', datafield: 'GRP_LST', filtertype: 'checkedlist', width: '60%' }
		            ]
				});
			});
		</script>
<?php
	}
?>
<?php
	//Ha Documents-ra kattintott a felhasznalo, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridHst)
	if(isset($_GET['showRepDocs']) && $_GET['showRepDocs']=='True')
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{

				var sourceRepDocs =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'DOC_ID', type: 'string' },
						{ name: 'DESCR', type: 'string' },
						{ name: 'DOC_DESCR', type: 'string' },
						{ name: 'VRS_NO', type: 'string' },
						{ name: 'LAST_UPD', type: 'string' }
					], 
		
					url: "rep_documents_db.php" ,
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridRepDocs").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridRepDocs").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				

				var dataAdapterRepDocs = new $.jqx.dataAdapter(sourceRepDocs);
		 		
            	$("#jqxgridRepDocs").jqxGrid(
				{
					width:'100%',
					height:'100%',
		            source: dataAdapterRepDocs,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
		                { text: 'Doc. ID', datafield: 'DOC_ID', filtertype: 'checkedlist', cellsalign: 'center', width: '10%' },
   		                { text: 'Description', datafield: 'DESCR', filtertype: 'default', cellsalign: 'left', width: '55%' },
   		                { text: 'Type', datafield: 'DOC_DESCR', filtertype: 'default', cellsalign: 'left', width: '12%' },
   		                { text: 'Version Number', datafield: 'VRS_NO', filtertype: 'default', cellsalign: 'right', width: '12%' },
		                { text: 'Date of last update', datafield: 'LAST_UPD', filtertype: 'default', cellsalign: 'center', width: '11%' }
		            ]
				});
			});
		</script>
<?php
	}
?>
<?php
	//Ha Document types-ra kattintott a felhasznalo, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridDTP)
	if(isset($_GET['showRepDTP']) && $_GET['showRepDTP']=='True')
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{

				var sourceRepDTP =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'TYPE_ID', type: 'string' },
						{ name: 'DT_ACT', type: 'string' },
						{ name: 'DOC_ID', type: 'string' },
						{ name: 'DESCR', type: 'string' },
						{ name: 'DOC_DESCR', type: 'string' },
						{ name: 'VRS_NO', type: 'string' },
						{ name: 'LAST_UPD', type: 'string' }
					], 
		
					url: "rep_dtp_db.php" ,
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridRepDTP").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridRepDTP").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				

				var dataAdapterRepDTP = new $.jqx.dataAdapter(sourceRepDTP);
		 		
            	$("#jqxgridRepDTP").jqxGrid(
				{
					width:'100%',
					height:'100%',
		            source: dataAdapterRepDTP,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
		                { text: 'Doc. type', datafield: 'TYPE_ID', filtertype: 'checkedlist', cellsalign: 'center', width: '6%' },
		                { text: 'Active Doc. type?', datafield: 'DT_ACT', filtertype: 'checkedlist', cellsalign: 'center', width: '8%' },
		                { text: 'Doc. ID', datafield: 'DOC_ID', filtertype: 'checkedlist', cellsalign: 'center', width: '6%' },
   		                { text: 'Description', datafield: 'DESCR', filtertype: 'default', cellsalign: 'left', width: '70%' },
   		                { text: 'Type', datafield: 'DOC_DESCR', filtertype: 'default', cellsalign: 'left', width: '10%' }
		            ]
				});
			});
		</script>
<?php
	}
?>
<?php
	//Ha Documents-ra kattintott a felhasznalo, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridHst)
	if(isset($_GET['showRepUsrsJobs']) && $_GET['showRepUsrsJobs']=='True')
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{

				var sourceRepUsrsJobs =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'DEPARTMENT', type: 'string' },
						{ name: 'WORKSTATION', type: 'string' },
						{ name: 'FULL_NAME', type: 'string' },
						{ name: 'HR_FUNCTION', type: 'string' },
						{ name: 'FN_TYPE', type: 'string' }
					], 
		
					url: "rep_usrsjobs_db.php" ,
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridRepUsrsJobs").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridRepUsrsJobs").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				

				var dataAdapterRepUsrsJobs = new $.jqx.dataAdapter(sourceRepUsrsJobs);
		 		
            	$("#jqxgridRepUsrsJobs").jqxGrid(
				{
					width:'100%',
					height:'100%',
		            source: dataAdapterRepUsrsJobs,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
		                { text: 'Department', datafield: 'DEPARTMENT', filtertype: 'checkedlist', cellsalign: 'left', width: '15%' },
   		                { text: 'Workstation', datafield: 'WORKSTATION', filtertype: 'default', cellsalign: 'left', width: '15%' },
   		                { text: 'User', datafield: 'FULL_NAME', filtertype: 'default', cellsalign: 'left', width: '20%' },
   		                { text: 'Job title', datafield: 'HR_FUNCTION', filtertype: 'default', cellsalign: 'left', width: '30%' },
		                { text: 'Type', datafield: 'FN_TYPE', filtertype: 'default', cellsalign: 'left', width: '20%' }
		            ]
				});
			});
		</script>
<?php
	}
?>
<?php
	//Ha Users-ra kattintott a felhasznalo a maintenance menun belul, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridUsrs)
	if(isset($_GET['showUsers']) && $_GET['showUsers']=='True')
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{

				var sourceUsrs =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'USR_NAME', type: 'string' },
						{ name: 'HR_FUNCTION', type: 'string' },
						{ name: 'E_LNK', type: 'string' },
						{ name: 'V_LNK', type: 'string' },
						{ name: 'UB_LNK', type: 'string' }  
					], 
		
					url: "users_db.php",
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridUsrs").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridUsrs").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				

				var dataAdapterUsrs = new $.jqx.dataAdapter(sourceUsrs);
		 		
            	$("#jqxgridUsrs").jqxGrid(
				{
					width:'100%',
					height:'100%',
		            source: dataAdapterUsrs,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
		                { text: 'Name', datafield: 'USR_NAME', filterable:true, filtertype: 'default', cellsalign: 'left', width: '20%' },
   		                { text: 'Function (HR)', datafield: 'HR_FUNCTION', filterable:true, filtertype: 'default', cellsalign: 'left', width: '50%' },
		                { text: '', datafield: 'E_LNK', filterable:false, filtertype: 'default', cellsalign: 'center', width: '10%' },
   		                { text: 'Signature', datafield: 'V_LNK', filterable:false, filtertype: 'default', cellsalign: 'center', width: '10%' },
   		                { text: 'Unblock user', datafield: 'UB_LNK', filterable:false, filtertype: 'default', cellsalign: 'center', width: '10%' }
		            ]
				});
			});
		</script>
<?php
	}
?>
<?php
	//Ha a Workstation Users-ra kattintott a felhasznalo a maintenance menun belul, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridWSUsrs)
	if(isset($_GET['showWSUsers']) && $_GET['showWSUsers']=='True')
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{

				var sourceWSUsrs =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'FULL_NAME', type: 'string' },
						{ name: 'HR_FUNCTION', type: 'string' },
						{ name: 'FN_TYPE', type: 'string'},
						{ name: 'D_LNK', type: 'string' }
					], 
		
					url: "ws_users_db.php",
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridWSUsrs").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridWSUsrs").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				

				var dataAdapterWSUsrs = new $.jqx.dataAdapter(sourceWSUsrs);
		 		
            	$("#jqxgridWSUsrs").jqxGrid(
				{
					width:'100%',
					height:'100%',
		            source: dataAdapterWSUsrs,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
		                { text: 'Name', datafield: 'FULL_NAME', filterable:true, filtertype: 'default', cellsalign: 'left', width: '30%' },
   		                { text: 'Job title', datafield: 'HR_FUNCTION', filterable:true, filtertype: 'default', cellsalign: 'left', width: '40%' },
   		                { text: 'Type', datafield: 'FN_TYPE', filterable:true, filtertype: 'default', cellsalign: 'left', width: '20%' },
		                { text: '', datafield: 'D_LNK', filterable:false, filtertype: 'default', cellsalign: 'center', width: '10%' }
		            ]
				});
			});
		</script>
<?php
	}
?>
<?php
	//Ha a HR funkciokra kattintott a felhasznalo a maintenance menun belul, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridFns)
	if(isset($_GET['showFns']) && $_GET['showFns']=='True')
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{

				var sourceFns =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'HR_FUNCTION', type: 'string' },
						{ name: 'HR_FUNC_SHORT', type: 'string' },
						{ name: 'DPT_DESCR', type: 'string' },
						{ name: 'ACTIVE', type: 'string' },
						{ name: 'E_LNK', type: 'string' },
						{ name: 'V_LNK', type: 'string' }
					], 
		
					url: "hr_functions_db.php",
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridFns").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridFns").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				

				var dataAdapterFns = new $.jqx.dataAdapter(sourceFns);
		 		
            	$("#jqxgridFns").jqxGrid(
				{
					width:'100%',
					height:'100%',
		            source: dataAdapterFns,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
		                { text: 'Job title', datafield: 'HR_FUNCTION', filterable:true, filtertype: 'default', cellsalign: 'left', width: '35%' },
   		                { text: 'Job title (shortened)', datafield: 'HR_FUNC_SHORT', filterable:false, filtertype: 'default', cellsalign: 'left', width: '20%' },
  		                { text: 'Department', datafield: 'DPT_DESCR', filterable:false, filtertype: 'default', cellsalign: 'left', width: '25%' },
   		                { text: 'Active', datafield: 'ACTIVE', filterable:false, filtertype: 'default', columntype: 'checkbox', cellsalign: 'left', width: '7%' },
		                { text: '', datafield: 'E_LNK', filterable:false, filtertype: 'default', cellsalign: 'center', width: '6%' },
		                { text: '', datafield: 'V_LNK', filterable:false, filtertype: 'default', cellsalign: 'center', width: '7%' }
		            ]
				});
			});
		</script>
<?php
	}
?>
<?php
	//Ha a Workstation - Job Titles-ra kattintott a felhasznalo a maintenance menun belul, akkor a kovetkezo Javascript kodcsoport lefut, hogy feltotlse a tablazatot (#jqxgridWSFns)
	if(isset($_GET['showWSFns']) && $_GET['showWSFns']=='True')
	{
?>
		<script type="text/javascript">
			$(document).ready(function () 
			{

				var sourceWSFns =
				{
					datatype: "json",
					datafields: 
					[
						{ name: 'HR_FUNCTION', type: 'string' },
						{ name: 'WS_DESCR', type: 'string' },
						{ name: 'D_LNK', type: 'string' }
					], 
		
					url: "ws_fns_db.php",
					filter: function()
		
					{
						// update the grid and send a request to the server.
						$("#jqxgridWSFns").jqxGrid('updatebounddata', 'filter');
					},
					sort: function()
					{
						// update the grid and send a request to the server.
						$("#jqxgridWSFns").jqxGrid('updatebounddata', 'sort');
					},
					cache: false
				};
				

				var dataAdapterWSFns = new $.jqx.dataAdapter(sourceWSFns);
		 		
            	$("#jqxgridWSFns").jqxGrid(
				{
					width:'100%',
					height:'100%',
		            source: dataAdapterWSFns,
		            columnsresize: true,
					//autoheight:true,
					altrows: true,
					//showfilterrow: true,
					filterable: true,  
					sortable: true,     
					theme: 'energyblue',         
					columns: 
					[
   		                { text: 'Job title', datafield: 'HR_FUNCTION', filterable:true, filtertype: 'default', cellsalign: 'left', width: '50%' },
   		                { text: 'Workstation', datafield: 'WS_DESCR', filterable:true, filtertype: 'default', cellsalign: 'left', width: '30%' },
		                { text: '', datafield: 'D_LNK', filterable:false, filtertype: 'default', cellsalign: 'center', width: '20%' }
		            ]
				});
			});
		</script>
<?php
	}
?>

<title>DMS</title>
</head>
<body class="main_body">

	<div id="dialog" title="Alert message" style="display: none">
		<div class="ui-dialog-content ui-widget-content">
			<!--Ha a dialog box tartalmat kozepre akarjuk igazitami, akkor a paragraph tag text-align-ja center kell, hogy legyen-->
			<p><label style="width:auto;" id="lblMessage"></label></p>
		</div>
	</div>
	
	<div id="dialogDAGA" title="Alert message" style="display: none">
		<div class="ui-dialog-content ui-widget-content">
			<!--Ha a dialog box tartalmat kozepre akarjuk igazitami, akkor a paragraph tag text-align-ja center kell, hogy legyen-->
			<p><label style="width:auto;" id="lblMessageDAGA"></label></p>
		</div>
	</div>
	<div id="dialogPWDDAGA" title="Alert message" style="display: none">
		<div class="ui-dialog-content ui-widget-content">
			<!--Ha a dialog box tartalmat kozepre akarjuk igazitami, akkor a paragraph tag text-align-ja center kell, hogy legyen-->
			<p><label style="width:auto;" id="lblMessagePWDDAGA"></label></p>
		</div>
	</div>
	<div id="dialogUSRS" title="Alert message" style="display: none">
		<div class="ui-dialog-content ui-widget-content">
			<!--Ha a dialog box tartalmat kozepre akarjuk igazitami, akkor a paragraph tag text-align-ja center kell, hogy legyen-->
			<p><label style="width:auto;" id="lblMessageUSRS"></label></p>
		</div>
	</div>
	<div id="dialogDistribLst" title="Distribution list" style="display: none">
		<div class="ui-dialog-content ui-widget-content">
			<!--Ha a dialog box tartalmat kozepre akarjuk igazitami, akkor a paragraph tag text-align-ja center kell, hogy legyen-->
			<p><label style="width:auto;" id="lblMessageDistribLst"></label></p>
		</div>
	</div>
	<?php 
		//Fejlec informacio - minden dokumentumban kozos, mindig latszodik
		echo $hdr; 
		
		//Kirajzolja a fomenut, ha az van kivalasztva	
		//Az elso DIV fix - ez es a zaro DIV tag koze kell betenni minden uj tartalmat!	
		echo '<div id="mainContainerDivMenu" class="main_mainContainerDivMenu">' . "\n";

//MAIN MENU -->
		if(isset($_GET['showMainMenu']) && $_GET['showMainMenu']=='True')
		{
			echo $main_menu;
		}

//NEW UPLOAD -->

		if(isset($_GET['showNewUpload']) && $_GET['showNewUpload']=='True')
		{
			echo $new_upload;
		}
		
//APPROVE -->		
		
		if(isset($_GET['showApprove']) && $_GET['showApprove']=='True')
		{
	?>
				<!-- Muszaly kulon beallitani a link tulajdonsagait, mert a tablazat css-e felulirja az eredetit -->
				<style>
					a:link { color: #0000EE; }
					a:visited { color: #551A8B; }
					a { text-decoration:underline; }
				</style>
	<?php				
				echo $approvals;
		}
		
//DOCUMENTS -->		
		
		if(isset($_GET['showDocuments']) && $_GET['showDocuments']=='True')
		{
				echo $documents_menu;
		}
		
//MY DOCUMENTS -->		
		
		if(isset($_GET['showMyDocuments']) && $_GET['showMyDocuments']=='True')
		{
	?>
				<!-- Muszaj kulon beallitani a link tulajdonsagait, mert a tablazat css-e felulirja az eredetit -->
				<style>
					a:link { color: #0000EE; }
					a:visited { color: #551A8B; }
					a { text-decoration:underline; }
				</style>
	<?php				
				echo $documents_my;
		}
		
//ALL DOCUMENTS -->		
		
		if(isset($_GET['showAllDocuments']) && $_GET['showAllDocuments']=='True')
		{
	?>
				<!-- Muszaj kulon beallitani a link tulajdonsagait, mert a tablazat css-e felulirja az eredetit -->
				<style>
					a:link { color: #0000EE; }
					a:visited { color: #551A8B; }
					a { text-decoration:underline; }
				</style>
	<?php				
				echo $documents_all;
		}
		
//UNREAD DOCUMENTS -->		
		
		if(isset($_GET['showNVDocuments']) && $_GET['showNVDocuments']=='True')
		{
	?>
				<!-- Muszaly kulon beallitani a link tulajdonsagait, mert a tablazat css-e felulirja az eredetit -->
				<style>
					a:link { color: #0000EE; }
					a:visited { color: #551A8B; }
					a { text-decoration:underline; }
				</style>
	<?php				
				echo $nvdocs;
		}
		
//REPORTS MENU -->

		if(isset($_GET['showReports']) && $_GET['showReports']=='True')
		{
			echo $reports_menu;
		}

//REPORTS DOCUMENTS -->

		if(isset($_GET['showRepDocs']) && $_GET['showRepDocs']=='True')
		{
			echo $rep_docs;
		}
		
//REPORTS DOCUMENT TYPES -->

		if(isset($_GET['showRepDTP']) && $_GET['showRepDTP']=='True')
		{
			echo $rep_dtp;
		}
		
//REPORTS USERS - JOB TITLES -->

		if(isset($_GET['showRepUsrsJobs']) && $_GET['showRepUsrsJobs']=='True')
		{
			echo $rep_ujt;
		}

		
//MAINTENANCE MENU -->

		if(isset($_GET['showMaintenance']) && $_GET['showMaintenance']=='True')
		{
			echo $maintenance_menu;
		}
		
//USERS MAINTENANCE -->		
		
		if(isset($_GET['showUsers']) && $_GET['showUsers']=='True')
		{
	?>
				<!-- Muszaly kulon beallitani a link tulajdonsagait, mert a tablazat css-e felulirja az eredetit -->
				<style>
					a:link { color: #0000EE; }
					a:visited { color: #551A8B; }
					a { text-decoration:underline; }
				</style>
	<?php				
				echo $users_form;
		}
//WS USERS MAINTENANCE -->		
		
		if(isset($_GET['showWSUsers']) && $_GET['showWSUsers']=='True')
		{
	?>
				<!-- Muszaly kulon beallitani a link tulajdonsagait, mert a tablazat css-e felulirja az eredetit -->
				<style>
					a:link { color: #0000EE; }
					a:visited { color: #551A8B; }
					a { text-decoration:underline; }
				</style>
	<?php				
				echo $ws_users_form;
		}
		
		if(isset($_GET['showPTypes']) && $_GET['showPTypes']=='True')
		{
	?>		<!-- Muszaj kulon beallitani a link tulajdonsagait, mert a tablazat css-e felulirja az eredetit -->
			<style>
				a:link { color: #0000EE; }
				a:visited { color: #551A8B; }
				a { text-decoration:underline; }
			</style>
	<?php				
			echo $ptypes_form;
		}
		
		if(isset($_GET['showTypes']) && $_GET['showTypes']=='True')
		{
	?>		<!-- Muszaj kulon beallitani a link tulajdonsagait, mert a tablazat css-e felulirja az eredetit -->
			<style>
				a:link { color: #0000EE; }
				a:visited { color: #551A8B; }
				a { text-decoration:underline; }
			</style>
	<?php				
			echo $types_form;
		}
		
		if(isset($_GET['showFns']) && $_GET['showFns']=='True')
		{
	?>		<!-- Muszaj kulon beallitani a link tulajdonsagait, mert a tablazat css-e felulirja az eredetit -->
			<style>
				a:link { color: #0000EE; }
				a:visited { color: #551A8B; }
				a { text-decoration:underline; }
			</style>
	<?php				
			echo $fns_form;
		}
		
//WS JOB TITLES MAINTENANCE -->		
		
		if(isset($_GET['showWSFns']) && $_GET['showWSFns']=='True')
		{
	?>
				<!-- Muszaly kulon beallitani a link tulajdonsagait, mert a tablazat css-e felulirja az eredetit -->
				<style>
					a:link { color: #0000EE; }
					a:visited { color: #551A8B; }
					a { text-decoration:underline; }
				</style>
	<?php				
				echo $ws_fns_form;
		}
	?>
	
	<?php

//END OF THE MAIN FORM -->		
		echo '</div>' . "\n";
	?>
	
</body>

</html>

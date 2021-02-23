<?php
	DEFINE('LOGO','img/Alltech%20CMYK.jpg');
	DEFINE('APP_ID','DMS'); //Az applikacio azonositoja, a tApps tablabol
	DEFINE('SUCCESS_UPL_MSG_BEF','<div class="main_success"><p class="main_l_msg">');
	DEFINE('SUCCESS_UPL_MSG_AFT','</p><img class="main_r_close" alt="Close" src="img/close.png" onclick="Javascript:ResetErrorSession();"></div>');
	DEFINE('ERROR_UPL_MSG_BEF','<div class="main_error"><p class="main_l_msg">');
	DEFINE('ERROR_UPL_MSG_AFT','</p><img class="main_r_close" alt="Close" src="img/close.png" onclick="Javascript:ResetErrorSession();"></div>');
	DEFINE('UPLOAD_FOLDER','DMS_UPLOADS'); //Ez az elo tarolo folder
	//DEFINE('UPLOAD_FOLDER','DMS_DEV_UPLOADS'); //Ez az teszt tarolo folder
	//DEFINE('CC',''); //Ha a GA jovahagyja, akkor arrol masolatot kell, hogy kapjon az Olga - test eseteben ez az en mail cimem
	DEFINE('CC',''); //Ha a GA jovahagyja, akkor arrol masolatot kell, hogy kapjon az uploader
	
	
/*

Amik kellenek az uj dokumentum jovahagyasara vonatkozo uzenet elougrasztasahoz es a Dokumetum nezesehez:

	-approved_doc_alert_job.js --> Javascript, ami bizonyos idokozonkent lefuttatja az approved_doc_alert_job.php fajlt a jovahagyas ellenorzesere az adott felhasznalora
	-approved_doc_alert_job.php --> PHP faj, amely tartalmazza a bazis lekerdezeseket arra vonatkozolag, hogy a bejelentkezett felhasznalo jovahagyta-e a neki szant informaciot,
									majd ez alapjan tovabbit egy valtozot az ot futtato Javascript fajlnak (approve_doc_alert_job.js) az eredmennyel
	-approved_doc_alert.php --> PHP fajl, amely maga az eloreugro uzenet-form, amely ertesiti a felhasznalot, hogy hagyja jova a neki szant informaciot + Submit
	
*/
?>

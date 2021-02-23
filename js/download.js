function DownloadFile(u_doc_id, src)
{
	var url='download.php?ID='+ u_doc_id + '&SRC=' + src;
	window.open(url,'_new');
	window.location.reload();
	//window.location = url;
}

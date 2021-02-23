$(document).ready(function()
{	
	$("#sel_dpt").change(function()
	{
		var element = document.getElementById("sel_dpt");
		var sel_dpt = element.options[element.selectedIndex].value;
		$.ajax(
		{
			type: "POST", 
			url: 'get_ws_for_dpt.php',
			cache: false, 
			data:
			{
				SEL_DPT:sel_dpt 
			},
			success: function(result)
			{
				$("#ws").html(result);
				$("#ws_lbl").css({"display":"inline-block","float":"left","position":"relative","clear":"left","margin-bottom":"1em"});
				$("#ws").css({"display":"inline-block","float":"left","position":"relative","clear":"right","margin-bottom":"1em"});
				$("#jqxgridRepUsrsJobs").css({"display":"none"});
				$("#job_titles").css({"display":"none"});
			}
		}); 
	});
});

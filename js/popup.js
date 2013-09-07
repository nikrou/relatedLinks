$(function() {
	$('#link-insert-cancel').click(function() {
		window.close();
	});

	$('#form-entries tr td.maximal a').click(function() {
		// Get post_id	
		var rl = window.opener.related_links;
		var data = rl.data;
		data.href = $(this).attr('title');
		data.id = $(this).attr('href').replace('post.php?id=','');
		data.text = $(this).text();
		
		rl.fncall.add_link.call();
		
		window.close();
	});
});


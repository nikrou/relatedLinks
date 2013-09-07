$(function() {
	$('.related-link-expand', '#related-links-expandable').click(function() {
		var id = $(this).next().attr('href').replace('post.php?id=','');

		$.get('services.php',
		      {f:'getRelatedLinks',postId:id},
		      function(data){
			      var rsp = $(data).children('rsp')[0];
			      if (rsp.attributes[0].value != 'ok') {
				      alert($(rsp).find('message').text());
			      } else {
				      
			      }
		      });
		return false;
	});
});

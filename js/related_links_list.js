$(function() {
	$('.related-link-expand', '#related-links-expandable').click(function() {
		var li = $(this).parent();
		var id = li.attr('id');

		if ($(this).hasClass('expanded')) {
			$(this).removeClass('expanded');
			$(this).attr('src', dotclear.img_plus_src);
			li.find('ul').slideUp();
		} else {
			$(this).addClass('expanded');
			$(this).attr('src', dotclear.img_minus_src);			
			li.find('ul').slideDown();
		}


		if (!li.hasClass('loaded')) {
			$.get('services.php',
			      {f:'getRelatedLinks',postId:id},
			      function(data) {
				      var rsp = $(data).children('rsp')[0];
				      if (rsp.attributes[0].value == 'ok') {
					      var lis = [];
					      $(rsp).find('related_link').each(function() {
						      lis.push('<li>'+$(this).text()+'</li>');
					      });
					      li.append('<ul>'+lis.join('')+'</ul>');
					      li.addClass('loaded');
				      } else {
					      alert($(rsp).find('message').text());
				      }
			      });
		}

		return false;
	});
});

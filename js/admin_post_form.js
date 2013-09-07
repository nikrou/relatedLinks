$(function() {
	$.related_links = {};
	$.related_links.data = {};
	$.related_links.fncall = {};

	if ($('#related_links_ids').val()=='') {
		$('#remove-all-posts').parent().hide();
	}

	$('#add-post').click(function() {
		var open_url = 'plugin.php?p=relatedLinks&popup=1';
		
		window.related_links = $.related_links;

		var p_win = window.open(open_url,'dc_popup',
					'alwaysRaised=yes,dependent=yes,toolbar=yes,height=500,width=760,'+
					'menubar=no,resizable=yes,scrollbars=yes,status=no');	
		return false;
	});

	var function_remove = function() {
		if (window.confirm(rl_text_confirm_remove)) {
			var id = $(this).prev().attr('class').replace('post-','');
			
			var links = [];
			var related_links = $('#related_links_ids').val();
			if (related_links!='') {
				links = related_links.split('|');
			}
			for (var i in links) {
				if (links[i]==id) {
					links.splice(i,1);
				}
			}
			$('#related_links_ids').val(links.join('|'));
			$(this).parent().remove();
			
			if ($('#id').length>0) {
				$.get('services.php',
				      {f:'removeRelatedLink',postId:$('#id').val(),linkId:id},
				      function(data){
					      var rsp = $(data).children('rsp')[0];
					      if (rsp.attributes[0].value != 'ok') {
						      alert($(rsp).find('message').text());
					      }
				      });
			}
		}
		return false;
	};
	
	$.related_links.fncall.add_link = function() {
		var data = $.related_links.data;
		var position = 1;
		
		var links = [];
		var related_links = $('#related_links_ids').val();
		if (related_links!='') {
			links = related_links.split('|');
		}
		var in_array = false;
		for(var i=0;i<links.length;i++) {
			if (links[i]==data.id) {
				in_array = true;
			}
		}
		if (!in_array && data.id!=$('#id').val()) {
			if ($('#no-links').length>0) {
				$('#no-links').remove();
			}

			if (links.length>0) {
				position = $('#related-links-list li.link:last input[type=hidden]').val()+1;
			} else {
				$('#remove-all-posts').parent().show();
			}

			var a_remove = $('<a class="remove" href="#">[x]</a>');
			a_remove.click(function_remove);
			var li = $('<li class="link"><input type="hidden" name="related_link_rank['+data.id+']" value="'+position+'"/><a class="post-'+data.id+'" href="'+data.href+'">'+data.text+'</a>&nbsp;&nbsp;</li>');
			li.append(a_remove);
			$('#related-links-list').append(li);

			links.push(data.id);
			$('#related_links_ids').val(links.join('|'));

			if ($('#id').length>0) {
				$.get('services.php', {f:'addRelatedLink',postId:$('#id').val(),linkId:data.id,position:position},
				      function(data){
					      var rsp = $(data).children('rsp')[0];
					      if (rsp.attributes[0].value != 'ok') {
						      alert($(rsp).find('message').text());
					      }
				      });
			}
		}
	};

	$('.remove').click(function_remove);

	$('#remove-all-posts').click(function() {
		if (window.confirm(rl_text_confirm_remove_all)) {
			if ($('#id').length>0) {
				$.get('services.php', {f:'removeRelatedLinks',postId:$('#id').val()},
				      function(data){
					      var rsp = $(data).children('rsp')[0];
					      if (rsp.attributes[0].value != 'ok') {
						      alert($(rsp).find('message').text());
					      }
				      });
			}

			$('#related_links_ids').val('');
			$('#related-links-list li').remove();
			$(this).parent().hide();
		}	
		return false;
	});

	$('#related-links-list')
		.sortable({
			revert:true,
			opacity:0.7,
			update: function() {
				$(this).find('li.link').each(function(i) {
					$(this).find("input[name^=related_link_rank]")
						.each(function() { $(this).attr('value', (i+1)*1)});
				});
			}
		});
});

$(function () {
  $('.related-link-expand', '#related-links-expandable').attr(
    'src',
    dotclear.img_plus_src
  );

  $('.related-link-expand', '#related-links-expandable').on(
    'click',
    function (e) {
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
        dotclear.jsonServicesGet(
          'getRelatedLinks',
          (data) => {
            var lis = [];
            data.links.forEach(function (link) {
              lis.push('<li>' + link + '</li>');
            });
            li.append('<ul>' + lis.join('') + '</ul>');
            li.addClass('loaded');
          },
          {
            postId: id,
          }
        );
      }

      e.preventDefault();
    }
  );
});

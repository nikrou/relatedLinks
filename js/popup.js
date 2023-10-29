$(function () {
  $('#link-insert-cancel').click(function () {
    window.close();
  });

  $('#form-entries tr td.maximal a').on('click', function (e) {
    e.preventDefault();
    // Get post_id
    var rl = window.opener.related_links;
    var data = rl.data;
    data.href = $(this).attr('title');
    data.id = $(this)
      .parentsUntil('tr.line')
      .parent()
      .attr('id')
      .replace('p', '');
    data.text = $(this).text();

    rl.fncall.add_link.call();

    window.close();
  });
});

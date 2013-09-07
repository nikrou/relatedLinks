<html>
  <head>
    <title><?php echo __('Add a link');?></title>
    <script type="text/javascript" src="js/_posts_list.js"></script>
    <script type="text/javascript" src="index.php?pf=relatedLinks/js/popup.js"></script>
  </head>
  <body>
    <h2><?php echo __('Add a link'); ?></h2>
    <form action="" method="get">
      <p>
	<label class="classic">
	  <?php echo __('Search entry:');?>
	  <?php echo form::field('q',30,255,html::escapeHTML($q));?>
	</label>
	<?php echo form::hidden('p', 'relatedLinks');?>
	<?php echo form::hidden('popup', 1);?>
	<input type="submit" value="<?php echo __('ok');?>"/>
      </p>
    </form>

    <div id="form-entries">
      <?php $post_list->display($page,$nb_per_page);?>
    </div>

    <p><a class="button" href="#" id="link-insert-cancel"><?php echo __('cancel');?></a></p>
  </body>
</html>

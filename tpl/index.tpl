<html>
  <head>
    <title>Related Links</title>
    <link rel="stylesheet" type="text/css" media="screen" href="index.php?pf=relatedLinks/css/related_link.css"/>
    <?php echo dcPage::jsPageTabs($default_tab);?>
    <?php echo dcPage::jsLoad('js/_posts_list.js');?>
    <?php echo dcPage::jsLoad('index.php?pf=relatedLinks/js/related_links_list.js');?>
  </head>
  <body>
    <h2><?php echo html::escapeHTML($core->blog->name); ?> &gt; Related Links</h2>
    <?php if (!empty($message)):?>
    <p class="message"><?php echo $message;?></p>
    <?php endif;?>

    <div class="multi-part" id="relatedlinks_settings" title="<?php echo __('Settings');?>">
      <form action="<?php echo $p_url;?>" method="post" enctype="multipart/form-data">
	<div class="fieldset">
	  <h3><?php echo __('Plugin activation'); ?></h3>
	  <p class="field">
	    <?php echo form::checkbox('relatedlinks_active', 1, $relatedlinks_active);?>
	    <label class="classic" for="relatedlinks_active"><?php echo __('Enable Related Links plugin');?></label>
	  </p>
	</div>
	<?php if ($relatedlinks_active):?>
	<div class="fieldset">
	  <h3><?php echo __('Content generated by plugin');?></h3>
	  <p><?php echo __('Content generated by the plugin is added automatically at end of each post content. If you don\'t choose that option, you need to customize generated content. Click on "installation" tab (or help button) to see available tags and to see a model example.');?></p>
	  <p>
	    <?php echo form::checkbox('relatedlinks_automatic_content', 1, $relatedlinks_automatic_content); ?>
	    <label class="classic" for="relatedlinks_automatic_content"><?php echo __('Put automatically at end of post content?');?></label>
	  </p>

	  <p>
	    <label class="classic">
	      <?php echo form::radio('relatedlinks_content_with_image', 1, $relatedlinks_content_with_image==1);?>
	      <?php echo __('Use image for related links?');?>
	    </label>
	  </p>
	  <p>
	    <label class="classic">
	      <?php echo form::radio('relatedlinks_content_with_image', 0, $relatedlinks_content_with_image==0);?>
	      <?php echo __('Do not use image for related links?');?>
	    </label>
	  </p>
	</div>
	<?php endif;?>
	<?php echo form::hidden('p','relatedLinks');?>
	<?php echo $core->formNonce();?>
	<input type="submit" name="saveconfig" value="<?php echo __('Save configuration'); ?>" />
      </form>
    </div>
    <?php if ($relatedlinks_active):?>
    <div class="multi-part" id="relatedlinks_links" title="<?php echo __('Links'); ?>">
      <p><?php echo __('Posts with related links');?></p>
      <?php if (!$related_links->isEmpty()):?>
      <form action="<?php echo $p_url;?>" method="post">
	<ul id="related-links-expandable">
	  <?php while ($related_links->fetch()):?>
	  <li id="<?php echo $related_links->post_id;?>">
	    <img src="images/expand.png" class="related-link-expand" alt=""/>
	    <?php echo form::checkbox('related_links_list[]', $related_links->post_id);?>
	    <a class="my_class" href="<?php echo $core->getPostAdminURL('post', $related_links->post_id);?>">
	      <?php echo $related_links->post_title;?>
	    </a>
	    &nbsp;(<?php echo $related_links->nb_links;?>)
	  </li>
	  <?php endwhile;?>
	</ul>

	<div class="two-cols">
	  <p class="col checkboxes-helpers"></p>
	  <p class="col right">
	    <?php echo form::combo('action',$combo_action);?>
	    <?php echo $core->formNonce();?>
	    <input type="submit" value="<?php echo __('ok');?>"/>
	  </p>
	</div>
      </form>
      <?php else:?>
      <p><?php echo __('No related link yet');?></p>
      <?php endif;?>
      <p>
    </div>
    <div class="multi-part" id="relatedlinks_code" title="<?php echo __('Installation'); ?>">
      <p><?php echo __('The plugin define new tags for template:');?></p>
      <ul>
	<li><strong>RelatedLinksIf</strong> (<?php echo __('block');?>) : <?php echo __('to only display related links if there are somes.');?></li>
	<li><strong>RelatedLinks</strong> (<?php echo __('block');?>) : <?php echo __('loop to display related links.');?></li>
	<li><strong>RelatedLinkTitle</strong> (<?php echo __('value');?>) : <?php echo __('link label');?></li>
	<li><strong>RelatedLinkURL</strong> (<?php echo __('value');?>) : <?php echo __('link URL');?></li>
      </ul>

      <p><?php echo __('Code example to add to your "post.html" theme template:');?></p>
      <pre>
	&lt;tpl:RelatedLinksIf&gt;
	&nbsp;&nbsp;&lt;div id="related-links"&gt;
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;h3&gt;{{tpl:lang You liked that article...}}&lt;/h3&gt;
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;p&gt;{{tpl:lang you should like the following articles:}}&lt;/p&gt;
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;ul class="posts"&gt;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;tpl:RelatedLinks&gt;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;li&gt;&lt;a href="{{tpl:RelatedLinkURL}}"&gt;{{tpl:RelatedLinkTitle}}&lt;/a&gt;&lt;/li&gt;
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tpl:RelatedLinks&gt;
	&nbsp;&nbsp;&nbsp;&nbsp;&lt;/ul&gt;
	&nbsp;&nbsp;&lt;/div&gt;
	&lt;/tpl:RelatedLinksIf&gt;
      </pre>
    </div>
    <div class="multi-part" id="relatedlinks_about" title="<?php echo __('About'); ?>">
      <p>
	<?php echo __('If you want more informations on that plugin or have new ideas to develope it, or want to submit a bug or need help (to install or configure it) or for anything else ...');?></p>
      <p>
	<?php printf(__('Go to %sthe dedicated page%s in'),
	      '<a href="http://www.nikrou.net/pages/relatedLinks">',
	      '</a>');?>
	<a href="http://www.nikrou.net/">Le journal de nikrou</a>
      </p>
      <p><?php echo __('Made by:');?>
	<a href="http://www.nikrou.net/contact">Nicolas</a> (nikrou)
      </p>
    </div>
    <?php endif;?>
    <?php
       dcPage::helpBlock('relatedLinks');
       ?>
  </body>
</html>

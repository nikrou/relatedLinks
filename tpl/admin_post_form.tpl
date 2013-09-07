<fieldset>
  <legend><?php echo __('Related Links');?></legend>  

  <p>
    <a id="add-post" href="#"><?php echo __('Add new post to list');?></a>
    <span>&nbsp;-&nbsp;<a id="remove-all-posts" href="#"><?php echo __('Remove all posts?');?></a></span>
    <?php echo form::hidden('related_links_ids',$related_links_ids);?>
  </p>

  <ul id="related-links-list">
    <?php if (!empty($related_links) && !$related_links->isEmpty()):?>
    <?php while ($related_links->fetch()):?>
    <li class="link"><input type="hidden" name="related_link_rank[<?php echo $related_links->link;?>]" value="<?php echo $related_links->position;?>"/><a class="post-<?php echo $related_links->link;?>" href="<?php echo $related_links->url;?>"><?php echo $related_links->title;?></a>&nbsp;<a class="remove" href="#">[x]</a></li>
    <?php endwhile;?>
    <?php else:?>
    <li id="no-links">
      <?php echo __('No related link yet');?>
    </li>
    <?php endif;?>
  </ul>
</fieldset>

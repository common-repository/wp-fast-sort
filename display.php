
<div class="wrap">
    <h2><?php _e('Display', 'wpfastsort') ?></h2>

    <?php if(isset($page_created)): ?>
    <p><?php _e('The page was successfully created.', 'wpfastsort') ?> </p>
    <?php endif; ?>
    <?php if(isset($page_deleted) && $page_deleted): ?>
    <p><?php _e('The page was successfully deleted.', 'wpfastsort') ?> </p>
    <?php endif; ?>
    <?php if(isset($page_deleted) && !$page_deleted): ?>
    <p><?php _e('Error in moving to trash...', 'wpfastsort') ?> </p>
    <?php endif; ?>

    <p><?php _e('This tool is to create a page with the different posts sorted by author.', 'wpfastsort') ?></p>

	
	
		
		
    <?php
    if(!$this->displayPageExists()) : ?>
		
	<p>Title of your page :  <br /></p>
	<form action="" method="post" accept-charset="utf-8">
				
        <input type="text" name="page_title" value="<?= get_option('wpfs_pagetitle')?>" size="55" />
        <input type="hidden" name="create_display_page" value="true" />
        <input type="submit" value="<? _e('Create Page', 'wpfastsort') ?>" />
    </form>
	
	
    <?php else: ?>

    <p>
            <?= _e('Do you want to delete the display page ?', 'wpfastsort') ?>
    </p>

    <form action="" method="post" accept-charset="utf-8">
        <input type="hidden" name="delete_display_page" value="true" />
        <input type="submit" value="<? _e('Delete Page', 'wpfastsort') ?>" />
    </form>
	
	

    <?php endif; ?>
</div>

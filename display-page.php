<?php

//For username_exists()
require_once ( ABSPATH . WPINC . '/registration.php' );

/* Returns the first letter in UPPERCASE */
function first_letter_upper($string) {
    return strtoupper($string[0]);
}

/* We either display the author list or the posts of a chosen author */
if (!isset($_REQUEST["postsof"])) {
    /* The list of all the post authors */
    $authors = array();

    $blogusers = get_users_of_blog(); //gets registered users
    if ($blogusers) {
        foreach ($blogusers as $bloguser) {
            $author = $bloguser->user_login; //gets the actual data about each user
			
            if (!in_array($author, $authors) )//&& (get_usernumposts($bloguser->user_id) > 0))
                $authors[]=$author;
				
        }
    }
    else {
        echo '<p>'._e('Bizarrely, your blog appears to have no users. Something weird has happened.', 'wpfastsort').'</p>';
    }

    /* Get the list of the Initials of the authors.
     * First we get which ones actually exist
     */

    $letter_used = array();

    foreach($authors as $author) {
        $first_letter = first_letter_upper($author);
        $letter_used[$first_letter] = true;
    };

    /*
     * Now we create an anchor for each letter
     */
    ?>

<p><b> <?php _e('Author List','wpfastsort'); ?> </b></p>

    <?php
    for($c=ord('A'); $c <= ord('Z'); $c++) {
        $letter = chr($c);
        if (isset($letter_used[$letter]))
            echo " <a href='#$letter'>$letter</a> ";
        else
            echo " $letter ";
    };

    // Sort it by lexical order, so that they appear that way
    natcasesort($authors);
    $current_letter = null;

    foreach($authors as $author) {
        if ($current_letter != first_letter_upper($author)) {
            $letter = first_letter_upper($author);
            echo "<h3><a name='$letter'>$letter</a></h3>";
            $current_letter = $letter;
        }
        // The current url + a get statement with the author's name
        //$url = get_page_link().'&postsof='.$author;
		$id = username_exists($author);
		$url = get_bloginfo('url').'/?author='.$id;
		
        //echo "<p><a href='$url'>$author</p>";
		echo "<p><a href='$url'>$author</p>";
    }
} /*else {
    // Posts of that author 
    $author = $_REQUEST["postsof"];
    $posts = array();





    // If the author exists already in the central blog, we get his posts //
    if ( ($id = username_exists($author)) ) {
        $args = array(
                'post_type' => 'post',
                'numberposts' => -1,
                'author' => $id
        );
        $posts = get_posts($args);
    }

    foreach($posts as $post) :
        ?>
<h2><a href="<?= get_page_link($post->ID); ?>" id="post-<?= $post->ID; ?>"><?= $post->post_title; ?></a></h2>
<small> 
        <?= date('F jS, Y' , strtotime($post->post_date)); ?>
</small>
<div class="entry">
    <p><?= $post->post_content;?></p>
</div>

    <?php
    endforeach;
}*/
?>

<?php

/*
Plugin Name: WP Fast Sort
Version: 1.0
Plugin URI: Plugin URL goes here (e.g. http://yoursite.com/wordpress-plugins/plugin/)
Description: This plugin allows you to sort your posts by authors. 
Author: Télécom Bretagne
Author URI: http://www.coyotte508.com
*/

class WPFastSort {
    var $version;
	
    /* Constructor */
    function WPFastSort() {
        $this->version =  '1.1.0';
        $this->installed = get_option('wpfs_version');

        /*
         * We specify all the hooks and filters for the plugin
        */
        register_activation_hook(__FILE__, array(&$this,'activate'));
        register_deactivation_hook(__FILE__, array(&$this,'deactivate'));

        /*
         * Admin menu.
        */
        add_action('admin_menu', array(&$this, 'adminMenu'));

        /*
         * To execute php in the display page
        */
        add_action('the_content', array(&$this, 'filterContent'));
        add_action('the_title', array(&$this, 'filterTitle'));
		
    }

    /**
     * Adds the WP Fast Sort item to menu
     *
     */
    function adminMenu() {
        add_options_page("WP Fast Sort", "WP Fast Sort" , 8, __FILE__, array(&$this, 'admin'));
    }

    /*
     * The admin menu that's displayed.
     * The display of the menu occurs in display.php
     */
    function admin() {
        /* We process the menu and then display it */
        if (isset($_REQUEST['create_display_page'])) {
			if (isset ($_REQUEST['page_title']) && $_REQUEST['page_title']) {
			update_option('wpfs_pagetitle', $_REQUEST['page_title']);
			}
            $page_created = $this->createDisplayPage();
        } else if (isset ($_REQUEST['delete_display_page'])) {
            $page_deleted = $this->deleteDisplayPage();
			update_option('wpfs_pagetitle', 'Posts by Author');
		}
				
        include('display.php');
		
		return true;
	}
	
    /*
     * Tests if the display page is already created
    */
    function displayPageExists() {
        $pageid = get_option("wpfs_display_page");

        return get_post_type($pageid) == 'page' && get_page($pageid)->post_status == 'publish';
    }


    function createDisplayPage() {
        if ($this->displayPageExists())
            return false;

        if (!current_user_can('publish_pages')) {
            return false;
        }

        $post_args = array();

        $post_args['post_type'] = 'page';
        $post_args['post_status'] = 'publish';
        $post_args['post_title'] = get_option('wpfs_pagetitle');
        $post_args['post_content'] = '[Content modified by WP Fast Sort on display]';

        $id = wp_insert_post($post_args);

        if (!$id)
            return false;

        /* So we know later if its the display by alphabetical order page */
        update_option("wpfs_display_page", $id);
        return true;
    }

    /*
     * Delete the page that displays authors by lexical order
    */
    function deleteDisplayPage() {
        if (!$this->displayPageExists())
            return false;

        /* The id of the display page */
        $pageid = get_option("wpfs_display_page");

        $page_deleted = current_user_can('delete_page', $pageid) &&wp_delete_post($pageid);
    }
	
    /**
     * To execute the display page
     *
     * @param string the content to change
     */
    function filterContent($content) {
        if (get_the_ID()) {
            /* Checking its the right page */
            if (!is_page() || get_option("wpfs_display_page") != get_the_ID())
                return $content;

            /* It's the right page, so we display its custom php code */
            $filename = "display-page.php";

            /* The custom php file is included, its output is captured
            * and then displayed.
            */
            ob_start();
            include $filename;
            $content = ob_get_contents();
            ob_end_clean();
            return $content;

        }
        return $content;
    }

    /**
     * Changes the title of the page if its the display authors by lexical order page
     *
     * @param string $title the title of the page being displayed
     */
    function filterTitle($title) {
        if (get_the_ID()) {
            /* Checking its the right page */
            if (!is_page() || get_option("wpfs_display_page") != get_the_ID() )
                return $title;

            /* We need this otherwise wordpress messes up with the side bar */
            global $already_displayed_title;
            if (isset($already_displayed_title[$title]))
                return $title;
            $already_displayed_title[$title] = true;

            /* It's the right page, so we display its custom title */
            if (isset($_REQUEST['postsof'])) {
                return 'Posts of '.$_REQUEST['postsof'];
            }
        }
        return $title;
    }    

    #Called at the activation of the plugin
    function activate() {
        global $wpdb;

        // only re-install if new version or uninstalled
        if(! $this->installed || $this->installed != $this->version) {
            /* use dbDelta() to create tables */
            add_option('wpfs_version', $this->version);

            $this->installed = true;
		if (!get_option('wpfs_pagetitle')) {
            add_option('wpfs_pagetitle', "Posts by Author");
        }
        }
		
    }

    #Called at the deactivation of the plugin
    function deactivate() {
    }
}

$wpfastsort = & new WPFastSort();

?>
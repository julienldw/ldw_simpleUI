<?php
/*
Plugin Name: Simple UI
Author: Julien Appert
Author URI: http://lamourduweb.com
Version: 0.1.1
Description:
*/

namespace ldw;

if( ! defined('ABSPATH')) exit;

class simple_ui{

  // crée le type d'utilisateur
  public static function activation(){

  	// à garder uniquement en développement
  	//remove_role('ldw_customer');

  	add_role('ldw_customer', 'Client LDW', array(
  		'read'					=>	true,
  		'upload_files'			=>	true,
  		'manage_categories'		=>	true,

  		'edit_posts'			=>	false,
  		'delete_post'			=>	false,
  		'edit_others_posts'		=>	false,
  		'edit_published_posts'	=>	false,
  		'publish_posts'			=>	false,
  		'delete_published_posts'=>	false,
  		'manage_categories'=>	false,

  		'edit_pages'			=>	true,
  		'delete_page'			=>	true,
  		'edit_others_pages'		=>	true,
  		'edit_published_pages'	=>	true,
  		'publish_pages'			=>	true,
  		'delete_published_pages'=>	true

  	));
  }

  // modifie le logo de la page de connexion
  public static function login_head(){
      ?>
      <style>
      .login h1 a{
          background-image:url(https://admin.lamourduweb.com/wp-content/uploads/2015/04/logo100.png)!important;
      }
      </style>
      <?php
  }

  // cache des éléments
  public static function admin_head(){
  	global $user_ID;
  	$userdatas = get_userdata($user_ID);
  	if(in_array('ldw_customer',$userdatas->roles)){
  		?>
  		<style>
  		select[name=seo_filter]{ display:none;}
  		table.wpseo-taxonomy-form{ display:none;}
  		form#edittag h2{ display:none;}
              #submitdiv .misc-pub-visibility,
              #submitdiv .misc-yoast{ display:none;}
  		</style>
  		<?php
  	}
      ?>
      <style>
      #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon::before {
          content:'';
      }
      #wpadminbar > #wp-toolbar > #wp-admin-bar-root-default #wp-admin-bar-wp-logo > .ab-item{
          background-image:url(https://admin.lamourduweb.com/wp-content/uploads/2015/04/logo30.png)!important; background-size:100%;
          background-position:50%;
      }
      </style>
      <?php
  }

  // cache des boites dans l'éditeur de pages et articles
  public static function do_meta_boxes(){
  	global $user_ID;
  	$userdatas = get_userdata($user_ID);
  	if(in_array('ldw_customer',$userdatas->roles)){
  		remove_meta_box('tagsdiv-post_tag','post','side');
  		remove_meta_box('wpseo_meta','post','normal');

  		remove_meta_box('wpseo_meta','page','normal');

  	}
  }
    //enlève des éléments dans la topbar
    public static function wp_before_admin_bar_render(){
    	global $user_ID;
    	$userdatas = get_userdata($user_ID);
    	if(in_array('ldw_customer',$userdatas->roles)){
    		global $wp_admin_bar;

    		$wp_admin_bar->remove_menu('comments'); // commentaires
    		$wp_admin_bar->remove_menu('wpseo-menu');
    	}
    }

    //modifie le tableau de bord
    public static function dashboard_setup(){
    	global $user_ID;
    	$userdatas = get_userdata($user_ID);
        wp_add_dashboard_widget( 'dashboard_rss', 'LdW Actus', array('\ldw\simple_ui','dashboard_rss') );
    	if(in_array('ldw_customer',$userdatas->roles)){
    		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
    		remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
    	}
    }

    public static function dashboard_rss() {
    	$feeds = array(
    		'ldw' => array(

    			'link' => 'http://lamourduweb.com',
    			'url' => 'http://lamourduweb.com/feed/',
    			'title'        => 'Actualités de Lamour du Web',
    			'items'        => 5,
    			'show_summary' => 1,
    			'show_author'  => 0,
    			'show_date'    => 1,
    		)
    	);
    	wp_dashboard_primary_output('dashboard_rss', $feeds);
    }

    public static function manage_post_columns($columns){
    	global $user_ID;
    	$userdatas = get_userdata($user_ID);
    	if(in_array('ldw_customer',$userdatas->roles)){
    		unset($columns['tags']);
    	}
    	return $columns;
    }

    public static function manage_edit_post_columns($columns){
    	global $user_ID;
    	$userdatas = get_userdata($user_ID);
    	if(in_array('ldw_customer',$userdatas->roles)){
    		unset($columns['wpseo-score']);
    		unset($columns['wpseo-title']);
    		unset($columns['wpseo-metadesc']);
    		unset($columns['wpseo-focuskw']);
    	}
    	return $columns;
    }

}

//add_action( 'admin_menu', array('\ldw\simple_ui','admin_menu') );
add_action('do_meta_boxes',array('\ldw\simple_ui','do_meta_boxes'));
add_action('wp_dashboard_setup', array('\ldw\simple_ui','dashboard_setup') );
add_action( 'wp_before_admin_bar_render', array('\ldw\simple_ui','wp_before_admin_bar_render') );
add_filter('manage_posts_columns', array('\ldw\simple_ui','manage_post_columns'),10,1);
add_filter('manage_edit-post_columns', array('\ldw\simple_ui','manage_edit_post_columns'),10,1);
add_filter('manage_edit-page_columns', array('\ldw\simple_ui','manage_edit_post_columns'),10,1);
add_filter('manage_edit-bien_columns', array('\ldw\simple_ui','manage_edit_post_columns'),10,1);
add_action('admin_head',array('\ldw\simple_ui','admin_head'));
add_action('login_head',array('\ldw\simple_ui','login_head'));
//add_action('init',array('\ldw\simple_ui','activation'));
register_activation_hook( __FILE__, array('\ldw\simple_ui','activation') );

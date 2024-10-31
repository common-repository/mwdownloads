<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://masterweb.com.co/
 * @since             1.0.0
 * @package           Downloads
 *
 * @wordpress-plugin
 * Plugin Name:       MWDownloads
 * Plugin URI:        http://masterweb.com.co/contacto
 * Description:       Carga tus archivos de certificaciones para ser buscados y descargados por el interesado.
 * Version:           1.0.0
 * Author:            Amaldo Molinares
0++ * Author URI:        http://masterweb.com.co'r5

 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mwdownloads
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'MWDOWNLOADS_VERSION', '1.0.0' );

function register_cpt_mwdownload() {

	/**
	 * Post Type: downloads.
	 */

	$labels = [
		"name" => __( "downloads", "mwdownloads" ),
		"singular_name" => __( "download", "mwdownloads" ),
		"menu_name" => __( "Downloads", "mwdownloads" ),
		"all_items" => __( "All Downloads", "mwdownloads" ),
		"add_new" => __( "Add New", "mwdownloads" ),
		"add_new_item" => __( "Add New Downloads", "mwdownloads" ),
		"edit_item" => __( "Edit Downloads", "mwdownloads" ),
		"new_item" => __( "New Download", "mwdownloads" ),
		"view_item" => __( "View Download", "mwdownloads" ),
		"view_items" => __( "View Downloads", "mwdownloads" ),
		"search_items" => __( "Search Download", "mwdownloads" ),
		"not_found" => __( "No Download Found", "mwdownloads" ),
		"not_found_in_trash" => __( "No Download Found in Trash", "mwdownloads" ),
		"name_admin_bar" => __( "Download", "mwdownloads" ),
	];

	$args = [
		"label" => __( "downloads", "mwdownloads" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "download", "with_front" => true ],
		"query_var" => true,
		"menu_icon" => "dashicons-download",
		"supports" => [ "title" ],
		"taxonomies" => [ "category" ],
	];

	register_post_type( "download", $args );
}

// archive template download

function mwdownload_archive_template($archive)
{
  $archiveTemplate = plugin_dir_path( __FILE__ );
  $archiveTemplate .= '/template/archive-download.php';
  
  if ('download' === get_post_type(get_the_ID())) {
      if (file_exists($archiveTemplate)) {
          return $archiveTemplate;
      }
  }
  
  return $archive;
}


// plugin init
add_action( 'init', 'mwdownloads_init' );
add_action('archive_template', 'mwdownload_archive_template');

// add necessary scripts

function mwdownloads_scripts() {
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_enqueue_media();
wp_register_script('mwdownloads-admin-js', plugin_dir_url( __FILE__ ).'/js/mwdownloads-admin.js', array('jquery','media-upload','thickbox'));
wp_enqueue_script('mwdownloads-admin-js');
wp_enqueue_script('search-download-js', plugin_dir_url( __FILE__ ).'/js/search-download.js', array(), '1.0', true);
wp_localize_script( 'search-download-js', 'ajax_url', admin_url( 'admin-ajax.php' ) );
wp_register_script('mwdownloads_bootstrap', plugin_dir_url( __FILE__ ).'/js/bootstrap/bootstrap.js', array( 'jquery' ),'',true);
wp_enqueue_script('mwdownloads_bootstrap');
wp_register_script('mwdownloads_popover', plugin_dir_url( __FILE__ ).'/js/bootstrap/bootstrap.bundle.min.js', array( 'mwdownloads_bootstrap' ),'',true);
wp_enqueue_script('mwdownloads_popover');
}

 // add style 
function mwdownloads_styles() {
wp_enqueue_style('thickbox');
wp_enqueue_style( 'mwdownloads-style', plugin_dir_url( __FILE__ ).'/css/mwdownloads-style.css' );
wp_register_style('mwdownloads_bootstrap', plugin_dir_url( __FILE__ ).'/css/bootstrap/bootstrap.css');
wp_enqueue_style('mwdownloads_bootstrap');
wp_enqueue_style( 'dashicons' );
}
 
if($GLOBALS['pagenow']=='post.php') {
add_action('admin_print_scripts', 'mwdownloads_scripts');
add_action('admin_print_styles', 'mwdownloads_styles');
}

// load scripts init plugin

function mwdownloads_init(){
	register_cpt_mwdownload();
	mwdownloads_scripts();
	mwdownloads_styles();
	
}


add_action('add_meta_boxes', 'mwdownloads_add_metabox');

// add metabox

function mwdownloads_add_metabox() {

	add_meta_box('mwdownloads_files', 
				  __('Adjuntar Archivo', 'mwdownloads' ), 
				 'mwdownloads_files_display', 
				 'download',
        		 'normal',
        		 'high'
    			);

}

// function display metabox

function mwdownloads_files_display($post) {

	wp_nonce_field( plugin_basename( __FILE__ ), 'mwdownloads_files_nonce' );

	$filearray = get_post_meta( get_the_ID($post->ID), 'mwdownloads_files', true );
	$data = explode(",", $filearray);
	$file_Name = $data[0];
	$file_FileName = $data[1];
	$file_Weight = $data[02];
	$icon_file = plugin_dir_url( __FILE__ ).'/img/icon_file.png';
	$icon_edit = plugin_dir_url( __FILE__ ).'/img/pencil.png';
	$icon_remove = plugin_dir_url( __FILE__ ).'/img/remove.png';
	if ($filearray){
		$this_file = $filearray;
		$html = '<table id="mwd-table" style="width:100%" >';
		$html .= '<tr>';
		$html .= '<td class="mwd-section-one"><img src="'. $icon_file .'" width="100" height="100" /></td>';
		$html .= '<td class="mwd-section-two"><small>'. $file_Name .'</small><br><small>'. $file_FileName .'</small><br><small>'. $file_Weight .'</small></td>';
		$html .= '<td class="mwd-section-three"><img class="mwdownloads_files" id="mwdownloads_files" name="mwdownloads_files" data-toggle="tooltip" data-placement="bottom" title="Update File" src="'. $icon_edit .'" width="20" height="20" />
					  <img id="mwdownloads_remove_files" data-toggle="tooltip" data-placement="bottom" title="Remove File" src="'. $icon_remove .'" width="20" height="20" />
	  </button></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td><small id="mwdownloads_files_name"></small></td>';
		$html .= '<td><input type="hidden" id="mwdownloads_files_hidden" name="mwdownloads_files" value="'. $this_file . '"/></td>';
		$html .= '</tr>';		
		$html .= '</table>';	

		
	}else{
		$html = '<table style="width:100%" >';
		$html .= '<tr>';
		$html .= '<td >seleccionar archivo</td>';
		$html .= '<td></td>';
		$html .= '<td></td>';
		$html .= '</tr>';
		$html .= '<tr>';
		$html .= '<td><input type="button" id="mwdownloads_files" name="mwdownloads_files" value="Upload File" size="25" class="button-primary" /></td>';
		$html .= '<td><small id="mwdownloads_files_name"></small></td>';
		$html .= '<td><input type="hidden" id="mwdownloads_files_hidden" name="mwdownloads_files" value=""/></td>';
		$html .= '</tr>';		
		$html .= '</table>';	
		
	}	

	echo $html;
	   

}


add_action('save_post', 'save_mwdownload', 15 ,1);

//save post download

function save_mwdownload($id) {
	global $post;
	
    /* --- security verification --- */
    if(!wp_verify_nonce($_POST['mwdownloads_files_nonce'], plugin_basename(__FILE__))) {
      return $id;
    } // end if
       
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $id;
      
    } // end if
       
    if('page' == $_POST['download']) {
      if(!current_user_can('edit_page', $id)) {
        return $id;
      } // end if
    } else {
        if(!current_user_can('edit_page', $id)) {
            return $id;
        } // end if
    } // end if
    /* - end security verification - */
    
	  $filearray = get_post_meta( get_the_ID($post->ID), 'mwdownloads_files', true );
	  
    if (!empty($filearray)) {

        update_post_meta(
            $id,
            'mwdownloads_files',
            strip_tags($_POST['mwdownloads_files'])
        );
    }else{
    	add_post_meta(
            $id,
            'mwdownloads_files',
            strip_tags($_POST['mwdownloads_files'])
        );
    }
    
} // end save_custom_meta_data

function update_edit_form_mwdownload() {
    echo ' enctype="multipart/form-data"';
} // end update_edit_form_mwdownload
add_action('post_edit_form_tag', 'update_edit_form_mwdownload');

// shortcode search
function search_mwdownload() {

	ob_start();
	?>
	<div class="row container search-download" id="search-download" >
					 <form role="search" method="get" class="search-form" action="">

					    <span class="screen-reader-text"><?php echo _x( 'Buscar Por:', 'label' ) ?></span>
					    <!-- <input type="hidden" name="post_type" value="download" />  -->
					    <input type="search" class="search-field" placeholder="<?php echo esc_attr_x( 'Buscar …', 'placeholder' ) ?>" value="<?php echo get_search_query() ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" />
					     <button type="submit" class="search-submit"> <?php echo esc_attr_x( 'Buscar', 'submit button' ) ?> </button>
					  	
					 </form>	 	
			</div> 
	<?php
	return ob_get_clean();
}

add_shortcode( 'search_mwdownload', 'search_mwdownload' );

add_action('wp_ajax_search_mwdownload', 'search_mwdownload_callback' );
add_action('wp_ajax_nopriv_search_mwdownload', 'search_mwdownload_callback' );

// search ajax

function search_mwdownload_callback(){

	 $result = array(); 

	 $title = sanitize_title( $_GET['info'] );
	 //var_dump($title); exit();

	 $arg = array(
	 	"post_type" => "download",
	 	's' => $title,
	 );

	 $download_query = new WP_Query($arg);

	 while ( $download_query->have_posts() ) :
				$download_query->the_post();
				$filearray = get_post_meta( get_the_ID($post->ID), 'mwdownloads_files', true );
				$data = explode(",", $filearray);
				$file_Name = $data[0];
				$file_fileName = $data[1];
				$file_weight = $data[02];
				$file_url = $data[03];
				
				?>

			<div id="download-results">
	 			<div class="row container mwd-container">
	 				<div class="col-md-2">
	 					<span class="dashicons dashicons-format-aside icon-file"></span>
	 				</div>
	 				<div class="col-md-6">
	 					<span class="mwd-tittle"><?php echo $file_Name ?></span class="mwd-tittle"><br>
	 					<span class="mwd-subtittle"><?php echo $file_fileName ?></span><br>
	 					<span class="mwd-subtittle"><?php echo $file_weight ?></span>
	 				</div>
	 				<div class="col-md-4">
	 					<a href="<?php echo $file_url ?>" type="button" class="btn btn-primary mwd-link-download" data-toggle="tooltip" data-placement="top" title="Descargar Archivo" download>
	 					 Descargar
	 					</a> 
	 				</div>
	 			</div>	
	 		</div>
	 				<?php
	 			// End the loop.
	 endwhile;


	 	wp_die( );

}
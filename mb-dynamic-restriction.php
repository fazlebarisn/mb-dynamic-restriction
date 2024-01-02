<?php 
/*
 * Plugin Name:       MB Dynamic Category Restriction
 * Description:       This is a helper plugin.
 * Version:           0.0.1
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            CanSoft
 * Author URI:        https://cansoft.com/
*/

require_once( plugin_dir_path( __FILE__ ) . '/inc/display-table-row.php');

//add cron schedules 
function mb_product_categories_res_cron_schedules($schedules){
    if(!isset($schedules['every_twelve_hours'])){
        $schedules['every_twelve_hours'] = array(
            'interval' => 12*60*60, // Every 12 hours
            'display' => __('Every 12 hours'));
    }
    return $schedules;
}
add_filter('cron_schedules','mb_product_categories_res_cron_schedules');


//For clear cron schedule after deactivate this plugin
function mb_res_cat_plugin_deactivation(){
    wp_clear_scheduled_hook('mb_product_cat_res');
}
register_deactivation_hook(__FILE__, 'mb_res_cat_plugin_deactivation');

/**
 * Create database when pluign is activated
 */
function mb_init(){
    global $wpdb; 
    $table_name = $wpdb->prefix.'mb_restrictions';
    $sql = "CREATE TABLE {$table_name} (
        id INT NOT NULL AUTO_INCREMENT,
        province VARCHAR(250),
        category_restrictions VARCHAR(250),
        in_store_restrictions VARCHAR(250),
        online_delivery_restrictions VARCHAR(250),
        PRIMARY KEY (id)
    )";

    require_once (ABSPATH . "wp-admin/includes/upgrade.php");
    dbDelta($sql);
}
register_activation_hook( __FILE__, 'mb_init' );

/**
 * Flush database when pluign is deactivate
 */
function flush_database(){
    global $wpdb; 
    $table_name = $wpdb->prefix.'mb_restrictions';
    $query = "TRUNCATE TABLE {$table_name}";
    $wpdb->query($query);
}
// register_deactivation_hook( __FILE__, 'flush_database' );

function mbd_load_assets(){
    wp_enqueue_style('mbd-main-style-css', plugin_dir_url(__FILE__) .  'resources/css/mbd-style.css', null, time());
}
add_action( 'admin_enqueue_scripts', 'mbd_load_assets');

/**
 * Creating Plugin Option Page
 */
function mbd_create_admin_page(){

    //THIS IS PLUGIN MAIN OPTION PAGE
    $page_title = __('Restrictions', 'modern-beauty');
    $menu_title = __('Restrictions', 'modern-beauty');
    $capability = 'manage_options';
    $slug = 'restrictions';
    $call_back = 'mbd_option_page_content';
    
    add_menu_page( $page_title, $menu_title, $capability, $slug, $call_back, 'dashicons-dismiss', 10);

    //List all sub menu
    add_submenu_page( $slug, 'Add Restriction', 'Add Restriction', $capability, 'add_restriction', 'mbd_add_restriction');

    add_submenu_page( $slug, 'Manages', 'Manages', $capability, 'manage_restrictions', 'mbd_manage_restrictions');


    // add_submenu_page( $slug, 'Settings', 'Settings', $capability, 'restrictions_setting', 'mbd_restrictions_setting');
}
add_action('admin_menu', 'mbd_create_admin_page');

/**
 * move the submenu undet Mb Syncs Menu 
 * @author Fazle Bari
 */
function mb_category_restrictions_sync_menu_pages() {
    add_submenu_page(
        'mb_syncs',
        'Category Restrictions Sync',
        'Category Restrictions Sync',
        'manage_options',
        'restrictions_setting',
        'mbd_restrictions_setting'
    );
}
add_action('admin_menu', 'mb_category_restrictions_sync_menu_pages', 999);

/**
 * Restriction Page main content
 */
function mbd_option_page_content(){
    //Include Group table
    include_once( plugin_dir_path( __FILE__ ) . '/part/mbd-group-table.php');
}

/**
 * Callback function for add new Restriction
 */
function mbd_add_restriction(){
    //Html form for adding data
    include_once( plugin_dir_path( __FILE__ ) . '/part/mbd-form.php');
}


// For Check data is already exit or not ?
function is_data_exit_in_database($column_name, $province, $data){
    global $wpdb; 
    $table_name = $wpdb->prefix.'mb_restrictions';

    $query = $wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE province = '{$province}' AND $column_name = '{$data}'");
    $result = $wpdb->get_var($query);

    if ($result > 0) {
        return true;
    } else {
        return false;
    }
}

//add_all_restriction
function add_all_restriction_cat($_single_res_cat_obj, $_province){
    global $wpdb; 
    $table_name = $wpdb->prefix.'mb_restrictions';

    if($_single_res_cat_obj->province == $_province){

        $all_res_cat = $_single_res_cat_obj->restriciton; //get all restricted segval/category from api
        $all_in_store_res_cat = $_single_res_cat_obj->in_store_restriciton; //get all in store restricted segval/category from api
        $all_online_delivery_res_cat = $_single_res_cat_obj->online_delivery_restriction; //get all Online delivery restricted segval/category from api

        $all_res_cat_count = count($all_res_cat); //chek how many element have
        $all_in_store_res_cat_count = count($all_in_store_res_cat); //chek how many element have
        $all_online_delivery_res_cat_count = count($all_online_delivery_res_cat); //chek how many element have


        //If have any all retricted than add this 
        if($all_res_cat_count > 0){
            for($i = 0; $i < $all_res_cat_count; $i++){

                //If cat not exit that it will insert our database
                if(! is_data_exit_in_database('category_restrictions', $_province, $all_res_cat[$i])){

                    $wpdb->insert($table_name, [
                        'province' => $_province,
                        'category_restrictions' => $all_res_cat[$i],
                        'in_store_restrictions' => '',
                        'online_delivery_restrictions' => ''
                    ]);

                }
            }
        }

        //Check have any in store res category 
        if($all_in_store_res_cat_count > 0){
            for($i = 0; $i < $all_in_store_res_cat_count; $i++){

                //If cat not exit that it will insert our database
                if(! is_data_exit_in_database('in_store_restrictions', $_province, $all_in_store_res_cat[$i])){

                    $wpdb->insert($table_name, [
                        'province' => $_province,
                        'category_restrictions' => '',
                        'in_store_restrictions' => $all_in_store_res_cat[$i],
                        'online_delivery_restrictions' => ''
                    ]);

                }
            }
        }

        //Check have any online delivery res category 
        if($all_online_delivery_res_cat_count > 0){
            for($i = 0; $i < $all_online_delivery_res_cat_count; $i++){

                //If cat not exit that it will insert our database
                if(! is_data_exit_in_database('online_delivery_restrictions', $_province, $all_online_delivery_res_cat[$i])){

                    $wpdb->insert($table_name, [
                        'province' => $_province,
                        'category_restrictions' => '',
                        'in_store_restrictions' => '',
                        'online_delivery_restrictions' => $all_online_delivery_res_cat[$i]
                    ]);

                }
            }
        }

    }
}


//this is for test function 
function mbd_restrictions_setting(){
    global $wpdb; 
    $table_name = $wpdb->prefix.'mb_restrictions';


    ?>
        <div class="wrap">
            <h1 style="margin-bottom: 20px;">This is Setting Page</h1>
            <form method="POST">
                <button name="delete-all-data">Delete all data</button>
                <button name="add-all-data">Sync Restriction Segval</button>
                <button name="start-sync-res">Start Cron</button>
            </form>

        <?php 

            //Sync Restriction segval
            if(isset($_POST['add-all-data'])){

                $url = 'https://modern.cansoft.com/tables/EZMS_EZCATESEGPERMISSION.php'; 
                $arguments = array(
                    'method' => 'GET'
                );
    
                $response = wp_remote_get( $url, $arguments );
                if ( is_wp_error( $response ) ) {
                    $error_message = $response->get_error_message();
                    echo "Something went wrong: {$error_message}";
                } else {
                    $all_restricted_categories = json_decode(wp_remote_retrieve_body($response));
    
                    foreach($all_restricted_categories as $_single_res_cat_obj){
    

                        add_all_restriction_cat($_single_res_cat_obj, 'AB');
                        add_all_restriction_cat($_single_res_cat_obj, 'BC');
                        add_all_restriction_cat($_single_res_cat_obj, 'MB');
                        add_all_restriction_cat($_single_res_cat_obj, 'NB');
                        add_all_restriction_cat($_single_res_cat_obj, 'NL');
                        add_all_restriction_cat($_single_res_cat_obj, 'NS');
                        add_all_restriction_cat($_single_res_cat_obj, 'NT');
                        add_all_restriction_cat($_single_res_cat_obj, 'NU');
                        add_all_restriction_cat($_single_res_cat_obj, 'QC');
                        add_all_restriction_cat($_single_res_cat_obj, 'PE');
                        add_all_restriction_cat($_single_res_cat_obj, 'SK');
                        add_all_restriction_cat($_single_res_cat_obj, 'ON');
                        add_all_restriction_cat($_single_res_cat_obj, 'YT');
    
                    }
                }
                
            }

            //for delete data
            if(isset($_POST['delete-all-data'])){
                $query = "TRUNCATE TABLE {$table_name}";
                $wpdb->query($query);
            }

            //Start cron job
            if(isset($_POST['start-sync-res'])){
                if (!wp_next_scheduled('mb_product_cat_res')) {
                    wp_schedule_event(time(), 'every_twelve_hours', 'mb_product_cat_res');
                }
            }
            

        ?>
        </div>
    <?php
}

//Callback hook for cron job
function mb_cat_res_cron_job(){

    global $wpdb; 
    $table_name = $wpdb->prefix.'mb_restrictions';

    $url = 'https://modern.cansoft.com/tables/EZMS_EZCATESEGPERMISSION.php'; 
    $arguments = array(
        'method' => 'GET'
    );

    $response = wp_remote_get( $url, $arguments );
    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        echo "Something went wrong: {$error_message}";
    } else {
        $all_restricted_categories = json_decode(wp_remote_retrieve_body($response));

        foreach($all_restricted_categories as $_single_res_cat_obj){

            add_all_restriction_cat($_single_res_cat_obj, 'AB');
            add_all_restriction_cat($_single_res_cat_obj, 'BC');
            add_all_restriction_cat($_single_res_cat_obj, 'MB');
            add_all_restriction_cat($_single_res_cat_obj, 'NB');
            add_all_restriction_cat($_single_res_cat_obj, 'NL');
            add_all_restriction_cat($_single_res_cat_obj, 'NS');
            add_all_restriction_cat($_single_res_cat_obj, 'NT');
            add_all_restriction_cat($_single_res_cat_obj, 'NU');
            add_all_restriction_cat($_single_res_cat_obj, 'QC');
            add_all_restriction_cat($_single_res_cat_obj, 'PE');
            add_all_restriction_cat($_single_res_cat_obj, 'SK');
            add_all_restriction_cat($_single_res_cat_obj, 'ON');
            add_all_restriction_cat($_single_res_cat_obj, 'YT');

        }
    }
}
add_action('mb_product_cat_res', 'mb_cat_res_cron_job');

function is_selected($db_province, $current_province){
    if($current_province === $db_province){
        echo "selected";
    }
}

/**
 * Callback function for Managae Restriciton
 */
function mbd_manage_restrictions(){
    global $wpdb; 
    $table_name = $wpdb->prefix.'mb_restrictions';
    $results = $wpdb->get_results(" select * from {$table_name}");
    
    include_once( plugin_dir_path( __FILE__ ) . '/part/mbd-manage-table.php');
}


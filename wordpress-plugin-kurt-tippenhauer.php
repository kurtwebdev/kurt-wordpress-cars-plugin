<?php
/**
 * Plugin Name: WordPress Cars Plugin - Kurt Tippenhauer
 * Description: A plugin I created using Object Oriented Programming to enter cars in a database. It interacts with the WordPress API and the WordPress database. 
 * Version: 0.0.1
 * Author: Kurt Tippenhauer
 * 
 */

// include the Car Class
include( plugin_dir_path( __FILE__ ) . 'class.car-class.php' );

// include the DbTableClass
include( plugin_dir_path( __FILE__ ) . 'class.db-table-class.php' );

// include javascript for api
function wordpress_cars_plugin_include_javascript() 
{
    wp_register_script( 'wordpress_plugin_index_js', plugin_dir_url( __FILE__ ) . 'js/wordpress-plugin-index.js', NULL, '', true ); 
    wp_enqueue_script( 'wordpress_plugin_index_js' );

    wp_localize_script( 'wordpress_plugin_index_js', 'signedinuser', array(
        'nonce' => wp_create_nonce( 'wp_rest' ),
        'siteURL' => get_site_url()
    ) );
}
add_action( 'admin_enqueue_scripts', 'wordpress_cars_plugin_include_javascript' );

// FUNCTION TO DROP TABLE WHEN PLUGIN IS UNINSTALLED
function wordpress_cars_plugin_drop_table_db( ) 
{
    // access WordPress database object
    global $wpdb;

    // define the database table name
    $table_name = $wpdb->prefix . 'wordpress_cars';

    // write query to drop table
    $query = "DROP TABLE IF EXISTS $table_name";

    // run query
    $wpdb->query( $query );
}
register_uninstall_hook( __FILE__, 'wordpress_cars_plugin_drop_table_db' );

// FUNCTION TO CREATE TABLE IN DATABASE
function wordpress_cars_plugin_create_db_table( )
{
    // load upgrade.php to use dbDelta() function
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    // instantiate WordPress cars table object
    $table_wordpress_cars = new DbTableClass( 'wordpress_cars' );

    // create table in database
    dbDelta( $table_wordpress_cars->get_create_table_query( ) );
}
add_action( 'plugins_loaded', 'wordpress_cars_plugin_create_db_table' );

// FUNCTION VALIDATE ARRAY FOR NULL
function wordpress_cars_plugin_validate_array_for_null( $arr ) 
{
    // for each item in the associative array...
    foreach( $arr as $key => $value )
    {
        // ...if the value of an item is null... 
        if ( is_null( $value ) )
        {
            // ...remove 'car_' from string...
            $format = str_replace( 'car_', '', $key );

            // ...replace '_' with spaces and uppercase each word...
            $label = ucwords( str_replace( '_', ' ', $format ) );
            
            // ...throw exception with formatted messages
            throw new Exception( "$label is required." );
        }
    }
}

// FUNCTION FOR ALERT 
function wordpress_cars_plugin_display_alert_notice( $msg, $type )
{
    ob_start();
    ?>
        <div id="setting-error-settings_updated" class="notice notice-<?php echo $type ?> settings-error is-dismissible"><?php echo $msg; ?></div>
    <?php
    $alert = ob_get_clean();
    return $alert;
}

//  FUNCTION TO ADD PLUGIN OPTION TO DASHBOARD MENU
function wordpress_cars_plugin_add_to_admin_menu( ) 
{
    add_menu_page( 'WordPress Cars Plugin - Kurt Tippenahuer', 'WordPress Cars Plugin - Kurt Tippenhauer', 'delete_posts', 'wordpress-plugin-admin-menu', 'wordpress_cars_plugin_start', 'dashicons-smiley', 200 );
}
add_action( 'admin_menu', 'wordpress_cars_plugin_add_to_admin_menu' );

// FUNCTION THAT WRITES $_POST DATA TO DB
function wordpress_cars_plugin_write_to_db( $req )
{
    try 
    {
        // access WordPress database object
        global $wpdb;

        // define the database table name
        $table_name = '';
        $table_name = apply_filters( 'get_table_name', $table_name );

        //  instantiate car object
        $carObject = new CarClass( $req );
        
        // create index for car_id
        // get all rows from cars table from database in descending order but limit it to one row (the last row)
        $row_wordpress_cars = $wpdb->get_row( "SELECT * FROM $table_name ORDER BY car_id DESC LIMIT 1" );
   
        // if the object is not null...
        if ( $row_wordpress_cars != null )
        {
            // ...take the car_id and add 1... 
            $unique_id = $row_wordpress_cars->car_id + 1;
        }
        // ...otherwise...
        else
        {
            // ...define the car_id as 1
            $unique_id = 1;
        }
   
        // create array to hold data to write to database
        $array_hold_my_data = array(
            'car_id' => $unique_id,
            'car_make' => '',
            'car_model' => '',
            'car_body_type' => '',
            'car_engine_size' => '',
            'car_transmission' => '',
            'car_drive_type' => ''
        );
            
        // combine the unique id with the data 
        $car_item = shortcode_atts( $array_hold_my_data, (array) $carObject );

        wordpress_cars_plugin_validate_array_for_null( $car_item );

        // write data into database
        $wpdb->insert( $table_name, $car_item );
        
        // unset variables 
        unset( $carObject, $car_item, $array_hold_my_data, $unique_id, $table_name );
        
        // notify that car has been written to db
        ?>
            <div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">Car has been written in the database!<br>(ﾉ◕ヮ◕)ﾉ*:･ﾟ✧</div>
        <?php
    }
    catch( Exception $e )
    {
        // notify that error messagea
        echo wordpress_cars_plugin_display_alert_notice( $e->getMessage(), 'error' );
    }


}

// CALLBACK FUNCTION TO GET ALL CARS FROM DB TABLE
function wordpress_cars_plugin_get_all_cars( $data )
{
    // access WordPress database object
    global $wpdb;

    // define the database table name
    $table_name = '';
    $table_name = apply_filters( 'get_table_name', $table_name );

    // MySQl query to get all from wordpress_cars table
    $query = "SELECT * FROM $table_name";

    // get results from query
    $cars = $wpdb->get_results( $query );

    // return the results
    return $cars;
}

// FUNCTION TO ADD CUSTOM ENDPOINT TO WORDPRESS REST API TO GET ALL CARS
function wordpress_cars_plugin_add_get_all_cars_endpoint( ) 
{
    register_rest_route( 'wordpresscars/v1', '/allcars', array( 
        'methods' => 'GET',
        'callback' => 'wordpress_cars_plugin_get_all_cars',
        'permission_callback' => function( ) { 
            return current_user_can( 'edit_others_posts' );
         }
     ) );
}
add_action( 'rest_api_init', 'wordpress_cars_plugin_add_get_all_cars_endpoint');

// callback to write into db
function wordpress_cars_plugin_write_car( WP_REST_Request $request )
{   
    $parameters = $request->get_params();
    
    wordpress_cars_plugin_write_to_db( $parameters );
    
    return new WP_REST_Response( array( 'message' => 'Car added to DB : )' ), 201 );
}

// FUNCTION TO ADD CUSTOM ENDPOINT TO WORDPRESS REST API TO WRITE NEW CAR TO DATABASE
function wordpress_cars_plugin_add_write_car_endpoint()
{
    register_rest_route( 'wordpresscars/v1', '/writecar', array( 
        'methods' => WP_REST_SERVER::EDITABLE,
        'callback' => 'wordpress_cars_plugin_write_car',
        'args' => array(),
        'permission_callback' => function( ) { 
            return current_user_can( 'edit_others_posts' );
         }
     ) );
}
add_action( 'rest_api_init', 'wordpress_cars_plugin_add_write_car_endpoint' );

// FUNCTION TO GENERATE FORM
function wordpress_cars_plugin_generate_form( $type )
{
    if ( $type == 'API' || $type == 'classic' )
    {
        ob_start();
        ?>
        <form method="post" actions="?page=add_data" id="wordpress-cars-plugin-form-<?php echo $type; ?>">
            <table class="form-table" role="presentation">
                <tbody>
                    <!-- VEHICLE MAKE INPUT -->
                    <tr>
                        <th scope="row">
                            <label for="wordpress-cars-plugin-make-input-<?php echo $type; ?>" id="wordpress-cars-plugin-make-label-<?php echo $type; ?>">Make</label>
                        </th>
                        <td>
                            <input name="wordpress-cars-plugin-make-input-<?php echo $type; ?>" type="text" id="wordpress-cars-plugin-make-input-<?php echo $type; ?>" class="regular-text" placeholder="Please enter the vehicle make" pattern="[a-zA-Z\d\s\-]+" required> 
                            <p class="description">Only letters, numbers and hyphens. No special characters. No funny business...</p>
                        </td>
                    </tr>
                    <!-- VEHICLE MODEL INPUT -->
                    <tr>
                        <th scope="row">
                            <label for="wordpress-cars-plugin-model-input-<?php echo $type; ?>" id="wordpress-cars-plugin-model-label-<?php echo $type; ?>">Model</label>
                        </th>
                        <td>
                            <input name="wordpress-cars-plugin-model-input-<?php echo $type; ?>" type="text" id="wordpress-cars-plugin-model-input-<?php echo $type; ?>" class="regular-text" placeholder="Please enter the vehicle model" pattern="[a-zA-Z\d\s\-]+" required> 
                            <p class="description">Only letters, numbers and hyphens. No special characters. No funny business...</p>
                        </td>
                    </tr>
                    <!-- VEHICLE BODY TYPE INPUT -->
                    <tr>
                        <th scope="row">
                            <label for="wordpress-cars-plugin-body-type-select<?php echo $type; ?>" id="wordpress-cars-plugin-body-type-label-<?php echo $type; ?>">Body Type</label>
                        </th>
                        <td>
                            <select name="wordpress-cars-plugin-body-type-select-<?php echo $type; ?>" id="wordpress-cars-plugin-body-type-select-<?php echo $type; ?>" required>
                                <option disabled selected="selected" value>-- Please Select An Option --</option>
                                <option>Coupe</option>
                                <option>Drop Top</option>
                                <option>Sedan</option>
                                <option>Swagin Wagon</option>
                                <option>SUV</option>
                                <option>Nice Truck - can you help me move some stuff</option>
                                <option>Minivan</option>
                            </select> 
                        </td>
                    </tr>
                    <!-- VEHICLE ENGINE SIZE INPUT -->
                    <tr>
                        <th scope="row">
                            <label for="wordpress-cars-plugin-engine-size-select-<?php echo $type; ?>" id="wordpress-cars-plugin-engine-size-label-<?php echo $type; ?>">Engine Size</label>
                        </th>
                        <td>
                            <select name="wordpress-cars-plugin-engine-size-select-<?php echo $type; ?>" id="wordpress-cars-plugin-engine-size-select-<?php echo $type; ?>" required>
                                <option disabled selected="selected" value>-- Please Select An Option --</option>
                                <option>Electric Motor</option>
                                <option>12 Cylinders</option>
                                <option>10 Cylinders</option>
                                <option>8 Cylinders</option>
                                <option>6 Cylinders</option>
                                <option>5 Cylinders</option>
                                <option>4 Cylinders</option>
                                <option>3 Cylinders</option>
                            </select> 
                        </td>
                    </tr>
                    <!-- VEHICLE TRANSMISSION INPUT -->
                    <tr>
                        <th scope="row">
                            <label for="wordpress-cars-plugin-transmission-select-<?php echo $type; ?>" id="wordpress-cars-plugin-transmission-label-<?php echo $type; ?>">Transmission</label>
                        </th>
                        <td>
                            <select name="wordpress-cars-plugin-transmission-select-<?php echo $type; ?>" id="wordpress-cars-plugin-transmission-select-<?php echo $type; ?>" required>
                                <option disabled selected="selected" value>-- Please Select An Option --</option>
                                <option>Manual</option>
                                <option>Flappy Paddles</option>
                                <option>Automatic</option>
                                <option>CVT</option>
                            </select> 
                        </td>
                    </tr>
                    <!-- VEHICLE DRIVE TYPE INPUT -->
                    <tr>
                        <th scope="row">
                            <label for="wordpress-cars-plugin-drive-type-select-<?php echo $type; ?>" id="wordpress-cars-plugin-drive-type-label-<?php echo $type; ?>">Drive Type</label>
                        </th>
                        <td>
                            <select name="wordpress-cars-plugin-drive-type-select-<?php echo $type; ?>" id="wordpress-cars-plugin-drive-type-select-<?php echo $type; ?>" required>
                                <option disabled selected="selected" value>-- Please Select An Option --</option>
                                <option>RWD</option>
                                <option>AWD</option>
                                <option>FWD</option>
                            </select> 
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- FORM SUBMIT BUTTON -->
            <input type="submit" name="submit_form_update_<?php echo $type; ?>" class="button button-primary" id="wordpress-cars-plugin-form-button-<?php echo $type; ?>" value="Let's Go!">
        </form>
        <?php

        $html_markup = ob_get_clean();

        return $html_markup;
    }
}

// FUNCTION TO GENERATE TABLE TO VIEW DATA IN CARS TABLE
function wordpress_cars_plugin_generate_table( $type )
{
    if ( $type == 'classic' || $type == 'API' )
    {
        ob_start();
        ?>
        <h2>Cars in the database:</h2>
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <input type="button" id="wordpress-cars-plugin-view-data-button-<?php echo $type; ?>" class="button button-primary" value="Get Cars">
                </tr>
            </tbody>
        </table>
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th scope="col" id="make" class="manage-column">Make</th>
                    <th scope="col" id="model" class="manage-column">Model</th>
                    <th scope="col" id="body-type" class="manage-column">Body Type</th>
                    <th scope="col" id="engine-size" class="manage-column">Engine Size</th>
                    <th scope="col" id="transmission" class="manage-column">Transmission</th>
                    <th scope="col" id="drive-type" class="manage-column">Drive Type</th>
                </tr>
            </thead>
            <tbody id="wordpress-cars-plugin-car-list-<?php echo $type; ?>">
                <tr id="wordpress-car-#-<?php echo $type; ?>">
                    <td>Make</td>
                    <td>Model</td>
                    <td>Body Type</td>
                    <td>Engine Size</td>
                    <td>Transmission</td>
                    <td>Drive Type</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th scope="col" id="make" class="manage-column">Make</th>
                    <th scope="col" id="model" class="manage-column">Model</th>
                    <th scope="col" id="body-type" class="manage-column">Body Type</th>
                    <th scope="col" id="engine-size" class="manage-column">Engine Size</th>
                    <th scope="col" id="transmission" class="manage-column">Transmission</th>
                    <th scope="col" id="drive-type" class="manage-column">Drive Type</th>
                </tr>

            </tfoot>
        </table>
        <?php

        $html_markup = ob_get_clean();

        return $html_markup;
    }
}

// FUNCTION TO DISPLAY PLUGIN MARKUP
function wordpress_cars_plugin_start( ) 
{
    // if the form submit button exists in the $_POST array (if the form is in the POST request)... 
    if ( array_key_exists( 'submit_form_update_classic', $_POST ) ) 
    {
        // ...write $_POST data to db 
       wordpress_cars_plugin_write_to_db( $_POST );
    } 
    ?>
    <div class="wrap">
        <h1 id="wordpress-cars-plugin-page-title">WordPress Cars Plugin - Kurt Tippenhauer</h1>
        <h2>Entering a car in the database using the WordPress REST API:</h2>
        <!-- FORM TO CAPTURE USER INPUT -->
        <?php echo wordpress_cars_plugin_generate_form( 'API' ); ?>
        <!-- TABLE TO VIEW DATA FROM DATABASE -->
        <?php echo wordpress_cars_plugin_generate_table( 'API' ); ?>
        <br><hr>
        <h2>Entering a car in the database the old fashioned way:</h2>
        <!-- FORM TO CAPTURE USER INPUT -->
        <?php echo wordpress_cars_plugin_generate_form( 'classic' ); ?>
        <!-- TABLE TO VIEW DATA FROM DATABASE -->
        <?php echo wordpress_cars_plugin_generate_table( 'classic' ); ?>

    </div>
<?php
}
?>
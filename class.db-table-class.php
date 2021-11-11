<?php
    // CLASS FOR DB TABLE
    class DbTableClass 
    {
        // PORPERTIES
        public $table_name;
        public $create_table_query;
        public $drop_table_query;

        // CONSTRUCTOR
        // take table nameand, add prefix, and generate query
        function __construct( $tablename )
        {
            // call database global object
            global $wpdb;
        
            // get collation charset
            $charset_collate = $wpdb->get_charset_collate( ); 
        
            // set the table name
            $this->table_name = $wpdb->prefix . $tablename;
        
            // write out create table query
            $this->create_table_query = "CREATE TABLE $this->table_name (
                car_id INTEGER NOT NULL AUTO_INCREMENT,
                car_make TEXT NOT NULL,
                car_model TEXT NOT NULL,
                car_body_type TEXT NOT NULL,
                car_engine_size TEXT NOT NULL,
                car_transmission TEXT NOT NULL,
                car_drive_type TEXT NOT NULL,
                PRIMARY KEY (car_id)
                ) $charset_collate;";
            
            // write out drop table query
            $this->drop_table_query = "DROP TABLE IF EXISTS $this->table_name;";

            // add filter to get table name
            add_filter( 'get_table_name', array($this, 'get_table_name') );

        }

        //METHOD TO GET TABLE NAME
        function get_table_name( ) 
        {
            return $this->table_name;
        }

        // METHOD TO GET CREATE TABLE QUERY
       function get_create_table_query( ) 
        {
            return $this->create_table_query;
        }

        // METHOD TO GET DROP TABLE QUERY
        function get_drop_table_query( )
        {
            return $this->drop_table_query;
        }

    }
?>
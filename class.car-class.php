<?php
//  CLASS FOR CAR 
class CarClass 
{
    // PROPERTIES 
    public $car_make; 
    public $car_model;
    public $car_body_type;
    public $car_engine_size;
    public $car_transmission;
    public $car_drive_type;

    //  CONSTRUCTOR
    function __construct( $associativeArray )
    {
        // define the regular expression
        $regex = "/[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\=_+\"¬\`\]!.]/";

        // for each element in the $_POST array...
        foreach( $associativeArray as $key => $value )
        {   
            // ...trim whitespaces from string...
            $trimed_value = trim( $value );
            
            // ...if the key contains 'make'...
            if ( strpos( $key, 'make' ) !== false ) 
            {
                // ...if the value matches the regular expression OR if the value is null...
                if ( preg_match_all( $regex, $trimed_value ) !== 0 || $trimed_value == '' )
                {
                    // ...throw an exception...
                    throw new Exception( "Make: Please match the requested format. Can't be left blank. Only letters, numbers or hyphens. No special characters. No funny business... ಠ_ಠ" );
                }
                // ...otherwise...
                else
                {
                    // ...store the value in the carMake property...
                    $this->car_make = $trimed_value;
                    
                }
            }
            // ...otherwise if the key contains 'model'...
            elseif ( strpos( $key, 'model' ) !== false )
            {
                // ...if the value matches the regular expression OR if the value is null...
                if ( preg_match_all( $regex, $trimed_value ) !== 0 || $trimed_value == '' )
                {
                    // ...throw an exception...
                    throw new Exception( "Model: Please match the requested format. Can't be left blank. Only letters, numbers or hyphens. No special characters. No funny business... ಠ_ಠ" );
                }
                // ...otherwise...
                else
                {
                    // ...store the value in the carModel property...
                    $this->car_model = $trimed_value;
                }
            }
            // ...otherwise if the key contains 'body-type'...
            elseif ( strpos( $key, 'body-type' ) !== false || strpos( $key, 'body_type' ) !== false )
            {
                // ...if the value matches the regular expression OR if the value is null...
                if( preg_match_all( $regex, $trimed_value ) !== 0 || $trimed_value == '' )
                {
                    // ...throw an exception...
                    throw New Exception( "Body Type: Please match the requested format. Can't be left blank. Only letters, numbers or hyphens. No special characters. No funny business... ಠ_ಠ" );
                }
                // ...otherwise...
                else
                {
                    // ...store the value in the carBodyType property...
                    $this->car_body_type = $trimed_value;

                }
            }
            // ...otherwise if the key contains 'engine-size'...
            elseif ( strpos( $key, 'engine-size' ) !== false || strpos( $key, 'engine_size' ) !== false )
            {
                // ...if the value matches the regular expression OR if the value is null...
                if( preg_match_all( $regex, $trimed_value ) !== 0 || $trimed_value == '' )
                {
                    // ...throw an exception...
                    throw New Exception( "Engine Size: Please match the requested format. Can't be left blank. Only letters, numbers or hyphens. No special characters. No funny business... ಠ_ಠ" );
                }
                // ...otherwise...
                else
                {
                    // ...store the value in the carEngineSize property...
                    $this->car_engine_size = $trimed_value;

                }
            }
            // ...otherwise if the key contains 'transmission'...
            elseif ( strpos( $key, 'transmission' ) !== false ) 
            {
                // ...if the value matches the regular expression OR if the value is null...
                if( preg_match_all( $regex, $trimed_value ) !== 0 || $trimed_value == '' )
                {
                    // ...throw an exception...
                    throw New Exception( "Transmission: Please match the requested format. Can't be left blank. Only letters, numbers or hyphens. No special characters. No funny business... ಠ_ಠ" );
                }
                // ...otherwise...
                else
                {
                    // ...store the value in the carTransmission property...
                    $this->car_transmission = $trimed_value;

                }
            }
            // ...otherwise if the key contains 'drive-type'...
            elseif ( strpos( $key, 'drive-type' ) !== false || strpos( $key, 'drive_type' ) !== false )
            {
                // ...if the value matches the regular expression OR if the value is null...
                if( preg_match_all( $regex, $trimed_value ) !== 0 || $trimed_value == '' )
                {
                    // ...throw an exception...
                    throw New Exception( "Drive Type: Please match the requested format. Can't be left blank. Only letters, numbers or hyphens. No special characters. No funny business... ಠ_ಠ" );
                }
                // ...otherwise...
                else
                {
                    // ...store the value in the carDriveType property
                    $this->car_drive_type = $trimed_value;

                }
            }
        }

    }
}
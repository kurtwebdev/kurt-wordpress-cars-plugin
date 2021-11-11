// object that contains all dom elements
const dom_elements = 
{
    // PAGE TITLE
    page_title: document.querySelector( '#wordpress-cars-plugin-page-title' ),

    // FORM AND SUBMIT BUTTON FOR CLASSIC FORM
    classic_form_car: document.querySelector( '#wordpress-cars-plugin-form-classic' ),
    classic_form_button_car: document.querySelector( '#wordpress-cars-plugin-form-button-classic' ),

    // FORM LABELS AND INPUTS FOR CLASSIC FORM
    classic_label_car_make: document.querySelector( '#wordpress-cars-plugin-make-label-classic' ),
    classic_input_car_make: document.querySelector( '#wordpress-cars-plugin-make-input-classic' ),
    classic_label_car_model: document.querySelector( '#wordpress-cars-plugin-model-label-classic' ),
    classic_input_car_model: document.querySelector( '#wordpress-cars-plugin-model-input-classic' ),
    classic_label_car_body_type: document.querySelector( '#wordpress-cars-plugin-body-type-label-classic' ),
    classic_select_car_body_type: document.querySelector( '#wordpress-cars-plugin-body-type-select-classic' ),
    classic_label_car_engine_size: document.querySelector( '#wordpress-cars-plugin-engine-size-label-classic' ),
    classic_select_car_engine_size: document.querySelector( '#wordpress-cars-plugin-engine-size-select-classic' ),
    classic_label_car_transmission: document.querySelector( '#wordpress-cars-plugin-transmission-label-classic' ),
    classic_select_car_transmission: document.querySelector( '#wordpress-cars-plugin-transmission-select-classic' ),
    classic_label_car_drive_type: document.querySelector( '#wordpress-cars-plugin-drive-type-label-classic' ),
    classic_select_car_drive_type: document.querySelector( '#wordpress-cars-plugin-drive-type-select-classic' ),

    // TABLE AND BUTTON TO VIEW DATA FROM DATABASE CLASSIC
    classic_table_car: document.querySelector( '#wordpress-cars-plugin-car-list-classic' ),
    classic_button_car_get_data: document.querySelector( '#wordpress-cars-plugin-view-data-button-classic' ),

    /******------******/
    
    // FORM AND SUBMIT BUTTON FOR API FORM
    API_form_car: document.querySelector( '#wordpress-cars-plugin-form-API' ),
    API_form_button_car: document.querySelector( '#wordpress-cars-plugin-form-button-API' ),

    // FORM LABELS AND INPUTS FOR API FORM
    API_label_car_make: document.querySelector( '#wordpress-cars-plugin-make-label-API' ),
    API_input_car_make: document.querySelector( '#wordpress-cars-plugin-make-input-API' ),
    API_label_car_model: document.querySelector( '#wordpress-cars-plugin-model-label-API' ),
    API_input_car_model: document.querySelector( '#wordpress-cars-plugin-model-input-API' ),
    API_label_car_body_type: document.querySelector( '#wordpress-cars-plugin-body-type-label-API' ),
    API_select_car_body_type: document.querySelector( '#wordpress-cars-plugin-body-type-select-API' ),
    API_label_car_engine_size: document.querySelector( '#wordpress-cars-plugin-engine-size-label-API' ),
    API_select_car_engine_size: document.querySelector( '#wordpress-cars-plugin-engine-size-select-API' ),
    API_label_car_transmission: document.querySelector( '#wordpress-cars-plugin-transmission-label-API' ),
    API_select_car_transmission: document.querySelector( '#wordpress-cars-plugin-transmission-select-API' ),
    API_label_car_drive_type: document.querySelector( '#wordpress-cars-plugin-drive-type-label-API' ),
    API_select_car_drive_type: document.querySelector( '#wordpress-cars-plugin-drive-type-select-API' ),

    // TABLE AND SELECT TO VIEW DATA FROM DATABASE API
    API_table_car: document.querySelector( '#wordpress-cars-plugin-car-list-API' ),
    API_button_car_get_data: document.querySelector( '#wordpress-cars-plugin-view-data-button-API' )


};

/**
 * FUNCTIONS
 */

// FUNCTION TO VALIDATE SINGLE FORM INPUT
const validate_form_input = ( input_value, label_value ) => 
{
    // trim whitespaces from string
    const trimed_input_value = input_value.trim( );

    // define an instanace of RegExp to test for special characters against a regular expression 
    const input_regex = new RegExp( /[\[^\'£$%^&*()}{@:\'#~?><>,;@\|\\=_+\"¬\`\]!.]/, 'g' );

    // test the input value against the regular expression
    const regex_result = input_regex.test( trimed_input_value );

    // if input value matches the regular expression...
    if( regex_result == true )
    {   
        // ...throw an error...
        throw `${label_value}:\nPlease match the requested format.\n- Can't be left blank.\n- Only letters, numbers and hyphens. \n- No special characters. \n- No funny business... ಠ_ಠ`;
    }
    // ...otherwise if the input value is null...
    else if ( trimed_input_value == '' )
    {   
        // ...throw an error...
        throw `${label_value} is required.\n(  ^ ω ^)`;
    }
    // ...otherwise...
    else 
    {
        // ...return the input value
        return trimed_input_value;
    }
    
};

// FUNCTION TO POPULATE THE VALIDATED INPUT OBJECT
const populate_validated_inputs_object = ( obj, value, label ) => 
{
    // for each property in the object...
    for ( const proprt in obj )
    {   
        // ...if the property string matches the label string formatted to replace the spaces with underscores...
        if ( proprt.includes( label.toLowerCase().replace( ' ', '_' ) ) )
        {
            // ...store the value in the property
            obj[proprt] = value;
        }
    }
};

// FUNCTION TO VALIDATE API FORM INPUTS
const validate_form_inputs = ( form_inputs ) =>
{
    // declare an object to store validated inputs
    const validated_inputs = 
    {
        // 'car_id': '',
        'car_make': '',
        'car_model': '',
        'car_body_type': '',
        'car_engine_size': '',
        'car_transmission': '',
        'car_drive_type': ''
    };
    // try...
    try
    {
        // ...for each input in the form inputs object...
        for ( const input in form_inputs ) 
        {
            // ...if the property value is not an integer ( aka skip over the car_id property )...
            if ( !Number.isInteger(form_inputs[input])  )
            {
                // ...for each input object in the form_inputs object...
                for ( const property in form_inputs[input] )
                {   
                    // ...populate the validated inputs object with the validated inputs..
                    populate_validated_inputs_object( validated_inputs, validate_form_input( form_inputs[input].value, form_inputs[input].label, form_inputs[input].evnt ), form_inputs[input].label );
                    
                    // ...break out of loop...
                    break;
                }
            }
        }
        // ...return the validated inputs...
        return validated_inputs;
    }
    // ...if an error is thrown...
    catch ( e )
    {   
        // ...display the error message 
        alert( e );
    }
};

// FUNCTION TO GENERATE THE UNIQUE ID ( this is handled on the PHP side as well )
const generate_unique_id = ( data  ) => 
{ 
    // if the data is not empty...
    if (  data.length !== 0 )
    {
        // ...return the car_id property incremented by 1
        return parseInt( data[Object.keys( data )[Object.keys( data ).length -1]].car_id, 10 ) + 1;
    }
};

// FUNCTION TO CLEAR API FORM INPUTS
const clear_API_form_inputs = ( ) =>
{
    dom_elements.API_input_car_make.value = '';
    dom_elements.API_input_car_model.value = '';
    dom_elements.API_select_car_body_type.selectedIndex = 0;
    dom_elements.API_select_car_engine_size.selectedIndex = 0;
    dom_elements.API_select_car_transmission.selectedIndex = 0;
    dom_elements.API_select_car_drive_type.selectedIndex = 0;
};

// FUNCTION TO FILL CARS TABLE
const fill_cars_table = ( data, table, table_type ) =>
{
    // declare variable to store HTML markup
    let html;

    // if the data array is empty...
    if ( data.length == 0 )
    {
        // ... set HTML markup to default...
        html = `
        <tr id="wordpress-car-#-API">
            <td>Make</td>
            <td>Model</td>
            <td>Body Type</td>
            <td>Engine Size</td>
            <td>Transmission</td>
            <td>Drive Type</td>
        </tr>`;
    }
    // ...otherwise...
    else 
    {
        // ...set HTML markup with the cars from the database...
        html = `${ data.map( ( car, index ) => 
            {
                return `           
                    <tr id="wordpress-car-${ index }-${ table_type }">
                        <td>${ car.car_make }</td>
                        <td>${ car.car_model }</td>
                        <td>${ car.car_body_type }</td>
                        <td>${ car.car_engine_size }</td>
                        <td>${ car.car_transmission }</td>
                        <td>${ car.car_drive_type }</td>
                    </tr>`;
            } ).join( "" ) }`;
    }

    // ...clear the cars table...
    table.innerHTML = '';

    // ...insert HTML markup in the the table
    table.insertAdjacentHTML( 'afterbegin', html );
};

// FUNCTION TO GET ALL CARS FROM DB VIA GET REQUEST
const get_cars_request = ( callback ) => 
{
    // instantiate the request
    const request_get_cars = new XMLHttpRequest();

    // set up the GET request route
    request_get_cars.open( 'GET', signedinuser.siteURL + '/wp-json/wordpressCars/v1/allcars' );

    // set the request headers for authentication
    request_get_cars.setRequestHeader( 'X-WP-Nonce', signedinuser.nonce );

    // when the request has completed...
    request_get_cars.onload = ( ) => 
    {
        // ...if the request status is anywhere between 200 or 400...
        if ( request_get_cars.status >= 200 && request_get_cars.status < 400 )
        {
            // ...pass the parsed JSON response to the callback function...
            callback( JSON.parse( request_get_cars.responseText ) );
        }
        // ...otherwise...
        else
        {   
            // ...the request has failed
            alert( 'Connected to server, but something isn\'t quite right... \n(•ิ_•ิ)?' );
        }
    };

    // when the request has failed to complete...
    request_get_cars.onerror = ( ) =>
    {
        // ...display an error message
        alert( 'Connection error...\n(っ˘̩╭╮˘̩)っ' );
    };

    // send the request
    request_get_cars.send();
};

// FUNCTION TO SEND CAR TO BE WRITTEN TO DB VIA POST REQUEST
const post_cars_requests = ( data ) => 
{
    // instantiate the request
    const request_post_cars = new XMLHttpRequest();

    // set up the POST request route
    request_post_cars.open( 'POST', signedinuser.siteURL + '/wp-json/wordpresscars/v1/writecar' );

    // set the request headers for authentication
    request_post_cars.setRequestHeader( 'X-WP-Nonce', signedinuser.nonce );

    // set the request headers for Content-Type
    request_post_cars.setRequestHeader( 'Content-Type', 'application/json;charset=UTF-8' );

    // send the JSON data via the request
    request_post_cars.send( JSON.stringify( data ) );

    // when the readyState has changed...
    request_post_cars.onreadystatechange = (  ) =>
    {
        // ...if the readyState is done...
        if( request_post_cars.readyState == 4)
        {
            // ...set the request status to 201...
            if ( request_post_cars.status == 201 )
            {
                // ...clear the API form inputs...
                clear_API_form_inputs();

                // ...get the cars in the DB via GET request...
                get_cars_request( data => 
                { 
                    // ...if the API table for cars is in the DOM...
                    if ( dom_elements.API_table_car )
                    {
                        // ...fill the table with cars from DB...
                    fill_cars_table( data, dom_elements.API_table_car, 'API' );
                    }
                } );
                // ...alert that the request has gone through...
                alert( 'Car has been written in the database! \n(ﾉ◕ヮ◕)ﾉ*:･ﾟ✧' )
            }
            // ...otherwise...
            else 
            {
                // ...alert that the request failed
                alert( 'Car was not added to the databse...\n(っ˘̩╭╮˘̩)っ' );
            }
        }
    }
};

/**
 * EVENT HANDLERS
 */

// if the API get cars button is in the DOM...
if ( dom_elements.API_button_car_get_data )
{
    // ...on the click event of the button...
    dom_elements.API_button_car_get_data.addEventListener( 'click', ( e ) => 
    {
        // ...get the cars in db via get request...
       get_cars_request( data => 
        { 
            // ...if the API table for cars is in the DOM...
            if ( dom_elements.API_table_car )
            {
                // ...fill the API table with cars from DB
                fill_cars_table( data, dom_elements.API_table_car, 'API' );
            }
        } );
    } );
}

// if the API form for cars is in the DOM...
if ( dom_elements.API_form_car )
{
    // ...on the submit event...
    dom_elements.API_form_car.addEventListener( 'submit', ( e ) =>
    {
        // ...prevent the form from sending ( and the page from refreshing )...
        e.preventDefault();
        
        // ...store the form inputs in an object...
        const form_inputs = 
        {
            // 'car_id': 0,
            'car_make': 
                { 
                    'value': dom_elements.API_input_car_make.value, 
                    'label': dom_elements.API_label_car_make.innerHTML,
                    'evnt': e 
                },
            'car_model': 
                {
                    'value': dom_elements.API_input_car_model.value, 
                    'label': dom_elements.API_label_car_model.innerHTML,
                    'evnt': e 
                },
            'car_body_type':
                {
                    'value': dom_elements.API_select_car_body_type.value, 
                    'label': dom_elements.API_label_car_body_type.innerHTML,
                    'evnt': e 
                },
            'car_engine_size': 
                {
                    'value': dom_elements.API_select_car_engine_size.value, 
                    'label': dom_elements.API_label_car_engine_size.innerHTML,
                    'evnt': e 
                },
            'car_transmission': 
                {
                    'value': dom_elements.API_select_car_transmission.value, 
                    'label': dom_elements.API_label_car_transmission.innerHTML,
                    'evnt': e 
                },
            'car_drive_type': 
                { 
                    'value': dom_elements.API_select_car_drive_type.value, 
                    'label': dom_elements.API_label_car_drive_type.innerHTML,
                    'evnt': e 
                }
        }
        
        // ...validate the form inputs...
        const validated_form_inputs = validate_form_inputs( form_inputs );

        // ...get the cars in the DB via GET request...
        get_cars_request( data => 
        {
            // ... if the validated form inputs object is not empty...
            if ( typeof validated_form_inputs !== 'undefined' )
            {
                // ...generate and store the unique id...
                validated_form_inputs.car_id = generate_unique_id( data);
                
                // ...write car to db via post request
                post_cars_requests( validated_form_inputs );
            }
        } );
    } );
}

// if the classic form for cars is in the DOM...
if ( dom_elements.classic_form_car )
{
    // ...on the submit event of the form...
    dom_elements.classic_form_car.addEventListener( 'submit', ( e ) => 
    {
        // ...try...
        try
        {
            // ...validate each input of the form...
            validate_form_input( dom_elements.classic_input_car_make.value, dom_elements.classic_label_car_make.innerHTML, e );
            validate_form_input( dom_elements.classic_input_car_model.value, dom_elements.classic_label_car_model.innerHTML, e );
            validate_form_input( dom_elements.classic_select_car_body_type.value, dom_elements.classic_label_car_body_type.innerHTML, e );
            validate_form_input( dom_elements.classic_select_car_engine_size.value, dom_elements.classic_label_car_engine_size.innerHTML, e );
            validate_form_input( dom_elements.classic_select_car_transmission.value, dom_elements.classic_label_car_transmission.innerHTML, e );
            validate_form_input( dom_elements.classic_select_car_drive_type.value, dom_elements.classic_label_car_drive_type.innerHTML, e );            
        }
        // ...if an error is thrown...
        catch ( err )
        {   
            // ...prevent the form from sending...
            e.preventDefault();

            // ...display an error message
            alert( err ); 
        }

    } );
}

// if the classic get cars button is in the DOM...
if ( dom_elements.classic_button_car_get_data )
{
    // ...on the click event of the button...
    dom_elements.classic_button_car_get_data.addEventListener( 'click', ( e ) => 
    {
        // ...get the cars in db via get request...
       get_cars_request( data => 
        { 
            // ...if the classic table for cars is in the DOM...
            if ( dom_elements.classic_table_car )
            {
                // ...fill the classic table with cars from DB
                fill_cars_table( data, dom_elements.classic_table_car, 'classic' );
            }
        } );
    } );
}



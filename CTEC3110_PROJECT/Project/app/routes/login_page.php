<?php
/**
 * login_page.php
 *
 * This is the first page that a user see's when they run this application,
 * it allows a user to either sign up, or if they have an existing account
 * login with their credentials.
 *
 * The default page is the sign up screen, however if a user needs to simply
 * login, they can select the other tab labeled 'log in'.
 *
 * User: p2442537
 */
/** Uses Slim to make a http request message */
use \Psr\Http\Message\ServerRequestInterface as Request;
/** Uses Slim to make a http response message */
use \Psr\Http\Message\ResponseInterface as Response;
//$app->active_profile = $this->get('profile_model');
/** Applies the http request and response messages to the get function as parameters */
$app->get('/', function(Request $request, Response $response)
{
    /** Variables that will eventually undergo sanitation and validation */
    /** @var String $validated_username A users app username that they signed up with */
    $this->validated_username = null;
    /** @var String $validated_password A users app password that they signed up with */
    $this->validated_password = null;
    /** @var String $validated_fname A users real firstname that they signed up with */
    $this->validated_fname = null;
    /** @var String $validated_lname A users real surname that they signed up with */
    $this->validated_lname = null;

    $this->validated_status = null;
    //$this->profile_model_result = false;
    /** @var String $arr_tainted_params are the parsed values from the previous form, currently tainted and needing sanitation */
    $this->arr_tainted_params = $request->getParsedBody();
    /** Variables that are assigned as instances of their class, called from the dependencies.php file */
    /** @var Object $validator_obj is used to ensure Strings are both sanitised and validated throughout the app */
    $this->validator_obj = $this->get('sanitised_validator'); //should be changed to validator_obj for consistency, however doing this breaks the app
    /** @var Object $profile_obj used to model the user profile's information */
    $this->profile_obj = $this->get('profile_model');
    /** @var Object $xml_parser is used to parse messages from the M2M Soap server with and turn it into XML */
    $this->xml_parser = $this->get('xml_parser');
    /** @var Object $session_wrapper used to read and write from session files */
    $this->session_wrapper = $this->get('session_wrapper');
    /** @var Object $session_obj used to format the necessary information to save to session files */
    $this->session_obj = $this->get('session_model');
    /**
     * The session object has to have multiple wrappers added to it these include the encoding, hashing,
     * and encryption classes at the moment encryption requires the session_id, so this is also added
     * lastly, the labels in the session file are also encrypted to obfuscate the information
     */
    $this->session_obj->set_wrapper_session_file($this->session_wrapper);



    $this->session_obj->generate_labels();
    $this->session_obj->set_wrapper_session_file($this->session_wrapper);

    /**
     * Variables are assigned to the validator Object to become sanitised by calling the sanitise_string function.
     * This function is supplied with each user detail in the current session, by calling the perform_detail_retrieval
     * function in the the Session_Model class (Note that an existing users details do not need to be validated again)
     */
    $this->validated_username = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('username'));
    $this->validated_password = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('password'));
    $this->validated_fname = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('fname'));
    $this->validated_lname = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('lname'));
    $this->validated_status = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('validated'));
    //echo $this->validator_obj->sanitise_string('To locate the session file, turn on the echo session_id'); // xampp/tmp/ this is so you can see the session id
    //echo session_id();
    /** If the following variables are not null then login and display the main commands page, else display the login page */
    if($this->validated_username != null && $this->validated_password != null && $this->validated_status)
    {
        return $this->view->render($response,
            'commands_page.html.twig',
            [
                'css_path' => CSS_PATH, //CSS_PATH corresponds to a css file directory path, and is defined in the settings.php file
                'landing_page' => LANDING_PAGE, //not sure what this is for, but it's defined in the settings.php file
                'method_get' => 'get', //method_get represents get
                'method_post' => 'post', //method_post represents post


                'greeting_text' => $this->validator_obj->sanitise_string('Welcome Back ' . $this->validated_fname . ' ' . $this->validated_lname), //'Welcome Back first name surname'
                'page_text' =>  $this->validator_obj->sanitise_string('Please select the desired command:'), //'Please select the desired command'
                'action_saved' => './commands/saved',
                'action_download' => './commands/download',
                'action_display' => './commands/display',
                'action_filter' => './commands/filter',
                'action_send' => './commands/send',
                'action_change' => './commands/change',
                'action_logout' => './commands/logout',
                'initial_input_box_value' => null,
            ]);
    }
    else
    {
        return $this->view->render($response,
            'login_page.html.twig',
            [
                'css_path' => CSS_PATH,
                'landing_page' => LANDING_PAGE,
                'method' => 'post',


                'page_heading_2' => $this->validator_obj->sanitise_string('Perform many SMS operations and management.'),
                'page_text' => $this->validator_obj->sanitise_string('It is required that you login or register in order to check or send messages.'),
                'action_login' => './commands',
                'action_signup' => './commands',
                'initial_input_box_value' => null,
            ]);
    }
})->setName('loginpage');
$app->post('/commands/logout', function(Request $request, Response $response) use ($app)
{
    /**
     * This part of the app starts the logout procedure this mostly involves clearing the session file and then
     * returning the user to the login_page. Note that a lot of the same variables and functions are used again
     * so the comments would be the same as the login section above
     */
    $this->validated_username = null;
    $this->validated_password = null;
    $this->validated_fname = null;
    $this->validated_lname = null;
    $this->validated_status = null;
    $this->arr_tainted_params = $request->getParsedBody();
    $this->validator_obj_obj = $this->get('sanitised_validator');
    $this->profile_obj = $this->get('profile_model');
    $this->session_wrapper = $this->get('session_wrapper');
    $this->session_obj = $this->get('session_model');
    $this->base64_wrapper = $this->get('base64_wrapper');
    $this->bcrypt_wrapper = $this->get('bcrypt_wrapper');
    $this->session_obj->set_wrapper_session_file($this->session_wrapper);
    $this->session_obj->set_base64_wrapper($this->base64_wrapper);
    $this->session_obj->set_bcrypt_wrapper($this->bcrypt_wrapper);
    $this->session_obj->set_openssl_wrapper($this->openssl_wrapper);
    $this->session_obj->set_sid(session_id());
    $this->session_obj->generate_labels();
    $this->session_obj->retrieve_secure_data();
    /**
     * The same applies as before in the login section (read line 76), however this time only the first name and
     * surname are sanitised as these are displayed as part of the successful logout message
     */
    $this->validated_fname = $this->validator_obj_obj->sanitise_string($this->session_obj->perform_detail_retrieval('fname'));
    $this->validated_lname = $this->validator_obj_obj->sanitise_string($this->session_obj->perform_detail_retrieval('lname'));
    /** The current session object has it's contents cleared */
    $this->session_obj->clear_data();
    return $this->view->render($response,
        'alert_layout.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'get',
            'action' => '../',
            'initial_input_box_value' => null,


            'page_heading_2' => $this->validator_obj_obj->sanitise_string('Logged Out'),
            'page_text' => $this->validator_obj_obj->sanitise_string($this->validated_fname . ' ' . $this->validated_lname . ', you have been successfully logged out.')
        ]);
})->setName('logout');
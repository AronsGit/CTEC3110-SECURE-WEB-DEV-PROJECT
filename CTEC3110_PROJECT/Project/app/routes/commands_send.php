<?php
/**
 * commands_send.php
 *
 * When the user selects the send message button on the commands mainpage, it redirects the user to a form to allow
 * them to send a message to the M2M server, note that authorisation is required in the form of the users username
 * and password before a message can be sent
 *
 * User: p2442537

 *
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app->post('/commands/send', function( Response $response) use ($app)
{
    /** @var Object $validator_obj is used to ensure Strings are both sanitised and validated throughout the app */
    $this->validator_obj = $this->get('sanitised_validator');
    /** Returns the homepageform_layout.html page, which displays a form to allow a user to send a message to the M2M sever */
    return $this->view->render($response,
        'compose_layout.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method_post' => 'post',
            'action_proceed' => './send/processed',
            'action_back' => '../commands',
            'initial_input_box_value' => null,
            'page_title' => APP_NAME,
            'page_heading_1' => APP_NAME,

            'type' => $this->validator_obj->sanitise_string('text'),
            'header_text' => $this->validator_obj->sanitise_string('Send Message'),
            'entry_zero' => $this->validator_obj->sanitise_string('Receiver Phone Number'),
            'entry_one' => $this->validator_obj->sanitise_string('Message'),
            'button_text' => $this->validator_obj->sanitise_string('Send Message'),
        ]);
})->setName('send');
$app->post('/commands/send/processed', function(Request $request, Response $response) use ($app)
{
    /**
     * Note that a lot of the same variables and functions are used here as in the login_page.php file so the comments
     * would be the same, therefore to see those particular DocBlock comments refer to the login_page.php file. Any
     * variables or function calls that are unique to this page will be commented.
     */
    $this->validated_username = null;
    $this->validated_password = null;
    $this->validated_fname = null;
    $this->validated_lname = null;
    $this->validated_status = null;
    $this->validated_message = '';
    $this->arr_tainted_params = $request->getParsedBody();
    $this->validator_obj = $this->get('sanitised_validator');
    $this->profile_obj = $this->get('profile_model');
    $this->soap_obj = $this->get('soap_model');
    $this->xml_parser = $this->get('xml_parser');
    $this->session_wrapper = $this->get('session_wrapper');
    $this->session_obj = $this->get('session_model');



    $this->session_obj->set_wrapper_session_file($this->session_wrapper);



    $this->session_obj->set_sid(session_id());
    $this->session_obj->generate_labels();
    $this->session_obj->retrieve_secure_data();
    /** @var Boolean $authenticated_password This checks and stores whether or not the password has been authenticated */
    $this->authenticated_password = false;
    /** These variables are retrieved from the session file and then sanitised */
    /** @var String $validated_session_username is the validated username that is stored in the current session file */
    $this->validated_session_username = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('username'));
    /** @var String $validated_session_password is the validated password that is stored in the current session file */
    $this->validated_session_password = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('password'));
    /**
     * If the following variables are not null and are set then put the username, password, entry_zero and entry_one
     * into an array of tainted parameters, and call the validate function (which both sanitises and validates) on the
     * username, old password, old password-zero and old password-one by supplying the function with the tainted
     * parameters from the array of tainted parameters
     */
    if (isset($this->arr_tainted_params['username']) && isset($this->arr_tainted_params['password']))
    {
        $tainted_username = $this->arr_tainted_params['username'];
        $this->validated_username = $this->validator_obj->validate_username($tainted_username);
        $tainted_password = $this->arr_tainted_params['password'];
        $this->validated_password = $this->validator_obj->validate_password($tainted_password);
        $tainted_message = group_denomination;
        $tainted_message .= ',';
        if($this->arr_tainted_params['switch_zero'] == null){   $tainted_message .= 'off';  }
        else{   $tainted_message .= $this->arr_tainted_params['switch_zero'];   }
        $tainted_message .= ',';
        if($this->arr_tainted_params['switch_one'] == null){   $tainted_message .= 'off';  }
        else{   $tainted_message .= $this->arr_tainted_params['switch_one'];   }
        $tainted_message .= ',';
        if($this->arr_tainted_params['switch_two'] == null){   $tainted_message .= 'off';  }
        else{   $tainted_message .= $this->arr_tainted_params['switch_two'];   }
        $tainted_message .= ',';
        if($this->arr_tainted_params['switch_three'] == null){   $tainted_message .= 'off';  }
        else{   $tainted_message .= $this->arr_tainted_params['switch_three'];   }
        $tainted_message .= ',';
        $tainted_message .= $this->arr_tainted_params['radio_fan'];
        $tainted_message .= ',';
        $tainted_message .= $this->arr_tainted_params['temperature'];
        $tainted_message .= ',';
        $tainted_message .= $this->arr_tainted_params['keypad'];
        $this->validated_message = $this->validator_obj->sanitise_string($tainted_message);
    }
    /** Not sure how to explain this */
    if($this->bcrypt_wrapper->authenticate_password($this->validated_password, $this->validated_session_password))
        $this->authenticated_password = true;
    //  This was used during development to populate the m2m server in the case that other students cleared it, it remains commented in case needed.
    /*
        if($this->validated_username == m2m_username && $this->validated_password == m2m_password){
            $this->soap_obj->set_xml_parser($this->xml_parser);
            $this->soap_obj->set_validator($this->validator_obj);
            $arr_messages = array(0=>group_denomination.',off,on,off,on,forward,-8,6',
                1=>group_denomination.',on,on,off,on,forward,8,#',
                2=>group_denomination.',off,off,off,off,reverse,48,1',
                3=>group_denomination.',on,on,on,on,forward,0,0',
                4=>group_denomination.',on,off,off,on,forward,12,4',
                5=>group_denomination.',on,on,off,off,reverse,17,7',
                6=>group_denomination.',off,on,off,on,reverse,20,8',
                7=>group_denomination.',on,off,off,on,forward,18,6',
                8=>group_denomination.',on,on,off,on,forward,23,8',
                9=>group_denomination.',on,on,off,on,forward,35,3',
                10=>group_denomination.',off,on,off,on,reverse,-3,4',
                11=>group_denomination.',on,on,on,off,reverse,19,*',
                12=>group_denomination.',on,on,off,on,forward,44,1',
                13=>group_denomination.',on,off,off,on,reverse,3,0',
                14=>group_denomination.',off,off,off,off,forward,8,#',
                15=>group_denomination.',on,on,off,off,reverse,42,9',
                16=>group_denomination.',on,on,on,on,forward,33,#',
                17=>group_denomination.',on,off,off,off,reverse,3,7',
                18=>group_denomination.',off,on,off,on,forward,39,3',
                19=>group_denomination.',off,on,off,off,reverse,50,*',
                20=>group_denomination.',on,on,off,on,reverse,21,2',
                21=>group_denomination.',on,off,off,off,reverse,32,#',
                22=>group_denomination.',on,off,on,on,forward,-1,1',
                23=>group_denomination.',on,on,off,on,reverse,15,8',
                24=>group_denomination.',off,on,off,on,forward,31,1',
                25=>group_denomination.',on,on,off,on,forward,41,4',
                26=>group_denomination.',off,on,off,on,reverse,27,3',
                27=>group_denomination.',off,on,on,on,reverse,38,*',
                28=>group_denomination.',on,off,off,on,forward,18,8',
                29=>group_denomination.',on,off,on,on,reverse,12,2');
            foreach($arr_messages as $msg){
                $this->soap_obj->send_message(m2m_destination, $msg);
            }
            $this->page_text = 'You have successfully cluster populated the m2m server.';
            $this->action_back = '../../commands';
        }
    */
    /**
     * If the validated_username is not equal to the validated_session_username and the password is not authenticated
     * then display an error message and return back to the change password form
     */
    if($this->validated_username != $this->validated_session_username && !$this->authenticated_password)
    {
        $this->page_text = 'Both Username and Current Password were Incorrect.';
        $this->action_back = '../../commands/send';
    }
    /**
     * Else if the validated_username is not equal to the validated_session_username
     * then display an error message and return back to the change password form
     */
    elseif($this->validated_username != $this->validated_session_username)
    {
        $this->page_text = 'Incorrect Username.';
        $this->action_back = '../../commands/send';
    }
    /**
     * Else if the current password is not authenticated then display an error message and return back to the change
     * password form
     */
    elseif(!$this->authenticated_password)
    {
        $this->page_text = 'Incorrect Current Password.';
        $this->action_back = '../../commands/send';
    }
    /**
     * Else display a success message and return back to the commands mainpage
     */
    else    {
        $this->soap_obj->set_xml_parser($this->xml_parser);
        $this->soap_obj->set_validator($this->validator_obj);
        $this->soap_obj->send_message(m2m_destination, $this->validated_message);
        $this->page_text = 'You have successfully sent your message.';
        $this->action_back = '../../commands';
    }
    /**
     * Returns the alert_layout.html page, which gives an alert of when an action happens when changing the password.
     * This action can either be an error or success.
     */
    return $this->view->render($response,
        'alert_layout.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method' => 'post',
            'action' => $this->action_back,
            'initial_input_box_value' => null,



            'page_heading_2' => $this->validator_obj->sanitise_string('Message Processed'),
            'page_text' => $this->validator_obj->sanitise_string($this->page_text),
        ]);
})->setName('processed');
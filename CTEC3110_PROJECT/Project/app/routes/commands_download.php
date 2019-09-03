<?php
/**
 * commands_download.php
 *
 * This page allows a user to download any pending messages from the SMS server
 *
 * User: p2442537
 *
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app->post('/commands/download', function(Request $request, Response $response) use ($app)
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
    $this->arr_tainted_params = $request->getParsedBody();
    $this->validator_obj = $this->get('sanitised_validator');
    $this->profile_obj = $this->get('profile_model');
    $this->session_wrapper = $this->get('session_wrapper');
    $this->session_obj = $this->get('session_model');


    $this->soap_obj = $this->get('soap_model');
    $this->soap_obj->set_xml_parser($this->xml_parser);
    $this->soap_obj->set_validator($this->validator_obj);
    $this->arr_tainted_messages = $this->soap_obj->peek_messages('+447817814149');
    $this->arr_validated_messages = [];
    //var_dump($this->soap_obj);
    foreach($this->arr_tainted_messages as $msg){
        $validated_msg = $this->validator_obj->validate_message($msg, group_denomination, 'PhpUnit');
        if($validated_msg != null){
            array_push($this->arr_validated_messages, implode(',', $validated_msg));
            //echo '<br>' . end($this->arr_validated_messages);
        }
    }
    //var_dump($this->arr_validated_messages);
    $this->session_obj->set_wrapper_session_file($this->session_wrapper);


    $this->session_obj->set_sid(session_id());
    $this->session_obj->generate_labels();
    $this->session_obj->retrieve_secure_data();
    $this->session_obj->clear_message_data();
    $this->session_obj->set_session_array_messages($this->arr_validated_messages);
    $this->validated_username = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('username'));
    $this->validated_password = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('password'));
    $this->validated_fname = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('fname'));
    $this->validated_lname = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('lname'));
    $this->validated_status = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('validated'));
    $this->session_obj->store_secure_data();
    /**
     * When downloading messages it is useful to know what time and date they were downloaded, therefore the timezone
     * should be set, which in this case is Europe/London for GMT +0. The date format is also specified.
     */
    date_default_timezone_set('Europe/London');
    $datetime = date("Y/m/d") . ' ' . date("H:i:s");
    return $this->view->render($response,
        'commands_page.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method_get' => 'get',
            'method_post' => 'post',
            'action_saved' => '../commands/saved',
            'action_download' => '../commands/download',
            'action_clear' => '../commands/clear',
            'action_display' => '../commands/display',
            'action_filter' => '../commands/filter',
            'action_send' => '../commands/send',
            'action_change' => '../commands/change',
            'action_logout' => '../commands/logout',
            'initial_input_box_value' => null,

            'greeting_text' => $this->validator_obj->sanitise_string('Welcome Back ' . $this->validated_fname . ' ' . $this->validated_lname),
            'message' => $this->validator_obj->sanitise_string('Messages Sucessfully Downloaded: ' . $datetime ),
            'message_1' => $this->validator_obj->sanitise_string('Clear Messages: If you desire additional privacy'),
            'page_text' => $this->validator_obj->sanitise_string('Please select the desired command:'),
        ]);
})->setName('download');
$app->post('/commands/clear', function(Request $request, Response $response) use ($app)
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
    $this->arr_tainted_params = $request->getParsedBody();
    $this->validator_obj = $this->get('sanitised_validator');
    $this->profile_obj = $this->get('profile_model');
    $this->session_wrapper = $this->get('session_wrapper');
    $this->session_obj = $this->get('session_model');



    $this->session_obj->set_wrapper_session_file($this->session_wrapper);



    $this->session_obj->set_sid(session_id());
    $this->session_obj->generate_labels();
    $this->session_obj->retrieve_secure_data();
    $this->session_obj->clear_message_data();
    $this->validated_username = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('username'));
    $this->validated_password = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('password'));
    $this->validated_fname = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('fname'));
    $this->validated_lname = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('lname'));
    $this->validated_status = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('validated'));
    //$this->session_obj->set_session_saved_indices(null);
    $this->session_obj->store_secure_data();
    /**
     * When downloading messages it is useful to know what time and date they were downloaded, therefore the timezone
     * should be set, which in this case is Europe/London for GMT +0. The date format is also specified.
     */
    date_default_timezone_set('Europe/London');
    $datetime = date("Y/m/d") . ' ' . date("H:i:s");
    return $this->view->render($response,
        'commands_page.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method_get' => 'get',
            'method_post' => 'post',
            'action_saved' => '../commands/saved',
            'action_download' => '../commands/download',
            'action_clear' => '../commands/clear',
            'action_display' => '../commands/display',
            'action_filter' => '../commands/filter',
            'action_send' => '../commands/send',
            'action_change' => '../commands/change',
            'action_logout' => '../commands/logout',
            'initial_input_box_value' => null,

            'greeting_text' => $this->validator_obj->sanitise_string('Welcome Back ' . $this->validated_fname . ' ' . $this->validated_lname),
            'message' => $this->validator_obj->sanitise_string('Messages Sucessfully Cleared: ' . $datetime),
            'page_text' => $this->validator_obj->sanitise_string('Please select the desired command:'),
        ]);
})->setName('clear');

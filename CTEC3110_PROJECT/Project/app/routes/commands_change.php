<?php
/**
 * commands_change.php
 * User: p2442537
 *
 * The purpose of this page is to allow a user to change their account password.
 *
 */


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app->post('/commands/change', function(Response $response) use ($app)
{
    /** @var Object $validator_obj is used to ensure Strings are both sanitised and validated throughout the app */
    $this->validator_obj = $this->get('sanitised_validator');
    /** Returns the homepageform_layout.html page, which displays a form to allow a user to change their password */
    return $this->view->render($response,
        'homepageform_layout.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method_post' => 'post',
            'action_proceed' => '../commands/change/status',
            'action_back' => '../commands',
            'type' => $this->validator_obj->sanitise_string('password'),
            'initial_input_box_value' => null,
            'header_text' => $this->validator_obj->sanitise_string('Change Password'),
            'entry_zero' => $this->validator_obj->sanitise_string('New Password'),
            'entry_one' => $this->validator_obj->sanitise_string('Confirm New Password'),
            'button_text' => $this->validator_obj->sanitise_string('Change Password'),
        ]);
})->setName('change');
$app->post('/commands/change/status', function(Request $request, Response $response) use ($app)
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
    $this->db_handle = $this->get('dbase');
    $this->mysql_obj = $this->get('mysql_model');
    $this->mysql_wrapper = $this->get('mysql_wrapper');
    $this->mysql_queries = $this->get('mysql_queries');
    $this->mysql_wrapper->set_db_handle($this->db_handle);
    $this->mysql_wrapper->set_sql_queries($this->mysql_queries);
    $this->mysql_obj->set_mysql_wrapper($this->mysql_wrapper);


    $this->mysql_obj->set_db_handle($this->db_handle);
    $this->mysql_obj->set_sql_queries($this->mysql_queries);
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
        $this->validated_old_password = $this->validator_obj->validate_password($tainted_password);
        $tainted_password = $this->arr_tainted_params['entry_zero'];
        $this->validated_new_password_zero = $this->validator_obj->validate_password($tainted_password);
        $tainted_password = $this->arr_tainted_params['entry_one'];
        $this->validated_new_password_one = $this->validator_obj->validate_password($tainted_password);
    }
    //var_dump($this->validated_old_password);
    //var_dump($this->validated_session_password);
    //var_dump($this->openssl_wrapper);
    /** Not sure how to explain this */
    if($this->bcrypt_wrapper->authenticate_password($this->validated_old_password, $this->validated_session_password))
        $this->authenticated_password = true;
    /**
     * If the validated_username is not equal to the validated_session_username and the password is not authenticated
     * then display an error message and return back to the change password form
     */
    if($this->validated_username != $this->validated_session_username && !$this->authenticated_password)
    {
        $this->page_text = 'Both Username and Current Password were Incorrect.';
        $this->action_back = '../../commands/change';
    }
    /**
     * Else if the validated_username is not equal to the validated_session_username
     * then display an error message and return back to the change password form
     */
    elseif($this->validated_username != $this->validated_session_username)
    {
        $this->page_text = 'Incorrect Username.';
        $this->action_back = '../../commands/change';
    }
    /**
     * Else if the current password is not authenticated then display an error message and return back to the change
     * password form
     */
    elseif(!$this->authenticated_password)
    {
        $this->page_text = 'Incorrect Current Password.';
        $this->action_back = '../../commands/change';
    }
    /**
     * Not sure what strisstr is for
     */
    elseif(stristr($this->validated_new_password_zero, "Unacceptable Password"))
    {
        $this->page_text = $this->validated_new_password_zero;
        $this->action_back = '../../commands/change';
    }
    /**
     * Not sure what strisstr is for
     */
    elseif(stristr($this->validated_new_password_one, "Unacceptable Password"))
    {
        $this->page_text = $this->validated_new_password_one;
        $this->action_back = '../../commands/change';
    }
    /**
     * Else if the validated_new_password_zero is not equal to the validated_new_password_one
     * then display an error message and return back to the change password form
     */
    elseif($this->validated_new_password_zero != $this->validated_new_password_one)
    {
        $this->page_text = 'New passwords must match.';
        $this->action_back = '../../commands/change';
    }
    /**
     * Else display a success message and return back to the commands mainpage
     */
    else
    {
        $this->page_text = 'You have successfully changed your password.';
        $this->action_back = '../../commands';
        /**
         * Update the profile object with the validated username and new validated password zero, not sure what the other
         * two lines do
         */
        $this->profile_obj->set_parameters($this->validated_username, $this->validated_new_password_zero,
            $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('fname')),
            $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('lname')));
        /** Update the session profile object with the updated profile object and then store it securely */
        $this->session_obj->set_session_profile($this->profile_obj);
        $this->session_obj->store_secure_data();
        $this->mysql_obj->set_database_profile($this->profile_obj);
        $this->mysql_obj->set_database_saved_indicies($this->session_obj->perform_detail_retrieval('saved'));
        $this->mysql_wrapper->set_db_handle($this->db_handle);
        $this->mysql_wrapper->set_sql_queries($this->mysql_queries);
        $this->mysql_obj->store_secure_data();
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
            'page_heading_2' => $this->validator_obj->sanitise_string('Change Procedure'),
            'page_text' => $this->validator_obj->sanitise_string($this->page_text),
        ]);
})->setName('change_status');
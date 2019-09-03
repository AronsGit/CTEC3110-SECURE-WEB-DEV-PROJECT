<?php
/**
 * commands_mainpage.php
 *
 * This is the second page a user see's when they first sign up or login,
 * it gives a number of options a user can choose to use.
 *
 * These include viewing the users saved messages, downloading pending,
 * messages, displaying the recently downloaded messages, filtering
 * message details by metadata, sending a message, changing the account
 * password and logging out of the application.
 *
 * User: p2442537
 *
 *
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app->post('/commands', function(Request $request, Response $response) use ($app)
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
    $this->db_handle = $this->get('dbase');
    $this->mysql_obj = $this->get('mysql_model');
    $this->mysql_wrapper = $this->get('mysql_wrapper');
    $this->mysql_queries = $this->get('mysql_queries');
    $this->mysql_wrapper->set_db_handle($this->db_handle);
    $this->mysql_wrapper->set_sql_queries($this->mysql_queries);
    $this->mysql_obj->set_mysql_wrapper($this->mysql_wrapper);



    $this->mysql_obj->set_db_handle($this->db_handle);
    $this->mysql_obj->set_sql_queries($this->mysql_queries);
    $this->mysql_wrapper->set_db_handle($this->db_handle);
    $this->mysql_wrapper->set_sql_queries($this->mysql_queries);
    $this->page_text = $this->validator_obj->sanitise_string('The following information is incorrect: ');
    /**
     * At this point, the app checks what the last operation was based on parsedtext
     * there are four main options: login, signup, (empty), and other
     * the first three are legitimate, whereas the last is not
     * the first two are straight-forward, the third just means the user came from another page
     * the last means a malicious user is trying to perform something unwanted, this leads to a logout scenario
     */
    if (isset($this->arr_tainted_params['source']))
    {
        $tainted_source = $this->arr_tainted_params['source'];
        $this->validated_source = $this->validator_obj->sanitise_string($tainted_source);
        unset($this->arr_tainted_params['source']);
        //  What follows is the source if one has just logged in
        if(stristr($this->validated_source, 'login'))
        {
            /**
             * Since the user has logged in, the app checks if there is username and password information
             * If there is, this information is sanitised and saved for later use
             */
            if (isset($this->arr_tainted_params['username']) && isset($this->arr_tainted_params['password']))
            {
                $tainted_username = $this->arr_tainted_params['username'];
                $this->validated_username = $this->validator_obj->validate_username($tainted_username);
                $tainted_password = $this->arr_tainted_params['password'];
                $this->validated_password = $this->validator_obj->validate_password($tainted_password);
            }
            $this->validated_status = false;
            $this->username_status = false;
            $this->password_status = false;
            if($this->validated_username == null && $this->validated_password == null) {
                $this->session_obj->retrieve_secure_data();
                $this->validated_username = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('username'));
                $this->validated_password = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('password'));
                $this->validated_fname = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('fname'));
                $this->validated_lname = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('lname'));
                $this->validated_status = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('validated'));
            }
            else    {
                $this->mysql_obj->set_database_id($this->validated_username);
                $this->mysql_obj->retrieve_secure_data();
                if($this->mysql_obj->check_username_database())
                    $this->username_status = true;
                if( $this->bcrypt_wrapper->authenticate_password($this->validated_password, $this->mysql_obj->perform_detail_retrieval('password'))){
                    $arr_fullname = explode(',', $this->mysql_obj->perform_detail_retrieval('fullname'));
                    $this->validated_fname = $arr_fullname[0];
                    $this->validated_lname = $arr_fullname[1];
                    $this->password_status = true;
                }
            }
            //  this is the end of what needs to be removed
            /** If the user information has been successfully authenticated, then return the main page of the app */
            if( $this->username_status && $this->password_status){
                $this->profile_obj->set_parameters($this->validated_username, $this->validated_password, $this->validated_fname, $this->validated_lname);
                $this->page_text = $this->validator_obj->sanitise_string('Please select the desired command: ');
                //  Saves relevant information locally, to allow user with some functionality
                $this->session_obj->set_session_profile($this->profile_obj);//
                $this->session_obj->set_sid($this->validator_obj->sanitise_string(session_id()));
                $this->session_obj->store_secure_data();
                $this->validated_fname = $this->validator_obj->sanitise_string($this->profile_obj->perform_detail_retrieval('fname'));
                $this->validated_lname = $this->validator_obj->sanitise_string($this->profile_obj->perform_detail_retrieval('lname'));
                return $this->view->render($response,
                    'commands_page.html.twig',
                    [
                        'css_path' => CSS_PATH,
                        'landing_page' => LANDING_PAGE,
                        'method_get' => 'get',
                        'method_post' => 'post',
                        'action_saved' => './commands/saved',
                        'action_download' => './commands/download',
                        'action_clear' => './commands/clear',
                        'action_display' => './commands/display',
                        'action_filter' => './commands/filter',
                        'action_send' => './commands/send',
                        'action_change' => './commands/change',
                        'action_logout' => './commands/logout',
                        'initial_input_box_value' => null,

                        'greeting_text' => 'Welcome Back ' . $this->validated_fname . ' ' . $this->validated_lname,
                        'page_text' => $this->page_text,
                    ]);
            }
            /** Otherwise, let the user know which part of the information was incorrect, and return to the login_page */
            else{
                if( !$this->username_status){
                    $this->page_text .= 'username ';
                }
                if( !$this->password_status){
                    $this->page_text .= 'password';
                }
                return $this->view->render($response,
                    'alert_layout.html.twig',
                    [
                        'css_path' => CSS_PATH,
                        'landing_page' => LANDING_PAGE,
                        'method' => 'get',
                        'action' => './',
                        'initial_input_box_value' => null,

                        'page_heading_2' => $this->validator_obj->sanitise_string('Invalid Information'),
                        'page_text' => $this->validator_obj->sanitise_string($this->page_text),
                    ]);
            }
        }
        /** This is the start of the sign up procedure */
        elseif(stristr($this->validated_source, 'signup')){
            //** the parsed data is checked for the relevant information, sanitised, and saved for later use */
            if (isset($this->arr_tainted_params['username']) && isset($this->arr_tainted_params['password']) &&
                isset($this->arr_tainted_params['fname']) && isset($this->arr_tainted_params['lname']))
            {
                $tainted_username = $this->arr_tainted_params['username'];
                $this->validated_username = $this->validator_obj->validate_username($tainted_username);
                $tainted_password = $this->arr_tainted_params['password'];
                $this->validated_password = $this->validator_obj->validate_password($tainted_password);
                $tainted_fname = $this->arr_tainted_params['fname'];
                $this->validated_fname = $this->validator_obj->validate_fname($tainted_fname);
                $tainted_lname = $this->arr_tainted_params['lname'];
                $this->validated_lname = $this->validator_obj->validate_lname($tainted_lname);
            }
            /** If the password returns as unacceptable, let the user know why specifically it is wrong, and return to login_page */
            if(stristr($this->validated_password, 'Unacceptable Password')){
                return $this->view->render($response,
                    'alert_layout.html.twig',
                    [
                        'css_path' => CSS_PATH,
                        'landing_page' => LANDING_PAGE,
                        'method' => 'get',
                        'action' => './',
                        'initial_input_box_value' => null,

                        'page_heading_2' => $this->validator_obj->sanitise_string('Invalid Information'),
                        'page_text' => $this->validated_password,
                    ]);
            }
            else if(stristr($this->validated_fname, 'Invalid first name') ||
                stristr($this->validated_fname, 'should be between 4 and 20')){
                return $this->view->render($response,
                    'alert_layout.html.twig',
                    [
                        'css_path' => CSS_PATH,
                        'landing_page' => LANDING_PAGE,
                        'method' => 'get',
                        'action' => './',
                        'initial_input_box_value' => null,

                        'page_heading_2' => $this->validator_obj->sanitise_string('Invalid Information'),
                        'page_text' => $this->validated_fname,
                    ]);
            }
            else if(stristr($this->validated_lname, 'A last name cannot be') ||
                stristr($this->validated_lname, 'should be between 4 and 20')){
                return $this->view->render($response,
                    'alert_layout.html.twig',
                    [
                        'css_path' => CSS_PATH,
                        'landing_page' => LANDING_PAGE,
                        'method' => 'get',
                        'action' => './',
                        'initial_input_box_value' => null,

                        'page_heading_2' => $this->validator_obj->sanitise_string('Invalid Information'),
                        'page_text' => $this->validated_lname,
                    ]);
            }
            else if(stristr($this->validated_username, 'Unacceptable Username')){
                return $this->view->render($response,
                    'alert_layout.html.twig',
                    [
                        'css_path' => CSS_PATH,
                        'landing_page' => LANDING_PAGE,
                        'method' => 'get',
                        'action' => './',
                        'initial_input_box_value' => null,

                        'page_heading_2' => $this->validator_obj->sanitise_string('Invalid Information'),
                        'page_text' => $this->validated_username,
                    ]);
            }
            else{
                $this->mysql_obj->set_database_id($this->validated_username);
                if(!$this->mysql_obj->check_username_database()){
                    $this->page_text = $this->validator_obj->sanitise_string('Please select the desired command: ');
                    $this->profile_obj->set_parameters($this->validated_username, $this->validated_password, $this->validated_fname, $this->validated_lname);
                    $this->session_obj->set_session_profile($this->profile_obj);
                    $this->session_obj->store_secure_data();
                    $this->mysql_obj->set_database_profile($this->profile_obj);
                    $this->mysql_wrapper->set_db_handle($this->db_handle);
                    $this->mysql_wrapper->set_sql_queries($this->mysql_queries);
                    $this->mysql_obj->store_secure_data();
                    //var_dump($this->mysql_obj);
                    /** display the main page of the app if everything is in order */
                    return $this->view->render($response,
                        'commands_page.html.twig',
                        [
                            'css_path' => CSS_PATH,
                            'landing_page' => LANDING_PAGE,
                            'method_get' => 'get',
                            'method_post' => 'post',
                            'action_saved' => './commands/saved',
                            'action_download' => './commands/download',
                            'action_clear' => './commands/clear',
                            'action_display' => './commands/display',
                            'action_filter' => './commands/filter',
                            'action_send' => './commands/send',
                            'action_change' => './commands/change',
                            'action_logout' => './commands/logout',
                            'initial_input_box_value' => null,


                            'greeting_text' => $this->validator_obj->sanitise_string('Welcome For the First Time ' . $this->validated_fname . ' ' . $this->validated_lname),
                            'page_text' => $this->page_text,
                        ]);
                }
                else{
                    $this->page_text = 'Username was already taken, Password was acceptable.';
                    return $this->view->render($response,
                        'alert_layout.html.twig',
                        [
                            'css_path' => CSS_PATH,
                            'landing_page' => LANDING_PAGE,
                            'method' => 'get',
                            'action' => './',
                            'initial_input_box_value' => null,


                            'page_heading_2' => $this->validator_obj->sanitise_string('Username Already Taken'),
                            'page_text' => $this->validator_obj->sanitise_string($this->page_text)
                        ]);
                }
            }
        }
        /** Passive-aggressively let the user know that they did something unexpected, send them back to login_page */
        else{
            $this->page_text = $this->validator_obj->sanitise_string('You have found an error, you must re-enter your information.');
            return $this->view->render($response,
                'alert_layout.html.twig',
                [
                    'css_path' => CSS_PATH,
                    'landing_page' => LANDING_PAGE,
                    'method' => 'get',
                    'action' => './',
                    'initial_input_box_value' => null,

                    'page_heading_2' => $this->validator_obj->sanitise_string('Unknown Source'),
                    'page_text' => $this->page_text,
                ]);
        }
    }
    /** The user has come back from one of the commands */
    else{
        $this->session_obj->set_wrapper_session_file($this->session_wrapper);
        $this->session_obj->retrieve_secure_data();
        $this->validated_username = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('username'));
        $this->validated_password = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('password'));
        $this->validated_fname = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('fname'));
        $this->validated_lname = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('lname'));
        $this->validated_status = $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('validated'));
        if( $this->validated_status){
            $this->profile_obj->set_parameters($this->validated_username, $this->validated_password, $this->validated_fname, $this->validated_lname);
            $this->page_text = $this->validator_obj->sanitise_string('Please select the desired command: ');
            $this->session_obj->set_session_profile($this->profile_obj);
            $this->session_obj->set_sid($this->validator_obj->sanitise_string(session_id()));
            $this->session_obj->set_wrapper_session_file($this->session_wrapper);
            $this->session_obj->store_secure_data();
            return $this->view->render($response,
                'commands_page.html.twig',
                [
                    'css_path' => CSS_PATH,
                    'landing_page' => LANDING_PAGE,
                    'method_get' => 'get',
                    'method_post' => 'post',
                    'action_saved' => './commands/saved',
                    'action_clear' => './commands/clear',
                    'action_download' => './commands/download',
                    'action_display' => './commands/display',
                    'action_filter' => './commands/filter',
                    'action_send' => './commands/send',
                    'action_change' => './commands/change',
                    'action_logout' => './commands/logout',
                    'initial_input_box_value' => null,

                    'greeting_text' => $this->validator_obj->sanitise_string('Welcome Back ' . $this->validated_fname . ' ' . $this->validated_lname),
                    'page_text' => $this->page_text,
                ]);
        }
        else{
            if( $this->validated_username != "root" ){
                $this->page_text .= 'username ';
            }
            if( stristr($this->validated_password, 'Unacceptable Password')){
                $this->page_text .= 'password';
            }
            return $this->view->render($response,
                'alert_layout.html.twig',
                [
                    'css_path' => CSS_PATH,
                    'landing_page' => LANDING_PAGE,
                    'method' => 'get',
                    'action' => '../',
                    'initial_input_box_value' => null,


                    'page_heading_2' => $this->validator_obj->sanitise_string('Invalid Information'),
                    'page_text' => $this->validator_obj->sanitise_string($this->page_text),
                ]);
        }
    }
})->setName('commands');

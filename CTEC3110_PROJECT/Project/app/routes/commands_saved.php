<?php
/**
 * commands_saved.php
 *
 * This page stores all of a users saved messages, and also displays details about the saved messages such as sender,
 * receiver and timestamp.
 *
 * User: p2442537
 *
 */

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
$app->post('/commands/saved', function(Request $request, Response $response) use ($app)
{
    /** @var Object $validator_obj is used to ensure Strings are both sanitised and validated throughout the app */
    $this->validator_obj = $this->get('sanitised_validator');
    /**
     * Returns the table_layout.html page, which presents a table, allowing the user to view their messages
     * while also allowing them to filter the metadata of message with a tick box. Messages can also be saved.
     */
    $this->validated_username = null;
    $this->validated_password = null;
    $this->validated_fname = null;
    $this->validated_lname = null;
    $this->validated_status = null;
    $this->index_start = 0;
    $this->arr_tainted_params = $request->getParsedBody();
    $this->arr_alters = [];
    $this->validator_obj = $this->get('sanitised_validator');;
    $this->profile_obj = $this->get('profile_model');
    $this->session_wrapper = $this->get('session_wrapper');
    $this->session_obj = $this->get('session_model');


    $this->session_obj->set_wrapper_session_file($this->session_wrapper);



    $this->session_obj->set_sid(session_id());
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
    $this->arr_tainted_messages = $this->session_obj->perform_detail_retrieval('messages');
    $this->arr_validated_messages = [];
    $this->arr_empty_value = array_fill(0 , 9, ' ' );
    //$this->arr_display_messages = array_fill(0, 9, $this->arr_empty_value);
    $this->arr_tainted_alters = explode(',', $this->validator_obj->sanitise_string($this->session_obj->perform_detail_retrieval('saved')));
    $this->arr_validated_alters = array_fill(0 , count($this->arr_tainted_alters), ' ' );
    //  no saved alterations or saved messages
    if( $this->arr_tainted_alters[0] == null || $this->arr_tainted_alters[0] == ' '){
        return $this->view->render($response,
            'alert_layout.html.twig',
            [
                'css_path' => CSS_PATH,
                'landing_page' => LANDING_PAGE,
                'method' => 'post',
                'action' => '../commands',
                'initial_input_box_value' => null,


                'page_heading_2' => $this->validator_obj->sanitise_string('No Saved Messages'),
                'page_text' => $this->validator_obj->sanitise_string('Download Messages and then Save Some'),
            ]);
    }
    //var_dump($this->arr_tainted_alters);
    //var_dump($this->session_obj->perform_detail_retrieval('saved'));
    for( $i = 0; $i < count($this->arr_tainted_alters); ++$i){
        if($this->arr_tainted_alters[$i] <= count($this->arr_tainted_messages) && $this->arr_tainted_alters[$i] >= 0 && $this->arr_tainted_alters[$i] != '')
            $this->arr_validated_alters[$i] = (int)$this->arr_tainted_alters[$i];
    }

    $total_pages = ceil(count($this->arr_validated_alters) / 10);
    //$total_pages = 5;
    $messages_count = 10;
    if(count($this->arr_validated_alters) > 10) $messages_count = count($this->arr_validated_alters);
    $this->arr_validated_messages = array_fill(0, $messages_count, $this->arr_empty_value);
    for($i = 0; $i < ($total_pages * 10); ++$i){
        if($i < count($this->arr_validated_alters) && $this->arr_validated_alters[$i] != ''
            && $this->arr_validated_alters[$i] != ' ' && $this->arr_validated_alters[$i] != null ){
            $this->arr_validated_messages[$i] =  explode(',', $this->arr_tainted_messages[$this->arr_validated_alters[$i]]);
        }
        else{   $this->arr_validated_messages[$i] =  $this->arr_empty_value;   }
    }
    //  similar to the commands/save functionality, but instead it removes the selected messages from the saved indicies
    if(isset($this->arr_tainted_params['source']) && isset($this->arr_tainted_params['alters'])
        && stristr($this->arr_tainted_params['source'], 'save')){
        $this->arr_alters = explode(',', $this->validator_obj->sanitise_string($this->arr_tainted_params['alters']));
        for($i = 0; $i < count($this->arr_validated_alters); ++$i){
            foreach($this->arr_alters as $alt){
                if($alt == $this->arr_validated_alters[$i] + 1 && (count($this->arr_alters) == 1 || count($this->arr_validated_alters) == 1 )){
                    unset($this->arr_validated_alters[$i]);
                    $this->arr_validated_alters = [' '];
                    $this->arr_validated_alters = array_filter($this->arr_validated_alters, 'is_int');
                }
                else if($alt == $this->arr_validated_alters[$i] + 1){
                    $this->arr_validated_alters[$i] = ' ';
                    unset($this->arr_validated_alters[$i]);
                    $this->arr_validated_alters = array_filter($this->arr_validated_alters, 'is_int');
                }
            }
        }
        //var_dump($this->arr_validated_alters);
        if(count($this->arr_validated_alters) > 0)
            $this->index_alters = implode(',', $this->arr_validated_alters);
        else
            $this->index_alters = [' '];
        $this->session_obj->set_session_saved_indices($this->index_alters);
        $this->session_obj->store_secure_data();
        return $this->view->render($response,
            'alert_layout.html.twig',
            [
                'css_path' => CSS_PATH,
                'landing_page' => LANDING_PAGE,
                'method' => 'post',
                'action' => '../commands',
                'initial_input_box_value' => null,


                'page_heading_2' => $this->validator_obj->sanitise_string('Removed Selected Messages'),
                'page_text' => $this->validator_obj->sanitise_string('Your selected messages have been successfully removed'),
            ]);
    }
    if(isset($this->arr_tainted_params['source']) && isset($this->arr_tainted_params['last_page'])){
        $change = 0;
        if(stristr($this->arr_tainted_params['source'], 'next') && $this->arr_tainted_params['last_page'] < $total_pages)
            $change = +1;
        else if(stristr($this->arr_tainted_params['source'], 'last') && $this->arr_tainted_params['last_page'] > 1)
            $change = -1;
        $current_page = (int)$this->arr_tainted_params['last_page'] + $change;
    }
    else{
        $current_page = 1;
    }
    //  if were on page 1 -> 1-1 = 0*10 + 0 = 0, display ten messages from starting from index 0,
    //  if were on page 2 -> 2-1 = 1*10 + 0 = 10, display ten messages from starting from index 10,
    //  if were on page 3 -> 3-1 = 2*10 + 0 = 20, display ten messages from starting from index 20,
    //  etc
    $this->index_start = 0 + (($current_page - 1) * 10);
    echo $this->validator_obj->sanitise_string($this->arr_tainted_params['source']);
    echo $this->validator_obj->sanitise_string($this->arr_tainted_params['last_page']);
    $arr_message_index = array_fill(0, $messages_count, '-');
    for($i = 0; $i < 10; ++$i){
        if($this->arr_validated_alters[$i] != ' ' && $this->arr_validated_alters[$i] != -1 && $this->arr_validated_alters[$i] != 0 ) {
            $arr_message_index[$i] =  $this->arr_validated_alters[$i] + 1;
        }
    }
    /*
        //  if the first message index is '-' then there were no saved messages, tell the user to save some messages
        if( $arr_message_index[0] == '-'){
            return $this->view->render($response,
                'alert_layout.html.twig',
                [
                    'css_path' => CSS_PATH,
                    'landing_page' => LANDING_PAGE,
                    'method' => 'post',
                    'action' => '../commands',
                    'initial_input_box_value' => null,
                    'page_title' => APP_NAME,
                    'page_heading_1' => APP_NAME,
                    'page_heading_2' => $this->validator_obj->sanitise_string('No Saved Messages'),
                    'page_text' => $this->validator_obj->sanitise_string('Download Messages and then Save Some'),
                ]);
        }
    */
    return $this->view->render($response,
        'table_layout.html.twig',
        [
            'css_path' => CSS_PATH,
            'landing_page' => LANDING_PAGE,
            'method_post' => 'post',
            'action_back' => '../commands',
            'action_next' => '../commands/saved',
            'action_last' => '../commands/saved',
            'action_save' => '../commands/saved',
            'initial_input_box_value' => null,


            'page_heading_2' => $this->validator_obj->sanitise_string('Saved Messages'),
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'Selected_Messages' => $this->validator_obj->sanitise_string('Remove Messages'),
            'Zero_Indicator' =>     $this->validator_obj->sanitise_string($arr_message_index[0]),
            'Zero_Destination' =>   $this->arr_validated_messages[$this->index_start][0],
            'Zero_Timestamp' =>     $this->arr_validated_messages[$this->index_start][1],
            'Zero_Switches_Zero' => $this->arr_validated_messages[$this->index_start][2],
            'Zero_Switches_One' =>  $this->arr_validated_messages[$this->index_start][3],
            'Zero_Switches_Two' =>  $this->arr_validated_messages[$this->index_start][4],
            'Zero_Switches_Three'=> $this->arr_validated_messages[$this->index_start][5],
            'Zero_Fan' =>           $this->arr_validated_messages[$this->index_start][6],
            'Zero_Temperature' =>   $this->arr_validated_messages[$this->index_start][7],
            'Zero_Keypad' =>        $this->arr_validated_messages[$this->index_start][8],
            'One_Indicator' =>     $this->validator_obj->sanitise_string($arr_message_index[1]),
            'One_Destination' =>    $this->arr_validated_messages[$this->index_start + 1][0],
            'One_Timestamp' =>      $this->arr_validated_messages[$this->index_start + 1][1],
            'One_Switches_Zero' =>  $this->arr_validated_messages[$this->index_start + 1][2],
            'One_Switches_One' =>   $this->arr_validated_messages[$this->index_start + 1][3],
            'One_Switches_Two' =>   $this->arr_validated_messages[$this->index_start + 1][4],
            'One_Switches_Three' => $this->arr_validated_messages[$this->index_start + 1][5],
            'One_Fan' =>            $this->arr_validated_messages[$this->index_start + 1][6],
            'One_Temperature' =>    $this->arr_validated_messages[$this->index_start + 1][7],
            'One_Keypad' =>         $this->arr_validated_messages[$this->index_start + 1][8],
            'Two_Indicator' =>     $this->validator_obj->sanitise_string($arr_message_index[2]),
            'Two_Destination' =>    $this->arr_validated_messages[$this->index_start + 2][0],
            'Two_Timestamp' =>      $this->arr_validated_messages[$this->index_start + 2][1],
            'Two_Switches_Zero' =>  $this->arr_validated_messages[$this->index_start + 2][2],
            'Two_Switches_One' =>   $this->arr_validated_messages[$this->index_start + 2][3],
            'Two_Switches_Two' =>   $this->arr_validated_messages[$this->index_start + 2][4],
            'Two_Switches_Three' => $this->arr_validated_messages[$this->index_start + 2][5],
            'Two_Fan' =>            $this->arr_validated_messages[$this->index_start + 2][6],
            'Two_Temperature' =>    $this->arr_validated_messages[$this->index_start + 2][7],
            'Two_Keypad' =>         $this->arr_validated_messages[$this->index_start + 2][8],
            'Three_Indicator' =>     $this->validator_obj->sanitise_string($arr_message_index[3]),
            'Three_Destination' =>    $this->arr_validated_messages[$this->index_start + 3][0],
            'Three_Timestamp' =>      $this->arr_validated_messages[$this->index_start + 3][1],
            'Three_Switches_Zero' =>  $this->arr_validated_messages[$this->index_start + 3][2],
            'Three_Switches_One' =>   $this->arr_validated_messages[$this->index_start + 3][3],
            'Three_Switches_Two' =>   $this->arr_validated_messages[$this->index_start + 3][4],
            'Three_Switches_Three' => $this->arr_validated_messages[$this->index_start + 3][5],
            'Three_Fan' =>            $this->arr_validated_messages[$this->index_start + 3][6],
            'Three_Temperature' =>    $this->arr_validated_messages[$this->index_start + 3][7],
            'Three_Keypad' =>         $this->arr_validated_messages[$this->index_start + 3][8],
            'Four_Indicator' =>     $this->validator_obj->sanitise_string($arr_message_index[4]),
            'Four_Destination' =>    $this->arr_validated_messages[$this->index_start + 4][0],
            'Four_Timestamp' =>      $this->arr_validated_messages[$this->index_start + 4][1],
            'Four_Switches_Zero' =>  $this->arr_validated_messages[$this->index_start + 4][2],
            'Four_Switches_One' =>   $this->arr_validated_messages[$this->index_start + 4][3],
            'Four_Switches_Two' =>   $this->arr_validated_messages[$this->index_start + 4][4],
            'Four_Switches_Three' => $this->arr_validated_messages[$this->index_start + 4][5],
            'Four_Fan' =>            $this->arr_validated_messages[$this->index_start + 4][6],
            'Four_Temperature' =>    $this->arr_validated_messages[$this->index_start + 4][7],
            'Four_Keypad' =>         $this->arr_validated_messages[$this->index_start + 4][8],
            'Five_Indicator' =>     $this->validator_obj->sanitise_string($arr_message_index[5]),
            'Five_Destination' =>    $this->arr_validated_messages[$this->index_start + 5][0],
            'Five_Timestamp' =>      $this->arr_validated_messages[$this->index_start + 5][1],
            'Five_Switches_Zero' =>  $this->arr_validated_messages[$this->index_start + 5][2],
            'Five_Switches_One' =>   $this->arr_validated_messages[$this->index_start + 5][3],
            'Five_Switches_Two' =>   $this->arr_validated_messages[$this->index_start + 5][4],
            'Five_Switches_Three' => $this->arr_validated_messages[$this->index_start + 5][5],
            'Five_Fan' =>            $this->arr_validated_messages[$this->index_start + 5][6],
            'Five_Temperature' =>    $this->arr_validated_messages[$this->index_start + 5][7],
            'Five_Keypad' =>         $this->arr_validated_messages[$this->index_start + 5][8],
            'Six_Indicator' =>     $this->validator_obj->sanitise_string($arr_message_index[6]),
            'Six_Destination' =>    $this->arr_validated_messages[$this->index_start + 6][0],
            'Six_Timestamp' =>      $this->arr_validated_messages[$this->index_start + 6][1],
            'Six_Switches_Zero' =>  $this->arr_validated_messages[$this->index_start + 6][2],
            'Six_Switches_One' =>   $this->arr_validated_messages[$this->index_start + 6][3],
            'Six_Switches_Two' =>   $this->arr_validated_messages[$this->index_start + 6][4],
            'Six_Switches_Three' => $this->arr_validated_messages[$this->index_start + 6][5],
            'Six_Fan' =>            $this->arr_validated_messages[$this->index_start + 6][6],
            'Six_Temperature' =>    $this->arr_validated_messages[$this->index_start + 6][7],
            'Six_Keypad' =>         $this->arr_validated_messages[$this->index_start + 6][8],
            'Seven_Indicator' =>     $this->validator_obj->sanitise_string($arr_message_index[7]),
            'Seven_Destination' =>    $this->arr_validated_messages[$this->index_start + 7][0],
            'Seven_Timestamp' =>      $this->arr_validated_messages[$this->index_start + 7][1],
            'Seven_Switches_Zero' =>  $this->arr_validated_messages[$this->index_start + 7][2],
            'Seven_Switches_One' =>   $this->arr_validated_messages[$this->index_start + 7][3],
            'Seven_Switches_Two' =>   $this->arr_validated_messages[$this->index_start + 7][4],
            'Seven_Switches_Three' => $this->arr_validated_messages[$this->index_start + 7][5],
            'Seven_Fan' =>            $this->arr_validated_messages[$this->index_start + 7][6],
            'Seven_Temperature' =>    $this->arr_validated_messages[$this->index_start + 7][7],
            'Seven_Keypad' =>         $this->arr_validated_messages[$this->index_start + 7][8],
            'Eight_Indicator' =>     $this->validator_obj->sanitise_string($arr_message_index[8]),
            'Eight_Destination' =>    $this->arr_validated_messages[$this->index_start + 8][0],
            'Eight_Timestamp' =>      $this->arr_validated_messages[$this->index_start + 8][1],
            'Eight_Switches_Zero' =>  $this->arr_validated_messages[$this->index_start + 8][2],
            'Eight_Switches_One' =>   $this->arr_validated_messages[$this->index_start + 8][3],
            'Eight_Switches_Two' =>   $this->arr_validated_messages[$this->index_start + 8][4],
            'Eight_Switches_Three' => $this->arr_validated_messages[$this->index_start + 8][5],
            'Eight_Fan' =>            $this->arr_validated_messages[$this->index_start + 8][6],
            'Eight_Temperature' =>    $this->arr_validated_messages[$this->index_start + 8][7],
            'Eight_Keypad' =>         $this->arr_validated_messages[$this->index_start + 8][8],
            'Nine_Indicator' =>     $this->validator_obj->sanitise_string($arr_message_index[9]),
            'Nine_Destination' =>    $this->arr_validated_messages[$this->index_start + 9][0],
            'Nine_Timestamp' =>      $this->arr_validated_messages[$this->index_start + 9][1],
            'Nine_Switches_Zero' =>  $this->arr_validated_messages[$this->index_start + 9][2],
            'Nine_Switches_One' =>   $this->arr_validated_messages[$this->index_start + 9][3],
            'Nine_Switches_Two' =>   $this->arr_validated_messages[$this->index_start + 9][4],
            'Nine_Switches_Three' => $this->arr_validated_messages[$this->index_start + 9][5],
            'Nine_Fan' =>            $this->arr_validated_messages[$this->index_start + 9][6],
            'Nine_Temperature' =>    $this->arr_validated_messages[$this->index_start + 9][7],
            'Nine_Keypad' =>         $this->arr_validated_messages[$this->index_start + 9][8],
        ]);
})->setName('saved');
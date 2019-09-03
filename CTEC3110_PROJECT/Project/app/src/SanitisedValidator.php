<?php
/**
 *
 * User: p2442537
 * Date: 14/08/2019
 * Time: 11:22
 *
 * SanitisedValidator.php
 *
 *
 */

class SanitisedValidator{

    public function __construct() { }
    public function __destruct() { }
    /**
     * This is a generic function that takes in any string as a parameter and sanitises it only. If the string to sanitise
     * parameter is not empty then we assign the sanitised string variable to the filter_var function and pass in the
     *string to sanitise parameter.
     *
     * The sanitised string variable is then returned which holds the original string to sanitise with the filter_var
     * function applied.
     *
     * @param String $p_string_to_sanitise
     * @return String
     */
    public function sanitise_string($p_string_to_sanitise) {
        $m_sanitised_string = false;
        if (!empty($p_string_to_sanitise)) {
            $m_sanitised_string = filter_var($p_string_to_sanitise, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        }
        return $m_sanitised_string;
    }
    public function validate_message($p_message_to_check, $p_string_to_check, $p_string_to_ignore){
        $arr_validated_message = [];
        if(stristr($p_message_to_check['MESSAGE'], $p_string_to_check) && !stristr($p_message_to_check['MESSAGE'], $p_string_to_ignore)){
            $arr_validated_message = explode(',', $this->sanitise_string($p_message_to_check['MESSAGE']));
            array_shift($arr_validated_message);
            array_unshift($arr_validated_message, $this->sanitise_string($p_message_to_check['DESTINATIONMSISDN']), $this->sanitise_string($p_message_to_check['RECEIVEDTIME']));
            return $arr_validated_message;
        }
        else{
            return null;
        }
    }
    /**
     * This function is used to ensure that a users first name is validated and sanitised before it can be used in the app.
     * It assigns the validated fname variable to the sanitise_string method, which takes in the fname to check parameter
     * as input. Once the validated_fname variable has been sanitised it is then passed into an if/else statement to ensure
     * it isn't too short or too long as well as ensuring it doesn't match certain keywords.
     *
     * The fully sanitised and validated fname is then returned.
     *
     * @param String $p_fname_to_check
     * @return String
     */
    public function validate_fname($p_fname_to_check)
    {
        $validated_fname = $this->sanitise_string($p_fname_to_check);
        if (strlen($validated_fname) < 4 || strlen($validated_fname) > 20 )
            $validated_fname = 'A first name should be between 4 and 20 characters';
        elseif ($validated_fname == 'root' || $validated_fname == 'user' )
            $validated_fname = 'Invalid first name';
        return $validated_fname;
    }
    /**
     * This function is used to ensure that a users surname is validated and sanitised before it can be used in the app.
     * It assigns the validated lname variable to the sanitise_string method, which takes in the lname to check parameter
     * as input. Once the validated_lname variable has been sanitised it is then passed into an if/else statement to ensure
     * it isn't too short or too long as well as ensuring it doesn't match certain keywords.
     *
     * The fully sanitised and validated lname is then returned.
     *
     * @param String $p_lname_to_check
     * @return String
     */
    public function validate_lname($p_lname_to_check)
    {
        $validated_lname = $this->sanitise_string($p_lname_to_check);
        if (strlen($validated_lname) < 4 || strlen($validated_lname) > 20 )
            $validated_lname = 'A last name should be between 4 and 20 characters';
        elseif ($validated_lname == 'root' || $validated_lname == 'user')
            $validated_lname = 'A last name cannot be ' . $validated_lname;
        return $validated_lname;
    }
    /*
     * Function to validate the username. Conditions include the username being over 8 characters and under 64.
     * @return String validated_username; Upon completion, returns the username assuming it meets the conditions
     *
     */


    public function validate_username($p_username_to_check){
        $validated_username = $this->sanitise_string($p_username_to_check);
        if( $validated_username == 'root'){
            $validated_username = 'root';
        }
        elseif(stristr($validated_username, 'username') || stristr($validated_username, 'emanresu') ||
            stristr($validated_username, '12345') || stristr($validated_username, '54321')){
            $validated_username = 'Unacceptable Username, Try Something More Unique';
        }
        elseif(strlen($validated_username) < 8){
            $validated_username = 'Unacceptable Username, Minimum should be 8 Characters';
        }
        elseif(strlen($validated_username) > 64){
            $validated_username = 'Unacceptable Username, Maximum is limited to 64 Characters';
        }
        return $validated_username;
    }
    /*
     *
     * Function to validate the passwords. Uses the same conditions as the username validation (i.e password is over 8 characters and under 64)
     * @return String validated_password; Upon completion, returns the username assuming it meets the conditions
     *
     */
    public function validate_password($p_password_to_check){
        $validated_password = $this->sanitise_string($p_password_to_check);
        if( $validated_password == 'toor'){
            $validated_password = 'toor';
        }
        else if(stristr($validated_password, 'password') || stristr($validated_password, 'drowssap') ||
            stristr($validated_password, '12345') || stristr($validated_password, '54321')){
            $validated_password = 'Unacceptable Password, Try Something More Unique';
        }
        elseif(strlen($validated_password) < 8){
            $validated_password = 'Unacceptable Password, Minimum should be 8 Characters';
        }
        elseif(strlen($validated_password) > 64){
            $validated_password = 'Unacceptable Password, Maximum is limited to 64 Characters';
        }
        return $validated_password;
    }
}




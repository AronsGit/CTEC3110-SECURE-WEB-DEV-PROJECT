<?php
/**
 * ProfileModel.php
 *
 * The purpose of this class is to create a profile for each user which signs up to the application.
 */

class ProfileModel
{

    /** @var Boolean $c_result. Deprecated code  */
    private $c_result;
    /** @var String $c_username The username a user signs up with */
    private $c_username;
    /** @var String $c_password The password a user signs up with */
    private $c_password;
    /** @var String fname The first name a user signs up with */
    private $c_fname;
    /** @var String $c_lname The surname a user signs up with */
    private $c_lname;
    /** @var String $c_validated. Variable to ensure the session file has not been tampered with */
    private $c_validated;
    /** @var  WHAT TYPE $c_obj_xml_parser Deprecated   */
    private $c_obj_xml_parser;
    public function __construct(){}
    public function __destruct(){}
    /**
     * This function is used to set the users details with the given parameters.
     *
     * @param String $p_username
     * @param String $p_password
     * @param String $p_fname
     * @param String $p_lname
     */
    public function set_parameters($p_username, $p_password, $p_fname, $p_lname)
    {
        $this->c_username = $p_username;
        $this->c_password = $p_password;
        $this->c_fname = $p_fname;
        $this->c_lname = $p_lname;
        $this->c_validated = session_id();

    }
    /** This function initialises the class xml_parser object to the given parameter xml_parser object  */
    public function set_xml_parser($p_obj_xml_parser)
    {
        $this->c_obj_xml_parser = $p_obj_xml_parser;
    }
    /**
     * The purpose of this function is used to retrieve a particular detail of a user.
     * If the passed in parameter is equal to the String 'username' then return the set class username variable, else
     * it it's equal to 'password' then return the set class password, else if it's equal to 'fname' then return the
     * set class first name, else if it's equal to 'lname' then return the set class surname and finally if it's equal
     * to 'validated'.
     *
     * @param String $p_detail
     * @return String
     */


    /*
     *
     * @param String c_username
     * @param String c_password
     * @param String c_fname
     * @param String c_lname
     * @param String c_validated
     *
     */


    public function perform_detail_retrieval($p_detail){
        if($p_detail == 'username'){
            return $this->c_username;
        }
        else if($p_detail == 'password'){
            return $this->c_password;
        }
        else if($p_detail == 'fname'){
            return $this->c_fname;
        }
        else if($p_detail == 'lname'){
            return $this->c_lname;
        }
        else if($p_detail == 'validated'){
            return $this->c_validated;
        }
    }
    public function get_result()
    {
        return true;
    }

}
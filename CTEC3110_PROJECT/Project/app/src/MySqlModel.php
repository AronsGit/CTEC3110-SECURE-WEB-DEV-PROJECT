<?php
/**
 * Created by PhpStorm.
 * User: p2442537

 */

class MySqlModel
{

    private $c_id;
    private $c_username;
    private $c_password;
    private $c_fullname;
    private $c_obj_bcrypt_wrapper;
    private $c_obj_base64_wrapper;
    private $c_obj_openssl_wrapper;
    private $c_saved_indicies;
    /** @var Object $c_obj_mysql_wrapper. Interfaces the MYsql queries*/
    private $c_obj_mysql_wrapper;
    /** @var Object $c_obj_db_handle Holds a reference to the database  */
    private $c_obj_db_handle;
    /** @var Object $c_obj_sql_queries This is used to access SQL queries from the MySQL class  */
    private $c_obj_sql_queries;
    /**
     * Class constructor that creates an instance of the MySQLModel class
     *
     * Initialises the username, password, first name, surname, session file Object and the profile model object to
     * null
     */
    public function __construct()
    {
        $this->c_username = null;
        $this->c_password = null;
        $this->c_fullname = null;
        $this->c_fname = null;
        $this->c_lname = null;
        $this->c_obj_wrapper_session_file = null;
        $this->c_obj_profile_model = null;
    }
    public function __destruct() { }
    public function perform_detail_retrieval($p_detail){
        /** This acts as a multi-getter, where necessary information can be extracted from this object */
        if($p_detail == 'username'){
            //$this->c_username = c_id;
            return $this->c_username;
        }
        else if($p_detail == 'password'){
            return $this->c_password;
        }
        else if($p_detail == 'fullname'){
            return $this->c_fullname;
        }
    }
    public function set_database_id($p_username){
        //$this->c_id = $this->c_obj_base64_wrapper->encode_base64($this->c_obj_openssl_wrapper->encrypt($this->c_username, OPENSSL_DB_KEY));
        $this->c_id = $p_username;
    }
    public function set_database_profile($p_profile){
        $this->c_username = $p_profile->perform_detail_retrieval('username');
        $this->c_password = $p_profile->perform_detail_retrieval('password');
        $arr_fullname = array($p_profile->perform_detail_retrieval('fname'), $p_profile->perform_detail_retrieval('lname'));
        $this->c_fullname = implode(',', $arr_fullname);
        $this->set_database_id($this->c_username);
        /** This merely sets the session model's information by retrieving it from the profile model */
    }
    public function set_bcrypt_wrapper($p_bcrypt_wrapper){
        $this->c_obj_bcrypt_wrapper = $p_bcrypt_wrapper;
    }
    public function set_base64_wrapper($p_base64_wrapper){
        $this->c_obj_base64_wrapper = $p_base64_wrapper;
    }
    public function set_openssl_wrapper($p_openssl_wrapper){
        $this->c_obj_openssl_wrapper = $p_openssl_wrapper;
    }
    /**
     * Function to set the MySQL wrapper.
     *
     * @param Object $p_obj_mysql_wrapper
     */
    public function set_mysql_wrapper($p_obj_mysql_wrapper)
    {
        $this->c_obj_mysql_wrapper = $p_obj_mysql_wrapper;
    }
    /**
     * A function that sets a reference to the database. It takes a database object handle as a parameter and assigns
     * it to the mysql wrapper class variable
     *
     * @param Object $p_obj_db_handle
     */
    public function set_db_handle($p_obj_db_handle)
    {
        $this->c_obj_db_handle = $p_obj_db_handle;
    }
    /**
     * A function that sets the sql queries to be used on the database. It takes an sql queries object as a parameter
     * and assigns it to sql queries class variable
     *
     * @param Object $p_obj_sql_queries
     */
    public function set_sql_queries($p_obj_sql_queries)
    {
        $this->c_obj_sql_queries = $p_obj_sql_queries;
    }
    public function store_secure_data(){
        //    The password is both hashed and encrypted, although this makes it overlong, it is a ngeligible cost
        //$this->c_username = $this->c_obj_base64_wrapper->encode_base64($this->c_obj_openssl_wrapper->encrypt($this->c_username, OPENSSL_DB_KEY));
        $this->c_password = $this->c_obj_base64_wrapper->encode_base64($this->c_obj_openssl_wrapper->encrypt($this->c_obj_bcrypt_wrapper->create_hashed_password($this->c_password), OPENSSL_DB_KEY));
        $this->c_fullname = $this->c_obj_base64_wrapper->encode_base64($this->c_obj_openssl_wrapper->encrypt($this->c_fullname, OPENSSL_DB_KEY));
        //$this->c_saved_indicies = $this->c_obj_base64_wrapper->encode_base64($this->c_obj_openssl_wrapper->encrypt($this->c_saved_indicies, OPENSSL_DB_KEY));
        return $this->store_data_in_database();
        /** This first encrypts, then encodes most information
         * Except for the password, which is first hashed, then encrypted, then encoded
         */
    }
    /**
     * A function that assigns a database for data to be stored and the sql queries to be carried out on that database
     * in order to store the data in the given database.
     *
     * It initialises the store result variable to false and takes the username and password class variables as parameters
     * to the store_session_var function and assigns it to the store result variable.
     *
     * The store result variable is returned which now holds the data to be stored in the database
     *
     * @return bool
     */
    private function store_data_in_database()  {
        $m_store_result = false;
        //$this->c_obj_mysql_wrapper->set_db_handle( $this->c_obj_db_handle);
        //$this->c_obj_mysql_wrapper->set_sql_queries( $this->c_obj_sql_queries);
        //$m_store_result_username = $this->c_obj_mysql_wrapper->store_database_var($this->c_id, 'user_username', $this->c_username);
        $m_store_result_password = $this->c_obj_mysql_wrapper->store_database_var($this->c_id, 'user_password', $this->c_password);
        $m_store_result_fullname = $this->c_obj_mysql_wrapper->store_database_var($this->c_id, 'user_fullname', $this->c_fullname);
        //$m_store_saved_indicies = $this->c_obj_mysql_wrapper->store_database_var($this->c_id, 'user_saved', $this->c_saved_indicies);
        if ($m_store_result_password !== false &&
            $m_store_result_fullname !== false)	{
            $m_store_result = true;
        }
        return $m_store_result;
    }
    public function check_username_database(){
        return $this->c_obj_mysql_wrapper->check_database_var($this->c_id, 'user_password');
    }
    public function retrieve_secure_data(){
        $result = $this->retrieve_data_in_database();
        //    The password is both hashed and encrypted, although this makes it overlong, it is at a negligible cost
        //$this->c_username = $this->c_obj_openssl_wrapper->decrypt($this->c_obj_base64_wrapper->decode_base64($this->c_username), OPENSSL_DB_KEY);
        $this->c_password = $this->c_obj_openssl_wrapper->decrypt($this->c_obj_base64_wrapper->decode_base64($this->c_password), OPENSSL_DB_KEY);
        $this->c_fullname = $this->c_obj_openssl_wrapper->decrypt($this->c_obj_base64_wrapper->decode_base64($this->c_fullname), OPENSSL_DB_KEY);
        //$this->c_saved_indicies = $this->c_obj_openssl_wrapper->decrypt($this->c_obj_base64_wrapper->decode_base64($this->c_saved_indicies), OPENSSL_DB_KEY);
        return $result;
        /** This first encrypts, then encodes most information
         * Except for the password, which is first hashed, then encrypted, then encoded
         */
    }
    private function retrieve_data_in_database(){
        //$this->c_password = '';
        //$this->c_fullname = '';
        //$this->c_saved_indicies = '';
        $m_retrieve_result = false;
        //$m_retrieve_result_username = $this->c_obj_mysql_wrapper->retrieve_database_var($this->c_id, 'user_username');
        $this->c_password = $this->c_obj_mysql_wrapper->retrieve_database_var($this->c_id, 'user_password');
        $this->c_fullname = $this->c_obj_mysql_wrapper->retrieve_database_var($this->c_id, 'user_fullname');
        //$this->c_saved_indicies = $this->c_obj_mysql_wrapper->retrieve_database_var($this->c_id, 'user_saved');
        //var_dump($this->c_password);
        //var_dump($this->c_obj_mysql_wrapper);
        //var_dump($this->c_obj_mysql_wrapper->retrieve_database_var($this->c_id, 'user_password'));
        if ($this->c_password != null)	{
            //$this->c_username = $m_retrieve_result_username;
            //$this->c_password = $m_retrieve_result_password;
            //$this->c_fullname = $m_retrieve_result_fullname;
            //$this->c_saved_indicies = $m_retrieve_result_saved;
            $m_retrieve_result = true;
        }
        return $m_retrieve_result;
    }





}
-=<?php
/**
 * BcryptWrapper.php
 *
 * This is a wrapper class that takes a String password and hashes it it for storage, it also ensures that a String
 * password matches a created hash value.
 */
class BcryptWrapper
{
    public function __construct(){}
    public function __destruct(){}
    /**
     * This function takes a string to hash as a parameter, and assigns the password to hash variable to the string to
     * hash parameter. The bcrypt hashed password variable is initialised to an empty String.
     *
     * If the password to hash is not empty then we create an array (NOT SURE WHY) and assign the bcrypt hashed password
     * to a password_hash function that takes the password to hash, the bcrypt alogorthim and an array value.
     *
     * The bcrypt hashed password is then returned, which now holds the original password to hash but with hashing
     * applied.
     *
     * @param String $p_string_to_hash
     * @return String
     *
     */
    public function create_hashed_password($p_string_to_hash)
    {
        $password_to_hash = $p_string_to_hash;
        $bcrypt_hashed_password = '';
        if (!empty($password_to_hash))
        {
            $arr_options = array('cost' => BCRYPT_COST);
            $bcrypt_hashed_password = password_hash($password_to_hash, BCRYPT_ALGO, $arr_options);
        }
        return $bcrypt_hashed_password;
    }
    /**
     * This function takes a string to check and a stored hashed user password as a parameter, and initialises the
     * user authenticated variable to false. The current user password variable is also assigned to the string to check
     * and the stored user password hash is assigned to the stored user password hash parameter.
     *
     * If the current user password and the stored user password is not empty then we use the password_verify function
     * to see if the current user password and the stored user password hash match, If they do we then update the user
     * authenticated variable to be true.
     *
     * The user authenticated variable is returned, which has the value of true.
     *
     * @param String $p_string_to_check
     * @param String $p_stored_user_password_hash
     * @return Boolean
     *
     */
    public function authenticate_password($p_string_to_check, $p_stored_user_password_hash)
    {
        $user_authenticated = false;
        $current_user_password = $p_string_to_check;
        $stored_user_password_hash = $p_stored_user_password_hash;
        if (!empty($current_user_password) && !empty($stored_user_password_hash))
        {
            if (password_verify($current_user_password, $stored_user_password_hash))
            {
                $user_authenticated = true;
            }
        }
        return $user_authenticated;
    }
}
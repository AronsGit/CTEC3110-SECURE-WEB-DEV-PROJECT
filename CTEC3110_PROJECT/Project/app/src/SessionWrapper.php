<?php
/**
 * SessionWrapper.php
 *
 * create a wrapper for the SESSION global array
 *
 * Author: CF Ingrams
 * Email: <clinton@cfing.co.uk>
 * Date: 22/10/2017
 *
 *
 * @author CF Ingrams <clinton@cfing.co.uk>
 * @copyright CFI
 *
 */
class SessionWrapper
{
    public function __construct()
    {
    }

    public function __destruct()
    {
    }

    /*
     * Function to save into a session file using @param session_key and the session_valey_to_set as a parameter (key: username, value: value)
     *
     */

    public static function set_session($p_session_key, $p_session_value_to_set)
    {
        $m_session_value_set_successfully = false;
        if (!empty($p_session_value_to_set)) {
            //session_save_path($session_path);
            $_SESSION[$p_session_key] = $p_session_value_to_set;
            if (strcmp($_SESSION[$p_session_key], $p_session_value_to_set) == 0) {
                $m_session_value_set_successfully = true;
            }
        }
        return $m_session_value_set_successfully;
    }

    /*
     * Get_session is a function to get the session key using @param session_key if the session value is false. Returns a value on completion
     */
    public static function get_session($p_session_key)
    {
        $m_session_value = false;
        if (isset($_SESSION[$p_session_key])) {
            $m_session_value = $_SESSION[$p_session_key];
        }
        return $m_session_value;
    }

    /*
     * Function to end a session by unsetting already existing keys to a session. Effectively ending the session
     */

    public static function unset_session($p_session_key)
    {
        $m_unset_session = false;
        if (isset($_SESSION[$p_session_key])) {
            unset($_SESSION[$p_session_key]);
        }
        if (!isset($_SESSION[$p_session_key])) {
            $m_unset_session = true;
        }
        return $m_unset_session;
    }

}
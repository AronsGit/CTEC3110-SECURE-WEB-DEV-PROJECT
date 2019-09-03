<?php
/**
 * MySQLQueries.php
 *
 * This class provides functions to perform operations on the
 */
class MySqlQueries {
    public function __construct() { }
    public function __destruct() { }

    /*
     * SQL script concatenated and built iteratively to retrieve information from the database. The SQL query itself is returned as a string
     */
    public static function check_database_var()
    {
        $m_query_string  = "SELECT database_value ";
        $m_query_string .= "FROM information_table ";
        $m_query_string .= "WHERE database_id = :database_id ";
        $m_query_string .= "AND database_label = :database_label ";
        $m_query_string .= "LIMIT 1";
        return $m_query_string;
    }
    /*
     * Creates a new row with given column information
     * @return query as a string
     */
    public static function create_database_var()
    {
        $m_query_string  = "INSERT INTO information_table ";
        $m_query_string .= "SET database_id = :database_id, ";
        $m_query_string .= "database_label = :database_label, ";
        $m_query_string .= "database_value = :database_value ";
        return $m_query_string;
    }
    /*
     * Overwrites information as a specific position in the database
     * @return query as a string
     */
    public static function set_database_var()
    {
        $m_query_string  = "UPDATE information_table ";
        $m_query_string .= "SET database_value = :database_value ";
        $m_query_string .= "WHERE database_id = :database_id ";
        $m_query_string .= "AND database_label = :database_label";
        return $m_query_string;
    }
    /*
     * Retrieves information at row and column
     * @return query as a string
     *
     */
    public static function get_database_var()
    {
        $m_query_string  = "SELECT database_value ";
        $m_query_string .= "FROM information_table ";
        $m_query_string .= "WHERE database_id = :database_id ";
        $m_query_string .= "AND database_label = :database_label";
        return $m_query_string;
    }
}

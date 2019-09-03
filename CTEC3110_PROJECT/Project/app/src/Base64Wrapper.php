<?php
/**
 * Base64Wrapper.php
 *
 * This is a wrapper class that allows String values to be encoded and decoded using a base 64 format.
 */
class Base64Wrapper
{
    public function __construct(){}
    public function __destruct(){}
    /**
     * This function takes a string to encode as a parameter, and sets the encoded string variable to false.
     *
     * If the string to encode is not empty then we assign the encoded string variable to the base64_encode
     * function and supply it with the string to encode.
     *
     * The encoded string is returned, which now holds the original string to encode but with it being encoded.
     *
     * @param String $p_string_to_encode
     * @return String
     *
     */
    public function encode_base64($p_string_to_encode)
    {
        $encoded_string = false;
        if (!empty($p_string_to_encode))
        {
            $encoded_string = base64_encode($p_string_to_encode);
        }
        return $encoded_string;
    }
    /**
     * This function takes a string to decode as a parameter, and sets the decoded string variable to false.
     *
     * If the string to decode is not empty then we assign the decoded string variable to the base64_decode
     * function and supply it with the string to decode.
     *
     * The decoded string is returned, which now holds the original string to decode but with it being decoded.
     *
     * @param String $p_string_to_decode
     * @return String
     *
     */
    public function decode_base64($p_string_to_decode)
    {
        $decoded_string = false;
        if (!empty($p_string_to_decode))
        {
            $decoded_string = base64_decode($p_string_to_decode);
        }
        return $decoded_string;
    }
}
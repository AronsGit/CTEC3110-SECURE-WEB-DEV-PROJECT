<?php
/**
 * Created by PhpStorm.
 * User: p2442537
 *
 */
class SoapModel {
    private $c_country;
    private $c_command;
    private $c_result;
    private $c_obj_xml_parser;
    private $c_obj_validator;
    public function __construct(){}
    public function __destruct(){}
    public function set_parameters($p_command)    {
        $this->c_command = $p_command;
    }
    /*
     * XML parser setup
     * @Param uses the parser itself as input
     */
    public function set_xml_parser($p_obj_xml_parser)
    {
        $this->c_obj_xml_parser = $p_obj_xml_parser;
    }
    /*
     * Validator setup
     * @Param uses the parser itself as input
     */
    public function set_validator($p_obj_validator){
        $this->c_obj_validator = $p_obj_validator;
    }
    /*
        public function perform_detail_retrieval()
        {
            $soapresult = null;
            $result = null;
            $obj_soap_client_handle = null;
            $obj_soap_client_handle = $this->create_soap_client();
            if ($obj_soap_client_handle !== false)
            {
                $soapresult = $this->retrieve_detail($obj_soap_client_handle);
            }
            if ($soapresult != null)
            {
                $this->c_obj_xml_parser->set_xml_string_to_parse($soapresult);
                $this->c_obj_xml_parser->parse_the_xml_string();
                $result = $this->c_obj_xml_parser->get_parsed_data();
            }
            $this->c_result = $result;
        }
    */
    /*
     * Creates the soap client. Sets up a connection to the SOAP server using WSDL.
     * @Return returns client handle to the soap server allowing data retrieval and sending. Can call server functions
     *
     */
    private function create_soap_client()
    {
        $obj_soap_client_handle = false;
        $m_arr_soapclient = ['trace' => true, 'exceptions' => true];
        $m_wsdl = 'https://m2mconnect.ee.co.uk/orange-soap/services/MessageServiceByCountry?wsdl';
        try
        {
            $obj_soap_client_handle = new SoapClient($m_wsdl, $m_arr_soapclient);
//      var_dump($obj_soap_client_handle->__getFunctions());
//      var_dump($obj_soap_client_handle->__getTypes());
        }
        catch (SoapFault $m_obj_exception)
        {
            trigger_error($m_obj_exception);
        }
        return $obj_soap_client_handle;
    }
    /*
     * @parameter msdidn serves as the phone number and corresponding message alongside it
     * This function allows the sending of messages from the server
     * @return if an error has occured, returns a soap_fault error.
     *
     */
    public function send_message($p_msisdn, $p_message){
        $obj_soap_client_handle = null;
        $obj_soap_client_handle = $this->create_soap_client();
        $tainted_message = $p_message;
        $validated_message = $this->c_obj_validator->sanitise_string($tainted_message);
//        var_dump($obj_soap_client_handle->__getFunctions());
//        var_dump($obj_soap_client_handle->__getTypes());
        try{
            //$obj_soap_client_handle->__soapCall($soapfunction, [$parameter]);
            $obj_soap_client_handle->sendMessage(m2m_username, m2m_password, $p_msisdn, $validated_message, false, 'SMS');
            return 'message_sent';
        }catch (SoapFault $m_obj_exception) {
            trigger_error($m_obj_exception);
            return 'soap_fault';
        }
    }
    /*
     *  Accepts the phone number (msidn) as a parameter. Peek_messages makes a request to the SOAP server to send messages
     * @Return arr_encoded_messages returns the messages that have been requested in an array of array of strings.
     *
     */
    public function peek_messages($p_msisdn){
        $arr_xml_messages = [];
        $arr_xml_keys = [];
        $arr_encoded_messages = [];
        $obj_soap_client_handle = null;
        $obj_soap_client_handle = $this->create_soap_client();
//        var_dump($obj_soap_client_handle->__getFunctions());
//        var_dump($obj_soap_client_handle->__getTypes());
        try{
            //$obj_soap_client_handle->__soapCall($soapfunction, [$parameter]);
            $arr_xml_messages = $obj_soap_client_handle->peekMessages(m2m_username, m2m_password, $p_msisdn);
            $arr_xml_keys = array_keys($arr_xml_messages);
            for($i = 0; $i < count($arr_xml_messages); ++$i){
                $this->c_obj_xml_parser->set_xml_string_to_parse($arr_xml_messages[$arr_xml_keys[$i]]);
                $this->c_obj_xml_parser->parse_the_xml_string();
                $result = $this->c_obj_xml_parser->get_parsed_data();
//                var_dump($arr_xml_messages[$i]);
//                var_dump($result);
//                var_dump($i);
                array_push($arr_encoded_messages, $result);
            }
            /*
                        foreach($arr_xml_messages as $msg){
                            $this->c_obj_xml_parser->set_xml_string_to_parse($msg);
                            $this->c_obj_xml_parser->parse_the_xml_string();
                            $result = $this->c_obj_xml_parser->get_parsed_data();s
                            array_push($arr_encoded_messages, $result);
                        }
            */
        }catch (SoapFault $m_obj_exception) {
            trigger_error($m_obj_exception);
        }
//        var_dump($arr_xml_messages);
        return $arr_encoded_messages;
    }
    /*
        private function retrieve_detail($p_obj_soap_client_handle)
        {
            $result = null;
            $details = null;
            $parameter = ['CountryName' => $this->c_country];
            $soapfunction = SOAP_COMMANDS[$this->c_command];
    //      $soapfunction = 'GetCountries';
            try
            {
                $details = $p_obj_soap_client_handle->__soapCall($soapfunction, [$parameter]);
                if(isset($details->GetISOCountryCodeByCountyNameResult)){
                    $result = $details->GetISOCountryCodeByCountyNameResult;
                }
                else if (isset($details->GetCurrencyByCountryResult))
                {
                    $result = $details->GetCurrencyByCountryResult;
                }
                else if (isset($details->GetGMTbyCountryResult))
                {
                    $result = $details->GetGMTbyCountryResult;
                }
    //      var_dump($p_obj_soap_client_handle->__getLastRequest());
    //      var_dump($p_obj_soap_client_handle->__getLastResponse());
    //      var_dump($p_obj_soap_client_handle->__getLastRequestHeaders());
    //      var_dump($p_obj_soap_client_handle->__getLastResponseHeaders());
            }
            catch (SoapFault $m_obj_exception)
            {
                trigger_error($m_obj_exception);
            }
            return $result;
        }
    */
    public function get_result()
    {
        return $this->c_result;
    }
    private function select_detail()
    {
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: p2442537
 * Date: 15/08/2019
 * Time: 16:41
 */


// this is the required path nature for the phpunittests to properly function
require_once 'H:/p3t/phpappfolder/public_php/CTEC3110_PROJECT/Project/app/src/SessionModel.php';
require_once 'H:/p3t/phpappfolder/public_php/CTEC3110_PROJECT/Project/app/src/SessionWrapper.php';
require_once 'H:/p3t/phpappfolder/public_php/CTEC3110_PROJECT/Project/app/src/ProfileModel.php';
require_once 'H:/p3t/phpappfolder/public_php/CTEC3110_PROJECT/Project/app/src/Base64Wrapper.php';
require_once 'H:/p3t/phpappfolder/public_php/CTEC3110_PROJECT/Project/app/src/BcryptWrapper.php';
require_once 'H:/p3t/phpappfolder/public_php/CTEC3110_PROJECT/Project/app/settings.php';
use PHPUnit\Framework\TestCase;
final class SessionModelTest extends TestCase{
    public function testSessionSetUp(){
        $session_obj = new SessionModel();
        $profile_obj = new ProfileModel();
        $session_wrapper = new SessionWrapper();
        $profile_obj->set_parameters('username', 'password', 'Robert', 'Paulson');
        $session_obj->set_wrapper_session_file($session_wrapper);
        $session_obj->set_session_profile($profile_obj);
        $retrieved_fname = $session_obj->perform_detail_retrieval('fname');
        $retrieved_lname = $session_obj->perform_detail_retrieval('lname');
        $this->assertEquals($profile_obj->perform_detail_retrieval('fname'), $retrieved_fname, $retrieved_fname);
        $this->assertEquals($profile_obj->perform_detail_retrieval('lname'), $retrieved_lname, $retrieved_lname);
    }
    /**
     * @depends testSessionSetUp
     */
    public function testSessionModelStore(){
        $session_obj = new SessionModel();
        $profile_obj = new ProfileModel();
        $session_wrapper = new SessionWrapper();
        $base64_wrapper = new Base64Wrapper();
        $bcrypt_wrapper = new BcryptWrapper();

        $profile_obj->set_parameters('username', 'password12', 'Robert', 'Paulson');
        $session_obj->set_wrapper_session_file($session_wrapper);
        $session_obj->set_session_profile($profile_obj);
        $session_obj->set_base64_wrapper($base64_wrapper);
        $session_obj->set_bcrypt_wrapper($bcrypt_wrapper);

        $storage_result = $session_obj->store_secure_data();
        $this->assertTrue($storage_result);
    }
    /**
     * @depends testSessionModelStore
     */
    public function testSessionModelRetrieve(){
        $session_obj = new SessionModel();
        $profile_obj = new ProfileModel();
        $session_wrapper = new SessionWrapper();
        $base64_wrapper = new Base64Wrapper();
        $bcrypt_wrapper = new BcryptWrapper();
        $openssl_wrapper = new OpenSSLWrapper();
        $profile_obj->set_parameters('username', 'password12', 'Robert', 'Paulson');
        $session_obj->set_wrapper_session_file($session_wrapper);
        $session_obj->set_session_profile($profile_obj);
        $session_obj->set_base64_wrapper($base64_wrapper);
        $session_obj->set_bcrypt_wrapper($bcrypt_wrapper);

        $session_obj->generate_labels();
        $storage_result = $session_obj->store_secure_data();
        $retrieval_result = $session_obj->retrieve_secure_data();
        $session_fname = $session_obj->perform_detail_retrieval('fname');
        $session_password = $session_obj->perform_detail_retrieval('password');
        $this->assertTrue($retrieval_result);
        $this->assertTrue($bcrypt_wrapper->authenticate_password($profile_obj->perform_detail_retrieval('password'), $session_password));
    }
    /**
     * @depends testSessionModelStore
     */
    public function testSessionArrayHandling(){
        $session_obj = new SessionModel();
        $profile_obj = new ProfileModel();
        $session_wrapper = new SessionWrapper();
        $base64_wrapper = new Base64Wrapper();
        $bcrypt_wrapper = new BcryptWrapper();

        $profile_obj->set_parameters('username', 'password12', 'Robert', 'Paulson');
        $arr_messages = array('123,123,123', '456,456,456');
        $session_obj->set_wrapper_session_file($session_wrapper);
        $session_obj->set_session_profile($profile_obj);
        $session_obj->set_session_array_messages($arr_messages);
        $session_obj->set_base64_wrapper($base64_wrapper);
        $session_obj->set_bcrypt_wrapper($bcrypt_wrapper);

        $session_obj->generate_labels();
        $storage_result = $session_obj->store_secure_data();
        $retrieval_result = $session_obj->retrieve_secure_data();
        $session_fname = $session_obj->perform_detail_retrieval('fname');
        $session_password = $session_obj->perform_detail_retrieval('password');
        $this->assertTrue($retrieval_result);
        $this->assertTrue($bcrypt_wrapper->authenticate_password($profile_obj->perform_detail_retrieval('password'), $session_password));
        $this->assertTrue(count($session_obj->perform_detail_retrieval('messages')) > 1);
    }
}
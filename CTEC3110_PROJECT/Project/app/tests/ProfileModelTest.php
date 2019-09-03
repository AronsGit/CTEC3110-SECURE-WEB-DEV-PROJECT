<?php
/**
 * Created by PhpStorm.
 * User: p2442537
 * Date: 15/08/2019
 * Time: 16:19
 */


// this is the required path nature for the phpunittests to properly function
require_once 'H:/p3t/phpappfolder/public_php/CTEC3110_PROJECT/Project/app/src/ProfileModel.php';
use PHPUnit\Framework\TestCase;
final class ProfileModelTest extends TestCase {
    public function testDetailRetrieval(){
        $arr_profile_details = array('BillyBob12', 'TalladegaNights23', 'Robert', 'Paulson');
        $profile_obj = new ProfileModel();
        $profile_obj->set_parameters($arr_profile_details[0], $arr_profile_details[1], $arr_profile_details[2], $arr_profile_details[3]);
        $this->assertEquals($arr_profile_details[0], $profile_obj->perform_detail_retrieval('username'));
        $this->assertEquals($arr_profile_details[1], $profile_obj->perform_detail_retrieval('password'));
        $this->assertEquals($arr_profile_details[2], $profile_obj->perform_detail_retrieval('fname'));
        $this->assertEquals($arr_profile_details[3], $profile_obj->perform_detail_retrieval('lname'));
        $this->assertEquals(session_id(), $profile_obj->perform_detail_retrieval('validated'));
    }
}
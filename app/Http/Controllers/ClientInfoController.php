<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\ClientInfo;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class ClientInfoController extends Controller
{
    private $AppHelper;
    private $ClientInfo;

    public function __construct()
    {
        $this->AppHelper = new AppHelper();
        $this->ClientInfo = new ClientInfo();
    }

    public function addNewClinetInfo(Request $request) {

        $firstName = (is_null($request->firstName) || empty($request->firstName)) ? "" : $request->firstName;
        $emailAddress = (is_null($request->emailAddress) || empty($request->emailAddress)) ? "" : $request->emailAddress;
        $birthday = (is_null($request->birthday) || empty($request->birthday)) ? "" : $request->birthday;
        $gender = (is_null($request->gender) || empty($request->gender)) ? "" : $request->gender;

        if ($firstName == "") {
            return $this->AppHelper->responseMessageHandle(0, "First Name is required.");
        } else if ($emailAddress == "") {
            return $this->AppHelper->responseMessageHandle(0, "Email address is required.");
        } else if ($birthday == "") {
            return $this->AppHelper->responseMessageHandle(0, "Birthday is required.");
        } else if ($gender == "") {
            return $this->AppHelper->responseMessageHandle(0, "Gender is required.");
        } else {
            try {
                $clientInfo = array();
                $clientInfo['firstName'] = $firstName;
                $clientInfo['email'] = $emailAddress;
                $clientInfo['birthday'] = strtotime($birthday);
                $clientInfo['gender'] = $gender;
                $clientInfo['createTime'] = $this->AppHelper->day_time();

                $res = $this->ClientInfo->add_log($clientInfo);

                if ($res) {
                    return $this->AppHelper->responseEntityHandle(1, "Operaion Successfully.", $res);
                } else {
                    return $this->AppHelper->responseMessageHandle(0, "Error Occured.");
                }
            } catch (Exception $e) {
                return $this->AppHelper->responseMessageHandle(0, "Error Occured. " . $e->getMessage());
            }
        }
    }
}

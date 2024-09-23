<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\ClientInfo;
use App\Models\ClientResponse;
use App\Models\Question;
use Exception;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    private $AppHelper;
    private $QuestionModel;
    private $Client;
    private $ClientResponse;

    public function __construct()
    {
        $this->AppHelper = new AppHelper();
        $this->QuestionModel = new Question();
        $this->Client = new ClientInfo();
        $this->ClientResponse = new ClientResponse();
    }

    public function getAllQuestionList() {
        $question_list = $this->QuestionModel->find_all();

        $formated_questions = array();
        foreach ($question_list as $key => $value) {
            $formated_questions[$key]['questionName'] = $value['question_name'];
            $formated_questions[$key]['categoryName'] = $value['category'];
            $formated_questions[$key]['answers'] = explode(",", $value['answers']);
        }

        return $this->AppHelper->responseEntityHandle(1, "Operation Successfully", $formated_questions);
    }

    public function submitQuestionResponse(Request $request) {

        $client_id = (is_null($request->client_id) || empty($request->client_id)) ? "" : $request->client_id;
        $vata_percentage = (is_null($request->vata_percentage) || empty($request->vata_percentage)) ? "" : $request->vata_percentage;
        $pitta_percentage = (is_null($request->pitta_percentage) || empty($request->pitta_percentage)) ? "" : $request->pitta_percentage;
        $kappa_percentage = (is_null($request->kappa_percentage) || empty($request->kappa_percentage)) ? "" : $request->kappa_percentage;

        if ($client_id == "") {
            return $this->AppHelper->responseMessageHandle(0, "Client id is required.");
        } else if ($vata_percentage == "") {
            return $this->AppHelper->responseMessageHandle(0, "vata Percentage is required.");
        } else if ($pitta_percentage == "") {
            return $this->AppHelper->responseMessageHandle(0, "Pitta Percentage is required.");
        } else if ($kappa_percentage == "") {
            return $this->AppHelper->responseMessageHandle(0, "Kappa Percentage is required.");
        } else {

            try {
                $client_info = $this->Client->find_by_id($client_id);

                if ($client_info) {
                    $info['clientId'] = $client_id;
                    $info['results'] = implode(',', [$vata_percentage, $pitta_percentage, $kappa_percentage]);
                    $info['createTime'] = $this->AppHelper->day_time();

                    $res = $this->ClientResponse->add_log($info);

                    if ($res) {
                        return $this->AppHelper->responseMessageHandle(1, "Operation Successfully");
                    } else {
                        return $this->AppHelper->responseMessageHandle(0, "Error Occur");
                    }
                } else {
                    return $this->AppHelper->responseMessageHandle(0, "Invalid Client Id");
                }
            } catch (Exception $e) {
                return $this->AppHelper->responseMessageHandle(0, "Error Occured " . $e->getMessage());
            }
        }
    }
}

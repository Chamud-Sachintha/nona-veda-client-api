<?php

namespace App\Http\Controllers;

use App\Events\QuizFormSubmited;
use App\Helpers\AppHelper;
use App\Mail\MailService;
use App\Models\ClientInfo;
use App\Models\ClientResponse;
use App\Models\Question;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Pusher\Pusher;

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
            $formated_questions[$key]['answers'] = explode("@", $value['answers']);
        }

        return $this->AppHelper->responseEntityHandle(1, "Operation Successfully", $formated_questions);
    }

    public function submitQuestionResponse(Request $request) {

        $client_id = (is_null($request->client_id) || empty($request->client_id)) ? "" : $request->client_id;
        $vata_percentage = (is_null($request->vataPercentage) || empty($request->vataPercentage) && ($request->vataPercentage != 0)) ? "" : $request->vataPercentage;
        $pitta_percentage = (is_null($request->pittaPercentage) || empty($request->pittaPercentage) && ($request->pittaPercentage != 0)) ? "" : $request->pittaPercentage;
        $kappa_percentage = (is_null($request->kappaPercentage) || empty($request->kappaPercentage) && ($request->kappaPercentage != 0)) ? "" : $request->kappaPercentage;

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
                        // Fire the event
                        
                        $pusher = new Pusher(
                            env('PUSHER_APP_KEY'),
                            env('PUSHER_APP_SECRET'),
                            env('PUSHER_APP_ID'),
                            [
                                'cluster' => env('PUSHER_APP_CLUSTER'),
                                'useTLS' => false
                            ]
                        );

                        $client_info = $this->Client->find_by_id($info['clientId']);

                        $info['clientName'] = $client_info['first_name'];
                        $info['emailAddress'] = $client_info['email'];
                        $result_list = explode(",", $info['results']);

                        $info['vataResult'] = $result_list[0];
                        $info['pittaResult'] = $result_list[1];
                        $info['kappaResult'] = $result_list[2];
                    
                        $pusher->trigger('my-channel', 'my-event', [
                            'dataValue' => $info
                        ]);

                        $results = [
                            'vata' => $info['vataResult'],
                            'pitta' => $info['pittaResult'],
                            'kappa' => $info['kappaResult'],
                        ];

                        // Get the type with the highest value
                        $type = array_keys($results, max($results))[0];

                        $sendMailRes = $this->sendMail($client_info['email'], $client_info['first_name'], $type);

                        if ($sendMailRes != true) {
                            return $this->AppHelper->responseMessageHandle(0, $sendMailRes);
                        }

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

    private function sendMail($email, $name ,$type) {
        try {
            $details = [
                'ClientName' => $name,
                'type' => $type
            ];

            Mail::to($email)->send(new MailService($details));

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}

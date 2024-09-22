<?php

namespace App\Http\Controllers;

use App\Helpers\AppHelper;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    private $AppHelper;
    private $QuestionModel;

    public function __construct()
    {
        $this->AppHelper = new AppHelper();
        $this->QuestionModel = new Question();
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
}

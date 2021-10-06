<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Question as QuestionResource;
use App\Category;
use App\Http\Resources\Category as CategoryResource;
use App\Question;
use App\UserQuestionAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    public function index()
    {
        return QuestionResource::collection(Question::all());
    }

    public function storeQuestionAnswer(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'response' => 'array',
            'response.*.question_id' => 'required|integer|exists:questions,id',
            'response.*.option_id' => 'required|integer|exists:question_options,id',
            'group_id' => 'required|integer|exists:categories,id',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors()->first(), $validator->errors());
        }
        try{
            $user = auth('sanctum')->user();
            if($user){
                DB::beginTransaction();
                    $user->answers()->sync($request->response);
                    $user->category()->create(['category_id'=>$request->group_id]);
                DB::commit();
                $response = [
                    'group' => new CategoryResource(Category::find($request->group_id))
                ];
                return $this->sendResponse($response,$message = 'Answers saved successfully!');
            }
            $message = 'User not logged in!';
            return $this->sendError($message,[]);
        }catch (\Exception $e){
            return $this->sendError($e->getMessage(),$e->errors());
        }
    }

}

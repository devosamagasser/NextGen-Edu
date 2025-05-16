<?php

namespace App\Modules\Quizzes;

use App\Facades\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Modules\Quizzes\Validation\QuizeStoreRequest;
use App\Modules\Quizzes\Validation\QuizeUpdateRequest;

class QuizzesController extends Controller
{
    public function __construct(public QuizzesServices $quizesServices)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quizzes = $this->quizesServices->getAllQuizzes();
        return ApiResponse::success(QuizResource::collection($quizzes));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(QuizeStoreRequest $request)
    {
        $quiz = $this->quizesServices->addNewQuize($request);
        return ApiResponse::created(new QuizResource($quiz));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $quiz = $this->quizesServices->getQuizeById($id);
        return ApiResponse::success(new QuizResource($quiz));
    }

    public function quizWithStudentAnswers(string $id, $student = null)
    {
        $quiz = $this->quizesServices->getQuizWithStudentAnswers($id, $student);
        return ApiResponse::success($quiz);
    }

    public function quizStudentsAnswers(string $id)
    {
        $answers = $this->quizesServices->getQuizStudentsAnswers($id);
        return ApiResponse::success($answers);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(QuizeUpdateRequest $request, string $id)
    {
        $quiz = $this->quizesServices->updateQuizInfo($request, $id);

        return ApiResponse::updated(new QuizResource($quiz));

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->quizesServices->deleteQuiz($id);
        return ApiResponse::deleted();
    }


    public function startStudentQuiz(string $id)
    {
        $quiz = $this->quizesServices->startStudentQuiz($id);
        return ApiResponse::success(new QuizResource($quiz));
    }
    
    public function submitAnswers(Request $request, string $id)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'required|array',
            'questions.*.question' => 'required|exists:questions,id',
            'questions.*.answer' => 'required|exists:answers,id',
        ]);
    
        $this->quizesServices->submitQuizAnswers($id, $request->questions);
        return ApiResponse::success('Answers submitted successfully');
    }
    
    

}

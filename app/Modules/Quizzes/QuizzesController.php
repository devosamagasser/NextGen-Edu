<?php

namespace App\Modules\Quizzes;

use App\Facades\ApiResponse;
use App\Http\Controllers\Controller;
use App\Modules\Quizzes\Validation\QuizeStoreRequest;

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

    /**
     * Update the specified resource in storage.
     */
    public function update(QuizeStoreRequest $request, string $id)
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

    

}

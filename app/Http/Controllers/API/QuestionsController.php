<?php

namespace App\Http\Controllers\API;

use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\API\QuestionsResource;

class QuestionsController extends APIController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $questions = Question::all();
        return $this->SuccessResponse(200, [
            'data' => QuestionsResource::collection($questions),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:100|regex:/^[a-z A-Z 0-9 ۰-۹ آ-ی ) (]+$/u',
            'question' => 'required|string|regex:/^[- 0-9 ۰-۹ a-z & A-Z  آ-ی . : ؛ ، , @ ? ؟ ) ( \n \r \t]+$/u'
        ]);

        if ($validator->fails()) {
            return $this->ErrorResponse(422, $validator->messages());
        }

        DB::beginTransaction();

        $question = Question::create([
            'title' => $request->title,
            'question' => $request->question,
            'code' => rand_nm()
        ]);

        DB::commit();

        return $this->SuccessResponse(201, new QuestionsResource($question), 'سوال شما با موفقیت ایجاد شد.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $code)
    {
        $request->merge([
            'code' => $code
        ]);
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|integer|regex:/^[0-9]+$/u',
        ]);

        if ($validator->fails()) {
            return $this->ErrorResponse(422, $validator->messages());
        }


        $question = Question::where('code', $request->code)->first();
        if ($question) {
            return $this->SuccessResponse(200, new QuestionsResource($question));
        } else {
            return $this->ErrorResponse(404, 'سوال شما پیدا نشد!!!');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function reply(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'secret' => 'required|string|min:65|max:65',
            'code' => 'required|string|integer|regex:/^[0-9]+$/u',
            'reply' => 'required|string|regex:/^[- 0-9 ۰-۹ a-z & A-Z  آ-ی . : ؛ ، , @ ? ؟ ) ( \n \r \t]+$/u'
        ]);

        if ($validator->fails()) {
            return $this->ErrorResponse(422, $validator->messages());
        }

        if ($request->secret !== env('API_KEY')) {
            return $this->ErrorResponse(403, 'شما امکان پاسخ دادن به سوالات را ندارید!!!');
        }

        $question = Question::where('code', $request->code)->first();

        if (!$question) {
            return $this->ErrorResponse(404, 'سوال مورد نظر پیدا نشد!!!');
        }

        DB::beginTransaction();

        $question->update([
            'reply' => $request->reply,
            'status' => 1
        ]);

        DB::commit();

        return $this->SuccessResponse(200, new QuestionsResource($question), 'پاسخ شما با موفقیت ثبت شد.');
    }
}

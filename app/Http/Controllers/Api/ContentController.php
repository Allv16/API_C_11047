<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Rule;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contents = Content::all();
        if (count($contents) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $contents
            ], 200);
        }
        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'title' => 'required|max:60',
            'released_year' => 'required',
            'genre' => 'required',
            'type' => 'required',
        ]);
        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $content = Content::create($storeData);
        return response([
            'message' => 'Add Content Success',
            'data' => $content,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $contents = Content::find($id);
        if (!is_null($contents)) {
            return response([
                'message' => 'Content found, it is' . $contents->title,
                'data' => $contents
            ], 200);
        }
        return response([
            'message' => 'Content Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, String $id)
    {
        $updateData = $request->all();
        $content = Content::find($id);
        if (is_null($content)) {
            return response([
                'message' => 'Content Not Found',
                'data' => null
            ], 404);
        }

        $validate = Validator::make($updateData, [
            'title' => 'required|max:60',
            'released_year' => 'required',
            'genre' => 'required',
            'type' => 'required',
        ]);
        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $content->title = $updateData['title'];
        $content->released_year = $updateData['released_year'];
        $content->type = $updateData['type'];
        $content->genre = $updateData['genre'];

        if ($content->save()) {
            return response([
                'message' => 'Update Content Success',
                'data' => $content,
            ], 200);
        }

        return response([
            'message' => 'Update Content Failed',
            'data' => null,
        ], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $content = Content::find($id);
        if (is_null($content)) {
            return response([
                'message' => 'Content Not Found',
                'data' => null
            ], 404);
        }
        if ($content->delete()) {
            return response([
                'message' => 'Delete Content Success',
                'data' => $content,
            ], 200);
        }
        return response([
            'message' => 'Delete Content Failed',
            'data' => null
        ], 404);
    }
}

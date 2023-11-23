<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Rule;
use App\Models\User;
use App\Models\Activities;
use App\Models\Content;

class ActivitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $activities = Activities::with(['User', 'Content'])->get();

        if (count($activities) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $activities
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
            'id_user' => 'required',
            'id_content' => 'required',
        ]);
        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $user = User::find($storeData['id_user']);
        if (!$user) {
            return response([
                'message' => 'User Not Found',
            ], 400);
        }

        $content = Content::find($storeData['id_content']);
        if (!$content) {
            return response([
                'message' => 'Content Not Found',
            ], 400);
        }

        if ($user->status == 0 && $content->type == 'Paid') {
            return response(['message' => 'User account not yet active, please activated the account before accessing paid content'], 400);
        }

        $storeData['accessed_at'] = date('Y-m-d H:i:s');

        $activities = Activities::create($storeData);
        return response([
            'message' => $user->name . ' has accessed ' . $content->title . ' at ' . $activities['accessed_at'] . '.',
            'data' => $activities,
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
        $activities = Activities::find($id);

        if (!is_null($activities)) {
            return response([
                'message' => 'Activities found',
                'data' => $activities
            ], 200);
        }

        return response([
            'message' => 'Activities Not Found',
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
    public function update(Request $request, $id)
    {
        $updateData = $request->all();
        $activities = Activities::find($id);
        if (is_null($activities)) {
            return response([
                'message' => 'Activities Not Found',
                'data' => null
            ], 400);
        }

        $validate = Validator::make($updateData, [
            'id_user' => 'required',
            'id_content' => 'required',
        ]);
        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);

        $user = User::find($updateData['id_user']);
        if (!$user) {
            return response(['message' => 'User not found'], 400);
        }
        $content = Content::find($updateData['id_content']);
        if (!$content) {
            return response(['message' => 'Content not found'], 404);
        }
        if ($user->status == 0 && $content->type == 'Paid') {
            return response(['message' => 'User account not yet active, please activated the account before accessing paid content'], 400);
        }

        $activities->id_user = $updateData['id_user'];
        $activities->id_content = $updateData['id_content'];
        $activities->accessed_at = date('Y-m-d H:i:s');

        if ($activities->save()) {
            return response([
                'message' => 'Update Activities Success',
                'data' => $activities,
            ], 200);
        }

        return response(['message' => 'Update Activities Failed', 'data' => null], 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $activities = Activities::find($id);
        if (is_null($activities)) {
            return response(['message' => 'Activities not found', 'data' => null], 404);
        }

        if ($activities->delete()) {
            return response(['message' => 'Delete Activities Success', 'data' => $activities], 200);
        }

        return response(['message' => 'Delete Activities Failed', 'data' => null], 400);
    }
}

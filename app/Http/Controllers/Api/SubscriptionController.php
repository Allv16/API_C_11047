<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subscriptions = Subscription::all();

        if (count($subscriptions) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $subscriptions
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ], 400);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_user' => 'required',
            'category' => 'required|in:Basic,Standard,Premium',
        ]);
        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);
        $user = User::find($storeData['id_user']);
        if (!$user) {
            return response([
                'message' => 'User Not Found',
            ], 400);
        }
        if ($user->status == 1) {
            return response([
                'message' => 'This user already active',
            ], 400);
        }
        if ($storeData['category'] == 'Basic') {
            $storeData['price'] = 50000;
        } else if ($storeData['category'] == 'Standard') {
            $storeData['price'] = 100000;
        } else {
            $storeData['price'] = 150000;
        }
        $storeData['transaction_date'] = date('Y-m-d H:i:s');

        $subscription = Subscription::create($storeData);
        //update user status
        $user->status = 1;
        $user->save();
        return response([
            'message' => 'Add Subscription Success',
            'data' => $subscription,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $subscriptions = Subscription::find($id);
        if (!is_null($subscriptions)) {
            return response([
                'message' => 'Subscription found',
                'data' => $subscriptions
            ], 200);
        }
        return response([
            'message' => 'Subscription Not Found',
            'data' => null
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $subscription = Subscription::find($id);
        if (is_null($subscription)) {
            return response([
                'message' => 'Subscription Not Found',
                'data' => null
            ], 400);
        }
        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_user' => 'required',
            'category' => 'required|in:Basic,Standard,Premium',
        ]);
        if ($validate->fails())
            return response(['message' => $validate->errors()], 400);
        $user = User::find($updateData['id_user']);
        if (!$user) {
            return response([
                'message' => 'User Not Found',
            ], 400);
        }
        if ($user->status == 0) {
            return response([
                'message' => 'This account is not active',
            ], 400);
        }
        $subscription->id_user = $updateData['id_user'];
        $subscription->category = $updateData['category'];
        if ($subscription->category == 'Basic') {
            $subscription->price = 50000;
        } else if ($subscription->category == 'Standard') {
            $subscription->price = 100000;
        } else {
            $subscription->price = 150000;
        }
        $subscription->transaction_date = date('Y-m-d H:i:s');
        if ($subscription->save()) {
            return response([
                'message' => 'Update Subscription Success',
                'data' => $subscription,
            ], 200);
        } else {
            return response([
                'message' => 'Update Subscription Failed',
                'data' => null,
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $subscription = Subscription::find($id);
        if (is_null($subscription)) {
            return response([
                'message' => 'Subscription Not Found',
                'data' => null
            ], 400);
        }
        if ($subscription->delete()) {
            return response([
                'message' => 'Delete Subscription Success',
                'data' => $subscription,
            ], 200);
        } else {
            return response([
                'message' => 'Delete Subscription Failed',
                'data' => null,
            ], 400);
        }
    }
}

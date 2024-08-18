<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends BaseController
{

    private function assignRoleAndPermissions(User $user, $roleName)
    {
        $role = Role::where('name', $roleName)->first();

        if ($role) {
            $user->assignRole($role);

            if ($roleName === 'superAdmin') {
                // Assign financialManager permissions
                $user->syncPermissions([
                    'userProcess',
                    'sportProcess',
                    'roomProcess',
                    'facilityProcess',
                    'mediaProcess',
                    'subscriptionProcess',
                    'tagProcess',
                    'articleCategoryProcess',
                    'articleProcess',
                    'offerProcess',
                    'memberProcess',
                    'paymentProcess',
                ]);
            } elseif ($roleName === 'admin') {
                // Assign employee permissions
                $user->syncPermissions([
                    'subscriptionProcess',
                    'tagProcess',
                    'articleProcess',
                    'articleCategoryProcess',
                    'memberProcess',
                    'paymentProcess',
                ]);
            }
        } else return $this->sendError("error", 'Role not found');
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $roles = Role::all();

        return response()->json([
            'users' => $users,
            'roles' => $roles,
        ]);
    }



    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreUserRequest $request)
    {

        // Start the transaction
        DB::beginTransaction();

        try {

            // Create a new user instance
            $user = User::create($request->validated());

            $user->save();

            if ($user) {

                $this->assignRoleAndPermissions($user, $request->roleName);

                $user->load('roles');
                $user->load('permissions');

                $success['token'] =  $user->createToken('MyApp')->plainTextToken;
                $success['name'] =  $user->name;
                $success["role"] = $user->roles->first();

                // Commit the transaction
                DB::commit();

                return $this->sendResponse($success, 'Register successfully.');
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occurred during the transaction

            // Rollback the transaction
            DB::rollback();

            // Additional error handling code
            return $this->sendError('An error occurred while processing the request.', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        $user->load('roles');
        $user->load('permissions');

        return $this->sendResponse($user, 'User retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        if ($user) {
            // Remove existing roles
            $user->roles()->detach();

            $this->assignRoleAndPermissions($user, $request->roleName);

            $user->load('roles');
            $user->load('permissions');

            $user->save();

            $success['data'] =  $user;


            return $this->sendResponse(
                $success,
                'Admin updated successfully.'
            );
        } else {
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        // Delete the user
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }


    public function resetPassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        // Find the user by ID
        $user = User::findOrFail($id);

        // Update the user's password
        $user->password = Hash::make($request['password']);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully'
        ], 200);
    }
}
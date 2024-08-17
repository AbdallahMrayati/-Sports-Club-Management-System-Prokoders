<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
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

            if ($roleName === 'financialManager') {
                // Assign financialManager permissions
                $user->syncPermissions([
                    "processRequestNewBalance",
                    "amountDispatch ",
                    "mangSer&PriceBalance",
                ]);
            } elseif ($roleName === 'employee') {
                // Assign employee permissions
                $user->syncPermissions([
                    "processPayment&inquiryRequests",
                    "accountStatement ",
                ]);
            } elseif ($roleName === 'pointOfSale') {
                // Assign pointOfSale permissions
                $user->syncPermissions([
                    "authorizedAgent",
                    "sendRequestNewBalance",
                    "sendPayment&inquiryRequests",
                ]);
            }
        }
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('accountCreate&Edit');
        $users = User::all();
        $roles = Role::all();
        $agents =  User::where('is_agent', true)->get();

        return response()->json([
            'users' => $users,
            'roles' => $roles,
            'agents' => $agents
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(UserRequest $request)
    {
        $this->authorize('accountCreate&Edit');
        // Start the transaction
        DB::beginTransaction();

        try {


            // Create a new user instance
            $user = new User();

            $user->username = $request->input('username');
            $user->name = $request->input('name');
            $user->password = bcrypt($request->input('password'));

            // Agent status
            if ($request->agent_id) {
                //check if the user that chosed to be agent is an agent or not
                $userAgent = User::find($request->agent_id);
                if ($userAgent->is_agent === 0) {
                    return response()->json([
                        'message' => 'User is not an agent'
                    ], 404);
                } else {
                    // put agent id into agent_id column
                    $user->agent_id = $request->agent_id;
                }
            }

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

    public function getAgentClients($id)
    {
        $this->authorize('enableAgents');
        $user =  User::find($id);
        $agentClients = $user->clients;
        return $agentClients;
    }


    public function makeUserAgent($id)
    {
        $this->authorize('enableAgents');
        $user = User::find($id);

        if ($user->is_agent === 0) {
            if ($user->agent_id != NULL) {
                return response()->json([
                    'message' => 'Sorry ,this user is client for an agent'
                ], 404);
            } else {
                $user->is_agent = 1;
                $user->save();
                return $this->sendResponse($user, 'User getting agent successfully.');
            }
        } else {
            return response()->json([
                'message' => 'This user is already agent'
            ], 404);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->authorize('accountCreate&Edit');
        $user = User::find($id);

        if (is_null($user)) {
            return $this->sendError('User not found.');
        }

        $user->load('roles');
        $user->load('permissions');

        return $this->sendResponse($user, 'User retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('accountCreate&Edit');

        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'name' => 'required',
            'roleName' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }


        // Update the user in the database
        $user = User::find($id);
        $user->username = $request['username'];
        $user->name = $request['name'];

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
        $this->authorize('accountCreate&Edit');
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

    public function updateUserState(Request $request, $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'is_disabled' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }
        // Update the user's state
        $user->is_disabled = $request->has('is_disabled');
        $user->save();

        return response()->json([
            'message' => 'User state updated successfully'
        ], 200);
    }

    public function permissionsUserPage($id)
    {
        // Retrieve the authenticated user
        $user = User::find($id);

        // Get the role names for the user
        $roleNames = $user->getRoleNames();

        // Assuming the user has only one role, you can get the first role name
        $role = $roleNames->first();

        // Retrieve the role model based on the role name
        $roleModel = Role::findByName($role);

        // Get the permissions for the role
        $permissions = $roleModel->permissions;

        // Loop through the permissions and extract the permissionType
        $permissionTypes = [];
        foreach ($permissions as $permission) {
            $permissionType = $permission->permissionType;
            $permissionTypes[] = $permissionType;
        }
        $uniquePermissionTypes = array_unique($permissionTypes);

        return response()->json([
            'user' => $user,
            'permissions' => $permissions,
        ], 200);
    }

    public function changePermissions(Request $request, $userId)
    {
        $user = User::find($userId);

        // Get the selected permission IDs from the form
        $selectedPermissions = $request->input('permissions', []);
        $permissionsToAssign = Permission::whereIn('id', $selectedPermissions)->get();

        // Sync the user's permissions
        $user->syncPermissions($permissionsToAssign);

        return response()->json([
            'message' => 'Permissions updated successfully'
        ], 200);
    }
}
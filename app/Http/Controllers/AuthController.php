<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }


    // Register user
    public function register(Request $request)
    {
        try {
            $registerUserData = $request->validate([
                'name'          => 'required|string',
                'email'         => 'required|string|email|unique:users',
                'partner_id'    => 'required|string',
                'password'      => 'required|min:8',
                'c_password'    => 'required|same:password',
            ]);

            $user = User::create([
                'name'      => $registerUserData['name'],
                'role_id'   => 2, // Static role_id
                'email'     => $registerUserData['email'],
                'partner_id'     => $registerUserData['partner_id'],
                'password'  => Hash::make($registerUserData['password']),
                'created_at' => Carbon::now('Asia/Phnom_Penh'),
                'updated_at' => Carbon::now('Asia/Phnom_Penh'),
            ]);

            return response()->json([
                'message' => 'User Created',
            ], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            // Validation error
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Unexpected error
            return response()->json([
                'message' => 'Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Login
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email'     => 'required|string|email',
                'password'  => 'required|min:8'
            ]);
            $token = Auth::guard('api')->attempt($credentials);
            if (!$token) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Unauthorized',
                ], Response::HTTP_UNAUTHORIZED);
            }
            $user = Auth::guard('api')->user();
            return response()->json([
                'message'       => 'Success',
                'user'          => $user,
                'access_token'  => $token,
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            // Validation error
            return response()->json([
                'message'   => 'Validation Error',
                'errors'    => $e->errors(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            // Unexpected error
            return response()->json([
                'message' => 'Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'partner_id' => 'required|exists:partners,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user->update($request->only(['name', 'email', 'partner_id']));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        return response()->json($user);
    }

    //delete user
    public function delete($id)
    {
        try {
            $deleteUser = User::find($id);
            if (!$deleteUser) {
                return response()->json(['message' => 'User not found.'], 404);
            }
            $deleteUser->delete();

            return response()->json(['message' => 'User deleted.'], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function me()
    {
        // Here we just get information about current user
        try {
            return response()->json(auth()->user());
        } catch (\Exception $e) {
            // Unexpected error
            return response()->json([
                'message' => 'Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function allUser()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function logout()
    {
        try {
            auth()->logout();
            return response()->json(['message' => 'Successfully logged out']);
        } catch (\Exception $e) {
            //Unexpected error
            return response()->json([
                'message' => 'Server Error',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function refresh()
    {
        // When access token will be expired, we are going to generate a new one wit this function
        // and return it here in response
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        // This function is used to make JSON response with new
        // access token of current user
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 180
        ]);
    }
    public function selfChangePassword(Request $request)
    {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'current_password' => 'required|string',
                'new_password' => 'required|string|min:8|different:current_password',
                'confirm_password' => 'required|string|same:new_password'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            // Get the authenticated user
            $user = Auth::guard('api')->user();

            // Verify current password
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Current password is incorrect'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'message' => 'Password successfully updated'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function selfChangeInfo(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::guard('api')->user();

            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'message' => 'Validation Error',
                    'errors' => $validator->errors()
                ], Response::HTTP_BAD_REQUEST);
            }

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Password is incorrect'
                ], Response::HTTP_UNAUTHORIZED);
            }

            // Update user information
            $user->name = $request->name;
            $user->email = $request->email;
            $user->updated_at = Carbon::now('Asia/Phnom_Penh');
            $user->save();

            return response()->json([
                'message' => 'User information successfully updated',
                'user' => $user
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Server Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}

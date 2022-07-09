<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\CpfCnpg;
use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * User's validation rules.
     *
     * @var array
     */
    private $rules;

    /**
     * Constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->rules = [
            'name' => 'required|min:5',
            'email' => ['required', 'email'],
            'document' => ['required', new CpfCnpg()],
            'password' => 'required|min:6',
            'logist' => 'nullable|boolean',
        ];
    }

    /**
     * Display all users.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::all();
    }

    /**
     * Display a specific user.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Creates a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = $request->only('name', 'email', 'document', 'password', 'logist');

        $validator = $this->makeValidator($data);

        if ($validator->fails()) {
            return $this->failure($validator);
        }

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return response()->json([
            'status' => 'created',
            'data' => $user,
        ], 201);
    }

    /**
     * Update a specific user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $data = $request->all();
        $validator = $this->makeValidator($data, $user->id);

        if ($validator->fails()) {
            return $this->failure($validator);
        }

        $user->update($data);

        return response()->json([
            'status' => 'updated',
            'data' => $user,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  User  $user
     * @return \Illuminate\Http\Response
     */
    public function delete(User $user)
    {
        $user->delete();

        return response()->json([
            'status' => 'deleted',
            'data' => $user,
        ], 200);
    }

    /**
     * Display request failure.
     *
     * @param ValidationValidator $validator
     * @return JsonResponse
     */
    private function failure(ValidationValidator $validator)
    {
        return response()->json([
            'status' => 'failed',
            'errors' => $validator->errors(),
        ], 403);
    }

    /**
     * Makes the apropriate user's validator.
     *
     * @param array $data
     * @param int $userId when null doesn't ignore any user id
     * @return ValidationValidator
     */
    private function makeValidator($data, $userId = null)
    {
        $rules = $this->rules;

        $uniqueEmailRule = Rule::unique('users', 'email');

        if ($userId) {
            // select only the submitted data
            $rules = array_intersect_key($rules, $data);

            // when updating ignore unique email rule
            $uniqueEmailRule->ignore($userId);
        }

        if (isset($rules['email'])) {
            array_push($rules['email'], $uniqueEmailRule);
        }

        return Validator::make($data, $rules);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }
    public function dataTable(request $request)
    {
        $totalData = User::where([
            [
                'users.id',
                (('Developer' == 'Developer') ? '<>' : '='),
                (('Developer' == 'Developer') ? null : Auth::user()->id),
            ],
        ])
            ->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = User::where([
                [
                    'users.id',
                    (('Developer' == 'Developer') ? '<>' : '='),
                    (('Developer' == 'Developer') ? null : Auth::user()->id),
                ],
            ]);

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['columns'][$request['order'][0]['column']]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->get();
        } else {
            $assets = User::where('users.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('users.username', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['columns'][$request['order'][0]['column']]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->where([
                [
                    'users.id',
                    (('Developer' == 'Developer') ? '<>' : '='),
                    (('Developer' == 'Developer') ? null : Auth::user()->id),
                ],
            ])->get();

            $totalFiltered = User::where('users.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('users.username', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['columns'][$request['order'][0]['column']]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->where([
                [
                    'users.id',
                    (('Developer' == 'Developer') ? '<>' : '='),
                    (('Developer' == 'Developer') ? null : Auth::user()->id),
                ],
            ])->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['number'] = $request['start'] + ($index + 1);
            $row['name'] = $item->name;
            $row['email'] = $item->email;
            $row['role'] = $item->role;
            $row['username'] = $item->username;
            $row['action'] = (($item->id !== Auth::user()->id) ? "<button title='Delete user' class='btn btn-icon btn-danger delete' data-user='" . $item->id . "' ><i class='bx bxs-trash'></i></button><button title='" . (($item->lock == 0) ? 'Lock user' : 'Unlock user') . "' class='btn btn-icon " . (($item->lock == 0) ? 'btn-danger' : 'btn-warning') . " lock' data-user='" . $item->id . "' ><i class='" . (($item->lock == 0) ? 'bx bxs-lock-alt' : 'bx bxs-lock-open-alt') . "'></i></button>" : "") . "<button title='Edit user' class='btn btn-icon btn-warning edit' data-user='" . $item->id . "' ><i class='bx bxs-pencil'></i></button>";
            $dataFiltered[] = $row;
        }
        $response = [
            'draw' => $request['draw'],
            'recordsFiltered' => $totalFiltered,
            'recordsTotal' => count($dataFiltered),
            'aaData' => $dataFiltered,
        ];

        return Response()->json($response, 200);
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'unique:users,username|required|min:8|max:100',
            'email' => 'unique:users,email|required|email|min:8|max:100',
            'name' => 'unique:users,name|required|min:8|max:100',
            'password' => 'required|min:8|max:100',
            'role' => 'required|in:Admin,Operator',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token');
            $data['password'] = Hash::make($data['password']);
            $user = User::create($data);
            $response = ['message' => 'User created successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $code = 422;
            $response = ['message' => 'Failed creating User'];
        }

        return response()->json($response, $code);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'username' => 'unique:users,username,' . $id . '|required|min:8|max:100',
            'email' => 'unique:users,email,' . $id . '|required|email|min:8|max:100',
            'name' => 'unique:users,name,' . $id . '|required|min:8|max:100',
            'password' => 'min:8|max:100',
            'role' => 'required|in:Admin,Operator',
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token');
            if ($request->password != '') {
                $data['password'] = Hash::make($data['password']);
            }
            User::find($id)->update($data);
            $response = ['message' => 'User created successfully'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            $code = 422;
            $response = ['message' => 'Failed creating User'];
        }

        return response()->json($response, $code);
    }


    public function show(string $id)
    {
        $user = User::find($id);
        $response = ['message' => 'showing user resources successfully', 'data' => $user];
        $code = 200;
        if (empty($user)) {
            $response = ['message' => 'failed showing user resources', 'data' => $user];
            $code = 404;
        }

        return response()->json($response, $code);
    }
    public function userManagement()
    {
        return view('content.user-management');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
            'lock' => 0,
        ])) {
            $request->session()->regenerate();
            return redirect()->route('home');
        }

        return redirect()->route('login')->withInput()->with('error', 'User not found or User is locked');
    }

    public function logout(Request $request)
    {
        Auth::user()->setRememberToken(null);
        Auth::logout();
        $request->session()->flush();
        $request->session()->invalidate();

        return redirect()->route('login');
    }
    public function lockUser($id)
    {
        $response = ['message' => 'lock user successfully'];
        $code = 200;
        $user = User::find($id);
        if (!User::find($id)->update(['lock' => !boolval($user->lock)])) {
            $response = ['message' => 'failed lock user'];
            $code = 404;
        }

        return response()->json($response, $code);
    }
    public function destroy(string $id)
    {
        $response = ['message' => 'deleting user resources successfully'];
        $code = 200;
        if (!User::find($id)->delete()) {
            $response = ['message' => 'failed deleting user resources'];
            $code = 404;
        }

        return response()->json($response, $code);
    }
}

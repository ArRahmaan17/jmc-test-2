<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('content.category-good');
    }
    public function dataTable(request $request)
    {
        $totalData = Category::orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = Category::select('*');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['columns'][$request['order'][0]['column']]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->get();
        } else {
            $assets = Category::where('name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('code', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['columns'][$request['order'][0]['column']]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->get();

            $totalFiltered = Category::where('code', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('name', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['columns'][$request['order'][0]['column']]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['number'] = $request['start'] + ($index + 1);
            $row['name'] = $item->name;
            $row['code'] = $item->code;
            $row['action'] = "<button title='Edit category' class='btn btn-icon btn-warning edit' data-category='" . $item->id . "' ><i class='bx bxs-pencil'></i></button><button title='Edit category' class='btn btn-icon btn-danger delete' data-category='" . $item->id . "' ><i class='bx bxs-trash'></i></button>";
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(['code' => 'required|unique:categories,code|max:10', 'name' => 'required|max:100']);
        DB::beginTransaction();
        try {
            Category::create($request->only('name', 'code'));
            DB::commit();
            $response = ['message' => "Successfully creating category resources"];
            $code = 200;
        } catch (\Throwable $th) {
            $response = ['message' => "failed creating category resources"];
            $code = 524;
        }
        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        $response = ['message' => 'showing category resources successfully', 'data' => $category];
        $code = 200;
        if (empty($category)) {
            $response = ['message' => 'failed showing category resources', 'data' => $category];
            $code = 404;
        }

        return response()->json($response, $code);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(['code' => 'required|max:10|unique:categories,code,' . $id, 'name' => 'required|max:100']);
        DB::beginTransaction();
        try {
            Category::find($id)->update($request->only('name', 'code'));
            DB::commit();
            $response = ['message' => "Successfully updating category resources"];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => "failed updating category resources"];
            $code = 524;
        }
        return response()->json($response, $code);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            Category::find($id)->delete();
            DB::commit();
            $response = ['message' => "Successfully deleting category resources"];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => "failed deleting category resources"];
            $code = 524;
        }
        return response()->json($response, $code);
    }
}

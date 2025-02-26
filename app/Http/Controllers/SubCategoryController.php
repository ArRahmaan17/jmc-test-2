<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('content.sub-category-good', compact('categories'));
    }
    public function all($id)
    {
        $subCategories = SubCategory::where('categoryId', $id)->get()->toArray();
        $response = ['message' => 'successfully showing all sub category resources', 'data' => $subCategories];
        $code = 200;
        if (empty($subCategories)) {
            $response = ['message' => 'successfully showing all sub category resources', 'data' => $subCategories];
            $code = 404;
        }
        return response()->json($response, $code);
    }
    public function dataTable(request $request)
    {
        $totalData = SubCategory::select('sub_categories.*')->join('categories as c', 'c.id', '=', 'sub_categories.categoryId')->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = SubCategory::select('sub_categories.*')->join('categories as c', 'c.id', '=', 'sub_categories.categoryId');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['columns'][$request['order'][0]['column']]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->get();
        } else {
            $assets = SubCategory::select('sub_categories.*')->join('categories as c', 'c.id', '=', 'sub_categories.categoryId')->where('c.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('sub_categories.name', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['columns'][$request['order'][0]['column']]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->get();

            $totalFiltered = SubCategory::select('sub_categories.*')->join('categories as c', 'c.id', '=', 'sub_categories.categoryId')->where('c.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('sub_categories.name', 'like', '%' . $request['search']['value'] . '%');

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
            $row['price_limit'] = $item->price_limit;
            $row['action'] = "<button title='Edit sub category' class='btn btn-icon btn-warning edit' data-sub-category='" . $item->id . "' ><i class='bx bxs-pencil'></i></button><button title='Edit sub category' class='btn btn-icon btn-danger delete' data-sub-category='" . $item->id . "' ><i class='bx bxs-trash'></i></button>";
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
        $request->validate([
            'categoryId' => 'required|exists:categories,id',
            'name' => 'required|max:100',
            'price_limit' => "required|regex:/(\d{1,3}(?:\.\d{3})*)/i"
        ]);
        DB::beginTransaction();
        try {
            $data = $request->except('_token');
            $data['price_limit'] = implode('', explode('.', $data['price_limit']));
            SubCategory::create($data);
            DB::commit();
            $response = ['message' => "Successfully creating sub category resources"];
            $code = 200;
        } catch (\Throwable $th) {
            $response = ['message' => "failed creating sub category resources"];
            $code = 524;
        }
        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = SubCategory::find($id);
        $response = ['message' => 'showing sub category resources successfully', 'data' => $category];
        $code = 200;
        if (empty($category)) {
            $response = ['message' => 'failed showing sub category resources', 'data' => $category];
            $code = 404;
        }

        return response()->json($response, $code);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate(['categoryId' => 'required|exists:categories,id', 'price_limit' => 'required|regex:/(\d{1,3}(?:\.\d{3})*)/i', 'name' => 'required|max:100']);
        DB::beginTransaction();
        try {
            SubCategory::find($id)->update($request->only('categoryId', 'name', 'price_limit'));
            DB::commit();
            $response = ['message' => "Successfully updating sub category resources"];
            $code = 200;
        } catch (\Throwable $th) {
            DB::rollBack();
            $response = ['message' => "failed updating sub category resources"];
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
            SubCategory::find($id)->delete();
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

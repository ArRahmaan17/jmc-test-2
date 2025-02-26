<?php

namespace App\Http\Controllers;

use App\Exports\IncomingGoodExport;
use App\Models\Category;
use App\Models\IncomingGood;
use App\Models\IncomingGoodDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class IncomingGoodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $operators = User::where('role', 'Operator')->get();
        return view('content.incoming-good', compact('categories', 'operators'));
    }
    public function downloadReport($id)
    {
        return Excel::download(new IncomingGoodExport($id), 'incoming_good.xlsx');
    }
    public function dataTable(request $request)
    {
        $totalData = IncomingGood::select('incoming_goods.*', 'igd.name', 'igd.price', 'igd.amount', 'igd.total', 'u.name as operator_name', 'c.code as code', 'igd.id as idDetail')->join('categories as c', 'c.id', '=', 'incoming_goods.categoryId')->join('users as u', 'incoming_goods.operatorId', '=', 'u.id')->join('incoming_good_details as igd', 'incoming_goods.id', '=', 'igd.incomingId')->orderBy('id', 'asc')
            ->count();
        $totalFiltered = $totalData;
        if (empty($request['search']['value'])) {
            $assets = IncomingGood::select('incoming_goods.*', 'igd.name', 'igd.price', 'igd.amount', 'igd.total', 'u.name as operator_name', 'c.code as code', 'igd.id as idDetail')->join('categories as c', 'c.id', '=', 'incoming_goods.categoryId')->join('users as u', 'incoming_goods.operatorId', '=', 'u.id')->join('incoming_good_details as igd', 'incoming_goods.id', '=', 'igd.incomingId');

            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['columns'][$request['order'][0]['column']]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $assets = $assets->get();
        } else {
            $assets = IncomingGood::select('incoming_goods.*', 'igd.name', 'igd.price', 'igd.amount', 'igd.total', 'u.name as operator_name', 'c.code as code', 'igd.id as idDetail')->join('categories as c', 'c.id', '=', 'incoming_goods.categoryId')->join('users as u', 'incoming_goods.operatorId', '=', 'u.id')->join('incoming_good_details as igd', 'incoming_goods.id', '=', 'igd.incomingId')->join('sub_categories as sc', 'sc.categoryId', '=', 'c.id')
                ->where('c.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('sc.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('igd.total', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('incoming_goods.created_at', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('incoming_goods.source', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('price_limit', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $assets->orderByRaw($request['columns'][$request['order'][0]['column']]['name'] . ' ' . $request['order'][0]['dir']);
            }
            if ($request['length'] != '-1') {
                $assets->limit($request['length'])
                    ->offset($request['start']);
            }
            $assets = $assets->get();

            $totalFiltered = IncomingGood::select('incoming_goods.*', 'igd.name', 'igd.price', 'igd.amount', 'igd.total', 'u.name as operator_name', 'c.code as code', 'igd.id as idDetail')->join('categories as c', 'c.id', '=', 'incoming_goods.categoryId')->join('users as u', 'incoming_goods.operatorId', '=', 'u.id')->join('incoming_good_details as igd', 'incoming_goods.id', '=', 'igd.incomingId')->join('sub_categories as sc', 'sc.categoryId', '=', 'c.id')
                ->where('c.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('sc.name', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('igd.total', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('incoming_goods.created_at', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('incoming_goods.source', 'like', '%' . $request['search']['value'] . '%')
                ->orWhere('price_limit', 'like', '%' . $request['search']['value'] . '%');

            if (isset($request['order'][0]['column'])) {
                $totalFiltered->orderByRaw($request['columns'][$request['order'][0]['column']]['name'] . ' ' . $request['order'][0]['dir']);
            }
            $totalFiltered = $totalFiltered->count();
        }
        $dataFiltered = [];
        foreach ($assets as $index => $item) {
            $row = [];
            $row['number'] = (!empty($assets[$index + 1]) && $assets[$index]->incomingId == $assets[$index + 1]->incomingId) ? $request['start'] + ($index + 1) : $index;
            $row['name'] = $item->name;
            $row['operator_name'] = $item->operator_name;
            $row['created_at'] = $item->created_at;
            $row['source'] = $item->source;
            $row['unit'] = $item->unit;
            $row['code'] = $item->code;
            $row['price'] = $item->price;
            $row['amount'] = $item->amount;
            $row['total'] = $item->total;
            $row['status'] = ($item->status == 0) ? "<button class='btn btn-icon btn-warning status-update' status-incoming-good=" . $item->idDetail . "><i class='bx bx-x'></i></button>" : "<button class='btn btn-icon btn-success'><i class='bx bx-check'></i></button>";
            $row['action'] = "<a href='" . route('incoming-good.download-report', $item->id) . "' title='Report incoming' class='btn btn-icon btn-success' data-incoming='" . $item->id . "' ><i class='bx bxs-file'></i></a><button title='Edit incoming' class='btn btn-icon btn-warning edit' data-incoming='" . $item->id . "' ><i class='bx bxs-pencil'></i></button><button title='Edit incoming' class='btn btn-icon btn-danger delete' data-incoming='" . $item->id . "' ><i class='bx bxs-trash'></i></button>";
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
            'operatorId' => 'required|numeric|exists:users,id',
            'categoryId' => 'required|numeric|exists:categories,id',
            'subCategoryId' => 'required|numeric|exists:sub_categories,id',
            'attachment' => 'file|mimes:doc,docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.documen,zip',
            'source' => 'required|max:200|',
            'mail_number' => 'max:100',
            'amount' => 'required|array',
            'amount.*' => 'required|regex:/(\d{1,3}(?:\.\d{3})*)/i',
            'name' => 'required|array',
            'name.*' => 'required|max:200',
            'price' => 'required|array',
            'price.*' => 'required|regex:/(\d{1,3}(?:\.\d{3})*)/i',
            'unit' => 'required|array',
            'unit.*' => 'required|max:40',
            'total' => 'required|array',
            'total.*' => 'required|regex:/(\d{1,3}(?:\.\d{3})*)/i',
        ]);
        DB::beginTransaction();
        try {
            $generalData = $request->only('operatorId', 'categoryId', 'subCategoryId', 'source', 'mail_number');
            if ($request->attachment) {
                $generalData['attachment'] = now('Asia/Jakarta') . '.' . $request->file('attachment')->getClientOriginalExtension();
                $request->file('attachment')->storeAs('attachment_incoming', $generalData['attachment']);
            }
            $goodData = $request->only('amount', 'name', 'price', 'unit', 'total', 'expired_at');
            $incomingGood = IncomingGood::create($generalData);
            $dataDetail = [];
            foreach (array_keys($goodData['amount']) as $index) {
                $dataDetail[] = array_merge(
                    [
                        "incomingId" => $incomingGood->id,
                        'created_at' => now('Asia/Jakarta'),
                        'updated_at' => now('Asia/Jakarta'),
                    ],
                    array_map(fn($item) => $item[$index], $goodData)
                );
            }
            IncomingGoodDetail::insert($dataDetail);
            DB::commit();
            $response = ['message' => 'successfully creating incoming good resources'];
            $code = 200;
        } catch (\Throwable $th) {
            $response = ['message' => 'failed creating incoming good resources'];
            $code = 524;
            DB::rollBack();
        }
        return response()->json($response, $code);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = IncomingGood::with('details')->find($id);
        $response = ['message' => 'showing incoming good resources successfully', 'data' => $data];
        $code = 200;
        if (empty($data)) {
            $code = 404;
            $response = ['message' => 'failed incoming good resources successfully', 'data' => $data];
        }
        return response()->json($response, $code);
    }

    public function updateStatus($id)
    {

        DB::beginTransaction();
        try {
            IncomingGoodDetail::find($id)->update(['status' => true]);
            $response = ['message' => 'successfully updating incoming good status'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            $response = ['message' => 'failed updating incoming good status'];
            $code = 524;
            DB::rollBack();
        }
        return response()->json($response, $code);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'operatorId' => "required|numeric|exists:users,id",
            'categoryId' => "required|numeric|exists:categories,id",
            'subCategoryId' => "required|numeric|exists:sub_categories,id",
            'attachment' => 'file|mimes:doc,docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.documen,zip',
            'source' => 'required|max:200|',
            'mail_number' => 'max:100',
            'amount' => 'required|array',
            'amount.*' => 'required|regex:/(\d{1,3}(?:\.\d{3})*)/i',
            'name' => 'required|array',
            'name.*' => 'required|max:200',
            'price' => 'required|array',
            'price.*' => 'required|regex:/(\d{1,3}(?:\.\d{3})*)/i',
            'unit' => 'required|array',
            'unit.*' => 'required|max:40',
            'total' => 'required|array',
            'total.*' => 'required|regex:/(\d{1,3}(?:\.\d{3})*)/i',
        ]);
        DB::beginTransaction();
        try {
            $generalData = $request->only('operatorId', 'categoryId', 'subCategoryId', 'source', 'mail_number');
            if ($request->attachment) {
                $generalData['attachment'] = now('Asia/Jakarta') . '.' . $request->file('attachment')->getClientOriginalExtension();
                $request->file('attachment')->storeAs('attachment_incoming', $generalData['attachment']);
            }
            $goodData = $request->only('id', 'amount', 'name', 'price', 'unit', 'total', 'expired_at');
            IncomingGood::find($id)->update($generalData);
            $dataDetail = [];
            foreach (array_keys($goodData['amount']) as $index) {
                $processingData = array_merge([
                    "incomingId" => $id,
                    'created_at' => now('Asia/Jakarta'),
                    'updated_at' => now('Asia/Jakarta'),
                ], array_map(fn($item) => $item[$index], $goodData));
                $processingData['expired_at'] = (empty($processingData['expired_at'])) ? null : $processingData['expired_at'];
                $dataDetail[] = $processingData;
            }
            IncomingGoodDetail::upsert($dataDetail, ['id', 'incomingId'], ['amount', 'name', 'price', 'unit', 'total', 'expired_at']);
            DB::commit();
            $response = ['message' => 'successfully updating incoming good resources'];
            $code = 200;
        } catch (\Throwable $th) {
            $response = ['message' => 'failed updating incoming good resources'];
            $code = 524;
            DB::rollBack();
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
            IncomingGood::find($id)->delete();
            IncomingGoodDetail::where('incomingId', $id)->delete();
            $response = ['message' => 'successfully deleting incoming good resources'];
            $code = 200;
            DB::commit();
        } catch (\Throwable $th) {
            $response = ['message' => 'failed deleting incoming good resources'];
            $code = 524;
            DB::rollBack();
        }
        return response()->json($response, $code);
    }
}

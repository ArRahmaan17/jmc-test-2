<?php

namespace App\Exports;

use App\Models\IncomingGood;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IncomingGoodExport implements FromCollection, WithHeadingRow
{
    use Exportable;
    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function headings(): array
    {
        return [
            'date',
            'recipient',
            'source',
            'unit',
            'code',
            "item name",
            "price",
            "amount",
            "unit good",
            "total",
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return IncomingGood::selectRaw("
            CASE 
                WHEN incoming_goods.created_at = LAG(incoming_goods.created_at) OVER (PARTITION BY incoming_goods.id ORDER BY igd.id) THEN ''
                ELSE incoming_goods.created_at
            END AS date,
            CASE 
                WHEN u.name = LAG(u.name) OVER (PARTITION BY incoming_goods.id ORDER BY igd.id) THEN ''
                ELSE u.name
            END AS recipient,
            CASE 
                WHEN incoming_goods.source = LAG(incoming_goods.source) OVER (PARTITION BY incoming_goods.id ORDER BY igd.id) THEN ''
                ELSE incoming_goods.source
            END AS source,
            CASE 
                WHEN incoming_goods.unit = LAG(incoming_goods.unit) OVER (PARTITION BY incoming_goods.id ORDER BY igd.id) THEN ''
                ELSE incoming_goods.unit
            END AS unit,
            c.code AS code,
        igd.name AS 'item name',
        igd.price,
        igd.amount,
        igd.unit as 'unit good',
        igd.total
    ")
            ->join('categories as c', 'c.id', '=', 'incoming_goods.categoryId')
            ->join('users as u', 'incoming_goods.operatorId', '=', 'u.id')
            ->join('incoming_good_details as igd', 'incoming_goods.id', '=', 'igd.incomingId')
            ->orderBy('incoming_goods.id', 'asc')
            ->orderBy('igd.id', 'asc')
            ->where('incoming_goods.id', ($this->id) ? '=' : '<>', $this->id)
            ->get();
    }
}

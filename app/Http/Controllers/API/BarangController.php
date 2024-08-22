<?php

namespace App\Http\Controllers\API;

use App\Models\Barang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangs = Barang::select(['id', 'nama_barang', 'kode', 'kategori', 'lokasi', 'created_at', 'updated_at']);
        return DataTables::of($barangs)
            ->addColumn('action', function ($barang) {
                return '<a href="/barang/edit/' . $barang->id . '" class="btn btn-sm btn-primary">Edit</a>';
            })
            ->make(true);
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
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
        ]);

        // Ambil data input
        $data = $request->all();

        // Dapatkan inisial nama barang, misalnya BRG
        $inisialBarang = strtoupper(substr($data['nama_barang'], 0, 3));

        // Ambil ID terakhir atau buat ID baru
        $lastId = Barang::max('id') + 1;

        // Ambil tanggal saat ini dalam format ddmmyy
        $tanggal = date('dmy');

        // Buat kode dengan format BRG0001220824
        $kodeBarang = $inisialBarang . str_pad($lastId, 4, '0', STR_PAD_LEFT) . $tanggal;

        // Tambahkan kode ke data yang akan disimpan
        $data['kode'] = $kodeBarang;

        // Simpan data barang
        $barang = Barang::create($data);

        return response()->json($barang, 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Barang::find($id);

        if ($data) {
            return Response::json($data);
        } else {
            return Response::json(['message' => 'Data not found'], 404);
        }
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
    public function update(Request $request, string $id)
    {
        $data = Barang::find($id);

        if (!$data) {
            return Response::json(['message' => 'Data not found'], 404);
        }

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode' => 'sometimes|required|string|email|max:255|unique:barang,kode,' . $id,
            'kategori' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
        ]);

        $data->update($request->all());
        return Response::json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $data = Barang::find($id);

        if (!$data) {
            return Response::json(['message' => 'Data not found'], 404);
        }

        $data->delete();
        return Response::json(['message' => 'Data deleted successfully']);
    }
}

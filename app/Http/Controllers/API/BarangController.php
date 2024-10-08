<?php

namespace App\Http\Controllers\API;

use App\Models\Barang;
use App\Models\Mutasi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $barangs = Barang::all();
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
        // Validasi input
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kategori' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'stok' => 'required|integer|min:1',
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

        // Catat mutasi barang masuk
        Mutasi::create([
            'barang_id' => $barang->id,
            'user_id' => $data['user_id'],
            'tanggal' => now(),
            'jenis_mutasi' => 'masuk',
            'jumlah' => $data['stok'], // Gunakan stok dari input
        ]);

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
            return response()->json(['message' => 'Data not found'], 404);
        }

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'kode' => 'sometimes|required|string|max:255|unique:barang,kode,' . $id,
            'kategori' => 'required|string|max:255',
            'lokasi' => 'required|string|max:255',
            'stok' => 'sometimes|required|integer|min:0', // Validasi untuk stok jika di-update
        ]);

        $originalStok = $data->stok;
        $updatedStok = $request->input('stok', $originalStok);

        // Update data barang
        $data->update($request->all());

        // Jika stok berubah, catat mutasi
        if ($originalStok != $updatedStok) {
            $jenisMutasi = $updatedStok < $originalStok ? 'keluar' : 'masuk';
            $jumlahMutasi = abs($updatedStok - $originalStok);

            Mutasi::create([
                // 'user_id' => $data['user_id'],
                'user_id' => $request->input('user_id'),
                'barang_id' => $data->id,
                'tanggal' => now(),
                'jenis_mutasi' => $jenisMutasi,
                'jumlah' => $jumlahMutasi,
            ]);
        }

        return response()->json($data);
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

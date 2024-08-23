@extends('layouts.app')
@section('title')
    Data Mutasi
@endsection

@section('content')
<div class="container-fluid py-4">
<div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Data Mutasi</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive m-5 p-0">
           <table id="mutasi" class="table table-striped" style="width:100%">
              {{-- <div class="row">
                <div class="col-2">
                    <select class="form-control" name="kelas" id="kelas">
                        <option value="">Jenis Mutasi</option>
                        <option value="Masuk">Masuk</option>
                        <option value="Keluar">Keluar</option>
                    </select>
                </div>
            </div> --}}
              <thead>
                  <tr>
                      <th>No</th>
                      <th>Tanggal</th>
                      <th>User</th>
                      <th>Barang</th>
                      <th>Jenis Mutasi</th>
                      <th>Jumlah</th>
                  </tr>
              </thead>
              <tbody></tbody>
          </table>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection
@push('addon-script')
<script type="text/javascript">
  $(document).ready(function() {
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    if (!token) {
        // Pengguna sudah login, redirect ke halaman utama atau dashboard
        window.location.href = '/Login';
    }
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'Authorization': 'Bearer ' + token,
      }
    });

    $('#mutasi').DataTable({
      "ajax": {
        "url": "api/mutasi",
        "dataSrc": "data"
      },
      "columns": [
        { 
          "data": null, 
          "render": function (data, type, row, meta) {
            return meta.row + 1;
          }
        },
        { "data": "tanggal" },       
        { "data": "user.name" },       
        { "data": function(row) {
            return row.barang.nama_barang + ' (' + row.barang.kode + ')';
        }},     
        { "data": "jenis_mutasi" },  
        { "data": "jumlah" },        
      ]
    });
  });

</script>

@endpush
@extends('layouts.app')
@section('title')
    Data Barang
@endsection

@section('content')
<div class="container-fluid py-4">
<div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <h6>Data Barang</h6>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive m-5 p-0">
            <button class="btn btn-success mb-3" onclick="openAddModal()">Add Data</button>
            <table id="barang" class="table table-striped" style="width:100%">
              <thead>
                  <tr>
                      <th>No</th>
                      <th>Kode Barang</th>
                      <th>Nama Barang</th>
                      <th>Kategori</th>
                      <th>Lokasi</th>
                      <th>Stok</th>
                      <th>Action</th>
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

  {{-- add modal --}}
  <div class="modal fade" id="dataModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Tambah Data</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="dataForm">
            <div class="mb-3">
              <label for="inputName" class="form-label">Nama Barang</label>
              <input type="text" class="form-control" id="inputName" required>
            </div>
            <div class="mb-3">
              <label for="inputEmail" class="form-label">Kategori</label>
              <input type="email" class="form-control" id="inputKategori" required>
            </div>
            <div class="mb-3">
              <label for="inputEmail" class="form-label">Lokasi</label>
              <input type="email" class="form-control" id="inputLokasi" required>
            </div>
            <div class="mb-3">
              <label for="inputEmail" class="form-label">Stok</label>
              <input type="email" class="form-control" id="inputStok" required>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
        </div>
      </div>
    </div>
  </div>
  
@endsection
@push('addon-script')
<script type="text/javascript">
  $(document).ready(function() {

    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': csrfToken
      }
    });

    $('#barang').DataTable({
      "ajax": {
        "url": "api/barang",
        "dataSrc": "data"
      },
      "columns": [
        { 
          "data": null, 
          "render": function (data, type, row, meta) {
            return meta.row + 1; 
          }
        },
        { "data": "kode" },       
        { "data": "nama_barang" },       
        { "data": "kategori" },      
        { "data": "lokasi" },        
        { "data": "stok" },        
        {
          "data": null,
          "render": function(data, type, row) {
            return `
              <button class="btn btn-warning btn-sm" onclick="openEditModal(${row.id})">Edit</button>
              <button class="btn btn-danger btn-sm" onclick="deleteRow(${row.id})">Delete</button>`;
          }
        }
      ]
    });
  });

  function openAddModal() {
    $('#modalLabel').text('Tambah Data');
    $('#dataForm')[0].reset(); 
    $('#saveChanges').attr('onclick', 'saveData()');
    $('#dataModal').modal('show');
  }


  function openEditModal(id) {
    $('#modalLabel').text('Edit Data');
    $('#dataForm')[0].reset(); 

    $.ajax({
      url: `api/barang/${id}`,
      method: 'GET',
      success: function(data) {
        $('#inputName').val(data.nama_barang);
        $('#inputKategori').val(data.kategori);
        $('#inputLokasi').val(data.lokasi);
        $('#inputStok').val(data.stok);
       
      }
    });

    $('#saveChanges').attr('onclick', `updateData(${id})`); 
    $('#dataModal').modal('show');
  }

  function saveData() {
    const data = {
      nama_barang: $('#inputName').val(),
      kategori: $('#inputKategori').val(),
      lokasi: $('#inputLokasi').val(),
      stok: $('#inputStok').val(),

    };

    $.ajax({
      url: 'api/barang',
      method: 'POST',
      data: JSON.stringify(data),
      contentType: 'application/json',
      success: function(response) {
        $('#dataModal').modal('hide');
        $('#barang').DataTable().ajax.reload(); 
      },
      error: function(xhr) {
        console.error('Error:', xhr.responseText);
      }
    });
  }

  function updateData(id) {
    const data = {
      nama_barang: $('#inputName').val(),
      kategori: $('#inputKategori').val(),
      lokasi: $('#inputLokasi').val(),
      stok: $('#inputStok').val(),
  
    };

    $.ajax({
      url: `api/barang/${id}`,
      method: 'PUT',
      data: JSON.stringify(data),
      contentType: 'application/json',
      success: function(response) {
        $('#dataModal').modal('hide');
        $('#barang').DataTable().ajax.reload(); 
      },
      error: function(xhr) {
        console.error('Error:', xhr.responseText);
      }
    });
  }
  function deleteRow(id) {
    if (confirm('Are you sure you want to delete this item?')) {
      $.ajax({
        url: `api/barang/${id}`,
        method: 'DELETE',
        success: function(response) {
          $('#barang').DataTable().ajax.reload(); 
          alert('Data deleted successfully');
        },
        error: function(xhr) {
          console.error('Error:', xhr.responseText);
          alert('Failed to delete data');
        }
      });
    }
  }
</script>

@endpush
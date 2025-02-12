@extends('etiket.admin.template.index')

@section('main')
<a class="btn btn-secondary w-fit text-start mb-3" href="{{ route('roles.index') }}">
   <i class="ti ti-arrow-left"></i>
   Kembali
</a>

<div class="card">
   <div class="card-header">
      <h5 class="text-white mb-0">Edit Permissions untuk Role: <b>{{ $role->name }}</b></h5>
   </div>
   <div class="card-body">
      <form action="{{ route('roles.updateAction') }}" method="POST">
         @csrf
         <input type="hidden" name="id" value="{{ $role->id }}">
         <div class="form-group">
            <label><b>Pilih Permissions:</b></label>
            <div class="row">
               @foreach ($permissions as $permission)
               <div class="col-md-4">
                  <div class="form-check">
                     <input class="form-check-input" type="checkbox"
                        name="permissions[]" value="{{ $permission->name }}"
                        id="perm_{{ $permission->id }}"
                        {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                     <label class="form-check-label" for="perm_{{ $permission->id }}">
                        {{ $permission->name }}
                     </label>
                  </div>
               </div>
               @endforeach
            </div>
         </div>
         <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-primary mt-3">Simpan Perubahan</button>
         </div>

      </form>
   </div>
</div>
@endsection
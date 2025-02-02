@extends('etiket.admin.template.index')

@section('css')


@endsection

@section('main')

<div class="card">
   <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
         <label class="text-2xl font-bold gk-text-base-black mb-2">Setting Web</label>
         <a class="btn btn-primary" href="{{route('admin.setting.add')}}">
            <i class="fa-regular fa-square-plus me-1"></i>
            Tambah Variabel
         </a>
      </div>
      <table class="table table-striped table-hover table-bordered tabel1">
         <thead>
            <tr>
               <th class="font-bold">No</th>
               <th class="font-bold">Nama</th>
               <th class="font-bold">Text1</th>
               <th class="font-bold">Text2</th>
               <th class="font-bold">Aksi</th>
            </tr>
         </thead>
         <tbody>
            @foreach ($setting as $item)
            <tr>
               <td class="py-2">{{$loop->iteration}}</td>
               <td class="py-2">{{$item->nama}}</td>
               <td class="py-2">{{$item->text1}}</td>
               <td class="py-2">{{$item->text2}}</td>
               <td class="py-2">
                  <a href="{{ route('admin.setting.update', ['id' => $item->id]) }}" class="btn btn-primary p-2 lh-1"><i class="fa-regular fa-pen-to-square"></i></a>
                  @if ($item->canDelete)
                  <form method="post" action="{{ route('admin.setting.deleteAction') }}" style="display: inline-block;">
                     @csrf
                     <input type="hidden" name="id" value="{{$item->id}}">
                     <button type="submit" class="btn btn-danger p-2 lh-1"><i class="fa-solid fa-trash-can"></i></button>
                  </form>
                  @endif
               </td>
            </tr>
            @endforeach
         </tbody>
      </table>


   </div>
</div>


@endsection

@section('js')

@endsection
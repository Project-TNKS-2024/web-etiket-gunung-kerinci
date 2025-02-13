<?php

namespace App\Http\Controllers\etiket\admin\master;

use App\Http\Controllers\AdminController;
use App\Models\destinasi;
use App\Models\DestinasiUser;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountAdminController extends AdminController
{
    /**
     * Menampilkan daftar admin.
     */
    public function index()
    {
        $admins = User::where('role', 'admin')->with('destinasis', 'biodata')->get();
        // return $admins;
        return view('etiket.admin.master.akunAdmin.index', compact('admins'));
    }

    /**
     * Menampilkan form tambah admin.
     */
    public function create()
    {
        $roles = Role::all();
        $destinasis = destinasi::all(); // Ambil semua destinasi
        return view('etiket.admin.master.akunAdmin.update', [
            'roles' => $roles,
            'destinasis' => $destinasis,
        ]);
    }

    /**
     * Menyimpan admin baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'role' => 'required|exists:roles,name',
            'destination_ids' => 'array|exists:destinasis,id',
        ]);

        $user = User::create([
            'email' => $request->email,
            'role' => 'admin',
            'password' => Hash::make('password123'), // Atur default password atau gunakan email verifikasi
        ]);

        // Assign role ke user
        $user->assignRole($request->role);

        // Hubungkan user dengan destinasi yang dipilih
        if ($request->has('destination_ids')) {
            $user->destinasis()->sync($request->destination_ids);
        }

        // kirim email verifikasi
        $user->sendEmailVerificationNotification();

        return redirect()->route('admins.akun.index')->with('success', 'Admin berhasil ditambahkan.');
    }


    /**
     * Menampilkan form edit admin.
     */
    public function edit($id)
    {
        $admin = User::with('destinasis')->findOrFail($id);
        $roles = Role::all();
        $destinasis = destinasi::all();

        // return $admin;
        // return $admin->destinasis->pluck('id')->toArray();

        return view('etiket.admin.master.akunAdmin.update', [
            'admin' => $admin,
            'roles' => $roles,
            'destinasis' => $destinasis,
        ]);
    }

    /**
     * Memperbarui data admin.
     */
    public function update(Request $request, $id)
    {
        $admin = User::findOrFail($id);

        $request->validate([
            'role' => 'required|exists:roles,name',
            'destination_ids' => 'array|exists:destinasis,id',
        ]);

        // return $request;
        // return $admin;

        $admin->syncRoles([$request->role]);

        // Perbarui data penanggung jawab destinasi dengan sync()
        if ($request->has('destination_ids')) {
            $dataDestinasi = collect($request->destination_ids)->mapWithKeys(function ($destinasiId) {
                return [$destinasiId => ['is_penanggungjawab' => true]];
            });

            $admin->destinasis()->sync($dataDestinasi);
        } else {
            // Jika tidak ada destinasi dikirim, kosongkan relasi
            $admin->destinasis()->detach();
        }

        return redirect()->route('admins.akun.index')->with('success', 'Admin berhasil diperbarui.');
    }

    /**
     * Menghapus admin.
     */
    public function destroy(Request $request)
    {
        $admin = User::findOrFail($request->id);
        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'Admin berhasil dihapus.');
    }
}

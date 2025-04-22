<?php

namespace App\Http\Controllers\etiket\admin\master;

use App\Http\Controllers\AdminController;
use App\Models\bio_pendaki;
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
            'fullName' => 'required|string',
            'nip' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|exists:roles,name',
            'destination_ids' => 'array|exists:destinasis,id',
        ]);

        // pisah full name menjadi first_name dan last_name
        $nameParts = explode(' ', $request->fullName);
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

        $biodata = bio_pendaki::create([
            'nik' => $request->nip,
            'kenegaraan' => 'ID',
            'first_name' => $firstName,
            'last_name' => $lastName,

            'no_hp' => '0000000000000000',
            'jeniss_kelamin' => 'L',
            'tempat_lahir' => '-',
            'tanggal_lahir' => now(),
            'lampiran_identitas' => '-',

        ]);

        $user = User::create([
            'email' => $request->email,
            'role' => 'admin',
            'id_bio' => $biodata->id,
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

        return redirect()->route('admins.akun.index')->with('success', 'Admin berhasil ditambahkan. Password default: "password123". Harap segera mengganti password.');
    }


    /**
     * Menampilkan form edit admin.
     */
    public function edit($id)
    {
        $admin = User::with('destinasis', 'biodata')->findOrFail($id);
        $roles = Role::all();
        $destinasis = destinasi::all();

        // return $admin;

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
        $admin = User::with('biodata')->findOrFail($id);

        // cek id user ada atau tidak
        if (!$admin) {
            return redirect()->route('admins.akun.index')->with('error', 'Admin tidak ditemukan.');
        }

        $request->validate([
            'fullName' => 'required|string',
            'nip' => 'required|string',
            'role' => 'required|exists:roles,name',
            'destination_ids' => 'array|exists:destinasis,id',
        ]);

        // update biodata
        // pisah full name menjadi first_name dan last_name
        $nameParts = explode(' ', $request->fullName);
        $firstName = $nameParts[0];
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

        if ($admin->biodata) {
            $admin->biodata->update([
                'nik' => $request->nip,
                'kenegaraan' => 'ID',
                'first_name' => $firstName,
                'last_name' => $lastName,
            ]);
        } else {
            $biodata = bio_pendaki::create([
                'nik' => $request->nip,
                'kenegaraan' => 'ID',
                'first_name' => $firstName,
                'last_name' => $lastName,

                'no_hp' => '0000000000000000',
                'jeniss_kelamin' => 'L',
                'tempat_lahir' => '-',
                'tanggal_lahir' => now(),
                'lampiran_identitas' => '-',

            ]);

            $admin->update([
                'id_bio' => $biodata->id,
            ]);
        }

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
        $biodata = $admin->biodata;
        $biodata->update([
            'nik' => '-',
            'kenegaraan' => '-',
            'no_hp' => '-',
            'tempat_lahir' => '-',
            'tanggal_lahir' => now(),
            'lampiran_identitas' => '-',
        ]);
        $admin->update([
            'email' => '-',
            'role' => '-',
            'password' => '-',
        ]);
        // hapus permision
        $admin->syncRoles([]);
        // hapus destinasi
        $admin->destinasis()->detach();


        return redirect()->route('admins.akun.index')->with('success', 'Admin berhasil dihapus.');
    }
}

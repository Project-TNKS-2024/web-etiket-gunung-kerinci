<?php

namespace App\Http\Controllers\etiket\admin\master;

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends AdminController
{
    // Menampilkan daftar role dan permission
    public function index()
    {
        // return auth()->user();

        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('etiket.admin.master.permision.index', compact('roles', 'permissions'));
    }

    // Menambahkan role baru
    public function roleAddAction(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);

        Role::create(['name' => $request->name]);

        return back()->with('success', 'Role berhasil ditambahkan!');
    }

    // Menambahkan permission baru
    public function permissionAddAction(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name'
        ]);

        Permission::create(['name' => $request->name]);

        return back()->with('success', 'Permission berhasil ditambahkan!');
    }

    public function rolesUpdate($id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::all();
        return view('etiket.admin.master.permision.updateRolePermission', compact('role', 'permissions'));
    }


    public function rolesUpdateAction(Request $request)
    {
        // Validasi input
        $request->validate([
            'id' => 'required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        // Ambil role berdasarkan ID
        $role = Role::findOrFail($request->id);

        // Sinkronisasi permission yang dipilih
        $role->syncPermissions($request->permissions ?? []);

        // Redirect dengan pesan sukses
        return redirect()->route('roles.index')->with('success', 'Permissions berhasil diperbarui.');
    }

    public function roleDeleteAction(Request $request)
    {
        // Validasi request
        $request->validate([
            'id' => 'required|exists:roles,id'
        ]);

        // Ambil role berdasarkan ID
        $role = Role::findOrFail($request->id);

        // jika role->id == 1 maka tidak bisa dihapus
        if ($role->id == 1) {
            return redirect()->route('roles.index')->with('error', 'Role tidak bisa dihapus.');
        }
        // Hapus role
        $role->delete();

        // Redirect dengan pesan sukses
        return redirect()->route('roles.index')->with('success', 'Role berhasil dihapus.');
    }
}

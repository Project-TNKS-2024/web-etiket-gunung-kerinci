<?php

namespace App\Http\Controllers\etiket\admin;

use App\Http\Controllers\AdminController;
use App\Models\setting;
use Illuminate\Http\Request;

class settingController extends AdminController
{
    private $setting;
    public function __construct()
    {
        $this->setting = setting::orderBy('created_at', 'asc')->get();
    }
    public function index()
    {
        // return $this->setting;
        return view('etiket.admin.setting.index', [
            'setting' => $this->setting
        ]);
    }
    public function add()
    {
        return view('etiket.admin.setting.form', [
            'title' => 'Tambah Variabel',
            'action' => route('admin.setting.addAction'),
        ]);
    }
    public function addAction(Request $request)
    {
        // validasi
        $request->validate([
            'nama' => 'required|string',
            'text1' => 'nullable|string',
            'text2' => 'nullable|string',
        ]);
        setting::create([
            'nama' => $request->nama,
            'text1' => $request->text1,
            'text2' => $request->text2,
        ]);
        return redirect()->route('admin.setting')->with('success', 'Data berhasil ditambahkan');
    }
    public function update($id)
    {
        $var = setting::find($id);
        // return $var;
        return view('etiket.admin.setting.form', [
            'title' => 'Update Variabel',
            'var' => $var,
            'action' => route('admin.setting.updateAction'),
        ]);
    }
    public function updateAction(Request $request)
    {
        // validasi
        $request->validate([
            'id' => 'required|exists:settings,id',
            'nama' => 'required|string',
            'text1' => 'nullable|string',
            'text2' => 'nullable|string',
        ]);
        $var = setting::find($request->id);
        if (!$var->canDelete) {
            $var->update([
                'text1' => $request->text1,
                'text2' => $request->text2,
            ]);
        } else {
            $var->update([
                'nama' => $request->nama,
                'text1' => $request->text1,
                'text2' => $request->text2,
            ]);
        }
        return redirect()->route('admin.setting')->with('success', 'Data berhasil diubah');
    }
    public function deleteAction(Request $request)
    {
        // validasi
        $request->validate([
            'id' => 'required|exists:settings,id',
        ]);
        $setting = setting::find($request->id);
        if (!$setting->canDelete) {
            return redirect()->route('admin.setting')->with('error', 'Data tidak bisa dihapus');
        }
        $setting->delete();
        return redirect()->route('admin.setting')->with('success', 'Data berhasil dihapus');
    }
}

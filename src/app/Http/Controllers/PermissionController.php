<?php

namespace App\Http\Controllers;

use App\Http\Resources\DetailPermissionResource;
use App\Http\Resources\DetailPermissionVerifikatorResource;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\PermissionVerifikatorResource;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class PermissionController extends Controller
{
    public function allVerifikator() {
        $permissions = Permission::with('user')->get();

        return PermissionVerifikatorResource::collection($permissions);
    }

    public function all() {
        $auth = Auth::user()->id;
        $permissions = Permission::where('user_id', $auth)->get();

        return PermissionResource::collection($permissions);
    }

    public function add(Request $request) {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'needs' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'description.required' => 'Deskripsi tidak boleh kosong',
            'needs.required' => 'Kebutuhan tidak boleh kosong',
            'description.required' => 'Deskripsi tidak boleh kosong',
            'start_date.required' => 'Tanggal mulai tidak boleh kosong',
            'end_date.required' => 'Tanggal akhir tidak boleh kosong',
        ]);

        $auth = Auth::user()->id;

        Permission::create([
            'user_id' => $auth,
            'name' => $request->name,
            'description' => $request->description,
            'needs' => $request->needs,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'Menunggu Persetujuan',
        ]);

        return response()->json(['message' => 'Berhasil menambahkan pengajuan'], 200);
    }

    public function detailVerifikator($id) {
        $permission = Permission::with('user')->find($id);

        return new DetailPermissionVerifikatorResource($permission);
    }

    public function detail($id) {
        $permission = Permission::find($id);

        return new DetailPermissionResource($permission);
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'needs' => 'required',
            'description' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'status' => 'required',
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'description.required' => 'Deskripsi tidak boleh kosong',
            'needs.required' => 'Kebutuhan tidak boleh kosong',
            'description.required' => 'Deskripsi tidak boleh kosong',
            'start_date.required' => 'Tanggal mulai tidak boleh kosong',
            'end_date.required' => 'Tanggal akhir tidak boleh kosong',
            'status.required' => 'Status tidak boleh kosong',
        ]);

        $permission = Permission::find($id);
        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
            'needs' => $request->needs,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
        ]);

        return response()->json(['message' => 'Berhasil mengubah pengajuan'], 200);
    }

    public function updateVerifikator(Request $request, $id){
        $request->validate([
            'status' => 'required',
        ], [
            'status.required' => 'Status tidak boleh kosong',
        ]);

        $permission = Permission::find($id);
        $permission->update([
            'status' => $request->status,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Berhasil mengubah status pengajuan'], 200);
    }

    public function delete($id) {
        $permission = Permission::find($id);
        $permission->delete();

        return response()->json(['message' => 'Berhasil menghapus pengajuan'], 200);
    }
}

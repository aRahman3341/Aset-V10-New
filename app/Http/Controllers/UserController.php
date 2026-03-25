<?php

namespace App\Http\Controllers;

use App\Models\employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function get_data()
    {
        $sess = Session::all();

        $query1 = DB::table('employees')
            ->select('id', 'nip', 'name', 'email', 'jabatan', 'alamat', 'gender', 'phone_number', DB::raw("'employee' as type"));

        $query2 = DB::table('users')
            ->select('id', 'nip', 'name', 'email', 'jabatan', 'alamat', 'gender', 'phone_number', DB::raw("'user' as type"));

        $allData = $query1->union($query2)->get();
        $rank    = $allData;

        $pageSize    = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $employee    = new LengthAwarePaginator(
            $allData->forPage($currentPage, $pageSize),
            $allData->count(),
            $pageSize,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('pengguna.getData', compact('employee', 'sess', 'rank'));
    }

    public function addData()
    {
        return view('pengguna.add');
    }

    public function dataStore(Request $request)
    {
        $request->validate([
            'nip'          => 'required|numeric',
            'name'         => 'required',
            'email'        => 'required|email',
            'jabatan'      => 'required|in:Admin,Manager,Operator,Karyawan',
            'alamat'       => 'required',
            'gender'       => 'required',
            'phone_number' => 'required|numeric',
        ], [
            'nip.required'          => 'NIP harus diisi',
            'nip.numeric'           => 'NIP harus berupa angka',
            'name.required'         => 'Nama harus diisi',
            'email.required'        => 'Email harus diisi',
            'email.email'           => 'Format email tidak valid',
            'jabatan.required'      => 'Jabatan harus dipilih',
            'jabatan.in'            => 'Jabatan tidak valid',
            'alamat.required'       => 'Alamat harus diisi',
            'gender.required'       => 'Jenis kelamin harus dipilih',
            'phone_number.required' => 'No Handphone harus diisi',
            'phone_number.numeric'  => 'No Handphone harus berupa angka',
        ]);

        $jabatan = $request->jabatan;

        if ($jabatan === 'Karyawan') {
            // Karyawan → tabel employees, tidak punya password
            DB::table('employees')->insert([
                'nip'          => $request->nip,
                'name'         => $request->name,
                'email'        => $request->email,
                'jabatan'      => $jabatan,
                'alamat'       => $request->alamat,
                'gender'       => $request->gender,
                'phone_number' => $request->phone_number,
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]);
        } else {
            // Admin, Manager, Operator → tabel users, password default = NIP
            DB::table('users')->insert([
                'nip'          => $request->nip,
                'name'         => $request->name,
                'email'        => $request->email,
                'jabatan'      => $jabatan,
                'alamat'       => $request->alamat,
                'gender'       => $request->gender,
                'phone_number' => $request->phone_number,
                'password'     => Hash::make($request->nip),
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]);
        }

        return redirect('/pengguna')->with('success', 'Pengguna berhasil ditambahkan. Password default: NIP');
    }

    public function editData(Request $request, $id)
    {
        $sess = session()->all();
        $type = $request->query('type');

        try {
            $data = ($type === 'user') ? User::findOrFail($id) : employee::findOrFail($id);
            return view('pengguna.update', ['employe' => $data, 'sess' => $sess]);
        } catch (ModelNotFoundException $e) {
            return redirect('/pengguna')->with('error', 'Data tidak ditemukan.');
        }
    }

    public function update(Request $request, $id)
    {
        $jabatan = $request->jabatan;

        $rules = [
            'nip'          => 'required|numeric',
            'name'         => 'required',
            'email'        => 'required|email',
            'jabatan'      => 'required',
            'alamat'       => 'required',
            'gender'       => 'required',
            'phone_number' => 'required|numeric',
        ];

        // Validasi password hanya jika diisi dan role bukan Karyawan
        if ($jabatan !== 'Karyawan' && $request->filled('password')) {
            $rules['password'] = [
                'min:8',
                'regex:/[0-9]/',       // harus ada angka
                'regex:/[\W_]/',       // harus ada simbol
                'confirmed',
            ];
        }

        $messages = [
            'nip.required'              => 'NIP harus diisi',
            'nip.numeric'               => 'NIP harus berupa angka',
            'name.required'             => 'Nama harus diisi',
            'email.required'            => 'Email harus diisi',
            'email.email'               => 'Format email tidak valid',
            'jabatan.required'          => 'Jabatan harus dipilih',
            'alamat.required'           => 'Alamat harus diisi',
            'gender.required'           => 'Jenis kelamin harus dipilih',
            'phone_number.required'     => 'No Handphone harus diisi',
            'phone_number.numeric'      => 'No Handphone harus berupa angka',
            'password.min'              => 'Password minimal 8 karakter',
            'password.regex'            => 'Password harus mengandung angka dan simbol (contoh: Abc123!)',
            'password.confirmed'        => 'Konfirmasi password tidak cocok',
        ];

        $request->validate($rules, $messages);

        if ($jabatan === 'Karyawan') {
            employee::where('id', $id)->update([
                'nip'          => $request->nip,
                'name'         => $request->name,
                'jabatan'      => $jabatan,
                'email'        => $request->email,
                'alamat'       => $request->alamat,
                'gender'       => $request->gender,
                'phone_number' => $request->phone_number,
                'updated_at'   => Carbon::now(),
            ]);
        } else {
            $update = [
                'nip'          => $request->nip,
                'name'         => $request->name,
                'jabatan'      => $jabatan,
                'email'        => $request->email,
                'alamat'       => $request->alamat,
                'gender'       => $request->gender,
                'phone_number' => $request->phone_number,
                'updated_at'   => Carbon::now(),
            ];

            // Update password hanya jika diisi
            if ($request->filled('password')) {
                $update['password'] = Hash::make($request->password);
            }

            User::where('id', $id)->update($update);
        }

        return redirect('/pengguna')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        employee::destroy($id);
        User::destroy($id);
        return redirect('/pengguna')->with('success', 'Data pengguna berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $sess  = Session::all();
        $query = $request->input('query');

        $users     = User::where('nip',  'LIKE', "%$query%")
                         ->orWhere('name', 'LIKE', "%$query%")
                         ->orWhere('email','LIKE', "%$query%")->get();
        $employees = employee::where('nip',  'LIKE', "%$query%")
                             ->orWhere('name', 'LIKE', "%$query%")
                             ->orWhere('email','LIKE', "%$query%")->get();

        $allData     = $employees->merge($users);
        $rank        = $allData;
        $pageSize    = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $employee    = new LengthAwarePaginator(
            $allData->forPage($currentPage, $pageSize),
            $allData->count(), $pageSize, $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('pengguna.getData', compact('employee', 'rank', 'sess'));
    }

    public function filter(Request $request)
    {
        $sess    = Session::all();
        $jabatan = $request->input('jabatan');
        $gender  = $request->input('gender');

        $usersQ = User::query();
        $empQ   = employee::query();

        if ($jabatan !== 'all') {
            $usersQ->where('jabatan', $jabatan);
            $empQ->where('jabatan', $jabatan);
        }
        if ($gender !== 'all') {
            $usersQ->where('gender', $gender);
            $empQ->where('gender', $gender);
        }

        $allData  = $empQ->get()->merge($usersQ->get());
        $rank     = $allData;
        $pageSize = 10;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $employee = new LengthAwarePaginator(
            $allData->forPage($currentPage, $pageSize),
            $allData->count(), $pageSize, $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return view('pengguna.getData', compact('employee', 'rank', 'sess'));
    }
}
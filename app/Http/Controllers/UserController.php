<?php

namespace App\Http\Controllers;

use App\Models\employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function get_data()
    {
        $sess      = Session::all();
        $users     = User::paginate(10, ['*'], 'users_page');
        $employees = employee::paginate(10, ['*'], 'emp_page');

        return view('pengguna.getData', compact('users', 'employees', 'sess'));
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

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan. Password default: NIP');
    }

    public function editData(Request $request, $id)
    {
        $sess = session()->all();
        $type = $request->query('type');

        try {
            $data = ($type === 'user') ? User::findOrFail($id) : employee::findOrFail($id);
            return view('pengguna.update', ['employe' => $data, 'sess' => $sess]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('pengguna.index')->with('error', 'Data tidak ditemukan.');
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

        if ($jabatan !== 'Karyawan' && $request->filled('password')) {
            $rules['password'] = [
                'min:8',
                'regex:/[0-9]/',
                'regex:/[\W_]/',
                'confirmed',
            ];
        }

        $messages = [
            'nip.required'          => 'NIP harus diisi',
            'nip.numeric'           => 'NIP harus berupa angka',
            'name.required'         => 'Nama harus diisi',
            'email.required'        => 'Email harus diisi',
            'email.email'           => 'Format email tidak valid',
            'jabatan.required'      => 'Jabatan harus dipilih',
            'alamat.required'       => 'Alamat harus diisi',
            'gender.required'       => 'Jenis kelamin harus dipilih',
            'phone_number.required' => 'No Handphone harus diisi',
            'phone_number.numeric'  => 'No Handphone harus berupa angka',
            'password.min'          => 'Password minimal 8 karakter',
            'password.regex'        => 'Password harus mengandung angka dan simbol',
            'password.confirmed'    => 'Konfirmasi password tidak cocok',
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

            if ($request->filled('password')) {
                $update['password'] = Hash::make($request->password);
            }

            User::where('id', $id)->update($update);
        }

        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        User::where('id', $id)->update([
            'password'   => Hash::make($user->nip),
            'updated_at' => Carbon::now(),
        ]);
        return redirect()->route('pengguna.index')->with('success', 'Password berhasil direset ke NIP.');
    }

    public function destroy($id, Request $request)
    {
        $type = $request->query('type');
        if ($type === 'employee') {
            employee::destroy($id);
        } else {
            User::destroy($id);
        }
        return redirect()->route('pengguna.index')->with('success', 'Data pengguna berhasil dihapus.');
    }

    public function search(Request $request)
    {
        $sess  = Session::all();
        $query = $request->input('query');

        $users     = User::where('nip',   'LIKE', "%$query%")
                         ->orWhere('name',  'LIKE', "%$query%")
                         ->orWhere('email', 'LIKE', "%$query%")
                         ->paginate(10, ['*'], 'users_page');

        $employees = employee::where('nip',   'LIKE', "%$query%")
                             ->orWhere('name',  'LIKE', "%$query%")
                             ->orWhere('email', 'LIKE', "%$query%")
                             ->paginate(10, ['*'], 'emp_page');

        return view('pengguna.getData', compact('users', 'employees', 'sess'));
    }

    public function filter(Request $request)
    {
        $sess    = Session::all();
        $jabatan = $request->input('jabatan');
        $gender  = $request->input('gender');

        $usersQ = User::query();
        $empQ   = employee::query();

        if ($jabatan && $jabatan !== 'all') {
            $usersQ->where('jabatan', $jabatan);
            $empQ->where('jabatan', $jabatan);
        }
        if ($gender && $gender !== 'all') {
            $usersQ->where('gender', $gender);
            $empQ->where('gender', $gender);
        }

        $users     = $usersQ->paginate(10, ['*'], 'users_page');
        $employees = $empQ->paginate(10, ['*'], 'emp_page');

        return view('pengguna.getData', compact('users', 'employees', 'sess'));
    }
}
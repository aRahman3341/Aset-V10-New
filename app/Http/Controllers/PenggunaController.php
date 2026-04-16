<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Employee;

class PenggunaController extends Controller
{
    // ═══════════════════════════════════════════════════
    //  Roles yang bisa login (ada di tabel users)
    // ═══════════════════════════════════════════════════
    const LOGIN_ROLES = ['admin', 'manager', 'operator'];

    // ─────────────────────────────────────────
    //  INDEX — tampilkan semua pengguna
    // ─────────────────────────────────────────
    public function index()
    {
        $sess = session('user');

        // Gabungkan users + employees agar tampil semua
        $users     = User::orderBy('name')->paginate(10, ['*'], 'upage');
        $employees = Employee::orderBy('name')->paginate(10, ['*'], 'epage');
        $rank      = User::select('jabatan', 'gender')->get()
                        ->merge(Employee::select('jabatan', 'gender')->get());

        return view('pengguna.getData', compact('users', 'employees', 'rank', 'sess'));
    }

    // ─────────────────────────────────────────
    //  SEARCH
    // ─────────────────────────────────────────
    public function search(Request $request)
    {
        $sess  = session('user');
        $query = $request->input('query', '');

        $users = User::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('nip',  'like', "%{$query}%")
              ->orWhere('email','like', "%{$query}%");
        })->paginate(10, ['*'], 'upage');

        $employees = Employee::where(function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('nip',  'like', "%{$query}%")
              ->orWhere('email','like', "%{$query}%");
        })->paginate(10, ['*'], 'epage');

        $rank = User::select('jabatan', 'gender')->get()
                    ->merge(Employee::select('jabatan', 'gender')->get());

        return view('pengguna.getData', compact('users', 'employees', 'rank', 'sess'));
    }

    // ─────────────────────────────────────────
    //  FILTER
    // ─────────────────────────────────────────
    public function filter(Request $request)
    {
        $sess    = session('user');
        $jabatan = $request->input('jabatan', 'all');
        $gender  = $request->input('gender',  'all');

        $uQuery = User::query();
        $eQuery = Employee::query();

        if ($jabatan !== 'all') {
            $uQuery->where('jabatan', $jabatan);
            $eQuery->where('jabatan', $jabatan);
        }
        if ($gender !== 'all') {
            $uQuery->where('gender', $gender);
            $eQuery->where('gender', $gender);
        }

        $users     = $uQuery->paginate(10, ['*'], 'upage');
        $employees = $eQuery->paginate(10, ['*'], 'epage');
        $rank      = User::select('jabatan', 'gender')->get()
                        ->merge(Employee::select('jabatan', 'gender')->get());

        return view('pengguna.getData', compact('users', 'employees', 'rank', 'sess'));
    }

    // ─────────────────────────────────────────
    //  ADD FORM
    // ─────────────────────────────────────────
    public function add()
    {
        $sess = session('user');

        if ($sess['jabatan'] !== 'admin') {
            abort(403);
        }

        return view('pengguna.add', compact('sess'));
    }

    // ─────────────────────────────────────────
    //  STORE — simpan pengguna baru
    //  • Login roles  → simpan ke tabel users, password default = NIP
    //  • Karyawan     → simpan ke tabel employees (tanpa password)
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $sess = session('user');

        if ($sess['jabatan'] !== 'admin') {
            abort(403);
        }

        $jabatan = strtolower($request->input('jabatan', ''));

        $request->validate([
            'nip'          => 'required|string|max:12',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'jabatan'      => 'required|string',
            'gender'       => 'required|in:L,P',
            'alamat'       => 'required|string|max:100',
            'phone_number' => 'required|string|max:45',
        ]);

        $data = $request->only(['nip', 'name', 'email', 'jabatan', 'gender', 'alamat', 'phone_number']);

        if (in_array($jabatan, self::LOGIN_ROLES)) {
            User::create(array_merge($data, [
                'password' => Hash::make($request->nip),
            ]));
        } else {
            Employee::create($data);
        }

        return redirect()->route('pengguna.getData')
            ->with('success', "Pengguna berhasil ditambahkan. Password default: NIP");
    }

    // ─────────────────────────────────────────
    //  EDIT FORM
    // ─────────────────────────────────────────
    public function edit(Request $request, $id)
    {
        $sess  = session('user');
        $type  = $request->input('type', 'user'); 

        if ($sess['jabatan'] === 'operator') {
            if ($sess['id'] != $id || $sess['type'] !== $type) {
                abort(403);
            }
        } elseif (!in_array($sess['jabatan'], ['admin', 'manager'])) {
            abort(403);
        }

        if ($type === 'employee') {
            $employe = Employee::findOrFail($id);
        } else {
            $employe = User::findOrFail($id);
        }

        return view('pengguna.update', compact('employe', 'sess', 'type'));
    }

    // ─────────────────────────────────────────
    //  UPDATE — simpan perubahan
    //  Password: wajib min 8 char, ada angka & simbol
    // ─────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $sess = session('user');
        $type = $request->input('type', 'user');

        // Operator hanya bisa update diri sendiri
        if ($sess['jabatan'] === 'operator') {
            if ($sess['id'] != $id || $sess['type'] !== $type) {
                abort(403);
            }
        } elseif (!in_array($sess['jabatan'], ['admin', 'manager'])) {
            abort(403);
        }

        $rules = [
            'nip'          => 'required|string|max:12',
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'jabatan'      => 'required|string',
            'gender'       => 'required|in:L,P',
            'alamat'       => 'required|string|max:100',
            'phone_number' => 'required|string|max:45',
        ];

        // Validasi password hanya jika diisi & role bisa login
        $jabatan = strtolower($request->input('jabatan', ''));
        if ($request->filled('password') && in_array($jabatan, self::LOGIN_ROLES)) {
            $rules['password'] = [
                'string',
                'min:8',
                'regex:/[0-9]/',
                'regex:/[\W_]/',
            ];
        }

        $request->validate($rules, [
            'password.regex' => 'Password harus mengandung minimal 1 angka dan 1 simbol.',
            'password.min'   => 'Password minimal 8 karakter.',
        ]);

        $data = $request->only(['nip', 'name', 'email', 'jabatan', 'gender', 'alamat', 'phone_number']);

        if ($type === 'employee') {
            $employe = Employee::findOrFail($id);
            $employe->update($data);
        } else {
            $employe = User::findOrFail($id);
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }
            $employe->update($data);
        }

        return redirect()->route('pengguna.getData')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    // ─────────────────────────────────────────
    //  DESTROY — hapus pengguna
    // ─────────────────────────────────────────
    public function destroy(Request $request, $id)
    {
        $sess = session('user');

        if ($sess['jabatan'] !== 'admin') {
            abort(403);
        }

        $type = $request->input('type', 'user');

        if ($type === 'employee') {
            Employee::findOrFail($id)->delete();
        } else {
            User::findOrFail($id)->delete();
        }

        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    // ─────────────────────────────────────────
    //  RESET PASSWORD ke default (NIP)
    //  Hanya Admin & Manager
    // ─────────────────────────────────────────
    public function resetPassword(Request $request, $id)
    {
        $sess = session('user');

        if (!in_array($sess['jabatan'], ['admin', 'manager'])) {
            abort(403);
        }

        $user = User::findOrFail($id);
        $user->update(['password' => Hash::make($user->nip)]);

        return back()->with('success', "Password berhasil direset ke NIP ({$user->nip}).");
    }
}
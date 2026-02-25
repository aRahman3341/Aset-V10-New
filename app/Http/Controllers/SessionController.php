<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionController extends Controller
{
	public function formLogin()
	{
		return view('auth.formLogin');
	}

	public function login(Request $request)
	{
		// Session::flash('email',$request->email);
		$request->validate([
			'email'=>'required',
			'password'=>'required',
		]);

		$login = [
			'email'=>$request->email,
			'password'=>$request->password,
		];

		if (Auth::attempt($login)) {
			//return 'sukses';
            $user = Auth::user();
            $request->session()->put('email', $user->email);
            $request->session()->put('id', $user->id);
            $request->session()->put('name', $user->name);
            $request->session()->put('nip', $user->nip);
            $request->session()->put('password', $user->password);
            $request->session()->put('jabatan', $user->jabatan);

			return redirect('/')->with('success', 'berhasil login');
		} else {
			//return 'gagal';
			return redirect('session')->withErrors('Username dan password yang dimasukan salah');
		}
	}

	public function logout()
	{
        Auth::logout();
        Session::forget('email');
		return redirect('/session')->with('success', 'Berhasil logout');
	}

	public function autoLogin()
	{
		$email = 'SuperUser@mail.com';
		$password = '123456';

		$login = [
			'email' => $email,
			'password' => $password,
		];

		if (Auth::attempt($login)) {
			$user = Auth::user();
			session()->put('email', $user->email);
			session()->put('id', $user->id);
			session()->put('name', $user->name);
			session()->put('nip', $user->nip);
			session()->put('password', $user->password);
			session()->put('jabatan', $user->jabatan);

			return redirect('/')->with('success', 'Berhasil login');
		} else {
			return redirect('session')->withErrors('Username dan password yang dimasukkan salah');
		}
	}
}

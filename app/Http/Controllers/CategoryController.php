<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
	public function get_data()
	{
		$paging=10;
		$category = Category::paginate($paging);

		return view('category.getData', compact(['category']));
	}

	public function dataStore(Request $request)
	{

        $validator = $request->validate([
            'code'   => 'required|numeric',
            'name'   => 'required',
		],[
			'code.required' => 'Kode tidak boleh kosong',
			'code.numeric' => 'Kode harus nomor',
			'name.required' => 'Nama kategori harus diisi',
		]);
		DB::table('categories')->insert([
			'code' => $validator['code'],
			'name' => $validator['name'],
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),
		]);

		return redirect('/category');
	}

	public function update(Request $request, $id)
	{
        $validator = $request->validate([
            'code'   => 'required|numeric',
            'name'   => 'required',
		],[
			'code.required' => 'Kode tidak boleh kosong',
			'code.numeric' => 'Kode harus nomor',
			'name.required' => 'Nama kategori harus diisi',
		]);
		$update = [
			'code' => $validator['code'],
			'name' => $validator['name'],
			'updated_at' => Carbon::now()
		];
		Category::where('id', $id)->update($update);
		return redirect('/category');
	}

	public function destroy($id)
	{
		Category::destroy($id);
		return redirect('/category');
	}

    public function search(Request $request)
    {
        $paging = 10;
        $query = $request->input('query');

        $category = Category::where('code', 'LIKE', '%' . $query . '%')
            ->orWhere('name', 'LIKE', '%' . $query . '%')
            ->paginate($paging);

		return view('category.getData', compact(['category']));
    }
}

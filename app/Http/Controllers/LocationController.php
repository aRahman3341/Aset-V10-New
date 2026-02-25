<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LocationController extends Controller
{
	public function get_data()
	{
		$paging=10;
        $locations = DB::table('locations')->get();
		$location = Location::paginate($paging);

		return view('location.getData', compact(['location', 'locations']));
	}

	public function dataStore(Request $request)
	{

        $validator = $request->validate([
            'office'   => 'required',
            'floor'   => 'required|numeric',
            'room'   => 'required',
		],[
			'office.required' => 'Nama gedung tidak boleh kosong',
			'floor.required' => 'Lantai tidak boleh kosong',
			'room.required' => 'Nama ruangan tidak boleh kosong',
			'floor.numeric' => 'Lantai harus nomor',
		]);
		DB::table('locations')->insert([
			'office' => $validator['office'],
			'floor' => $validator['floor'],
			'room' => $validator['room'],
			'created_at' => Carbon::now(),
			'updated_at' => Carbon::now(),
		]);

		return redirect('/location');
	}

	public function update(Request $request, $id)
	{
        $validator = $request->validate([
            'office'   => 'required',
            'floor'   => 'required|numeric',
            'room'   => 'required',
		],[
			'office.required' => 'Nama gedung tidak boleh kosong',
			'floor.required' => 'Lantai tidak boleh kosong',
			'room.required' => 'Nama ruangan tidak boleh kosong',
			'floor.numeric' => 'Lantai harus nomor',
		]);

		$update = [
			'office' => $validator['office'],
			'floor' => $validator['floor'],
			'room' => $validator['room'],
			'updated_at' => Carbon::now()
		];
		Location::where('id', $id)->update($update);
		return redirect('/location');
	}

	public function destroy($id)
	{
		Location::destroy($id);
		return redirect('/location');
	}

    public function search(Request $request)
    {
        $paging = 10;
        $query = $request->input('query');
        $locations = DB::table('locations')->get();


        $location = Location::where('office', 'LIKE', '%' . $query . '%')
            ->orWhere('floor', 'LIKE', '%' . $query . '%')
            ->orWhere('room', 'LIKE', '%' . $query . '%')
            ->paginate($paging);

        return view('location.getData', compact('location', 'locations'));
    }

    public function filter(Request $request)
    {

        $query = DB::table('locations');
        $locations = DB::table('locations')->get();

        $office = $request->input('gedung');
        $floor = $request->input('lantai');
        $room = $request->input('ruangan');


                $query->select('*')
                    ->from('locations')
                    ->where('office', $office);

                if ($floor) {
                    $query->where('floor', $floor);
                }

                if ($room) {
                    $query->where('room', $room);
                }

        $location = $query->paginate(10);
        return view('location.getData', compact('location', 'locations'));

    }
}

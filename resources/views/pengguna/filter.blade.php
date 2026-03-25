@php
    // Ambil data jabatan & gender langsung dari DB tanpa bergantung $rank
    $jabatanList = collect(
        array_merge(
            \DB::table('users')->select('jabatan')->distinct()->pluck('jabatan')->toArray(),
            \DB::table('employees')->select('jabatan')->distinct()->pluck('jabatan')->toArray()
        )
    )->unique()->sort()->values();

    $genderList = collect(['L', 'P']);
@endphp

<form action="{{ route('pengguna.filter') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <div class="form-group ml-2">
                <label>Jabatan:</label>
                <select class="form-control" name="jabatan">
                    <option value="all">All</option>
                    @foreach($jabatanList as $jab)
                        <option value="{{ $jab }}" {{ request()->input('jabatan') == $jab ? 'selected' : '' }}>
                            {{ $jab }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group ml-2">
                <label>Jenis Kelamin:</label>
                <select class="form-control" name="gender">
                    <option value="all">All</option>
                    @foreach($genderList as $g)
                        <option value="{{ $g }}" {{ request()->input('gender') == $g ? 'selected' : '' }}>
                            {{ $g }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group ml-2 mt-4">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </div>
</form>
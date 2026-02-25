<form action="{{ route('pengguna.filter') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <div class="form-group ml-2">
                <label for="">Jabatan:</label>
                <select class="form-control" name="jabatan">
                    <option value="all" {{ request()->input('jabatan') == 'all' ? 'selected' : '' }}>All</option>
                        @php
                            $uniqueEmployees = $rank->unique('jabatan')->sortBy('jabatan');
                        @endphp
                    @foreach($uniqueEmployees as $code)
                        <option value="{{ $code->jabatan }}" {{ request()->input('jabatan') == $code->jabatan ? 'selected' : '' }}>
                            {{ $code->jabatan }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group ml-2">
                <label for="">Jenis Kelamin:</label>
                <select class="form-control" name="gender">
                    <option value="all" {{ request()->input('gender') == 'all' ? 'selected' : '' }}>All</option>
                        @php
                            $uniqueEmployees = $rank->unique('gender')->sortBy('gender');
                        @endphp
                    @foreach($uniqueEmployees as $code)
                        <option value="{{ $code->gender }}" {{ request()->input('gender') == $code->gender ? 'selected' : '' }}>
                            {{ $code->gender }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>


        <div class="col-md-3">
            <div class="form-group ml-2 mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</form>

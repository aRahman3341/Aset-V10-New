<form action="{{ route('asetTetap.filter') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <div class="form-group ml-2">
                <label for="">Janis Aset:</label>
                <select class="form-control" name="type">
                    <option value="all" {{ request()->input('type') == 'all' ? 'selected' : '' }}>All</option>
                    <option value="Tetap" {{ request()->input('type') == 'Tetap' ? 'selected' : '' }}>Tetap</option>
                    <option value="Bergerak" {{ request()->input('type') == 'Bergerak' ? 'selected' : '' }}>Bergerak</option>
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label for="">Kategori:</label>
            <div class="form-group ml-2">
                <select class="form-control" name="category">
                    <option value="all" {{ request()->input('category') == 'all' ? 'selected' : '' }}>All</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request()->input('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label for="">Tahun:</label>
            <div class="form-group ml-2">
                <div class="d-flex">
                    <select class="form-control" name="years_from">
                        <option value="dari" {{ request()->input('years_from') == 'dari' ? 'selected' : '' }}>Dari</option>
                        @php
                            $uniqueYears = $tahun->unique('years')->sortBy('years');
                        @endphp
                        @foreach($uniqueYears as $item)
                            <option value="{{ $item->years }}" {{ request()->input('years_from') == $item->years ? 'selected' : '' }}>{{ $item->years }}</option>
                        @endforeach
                    </select>
                    <div class="mx-2"></div>
                    <select class="form-control ml-2" name="years_till">
                        <option value="sampai" {{ request()->input('years_till') == 'sampai' ? 'selected' : '' }}>Sampai</option>
                        @foreach($uniqueYears as $item)
                            <option value="{{ $item->years }}" {{ request()->input('years_till') == $item->years ? 'selected' : '' }}>{{ $item->years }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <label for="">Lokasi:</label>
            <div class="form-group ml-2">
                <div class="d-flex">
                    <select class="form-control" name="gedung" id="gedung">
                        <option value="" {{ request()->input('gedung') == '' ? 'selected' : '' }}>Gedung</option>
                        @php
                            $uniqueOffices = $locations->unique('office')->sortBy('office');
                        @endphp
                        @foreach($uniqueOffices as $location)
                            <option value="{{ $location->office }}" {{ request()->input('gedung') == $location->office ? 'selected' : '' }}>{{ $location->office }}</option>
                        @endforeach
                    </select>

                    <div class="mx-2"></div>
                        <select class="form-control ml-2" name="lantai" id="lantai" disabled>
                            <option value="" {{ request()->input('lantai') == '' ? 'selected' : '' }}>Lantai</option>
                            @php
                                $uniqueFloors = $locations->unique('floor')->sortBy('floor');
                            @endphp
                            @foreach($uniqueFloors as $location)
                                <option value="{{ $location->floor }}" {{ request()->input('lantai') == $location->floor ? 'selected' : '' }}>{{ $location->floor }}</option>
                            @endforeach
                        </select>
                    <div class="mx-2"></div>
                    <select class="form-control ml-2" name="ruangan" id="ruangan" disabled>
                        <option value="" {{ request()->input('ruangan') == '' ? 'selected' : '' }}>Ruangan</option>
                        @php
                            $uniqueRooms = $locations->unique('room')->sortBy('room');
                        @endphp
                        @foreach($uniqueRooms as $location)
                            <option value="{{ $location->room }}" {{ request()->input('ruangan') == $location->room ? 'selected' : '' }}>{{ $location->room }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <label for="">Kondisi:</label>
            <div class="form-group ml-2">
                <select class="form-control" name="condition">
                    <option value="all">All</option>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label for="">Penanggung Jawab:</label>
            <div class="form-group ml-2">
                <select class="form-control" name="supervisor">
                    <option value="all">All</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <label for="">Kalibrasi:</label>
            <div class="form-group ml-2">
                <select class="form-control" name="calibrate">
                    <option value="all">All</option>
                    <option value="1">Dikalibrasi</option>
                    <option value="0">Tidak dikalibrasi</option>
                </select>
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group ml-2 mt-4">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</form>

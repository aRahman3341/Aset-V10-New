<form action="{{ route('location.filter') }}" method="post">
    @csrf
    <div class="row">
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
            <div class="form-group ml-2 mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</form>

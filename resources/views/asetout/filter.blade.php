<form action="{{ route('asetout.filter') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <div class="form-group ml-2">
                <label for="">No Faktur:</label>
                <select class="form-control" name="no_faktur">
                    <option value="all" {{ request()->input('no_faktur') == 'all' ? 'selected' : '' }}>All</option>
                    @foreach($faktur as $item)
                        <option value="{{ $item->no_faktur }}" {{ request()->input('categories') == $item->no_faktur ? 'selected' : '' }}>
                            {{ $item->no_faktur }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group ml-2">
                <label for="start_date">Rentang Awal:</label>
                <input type="date" type="text" name="start_date" id="datepicker" value="{{ request('start_date') }}">

                <label for="end_date">Rentang Akhir:</label>
                <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group ml-2 mt-4">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </div>
</form>
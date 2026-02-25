<form action="{{ route('peminjaman.filter') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <div class="form-group ml-2">
                <label for="">Operator:</label>
                <select class="form-control" name="code">
                    <option value="all" {{ request()->input('employee_id') == 'all' ? 'selected' : '' }}>All</option>
                    @foreach($codes as $code)
                        <option value="{{ $code->employee_id }}" {{ request()->input('employee_id') == $code->employee->name ? 'selected' : '' }}>
                            {{ $code->employee->name }}
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

<form action="{{ route('items.filter') }}" method="post">
    @csrf
    <div class="row">
        <div class="col-md-3">
            <div class="form-group ml-2">
                <label for="">Kategori Barang:</label>
                <select class="form-control" name="categories">
                    <option value="all" {{ request()->input('categories') == 'all' ? 'selected' : '' }}>All</option>
                    <option value="Rumah Tangga" {{ request()->input('categories') == 'Tetap' ? 'selected' : '' }}>Rumah Tangga</option>
                    <option value="ATK" {{ request()->input('categories') == 'Bergerak' ? 'selected' : '' }}>ATK</option>
                    <option value="Laboratorium" {{ request()->input('categories') == 'Bergerak' ? 'selected' : '' }}>Laboratorium</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group ml-2">
                <label for="">Status:</label>
                <select class="form-control" name="status">
                    <option value="all" {{ request()->input('status') == 'all' ? 'selected' : '' }}>All</option>
                    <option value="0" {{ request()->input('status') == 'Tetap' ? 'selected' : '' }}>Belum Teregister</option>
                    <option value="1" {{ request()->input('status') == 'Bergerak' ? 'selected' : '' }}>Teregister</option>
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
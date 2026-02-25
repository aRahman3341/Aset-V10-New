<form class="row g-3" method="POST" action="{{ route('category.store') }}">
	@csrf
	<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="h4 fw-bold">Tambah Data</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">Kode Kategori</label>
						<input type="text" class="form-control" name="code" id="inputNanme4">
						@error('code')
							<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">Nama Kategori</label>
						<input type="text" class="form-control" name="name" id="inputNanme4">
						@error('name')
							<div class="text-danger">{{ $message }}</div>
						@enderror
					</div>
				</div>
				<div class="modal-footer">
						<div class="text-center">
							<button type="submit" class="btn btn-success" value="save">Tambah</button>
							<button type="reset" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
						</div>
				</div>
			</div>
		</div>
	</div>
</form>

@foreach ($category as $item)
	
{{--<form class="row g-3" method="POST" action="/category/{{ $item->id }}">--}}
<form class="row g-3" method="POST" action="{{ route('category.update',$item->id) }}">
	@method('put')
	@csrf
	<div class="modal fade" id="ModalEdit-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="h4 fw-bold">Update</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">Kode Kategori</label>
						<input type="text" class="form-control" name="code" value="{{ $item->code }}" id="inputNanme4">
					</div>
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">Nama Kategori</label>
						<input type="text" class="form-control" name="name" value="{{ $item->name }}" id="inputNanme4">
					</div>
				</div>
				<div class="modal-footer">
						<div class="text-center">
							<button type="submit" class="btn btn-success" value="save">Update</button>
							<button type="reset" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
						</div>
				</div>
			</div>
		</div>
	</div>
</form>
@endforeach
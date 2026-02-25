@foreach ($location as $item)
	
{{--<form class="row g-3" method="POST" action="/location/{{ $item->id }}">--}}
<form class="row g-3" method="POST" action="{{ route('location.update',$item->id) }}">
	@method('put')
	@csrf
	<div class="modal fade" id="ModalEdit-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="h4 fw-bold">Update</h4>
					<button type="button" class="btn-close fw-bold" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">Nama Gedung</label>
						<input type="text" class="form-control" name="office" value="{{ $item->office }}" id="inputNanme4">
					</div>
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">Lantai</label>
						<input type="text" class="form-control" name="floor" value="{{ $item->floor }}" id="inputNanme4">
					</div>
					<div class="col-12">
						<label for="inputNanme4" class="form-label fw-bold">Nama Ruangan</label>
						<input type="text" class="form-control" name="room" value="{{ $item->room }}" id="inputNanme4">
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
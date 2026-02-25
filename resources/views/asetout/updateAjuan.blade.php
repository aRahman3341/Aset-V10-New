@foreach ($data as $item)

{{--<form class="row g-3" method="POST" action="/asetout/ajuan/edit/{{ $item->id }}">--}}
<form class="row g-3" method="POST" action="{{ route('ajuan.approve', $item->id) }}">
	@method('put')
	@csrf
		
	<div class="modal fade" id="ModalApprove-{{ $item->id }}" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="h4 fw-bold">Approval</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-12">
							<label for="inputNanme4" class="form-label fw-bold">Nama Barang</label>
							<input type="text" class="form-control" name="no_faktur" value="{{ $item->name }}" id="inputNanme4" readonly>
						</div>
						<div class="col-6">
							<label for="inputNanme4" class="form-label fw-bold">Qty Pengajuan</label>
							<input type="text" class="form-control" name="qty" value="{{ $item->qty }}" id="inputNanme4" readonly>
						</div>
						<div class="col-6">
							<label for="inputNanme4" class="form-label fw-bold">Qty Disetujui</label>
							<input type="text" class="form-control" name="total_qty" value="{{ $item->qty }}" id="inputNanme4">
							<input type="hidden" name="status" value="Disetujui">
						</div>
					</div>
				</div>
				<div class="modal-footer">
						<div class="text-center">
							<button type="submit" class="btn btn-success" value="save">Approve</button>
							<button type="reset" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
						</div>
				</div>
			</div>
		</div>
	</div>
</form>
@endforeach

<form class="row g-3" method="POST" action="{{ route('items.import') }}" enctype="multipart/form-data">
	@csrf
	<div class="modal fade" id="ModalImport" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="h4 fw-bold">Import Data</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div id="reader" width="600px" style="position: relative; padding: 0px; border: 1px solid silver;">
						<div class="col-12">
							<input type="file" class="form-control" name="file" id="inputNanme4" accept=".xls, .xlsx">
						</div>
					</div>
				</div>
				<div class="modal-footer">
						<div class="text-center">
							<button type="submit" class="btn btn-success" value="save">Import</button>
							{{--<button type="submit" class="btn btn-success">Import</button>--}}
							<button type="reset" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
						</div>
				</div>
			</div>
		</div>
	</div>
</form>

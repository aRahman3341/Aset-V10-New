<form class="row g-3" method="get" action="{{ route('items.export') }}">
	@csrf
	<div class="modal fade" id="ModalExport" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="h4 fw-bold">Export Data</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div id="reader" width="600px" style="position: relative; padding: 0px; border: 1px solid silver;">
						<div class="">
							<select id="categories" name="categories" class="form-control" >
								<option value="">Pilih Kategori</option>
								<option value="ATK">ATK</option>
								<option value="Rumah Tangga">Rumah Tangga</option>
								<option value="Laboratorium">Laboratorium</option>
							</select>
						</div>
						<div class="col-md-6">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="text-center">
						<button type="submit" class="btn btn-success" value="save">Export</button>
						<button type="reset" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

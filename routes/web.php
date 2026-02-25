<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ChartController,
    BarangController,
    ItemsController,
    LocationController,
    CategoryController,
    UserController,
    PeminjamanController,
    AsetOutController,
    AjuanController,
    AsetKeluarController,
    QrCodeController,
    SessionController
};

/*
|--------------------------------------------------------------------------
| ROUTE YANG MEMBUTUHKAN LOGIN
|--------------------------------------------------------------------------
*/
Route::middleware('IsLogin')->group(function () {

    /* ================= DASHBOARD ================= */
    Route::get('/', [ChartController::class, 'index'])->name('dashboard');

    /* ================= CEK DATA ================= */
    Route::post('/checkNupExists', [BarangController::class, 'checkNupExists'])->name('checkNupExists');
    Route::post('/checkNoSeriExists', [BarangController::class, 'checkNoSeriExists'])->name('checkNoSeriExists');
    Route::post('/checkCodeBExists', [ItemsController::class, 'checkCodeBExists'])->name('checkCodeBExists');

    /* ================= ASET TETAP ================= */
    Route::prefix('asetTetap')->name('asetTetap.')->group(function () {
        Route::get('/', [BarangController::class, 'index'])->name('index');
        Route::get('/add', [BarangController::class, 'create'])->name('create');
        Route::post('/store', [BarangController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [BarangController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BarangController::class, 'update'])->name('update');
        Route::delete('/{id}', [BarangController::class, 'destroy'])->name('destroy');

        Route::post('/search', [BarangController::class, 'search'])->name('search');
        Route::post('/filter', [BarangController::class, 'filter'])->name('filter');
        Route::post('/multi-delete', [BarangController::class, 'multiDelete'])->name('multiDelete');

        Route::get('/import', [BarangController::class, 'import'])->name('import');
        Route::post('/import', [BarangController::class, 'importStore'])->name('import.store');
        Route::post('/export', [BarangController::class, 'export'])->name('export');
    });

    /* ================= ITEMS ================= */
    Route::get('/dashboard', function () {})->middleware('IsLogin');
    Route::get('/items', [ItemsController::class, 'index'])->name('items.index');
    Route::get('/items/add', [ItemsController::class, 'create'])->name('items.create');
    Route::post('/items/store', [ItemsController::class, 'store'])->name('items.store');
    Route::get('/items/{id}/edit', [ItemsController::class, 'edit'])->name('items.edit');
    Route::put('/items/{id}', [ItemsController::class, 'update'])->name('items.update');
    Route::delete('/items/{id}', [ItemsController::class, 'destroy'])->name('items.destroy');
    Route::match(['get', 'post'], '/items/filter', [ItemsController::class, 'filter'])->name('items.filter');
    Route::post('/items/multi-delete', [ItemsController::class, 'multiDelete'])->name('items.multiDelete');
    Route::post('/items/import', [ItemsController::class, 'fileImport'])->name('items.import');
    Route::get('/items/export', [ItemsController::class, 'export'])->name('items.export');
    Route::post('/items/qrcodes', [ItemsController::class, 'qrcodes'])->name('items.qrcodes');
    
    /* ================= QR CODE ================= */
    Route::prefix('generate_qrcodes')->group(function () {
        Route::post('/', [QrCodeController::class, 'generateQRCodes'])->name('generate_qrcodes');
        Route::post('/scanning', [QrCodeController::class, 'scanning'])->name('scanning');
        Route::get('/scanningResult', [QrCodeController::class, 'scanningResult'])->name('generate_qrcodes.scanningResult');
    });

    /* ================= PEMINJAMAN ================= */
    Route::get('/peminjaman/report', [PeminjamanController::class, 'report'])
        ->name('peminjaman.report-peminjaman');
    Route::get('/peminjaman/export', [PeminjamanController::class, 'export'])
        ->name('peminjaman.export');
    Route::post('/peminjaman/filter', [PeminjamanController::class, 'filter'])
        ->name('peminjaman.filter');
    Route::get('/peminjaman/{id}/kembali', [PeminjamanController::class, 'kembali'])
        ->name('peminjaman.kembali');
    Route::put('/peminjaman/{id}/pengembalian', [PeminjamanController::class, 'pengembalian'])
        ->name('peminjaman.pengembalian');
    Route::resource('peminjaman', PeminjamanController::class);
    
    /* ================= ASET KELUAR ================= */
    Route::prefix('asetkeluar')->name('asetkeluar.')->group(function () {
        Route::get('/', [AsetKeluarController::class, 'index'])->name('index');
        Route::get('/add', [AsetKeluarController::class, 'addData'])->name('add');
        Route::post('/store', [AsetKeluarController::class, 'dataStore'])->name('store');
        Route::get('/{id}/edit', [AsetKeluarController::class, 'editData'])->name('edit');
        Route::put('/{id}', [AsetKeluarController::class, 'update'])->name('update');
        Route::delete('/{id}', [AsetKeluarController::class, 'destroy'])->name('destroy');
        Route::post('/search', [AsetKeluarController::class, 'search'])->name('search');
        Route::get('/export', [AsetKeluarController::class, 'export'])->name('export');
        Route::get('/download/{id}', [AsetKeluarController::class, 'download'])->name('download');
        Route::get('/asetkeluar/report', [AsetKeluarController::class, 'report'])
            ->name('asetkeluar.report-asetkeluar');
        Route::get('/report', [AsetKeluarController::class, 'report'])->name('report-asetkeluar');
    });

    /* ================= PENGAJUAN ================= */
    Route::prefix('pengajuan/ajuan')->group(function () {
        Route::get('/', [ItemsController::class, 'ajuan'])->name('getPengajuan');
        Route::get('/add', [ItemsController::class, 'pengajuan'])->name('addPengajuan');
        Route::post('/store', [ItemsController::class, 'addPengajuan'])->name('pengajuan.store');
    });

    /* ================= LOCATION ================= */
    Route::prefix('location')->group(function () {
        Route::get('/', [LocationController::class, 'get_data']);
        Route::post('/store', [LocationController::class, 'dataStore'])->name('location.store');
        Route::put('/{id}', [LocationController::class, 'update'])->name('location.update');
        Route::delete('/{id}', [LocationController::class, 'destroy'])->name('location.destroy');
        Route::post('/search', [LocationController::class, 'search'])->name('location.search');
        Route::post('/filter', [LocationController::class, 'filter'])->name('location.filter');
    });

    /* ================= CATEGORY ================= */
    Route::prefix('category')->group(function () {
        Route::get('/', [CategoryController::class, 'get_data']);
        Route::post('/store', [CategoryController::class, 'dataStore'])->name('category.store');
        Route::put('/{id}', [CategoryController::class, 'update'])->name('category.update');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('category.destroy');
        Route::post('/search', [CategoryController::class, 'search'])->name('category.search');
    });

    /* ================= USER ================= */
    Route::prefix('pengguna')->group(function () {
        Route::get('/', [UserController::class, 'get_data']);
        Route::get('/add', [UserController::class, 'addData'])->name('pengguna.add');
        Route::post('/store', [UserController::class, 'dataStore'])->name('pengguna.store');
        Route::get('/edit/{id}', [UserController::class, 'editData'])->name('pengguna.edit');
        Route::put('/edit/{id}', [UserController::class, 'update'])->name('pengguna.update');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('pengguna.destroy');
        Route::post('/search', [UserController::class, 'search'])->name('pengguna.search');
        Route::post('/filter', [UserController::class, 'filter'])->name('pengguna.filter');
    });

});

/*
|--------------------------------------------------------------------------
| SESSION / AUTH
|--------------------------------------------------------------------------
*/
Route::prefix('session')->group(function () {
    Route::get('/', [SessionController::class, 'formLogin'])->middleware('IsTamu')->name('session.formLogin');
    Route::post('/login', [SessionController::class, 'login'])->middleware('IsTamu');
    Route::get('/logout', [SessionController::class, 'logout'])->name('session.logout');
});

Route::get('/autoLogin', [SessionController::class, 'autoLogin']);

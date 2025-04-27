<?php

use App\Models\Website\WebMenuModel;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SummernoteController;
use App\Http\Controllers\DashboardMPUController;
use App\Http\Controllers\DashboardSARController;
use App\Http\Controllers\DashboardAdminController;
use App\Http\Controllers\HakAkses\SetHakAksesController;
use App\Http\Controllers\DashboardRespondenController;
use App\Http\Controllers\DashboardVerifikatorController;
use App\Http\Controllers\ManagePengguna\HakAksesController;
use App\Http\Controllers\Notifikasi\NotifAdminController;
use App\Http\Controllers\AdminWeb\Berita\BeritaController;
use App\Http\Controllers\AdminWeb\Footer\FooterController;
use App\Http\Controllers\SistemInformasi\EForm\WBSController;
use App\Http\Controllers\AdminWeb\Berita\BeritaDinamisController;
use App\Http\Controllers\AdminWeb\Footer\KategoriFooterController;
use App\Http\Controllers\AdminWeb\Pengumuman\PengumumanController;
use App\Http\Controllers\AdminWeb\KategoriAkses\AksesCepatController;
use App\Http\Controllers\SistemInformasi\Timeline\TimelineController;
use App\Http\Controllers\AdminWeb\MediaDinamis\MediaDinamisController;
use App\Http\Controllers\AdminWeb\InformasiPublik\LHKPN\LhkpnController;
use App\Http\Controllers\AdminWeb\KategoriAkses\KategoriAksesController;
use App\Http\Controllers\AdminWeb\Pengumuman\PengumumanDinamisController;
use App\Http\Controllers\AdminWeb\KategoriAkses\PintasanLainnyaController;
use App\Http\Controllers\AdminWeb\MenuManagement\MenuManagementController;
use App\Http\Controllers\AdminWeb\MediaDinamis\DetailMediaDinamisController;
use App\Http\Controllers\SistemInformasi\EForm\PengaduanMasyarakatController;
use App\Http\Controllers\SistemInformasi\EForm\PermohonanInformasiController;
use App\Http\Controllers\SistemInformasi\EForm\PermohonanPerawatanController;
use App\Http\Controllers\SistemInformasi\EForm\PernyataanKeberatanController;
use App\Http\Controllers\SistemInformasi\KategoriForm\KategoriFormController;
use App\Http\Controllers\AdminWeb\InformasiPublik\LHKPN\DetailLhkpnController;
use App\Http\Controllers\AdminWeb\InformasiPublik\Regulasi\RegulasiController;
use App\Http\Controllers\AdminWeb\KategoriAkses\DetailPintasanLainnyaController;
use App\Http\Controllers\AdminWeb\InformasiPublik\Regulasi\RegulasiDinamisController;
use App\Http\Controllers\AdminWeb\InformasiPublik\Regulasi\KategoriRegulasiController;
use App\Http\Controllers\SistemInformasi\KetentuanPelaporan\KetentuanPelaporanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::pattern('id', '[0-9]+'); // Artinya: Ketika ada parameter {id}, maka harus berupa angka

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::get('logout', [AuthController::class, 'logout'])->middleware('auth');
Route::post('/pilih-level', [AuthController::class, 'pilihLevel'])->name('pilih.level');

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postRegister']);

// Group route yang memerlukan autentikasi
Route::middleware('auth')->group(function () {
    Route::get('/dashboardSAR', [DashboardSARController::class, 'index'])->middleware('authorize:SAR');
    Route::get('/dashboardADM', [DashboardAdminController::class, 'index'])->middleware('authorize:ADM');
    Route::get('/dashboardRPN', [DashboardRespondenController::class, 'index'])->middleware('authorize:RPN');
    Route::get('/dashboardMPU', [DashboardMPUController::class, 'index'])->middleware('authorize:MPU');
    Route::get('/dashboardVFR', [DashboardVerifikatorController::class, 'index'])->middleware('authorize:VFR');

    Route::group(['prefix' => 'HakAkses', 'middleware' => 'authorize:SAR'], function () {
        Route::get('/', [SetHakAksesController::class, 'index']);
        Route::get('/addData', [SetHakAksesController::class, 'addData']);
        Route::post('/createData', [SetHakAksesController::class, 'createData']);
        Route::get('/getHakAksesData/{param1}/{param2?}', [SetHakAksesController::class, 'editData']);
        Route::post('/updateData', [SetHakAksesController::class, 'updateData']);
    });

    Route::get('/session', [AuthController::class, 'getData']);
    Route::get('/js/summernote.js', [SummernoteController::class, 'getSummernoteJS']);
    Route::get('/css/summernote.css', [SummernoteController::class, 'getSummernoteCSS']);

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', [ProfileController::class, 'index']);
        Route::put('/update_pengguna/{id}', [ProfileController::class, 'update_pengguna']);
        Route::put('/update_password/{id}', [ProfileController::class, 'update_password']);
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('menu-management'), 'middleware' => 'authorize:ADM,SAR'], function () {
        Route::get('/', [MenuManagementController::class, 'index'])->middleware('permission:view');
        Route::get('/menu-item', [MenuManagementController::class, 'menu-item']);
        Route::post('/list', [MenuManagementController::class, 'list']);
        Route::post('/store', [MenuManagementController::class, 'store'])->middleware('permission:create');
        Route::get('/{id}/edit', [MenuManagementController::class, 'edit']);
        Route::put('/{id}/update', [MenuManagementController::class, 'update'])->middleware('permission:update');
        Route::delete('/{id}/delete', [MenuManagementController::class, 'delete'])->middleware('permission:delete');
        Route::get('/{id}/detail_menu', [MenuManagementController::class, 'detail_menu']);
        Route::post('/reorder', [MenuManagementController::class, 'reorder']); // New route for drag-drop reordering
        Route::get('/get-parent-menus/{hakAksesId}', [MenuManagementController::class, 'getParentMenus']);
    });
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('kategori-footer')], function () {
        Route::get('/', [KategoriFooterController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [KategoriFooterController::class, 'getData']);
        Route::get('/addData', [KategoriFooterController::class, 'addData']);
        Route::post('/createData', [KategoriFooterController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [KategoriFooterController::class, 'editData']);
        Route::post('/updateData/{id}', [KategoriFooterController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [KategoriFooterController::class, 'detailData']);
        Route::get('/deleteData/{id}', [KategoriFooterController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [KategoriFooterController::class, 'deleteData'])->middleware('permission:delete');
    });
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('detail-footer')], function () {
        Route::get('/', [FooterController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [FooterController::class, 'getData']);
        Route::get('/addData', [FooterController::class, 'addData']);
        Route::post('/createData', [FooterController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [FooterController::class, 'editData']);
        Route::post('/updateData/{id}', [FooterController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [FooterController::class, 'detailData']);
        Route::get('/deleteData/{id}', [FooterController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [FooterController::class, 'deleteData'])->middleware('permission:delete');
    });
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('kategori-akses-cepat')], function () {
        Route::get('/', [KategoriAksesController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [KategoriAksesController::class, 'getData']);
        Route::get('/addData', [KategoriAksesController::class, 'addData']);
        Route::post('/createData', [KategoriAksesController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [KategoriAksesController::class, 'editData']);
        Route::post('/updateData/{id}', [KategoriAksesController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [KategoriAksesController::class, 'detailData']);
        Route::get('/deleteData/{id}', [KategoriAksesController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [KategoriAksesController::class, 'deleteData'])->middleware('permission:delete');
    });
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('detail-akses-cepat')], function () {
        Route::get('/', [AksesCepatController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [AksesCepatController::class, 'getData']);
        Route::get('/addData', [AksesCepatController::class, 'addData']);
        Route::post('/createData', [AksesCepatController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [AksesCepatController::class, 'editData']);
        Route::post('/updateData/{id}', [AksesCepatController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [AksesCepatController::class, 'detailData']);
        Route::get('/deleteData/{id}', [AksesCepatController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [AksesCepatController::class, 'deleteData'])->middleware('permission:delete');
    });
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('kategori-berita')], function () {
        Route::get('/', [BeritaDinamisController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [BeritaDinamisController::class, 'getData']);
        Route::get('/addData', [BeritaDinamisController::class, 'addData']);
        Route::post('/createData', [BeritaDinamisController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [BeritaDinamisController::class, 'editData']);
        Route::post('/updateData/{id}', [BeritaDinamisController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [BeritaDinamisController::class, 'detailData']);
        Route::get('/deleteData/{id}', [BeritaDinamisController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [BeritaDinamisController::class, 'deleteData'])->middleware('permission:delete');
    });
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('detail-berita')], function () {
        Route::get('/', [BeritaController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [BeritaController::class, 'getData']);
        Route::get('/addData', [BeritaController::class, 'addData']);
        Route::post('/createData', [BeritaController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [BeritaController::class, 'editData']);
        Route::post('/updateData/{id}', [BeritaController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [BeritaController::class, 'detailData']);
        Route::get('/deleteData/{id}', [BeritaController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [BeritaController::class, 'deleteData'])->middleware('permission:delete');
        Route::post('/uploadImage', [BeritaController::class, 'uploadImage']);
        Route::post('/removeImage', [BeritaController::class, 'removeImage']);
    });
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('kategori-media')], function () {
        Route::get('/', [MediaDinamisController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [MediaDinamisController::class, 'getData']);
        Route::get('/addData', [MediaDinamisController::class, 'addData']);
        Route::post('/createData', [MediaDinamisController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [MediaDinamisController::class, 'editData']);
        Route::post('/updateData/{id}', [MediaDinamisController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [MediaDinamisController::class, 'detailData']);
        Route::get('/deleteData/{id}', [MediaDinamisController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [MediaDinamisController::class, 'deleteData'])->middleware('permission:delete');
    });
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('detail-media')], function () {
        Route::get('/', [DetailMediaDinamisController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [DetailMediaDinamisController::class, 'getData']);
        Route::get('/addData', [DetailMediaDinamisController::class, 'addData']);
        Route::post('/createData', [DetailMediaDinamisController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [DetailMediaDinamisController::class, 'editData']);
        Route::post('/updateData/{id}', [DetailMediaDinamisController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [DetailMediaDinamisController::class, 'detailData']);
        Route::get('/deleteData/{id}', [DetailMediaDinamisController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [DetailMediaDinamisController::class, 'deleteData'])->middleware('permission:delete');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('kategori-tahun-lhkpn')], function () {
        Route::get('/', [LhkpnController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [LhkpnController::class, 'getData']);
        Route::get('/addData', [LhkpnController::class, 'addData']);
        Route::post('/createData', [LhkpnController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [LhkpnController::class, 'editData']);
        Route::post('/updateData/{id}', [LhkpnController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [LhkpnController::class, 'detailData']);
        Route::get('/deleteData/{id}', [LhkpnController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [LhkpnController::class, 'deleteData'])->middleware('permission:delete');
        Route::post('/uploadImage', [LhkpnController::class, 'uploadImage']);
        Route::post('/removeImage', [LhkpnController::class, 'removeImage']);
    });
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('detail-lhkpn')], function () {
        Route::get('/', [DetailLhkpnController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [DetailLhkpnController::class, 'getData']);
        Route::get('/addData', [DetailLhkpnController::class, 'addData']);
        Route::post('/createData', [DetailLhkpnController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [DetailLhkpnController::class, 'editData']);
        Route::post('/updateData/{id}', [DetailLhkpnController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [DetailLhkpnController::class, 'detailData']);
        Route::get('/deleteData/{id}', [DetailLhkpnController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [DetailLhkpnController::class, 'deleteData'])->middleware('permission:delete');
    });


    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('kategori-pintasan-lainnya')], function () {
        Route::get('/', [PintasanLainnyaController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [PintasanLainnyaController::class, 'getData']);
        Route::get('/addData', [PintasanLainnyaController::class, 'addData']);
        Route::post('/createData', [PintasanLainnyaController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [PintasanLainnyaController::class, 'editData']);
        Route::post('/updateData/{id}', [PintasanLainnyaController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [PintasanLainnyaController::class, 'detailData']);
        Route::get('/deleteData/{id}', [PintasanLainnyaController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [PintasanLainnyaController::class, 'deleteData'])->middleware('permission:delete');
    });


    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('detail-pintasan-lainnya')], function () {
        Route::get('/', [DetailPintasanLainnyaController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [DetailPintasanLainnyaController::class, 'getData']);
        Route::get('/addData', [DetailPintasanLainnyaController::class, 'addData']);
        Route::post('/createData', [DetailPintasanLainnyaController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [DetailPintasanLainnyaController::class, 'editData']);
        Route::post('/updateData/{id}', [DetailPintasanLainnyaController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [DetailPintasanLainnyaController::class, 'detailData']);
        Route::get('/deleteData/{id}', [DetailPintasanLainnyaController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [DetailPintasanLainnyaController::class, 'deleteData'])->middleware('permission:delete');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('regulasi-dinamis')], function () {
        Route::get('/', [RegulasiDinamisController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [RegulasiDinamisController::class, 'getData']);
        Route::get('/addData', [RegulasiDinamisController::class, 'addData']);
        Route::post('/createData', [RegulasiDinamisController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [RegulasiDinamisController::class, 'editData']);
        Route::post('/updateData/{id}', [RegulasiDinamisController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [RegulasiDinamisController::class, 'detailData']);
        Route::get('/deleteData/{id}', [RegulasiDinamisController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [RegulasiDinamisController::class, 'deleteData'])->middleware('permission:delete');
    });
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('detail-regulasi')], function () {
        Route::get('/', [RegulasiController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [RegulasiController::class, 'getData']);
        Route::get('/addData', [RegulasiController::class, 'addData']);
        Route::post('/createData', [RegulasiController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [RegulasiController::class, 'editData']);
        Route::post('/updateData/{id}', [RegulasiController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [RegulasiController::class, 'detailData']);
        Route::get('/deleteData/{id}', [RegulasiController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [RegulasiController::class, 'deleteData'])->middleware('permission:delete');
    });
    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('kategori-regulasi')], function () {
        Route::get('/', [KategoriRegulasiController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [KategoriRegulasiController::class, 'getData']);
        Route::get('/addData', [KategoriRegulasiController::class, 'addData']);
        Route::post('/createData', [KategoriRegulasiController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [KategoriRegulasiController::class, 'editData']);
        Route::post('/updateData/{id}', [KategoriRegulasiController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [KategoriRegulasiController::class, 'detailData']);
        Route::get('/deleteData/{id}', [KategoriRegulasiController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [KategoriRegulasiController::class, 'deleteData'])->middleware('permission:delete');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('permohonan-informasi'), 'middleware' => ['authorize:RPN']], function () {
        Route::get('/', [PermohonanInformasiController::class, 'index']);
        Route::get('/getData', [PermohonanInformasiController::class, 'getData']);
        Route::get('/addData', [PermohonanInformasiController::class, 'addData']);
        Route::post('/createData', [PermohonanInformasiController::class, 'createData']);
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('permohonan-informasi-admin')], function () {
        Route::get('/', [PermohonanInformasiController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [PermohonanInformasiController::class, 'getData']);
        Route::get('/addData', [PermohonanInformasiController::class, 'addData']);
        Route::post('/createData', [PermohonanInformasiController::class, 'createData'])->middleware('permission:create');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('pernyataan-keberatan'), 'middleware' => ['authorize:RPN']], function () {
        Route::get('/', [PernyataanKeberatanController::class, 'index']);
        Route::get('/getData', [PernyataanKeberatanController::class, 'getData']);
        Route::get('/addData', [PernyataanKeberatanController::class, 'addData']);
        Route::post('/createData', [PernyataanKeberatanController::class, 'createData']);
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('pernyataan-keberatan-admin')], function () {
        Route::get('/', [PernyataanKeberatanController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [PernyataanKeberatanController::class, 'getData']);
        Route::get('/addData', [PernyataanKeberatanController::class, 'addData']);
        Route::post('/createData', [PernyataanKeberatanController::class, 'createData'])->middleware('permission:create');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('pengaduan-masyarakat'), 'middleware' => ['authorize:RPN']], function () {
        Route::get('/', [PengaduanMasyarakatController::class, 'index']);
        Route::get('/getData', [PengaduanMasyarakatController::class, 'getData']);
        Route::get('/addData', [PengaduanMasyarakatController::class, 'addData']);
        Route::post('/createData', [PengaduanMasyarakatController::class, 'createData']);
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('pengaduan-masyarakat-admin')], function () {
        Route::get('/', [PengaduanMasyarakatController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [PengaduanMasyarakatController::class, 'getData']);
        Route::get('/addData', [PengaduanMasyarakatController::class, 'addData']);
        Route::post('/createData', [PengaduanMasyarakatController::class, 'createData'])->middleware('permission:create');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('whistle-blowing-system'), 'middleware' => ['authorize:RPN']], function () {
        Route::get('/', [WBSController::class, 'index']);
        Route::get('/getData', [WBSController::class, 'getData']);
        Route::get('/addData', [WBSController::class, 'addData']);
        Route::post('/createData', [WBSController::class, 'createData']);
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('whistle-blowing-system-admin')], function () {
        Route::get('/', [WBSController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [WBSController::class, 'getData']);
        Route::get('/addData', [WBSController::class, 'addData']);
        Route::post('/createData', [WBSController::class, 'createData'])->middleware('permission:create');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('permohonan-sarana-dan-prasarana'), 'middleware' => ['authorize:RPN']], function () {
        Route::get('/', [PermohonanPerawatanController::class, 'index']);
        Route::get('/getData', [PermohonanPerawatanController::class, 'getData']);
        Route::get('/addData', [PermohonanPerawatanController::class, 'addData']);
        Route::post('/createData', [PermohonanPerawatanController::class, 'createData']);
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('permohonan-sarana-dan-prasarana-admin')], function () {
        Route::get('/', [PermohonanPerawatanController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [PermohonanPerawatanController::class, 'getData']);
        Route::get('/addData', [PermohonanPerawatanController::class, 'addData']);
        Route::post('/createData', [PermohonanPerawatanController::class, 'createData'])->middleware('permission:create');
    });

    Route::group(['prefix' => 'Notifikasi/NotifAdmin', 'middleware' => ['authorize:ADM']], function () {
        Route::get('/', [NotifAdminController::class, 'index']);
        Route::get('/notifPI', [NotifAdminController::class, 'notifikasiPermohonan']);
        Route::post('/tandai-dibaca/{id}', [NotifAdminController::class, 'tandaiDibaca'])->name('NotifAdmin.tandaiDibaca');
        Route::delete('/hapus/{id}', [NotifAdminController::class, 'hapusNotifikasi'])->name('NotifAdmin.hapus');
        Route::post('/tandai-semua-dibaca', [NotifAdminController::class, 'tandaiSemuaDibaca']);
        Route::delete('/hapus-semua-dibaca', [NotifAdminController::class, 'hapusSemuaDibaca']);
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('timeline')], function () {
        Route::get('/', [TimelineController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [TimelineController::class, 'getData']);
        Route::get('/addData', [TimelineController::class, 'addData']);
        Route::post('/createData', [TimelineController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [TimelineController::class, 'editData']);
        Route::post('/updateData/{id}', [TimelineController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [TimelineController::class, 'detailData']);
        Route::get('/deleteData/{id}', [TimelineController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [TimelineController::class, 'deleteData'])->middleware('permission:delete');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('ketentuan-pelaporan')], function () {
        Route::get('/', [KetentuanPelaporanController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [KetentuanPelaporanController::class, 'getData']);
        Route::get('/addData', [KetentuanPelaporanController::class, 'addData']);
        Route::post('/createData', [KetentuanPelaporanController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', action: [KetentuanPelaporanController::class, 'editData']);
        Route::post('/updateData/{id}', [KetentuanPelaporanController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [KetentuanPelaporanController::class, 'detailData']);
        Route::get('/deleteData/{id}', [KetentuanPelaporanController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [KetentuanPelaporanController::class, 'deleteData'])->middleware('permission:delete');
        Route::post('/uploadImage', [KetentuanPelaporanController::class, 'uploadImage']);
        Route::post('/removeImage', [KetentuanPelaporanController::class, 'removeImage']);
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('kategori-form')], function () {
        Route::get('/', [KategoriFormController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [KategoriFormController::class, 'getData']);
        Route::get('/addData', [KategoriFormController::class, 'addData']);
        Route::post('/createData', [KategoriFormController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [KategoriFormController::class, 'editData']);
        Route::post('/updateData/{id}', [KategoriFormController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [KategoriFormController::class, 'detailData']);
        Route::get('/deleteData/{id}', [KategoriFormController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [KategoriFormController::class, 'deleteData'])->middleware('permission:delete');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('kategori-pengumuman')], function () {
        Route::get('/', [PengumumanDinamisController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [PengumumanDinamisController::class, 'getData']);
        Route::get('/addData', [PengumumanDinamisController::class, 'addData']);
        Route::post('/createData', [PengumumanDinamisController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [PengumumanDinamisController::class, 'editData']);
        Route::post('/updateData/{id}', [PengumumanDinamisController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [PengumumanDinamisController::class, 'detailData']);
        Route::get('/deleteData/{id}', [PengumumanDinamisController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [PengumumanDinamisController::class, 'deleteData'])->middleware('permission:delete');
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('detail-pengumuman')], function () {
        Route::get('/', [PengumumanController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [PengumumanController::class, 'getData']);
        Route::get('/addData', [PengumumanController::class, 'addData']);
        Route::post('/createData', [PengumumanController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [PengumumanController::class, 'editData']);
        Route::post('/updateData/{id}', [PengumumanController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [PengumumanController::class, 'detailData']);
        Route::get('/deleteData/{id}', [PengumumanController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [PengumumanController::class, 'deleteData'])->middleware('permission:delete');
        Route::post('/uploadImage', [PengumumanController::class, 'uploadImage']);
        Route::post('/removeImage', [PengumumanController::class, 'removeImage']);
    });

    Route::group(['prefix' => WebMenuModel::getDynamicMenuUrl('management-level')], function () {
        Route::get('/', [HakAksesController::class, 'index'])->middleware('permission:view');
        Route::get('/getData', [HakAksesController::class, 'getData']);
        Route::get('/addData', [HakAksesController::class, 'addData']);
        Route::post('/createData', [HakAksesController::class, 'createData'])->middleware('permission:create');
        Route::get('/editData/{id}', [HakAksesController::class, 'editData']);
        Route::post('/updateData/{id}', [HakAksesController::class, 'updateData'])->middleware('permission:update');
        Route::get('/detailData/{id}', [HakAksesController::class, 'detailData']);
        Route::get('/deleteData/{id}', [HakAksesController::class, 'deleteData']);
        Route::delete('/deleteData/{id}', [HakAksesController::class, 'deleteData'])->middleware('permission:delete');
    });
});

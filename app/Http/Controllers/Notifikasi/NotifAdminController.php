<?php

namespace App\Http\Controllers\Notifikasi;

use App\Http\Controllers\TraitsController;
use App\Models\Log\NotifAdminModel;
use Illuminate\Routing\Controller;

class NotifAdminController extends Controller
{
    use TraitsController;

    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Notifikasi',
            'list' => ['Home', 'Notifikasi']
        ];

        $page = (object) [
            'title' => 'Notifikasi Pengajuan Permohonan dan Pertanyaan'
        ];

        $activeMenu = 'notifikasi';

        return view('Notifikasi/NotifAdmin.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function notifikasiPermohonan()
    {
        $notifikasi = NotifAdminModel::with('t_permohonan_informasi')
            ->where('kategori_notif_admin', 'E-Form Permohonan Informasi')
            ->where('isDeleted', 0)
            ->get();

        $breadcrumb = (object) [
            'title' => 'Notifikasi',
            'list' => ['Home', 'Notifikasi']
        ];

        $page = (object) [
            'title' => 'Notifikasi Pengajuan Permohonan'
        ];

        $activeMenu = 'notifikasi';

        return view('Notifikasi/NotifAdmin.notifPI', [
            'notifikasi' => $notifikasi,
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function tandaiDibaca($id)
    {
        $result = NotifAdminModel::tandaiDibaca($id);
        return response()->json($result);
    }

    public function hapusNotifikasi($id)
    {
        $result = NotifAdminModel::hapusNotifikasi($id);
        return response()->json($result);
    }
    
    public function tandaiSemuaDibaca()
    {
        $result = NotifAdminModel::tandaiSemuaDibaca();
        return response()->json($result);
    }

    public function hapusSemuaDibaca()
    {
        $result = NotifAdminModel::hapusSemuaDibaca();
        return response()->json($result);
    }
}

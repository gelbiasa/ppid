<?php

namespace App\Http\Controllers\ManagePengguna;

use App\Http\Controllers\TraitsController;
use App\Models\UserModel;
use App\Models\HakAksesModel;
use App\Models\SetUserHakAksesModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan User';
    public $pagename = 'ManagePengguna/ManageUser';

    public function index(Request $request)
    {
        $search = $request->query('search', '');
        $levelId = $request->query('level_id', null);

        $breadcrumb = (object) [
            'title' => 'Pengaturan User',
            'list' => ['Home', 'Pengaturan User']
        ];

        $page = (object) [
            'title' => 'Daftar Pengguna'
        ];

        $activeMenu = 'manageuser';

        // Ambil semua level
        $hakAkses = HakAksesModel::where('isDeleted', 0)->get();

        // Jika ada level_id, ambil data user untuk level tersebut
        if ($levelId) {
            $level = HakAksesModel::findOrFail($levelId);
            $users = UserModel::getUsersByLevel($levelId, 10, $search);
            $currentLevel = $level;
        } else {
            $users = UserModel::selectData(10, $search);
            $currentLevel = null;
        }

        return view("ManagePengguna/ManageUser.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'hakAkses' => $hakAkses,
            'users' => $users,
            'currentLevel' => $currentLevel,
            'search' => $search,
            'levelId' => $levelId
        ]);
    }

    public function getData(Request $request)
    {
        $search = $request->query('search', '');
        $levelId = $request->query('level_id', null);

        // Ambil semua level
        $hakAkses = HakAksesModel::where('isDeleted', 0)->get();

        // Jika ada level_id, ambil data user untuk level tersebut
        if ($levelId) {
            $level = HakAksesModel::findOrFail($levelId);
            $users = UserModel::getUsersByLevel($levelId, 10, $search);
            $currentLevel = $level;
        } else {
            $users = UserModel::selectData(10, $search);
            $currentLevel = null;
        }

        if ($request->ajax()) {
            return view('ManagePengguna/ManageUser.data', compact('users', 'hakAkses', 'currentLevel', 'search', 'levelId'))->render();
        }

        return redirect()->route('user.index');
    }

    public function addData(Request $request)
    {
        try {
            $levelId = $request->query('level_id', null);
            
            // Ambil semua level
            $hakAkses = HakAksesModel::where('isDeleted', 0)
                ->when(Auth::user()->level->hak_akses_kode !== 'SAR', function($query) {
                    return $query->where('hak_akses_kode', '!=', 'SAR');
                })
                ->get();
                
            $selectedLevel = null;
            if ($levelId) {
                $selectedLevel = HakAksesModel::findOrFail($levelId);
                
                // Jika user bukan SAR dan mencoba menambah user ke level SAR
                if (Auth::user()->level->hak_akses_kode !== 'SAR' && $selectedLevel->hak_akses_kode === 'SAR') {
                    return response()->json(['error' => 'Anda tidak memiliki izin untuk menambahkan pengguna ke level Super Administrator'], 403);
                }
            }

            return view("ManagePengguna/ManageUser.create", [
                'hakAkses' => $hakAkses,
                'selectedLevel' => $selectedLevel
            ]);
        } catch (\Exception $e) {
            Log::error('Add Data Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function createData(Request $request)
    {
        try {
            // Validasi apakah pengguna mencoba menambahkan ke level SAR
            if ($request->hak_akses_id) {
                $level = HakAksesModel::findOrFail($request->hak_akses_id);
                if (Auth::user()->level->hak_akses_kode !== 'SAR' && $level->hak_akses_kode === 'SAR') {
                    return $this->jsonError(new \Exception('Anda tidak memiliki izin untuk menambahkan pengguna ke level Super Administrator'), 'Anda tidak memiliki izin untuk operasi ini');
                }
            }
            
            $result = UserModel::createData($request);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Pengguna berhasil dibuat'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat membuat pengguna');
        }
    }

    public function editData($id)
    {
        $user = UserModel::detailData($id);
        
        // Cek apakah user yang diedit memiliki hak akses SAR, jika ya, pastikan yang mengedit adalah user SAR
        $isSAR = $user->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
        if ($isSAR && Auth::user()->level->hak_akses_kode !== 'SAR') {
            return response()->json(['error' => 'Anda tidak memiliki izin untuk mengedit pengguna dengan level Super Administrator'], 403);
        }
        
        // Ambil semua hak akses yang bisa ditambahkan ke user
        $availableHakAkses = HakAksesModel::where('isDeleted', 0)
            ->when(Auth::user()->level->hak_akses_kode !== 'SAR', function($query) {
                return $query->where('hak_akses_kode', '!=', 'SAR');
            })
            ->whereNotIn('hak_akses_id', $user->hakAkses->pluck('hak_akses_id'))
            ->get();

        return view("ManagePengguna/ManageUser.update", [
            'user' => $user,
            'availableHakAkses' => $availableHakAkses
        ]);
    }

    public function updateData(Request $request, $id)
    {
        try {
            $user = UserModel::findOrFail($id);
            
            // Cek apakah user yang diedit memiliki hak akses SAR, jika ya, pastikan yang mengedit adalah user SAR
            $isSAR = $user->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
            if ($isSAR && Auth::user()->level->hak_akses_kode !== 'SAR') {
                return $this->jsonError(new \Exception('Anda tidak memiliki izin untuk mengedit pengguna dengan level Super Administrator'), 'Anda tidak memiliki izin untuk operasi ini');
            }
            
            $result = UserModel::updateData($request, $id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Pengguna berhasil diperbarui'
            );
        } catch (ValidationException $e) {
            return $this->jsonValidationError($e);
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat memperbarui pengguna');
        }
    }

    public function detailData($id)
    {
        $user = UserModel::detailData($id);

        return view("ManagePengguna/ManageUser.detail", [
            'user' => $user,
            'title' => 'Detail Pengguna'
        ]);
    }

    public function deleteData(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            $user = UserModel::detailData($id);
            
            // Cek apakah user yang dihapus memiliki hak akses SAR, jika ya, pastikan yang menghapus adalah user SAR
            $isSAR = $user->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
            if ($isSAR && Auth::user()->level->hak_akses_kode !== 'SAR') {
                return response()->json(['error' => 'Anda tidak memiliki izin untuk menghapus pengguna dengan level Super Administrator'], 403);
            }

            return view("ManagePengguna/ManageUser.delete", [
                'user' => $user
            ]);
        }

        try {
            $user = UserModel::findOrFail($id);
            
            // Cek apakah user yang dihapus memiliki hak akses SAR, jika ya, pastikan yang menghapus adalah user SAR
            $isSAR = $user->hakAkses->where('hak_akses_kode', 'SAR')->count() > 0;
            if ($isSAR && Auth::user()->level->hak_akses_kode !== 'SAR') {
                return $this->jsonError(new \Exception('Anda tidak memiliki izin untuk menghapus pengguna dengan level Super Administrator'), 'Anda tidak memiliki izin untuk operasi ini');
            }
            
            $result = UserModel::deleteData($id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Pengguna berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus pengguna');
        }
    }

    // Menambahkan hak akses ke user
    public function addHakAkses(Request $request, $userId)
    {
        try {
            $hakAksesId = $request->hak_akses_id;
            
            // Cek apakah menambahkan hak akses SAR
            $hakAkses = HakAksesModel::findOrFail($hakAksesId);
            if ($hakAkses->hak_akses_kode === 'SAR' && Auth::user()->level->hak_akses_kode !== 'SAR') {
                return $this->jsonError(new \Exception('Anda tidak memiliki izin untuk menambahkan level Super Administrator'), 'Anda tidak memiliki izin untuk operasi ini');
            }
            
            // Cek apakah user sudah memiliki hak akses tersebut
            $exists = SetUserHakAksesModel::where('fk_m_user', $userId)
                ->where('fk_m_hak_akses', $hakAksesId)
                ->where('isDeleted', 0)
                ->exists();
                
            if ($exists) {
                return $this->jsonError(new \Exception('Pengguna sudah memiliki hak akses ini'), 'Hak akses sudah ada');
            }
            
            $result = SetUserHakAksesModel::createData($userId, $hakAksesId);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Hak akses berhasil ditambahkan'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menambahkan hak akses');
        }
    }

    // Menghapus hak akses dari user
    public function deleteHakAkses(Request $request, $id)
    {
        try {
            $userHakAkses = SetUserHakAksesModel::with('HakAkses')->findOrFail($id);
            
            // Cek apakah menghapus hak akses SAR
            if ($userHakAkses->HakAkses->hak_akses_kode === 'SAR' && Auth::user()->level->hak_akses_kode !== 'SAR') {
                return $this->jsonError(new \Exception('Anda tidak memiliki izin untuk menghapus level Super Administrator'), 'Anda tidak memiliki izin untuk operasi ini');
            }
            
            $result = SetUserHakAksesModel::deleteData($id);

            return $this->jsonSuccess(
                $result['data'] ?? null,
                $result['message'] ?? 'Hak akses berhasil dihapus'
            );
        } catch (\Exception $e) {
            return $this->jsonError($e, 'Terjadi kesalahan saat menghapus hak akses');
        }
    }
}
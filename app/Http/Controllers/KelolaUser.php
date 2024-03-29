<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use App\Models\ModelUser;
use App\Models\ModelAdmin;

class KelolaUser extends Controller
{

    private $ModelUser;
    private $ModelAdmin;

    public function __construct()
    {
        $this->ModelUser = new ModelUser();
        $this->ModelAdmin = new ModelAdmin();
    }

    public function index()
    {
        if (!Session()->get('email')) {
            return redirect()->route('admin');
        }

        $data = [
            'title'     => 'Data User',
            'subTitle'  => 'Daftar User',
            'daftarUser' => $this->ModelUser->dataUser(),
            'user'      => $this->ModelAdmin->detail(Session()->get('id_admin')),
        ];

        return view('admin.kelolaUser.dataUser', $data);
    }

    public function tambah()
    {
        if (!Session()->get('email')) {
            return redirect()->route('admin');
        }

        $data = [
            'title'     => 'Data User',
            'subTitle'  => 'Tambah User',
            'user'      => $this->ModelAdmin->detail(Session()->get('id_admin')),
            'form'      => 'Tambah',
        ];

        return view('admin.kelolaUser.form', $data);
    }

    public function prosesTambah()
    {
        Request()->validate([
            'nama'              => 'required',
            'nomor_telepon'     => 'required|numeric',
            'email'             => 'required|unique:user,email|email',
            'password'          => 'min:6|required',
            'foto_user'         => 'required|mimes:jpeg,png,jpg|max:2048',
        ], [
            'nama.required'             => 'Nama lengkap harus diisi!',
            'nomor_telepon.required'    => 'Nomor telepon harus diisi!',
            'nomor_telepon.numeric'     => 'Nomor telepon harus angka!',
            'email.required'            => 'Email harus diisi!',
            'email.unique'              => 'Email sudah digunakan!',
            'email.email'               => 'Email harus sesuai format! Contoh: contoh@gmail.com',
            'password.required'         => 'Password harus diisi!',
            'password.min'              => 'Password minimal 6 karakter!',
            'foto_user.required'        => 'Foto Anda harus diisi!',
            'foto_user.mimes'           => 'Format Foto Anda harus jpg/jpeg/png!',
            'foto_user.max'             => 'Ukuran Foto Anda maksimal 2 mb',
        ]);

        $file1 = Request()->foto_user;
        $fileUser = date('mdYHis') . Request()->nama . '.' . $file1->extension();
        $file1->move(public_path('foto_user'), $fileUser);

        $data = [
            'nama'              => Request()->nama,
            'nomor_telepon'     => Request()->nomor_telepon,
            'email'             => Request()->email,
            'foto_user'         => $fileUser,
            'password'          => Hash::make(Request()->password),
        ];

        $this->ModelUser->tambah($data);
        return redirect()->route('daftar-user')->with('success', 'Data user berhasil ditambahkan !');
    }

    public function edit($id_user)
    {
        if (!Session()->get('email')) {
            return redirect()->route('admin');
        }

        $data = [
            'title'         => 'Data User',
            'subTitle'      => 'Edit User',
            'form'          => 'Edit',
            'user'          => $this->ModelAdmin->detail(Session()->get('id_admin')),
            'detailUser'    => $this->ModelUser->detail($id_user)
        ];

        return view('admin.kelolaUser.form', $data);
    }

    public function prosesEdit($id_user)
    {
        Request()->validate([
            'nama'              => 'required',
            'nomor_telepon'     => 'required|numeric',
            'email'             => 'required|email',
            'foto_user'         => 'mimes:jpeg,png,jpg|max:2048',
        ], [
            'nama.required'             => 'Nama lengkap harus diisi!',
            'nomor_telepon.required'    => 'Nomor telepon harus diisi!',
            'nomor_telepon.numeric'     => 'Nomor telepon harus angka!',
            'email.required'            => 'Email harus diisi!',
            'email.email'               => 'Email harus sesuai format! Contoh: contoh@gmail.com',
            'foto_user.mimes'           => 'Format Foto Anda harus jpg/jpeg/png!',
            'foto_user.max'             => 'Ukuran Foto Anda maksimal 2 mb',
        ]);

        if (Request()->password) {

            $user = $this->ModelUser->detail($id_user);

            if (Request()->foto_user <> "") {
                if ($user->foto_user <> "") {
                    unlink(public_path('foto_user') . '/' . $user->foto_user);
                }

                $file = Request()->foto_user;
                $fileUser = date('mdYHis') . Request()->nama . '.' . $file->extension();
                $file->move(public_path('foto_user'), $fileUser);

                $data = [
                    'id_user'           => $id_user,
                    'nama'              => Request()->nama,
                    'nomor_telepon'     => Request()->nomor_telepon,
                    'email'             => Request()->email,
                    'foto_user'         => $fileUser,
                    'password'          => Hash::make(Request()->password),
                ];
                $this->ModelUser->edit($data);
            } else {
                $data = [
                    'id_user'           => $id_user,
                    'nama'              => Request()->nama,
                    'nomor_telepon'     => Request()->nomor_telepon,
                    'email'             => Request()->email,
                    'password'          => Hash::make(Request()->password),
                ];
                $this->ModelUser->edit($data);
            }
        } else {
            $user = $this->ModelUser->detail($id_user);

            if (Request()->foto_user <> "") {
                if ($user->foto_user <> "") {
                    unlink(public_path('foto_user') . '/' . $user->foto_user);
                }

                $file = Request()->foto_user;
                $fileUser = date('mdYHis') . Request()->nama . '.' . $file->extension();
                $file->move(public_path('foto_user'), $fileUser);

                $data = [
                    'id_user'           => $id_user,
                    'nama'              => Request()->nama,
                    'nomor_telepon'     => Request()->nomor_telepon,
                    'email'             => Request()->email,
                    'foto_user'         => $fileUser,
                ];
                $this->ModelUser->edit($data);
            } else {
                $data = [
                    'id_user'           => $id_user,
                    'nama'              => Request()->nama,
                    'nomor_telepon'     => Request()->nomor_telepon,
                    'email'             => Request()->email,
                ];
                $this->ModelUser->edit($data);
            }
        }
        return redirect()->route('daftar-user')->with('success', 'Data user berhasil diedit !');
    }

    public function prosesHapus($id_user)
    {
        $user = $this->ModelUser->detail($id_user);

        if ($user->foto_user <> "") {
            unlink(public_path('foto_user') . '/' . $user->foto_user);
        }

        $this->ModelUser->hapus($id_user);
        return redirect()->route('daftar-user')->with('success', 'Data user berhasil dihapus !');
    }

    public function profil()
    {
        if (!Session()->get('email')) {
            return redirect()->route('login');
        }

        $data = [
            'title'     => 'Profil',
            'user'      => $this->ModelUser->detail(Session()->get('id_member'))
        ];

        return view('user.profil.profil', $data);
    }

    public function editProfil($id_member)
    {
        Request()->validate([
            'nama'              => 'required',
            'nomor_telepon'     => 'required|numeric',
            'alamat'            => 'required',
            'nama_perusahaan'   => 'required',
            'alamat_perusahaan' => 'required',
            'email'             => 'required|email',
            'foto_perusahaan'   => 'mimes:jpeg,png,jpg|max:2048',
            'foto_user'         => 'mimes:jpeg,png,jpg|max:2048',
        ], [
            'nama.required'             => 'Nama lengkap harus diisi!',
            'nomor_telepon.required'    => 'Nomor telepon harus diisi!',
            'nomor_telepon.numeric'     => 'Nomor telepon harus angka!',
            'alamat.required'           => 'Alamat harus diisi!',
            'nama_perusahaan.required'  => 'Nama perusahaan harus diisi!',
            'alamat_perusahaan.required' => 'Alamat perusahaan harus diisi!',
            'email.required'            => 'Email harus diisi!',
            'email.email'               => 'Email harus sesuai format! Contoh: contoh@gmail.com',
            'foto_perusahaan.mimes'     => 'Format Logo Perusahaan harus jpg/jpeg/png!',
            'foto_perusahaan.max'       => 'Ukuran Logo Perusahaan maksimal 2 mb',
            'foto_user.mimes'           => 'Format Foto Anda harus jpg/jpeg/png!',
            'foto_user.max'             => 'Ukuran Foto Anda maksimal 2 mb',
        ]);

        $user = $this->ModelUser->detail($id_member);

        if (Request()->foto_perusahaan <> "") {
            if ($user->foto_perusahaan <> "") {
                unlink(public_path('foto_perusahaan') . '/' . $user->foto_perusahaan);
            }

            $file1 = Request()->foto_perusahaan;
            $filePerusahaan = date('mdYHis') . Request()->nama_perusahaan . '.' . $file1->extension();
            $file1->move(public_path('foto_perusahaan'), $filePerusahaan);

            $data = [
                'id_member'         => $id_member,
                'nama'              => Request()->nama,
                'nomor_telepon'     => Request()->nomor_telepon,
                'alamat'            => Request()->alamat,
                'nama_perusahaan'   => Request()->nama_perusahaan,
                'alamat_perusahaan' => Request()->alamat_perusahaan,
                'email'             => Request()->email,
                'foto_perusahaan'   => $filePerusahaan
            ];
            $this->ModelUser->edit($data);
        } else {
            $data = [
                'id_member'         => $id_member,
                'nama'              => Request()->nama,
                'nomor_telepon'     => Request()->nomor_telepon,
                'alamat'            => Request()->alamat,
                'nama_perusahaan'   => Request()->nama_perusahaan,
                'alamat_perusahaan' => Request()->alamat_perusahaan,
                'email'             => Request()->email,
            ];
            $this->ModelUser->edit($data);
        }

        if (Request()->foto_user <> "") {
            if ($user->foto_user <> "") {
                unlink(public_path('foto_user') . '/' . $user->foto_user);
            }

            $file2 = Request()->foto_user;
            $fileUser = date('mdYHis') . Request()->nama_perusahaan . '.' . $file2->extension();
            $file2->move(public_path('foto_user'), $fileUser);

            $data = [
                'id_member'         => $id_member,
                'nama'              => Request()->nama,
                'nomor_telepon'     => Request()->nomor_telepon,
                'alamat'            => Request()->alamat,
                'nama_perusahaan'   => Request()->nama_perusahaan,
                'alamat_perusahaan' => Request()->alamat_perusahaan,
                'email'             => Request()->email,
                'foto_user'         => $fileUser
            ];
            $this->ModelUser->edit($data);
        } else {
            $data = [
                'id_member'         => $id_member,
                'nama'              => Request()->nama,
                'nomor_telepon'     => Request()->nomor_telepon,
                'alamat'            => Request()->alamat,
                'nama_perusahaan'   => Request()->nama_perusahaan,
                'alamat_perusahaan' => Request()->alamat_perusahaan,
                'email'             => Request()->email,
            ];
            $this->ModelUser->edit($data);
        }

        return redirect()->route('profil')->with('berhasil', 'Profil berhasil diedit !');
    }

    public function ubahPassword($id_member)
    {
        if (!Session()->get('email')) {
            return redirect()->route('login');
        }


        $data = [
            'title'     => 'Ubah Password',
            'user'      => $this->ModelUser->detail($id_member)
        ];

        return view('user.profil.ubahPassword', $data);
    }

    public function prosesUbahPassword($id_member)
    {
        Request()->validate([
            'password_lama'     => 'required|min:6',
            'password_baru'     => 'required|min:6',
        ], [
            'password_lama.required'    => 'Password Lama harus diisi!',
            'password_lama.min'         => 'Password Lama minimal 6 karakter!',
            'password_baru.required'    => 'Passwofd Baru harus diisi!',
            'password_baru.min'         => 'Password Lama minimal 6 karakter!',
        ]);

        $user = $this->ModelUser->detail($id_member);

        if (Hash::check(Request()->password_lama, $user->password)) {
            $data = [
                'id_member'         => $id_member,
                'password'          => Hash::make(Request()->password_baru)
            ];

            $this->ModelUser->edit($data);
            return back()->with('berhasil', 'Password berhasil diedit !');
        } else {
            return back()->with('gagal', 'Password Lama tidak sesuai.');
        }
    }
}

<?php

namespace App\Controllers;

use App\Models\KomikModel;

class Komik extends BaseController
{
    protected $komikModel;
    public function __construct()
    {
        $this->komikModel = new KomikModel();
    }
    public function index()
    {
        //$komik = $this->komikModel->findAll();

        $data = [
            'title' => 'Daftar Komik | CI 4 Application',
            'komik' => $this->komikModel->getKomik()
        ];

        // cara konek db tanpa model
        // $db = \Config\Database::connect();
        // $komik = $db->query("SELECT * FROM komik");
        // foreach ($komik->getResultArray() as $row) {
        //     d($row);
        // }

        // cara konek db dengan model
        // $komikModel = new \App\Models\KomikModel();
        //$komikModel = new KomikModel();
        //dd($komik);


        return view('komik/index', $data);
    }

    public function detail($slug)
    {
        // $komik = $this->komikModel->getKomik($slug);
        $data = [
            'title' => 'Detail Komik | CI 4 Application',
            'komik' => $this->komikModel->getKomik($slug)
        ];

        // jika komik tidak ada di tabel
        if (empty($data['komik'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul Komik ' . $slug . ' tidak ditemukan');
        }

        return view('komik/detail', $data);
    }

    public function create()
    {
        //session();
        $data = [
            'title' => 'Form Tambah Data Komik | CI 4 Application',
            'validation' => \Config\Services::validation()
        ];
        return view('komik/create', $data);
    }

    public function save()
    {
        // getVar berfungsi untuk mengambil data dari form (get bisa post juga bisa)
        // dd($this->request->getVar());

        // validasi input
        if (!$this->validate([
            //'judul' => 'required|is_unique[komik.judul]'
            'judul' => [
                'rules' => 'required|is_unique[komik.judul]',
                'errors' => [
                    'required' => '{field} komik harus diisi.',
                    'is_unique' => '{field} komik sudah ada.'
                ]
            ]
        ])) {
            $validation = \Config\Services::validation();
            return redirect()->to('komik/create')->withInput();
        }


        $slug = url_title($this->request->getVar('judul'), '-', true);

        $this->komikModel->save([
            //'nama_column' => $this->request->getVar('name di form')'),
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $this->request->getVar('sampul')
        ]);

        // menampilkan pesan berhasil
        session()->setFlashdata('pesan', 'Data Komik berhasil ditambahkan');

        return redirect()->to('/komik');
    }

    public function delete($id)
    {
        $this->komikModel->delete($id);
        session()->setFlashdata('pesan', 'Data Komik berhasil dihapus');
        return redirect()->to('/komik');
    }

    // public function edit($id)
    // {
    //     $data = [
    //         'title' => 'Form Edit Data Komik | CI 4 Application',
    //         'validation' => \Config\Services::validation(),
    //         'komik' => $this->komikModel->getKomik($id)
    //     ];
    //     return view('komik/edit', $data);
    // }
}

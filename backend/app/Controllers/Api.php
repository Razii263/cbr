<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BaseCaseModel;
use App\Models\SiswaModel;
use App\Models\PernyataanSiswaModel;
use App\Models\KasusModel;
use CodeIgniter\API\ResponseTrait;

class Api extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $basecmodel = new BaseCaseModel();

        $data = [
            'base case' => $basecmodel->GetKasus()
        ];
        if ($data) {
            return $this->respond($data);
        } else {
            return $this->failNotFound('Data tidak ditemukan.');
        }
    }

    public function create()
    {
        // Ambil data mahasiswa dari form
        $nama = $this->request->getPost('nama');
        $umur = $this->request->getPost('umur');
        $jenis_kelamin = $this->request->getPost('jenis_kelamin');
        $kelas = $this->request->getPost('kelas');
        
        // Simpan data mahasiswa ke tabel 'siswa'
        $siswaModel = new SiswaModel();
        $data_siswa = [
            'nama' => $nama,
            'umur' => $umur,
            'jenis_kelamin' => $jenis_kelamin,
            'kelas' => $kelas,
        ];
        $siswaModel->insert($data_siswa);
        
        // Ambil ID mahasiswa yang baru saja dimasukkan
        $id_siswa = $siswaModel->getInsertID();
        $number = 1;
        $kasuspre_id = 'K';

        $kasusModel = new KasusModel();

        while ($kasusModel->CekId($kasuspre_id.$number)){
            $number++;
            $id_kasus = $kasuspre_id.$number;
        }
        $data_kasus = [
            'id_kasus' => $id_kasus,
            'id_siswa' => $id_siswa
        ];
        
        // Simpan pilihan pernyataan ke dalam tabel 'pernyataan_siswa'
        $pernyataanSiswaModel = new PernyataanSiswaModel();
        $jawaban = $this->request->getVar('jawaban');
        
        if (!empty($jawaban)) {
            foreach ($jawaban as $jawab) {
                $data_pernyataan_siswa = [
                    'id_kasus' => 'ID_KASUS_YANG_DIPILIH', // Ganti dengan ID kasus yang sesuai
                    'id_pernyataan' => $jawab,
                    'id_siswa' => $id_siswa,
                ];
                $pernyataanSiswaModel->insert($data_pernyataan_siswa);
            }
        }
    }
}

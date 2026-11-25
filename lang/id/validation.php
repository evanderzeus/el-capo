<?php

return [
    'required' => ':attribute wajib diisi.',
    'required_with' => ':attribute wajib diisi.',
    'string' => ':attribute harus berupa teks.',
    'email' => ':attribute harus berupa email yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'exists' => ':attribute tidak ditemukan.',
    'in' => ':attribute tidak valid.',
    'integer' => ':attribute harus berupa angka bulat.',
    'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, strip, dan garis bawah.',
    'different' => ':attribute dan :other tidak boleh sama.',
    'gte' => [
        'numeric' => ':attribute harus lebih besar atau sama dengan :value.',
    ],
    'image' => ':attribute harus berupa gambar.',
    'mimes' => ':attribute harus berformat: :values.',
    'min' => [
        'numeric' => ':attribute minimal :min.',
        'string' => ':attribute minimal :min karakter.',
    ],
    'max' => [
        'numeric' => ':attribute maksimal :max.',
        'string' => ':attribute maksimal :max karakter.',
        'file' => ':attribute maksimal :max KB.',
    ],
    'attributes' => [
        'username' => 'Username',
        'email' => 'Email',
        'password' => 'Password',
        'current_password' => 'Password saat ini',
        'name' => 'Nama',
        'nama_barang' => 'Nama barang',
        'deskripsi' => 'Deskripsi',
        'kondisi' => 'Kondisi',
        'harga_min' => 'Harga minimum',
        'harga_max' => 'Harga maksimum',
        'foto' => 'Foto',
        'status_penawaran' => 'Status penawaran',
        'barang_tujuan_id' => 'Barang tujuan',
        'barang_penawaran_id' => 'Barang penawaran',
    ],
];

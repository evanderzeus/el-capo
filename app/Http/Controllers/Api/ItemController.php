<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ItemController extends ApiController
{
    // GET /api/items            -> daftar barang
    // GET /api/items?id=1       -> detail barang
    // Filter opsional: q, kondisi, mine=1, status=tersedia|ditukar
    public function index(Request $request): JsonResponse
    {
        $me = $request->user()->id;

        if ($request->filled('id')) {
            $item = Item::with('user')->findOrFail($request->query('id'));

            return $this->ok('Detail barang ditemukan', $item->toApi($me));
        }

        $query = Item::with('user')->latest('id');

        if ($request->boolean('mine')) {
            $query->where('user_id', $me);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        } elseif (! $request->boolean('mine')) {
            $query->where('status', 'tersedia');
        }
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->query('kondisi'));
        }
        if ($request->filled('q')) {
            $q = '%'.addcslashes($request->query('q'), '%_\\').'%';
            $query->where(fn ($w) => $w->where('nama_barang', 'like', $q)->orWhere('deskripsi', 'like', $q));
        }

        $items = $query->get()->map(fn (Item $i) => $i->toApi($me));

        return $this->ok('Daftar barang', $items);
    }

    // POST /api/items  (JSON atau multipart/form-data bila ada foto)
    public function store(Request $request): JsonResponse
    {
        $this->normalizeHarga($request);
        $data = $request->validate($this->rules());

        $item = new Item(collect($data)->except('foto')->all());
        $item->user_id = $request->user()->id;
        $item->status = 'tersedia';
        if ($request->hasFile('foto')) {
            $item->foto = $this->saveFoto($request);
        }
        $item->save();

        return $this->ok('Barang berhasil ditambahkan', $item->load('user')->toApi($item->user_id), 201);
    }

    // PUT /api/items?id=1
    // Untuk ganti foto kirim POST multipart ke URL yang sama dengan field _method=PUT
    public function update(Request $request): JsonResponse
    {
        $item = Item::findOrFail($request->query('id'));
        if ($item->user_id !== $request->user()->id) {
            return $this->fail('Anda tidak berhak mengubah barang ini', 403);
        }

        $this->normalizeHarga($request);
        $data = $request->validate($this->rules(partial: true));

        $item->fill(collect($data)->except('foto')->all());
        if ($request->hasFile('foto')) {
            $this->deleteFoto($item);
            $item->foto = $this->saveFoto($request);
        }
        $item->save();

        return $this->ok('Barang berhasil diperbarui', $item->load('user')->toApi($item->user_id));
    }

    // DELETE /api/items?id=1
    public function destroy(Request $request): JsonResponse
    {
        $item = Item::findOrFail($request->query('id'));
        if ($item->user_id !== $request->user()->id) {
            return $this->fail('Anda tidak berhak menghapus barang ini', 403);
        }

        $this->deleteFoto($item);
        $item->delete(); // penawaran terkait ikut terhapus (cascade)

        return $this->ok('Barang berhasil dihapus');
    }

    private function rules(bool $partial = false): array
    {
        $req = $partial ? 'sometimes' : 'required';

        return [
            'nama_barang' => [$req, 'string', 'max:100'],
            'deskripsi' => [$req, 'string', 'max:2000'],
            'kondisi' => [$req, 'in:'.implode(',', Item::KONDISI)],
            'harga_min' => [$req, 'integer', 'min:0', 'max:100000000'],
            'harga_max' => [$req, 'integer', 'gte:harga_min', 'max:100000000'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    // Terima "kisaran_harga": "50000-100000" sebagai pengganti harga_min & harga_max
    private function normalizeHarga(Request $request): void
    {
        if ($request->filled('kisaran_harga')
            && preg_match('/^\s*(\d+)\s*-\s*(\d+)\s*$/', (string) $request->input('kisaran_harga'), $m)) {
            $request->merge(['harga_min' => (int) $m[1], 'harga_max' => (int) $m[2]]);
        }
    }

    private function saveFoto(Request $request): string
    {
        $file = $request->file('foto');
        $name = bin2hex(random_bytes(12)).'.'.$file->extension();
        $file->move(public_path('uploads/items'), $name);

        return $name;
    }

    private function deleteFoto(Item $item): void
    {
        if ($item->foto) {
            @unlink(public_path('uploads/items/'.$item->foto));
        }
    }
}

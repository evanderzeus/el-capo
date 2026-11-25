<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferController extends ApiController
{
    // GET /api/offers -> penawaran masuk & penawaran yang diajukan
    public function index(Request $request): JsonResponse
    {
        $me = $request->user()->id;
        $with = ['barangTujuan.user', 'barangPenawaran.user', 'pengaju', 'pemilik'];

        $masuk = Offer::with($with)->where('pemilik_id', $me)->latest('id')->get();
        $dikirim = Offer::with($with)->where('pengaju_id', $me)->latest('id')->get();

        return $this->ok('Daftar penawaran', [
            'masuk' => $masuk->map(fn (Offer $o) => $o->toApi($me))->values(),
            'dikirim' => $dikirim->map(fn (Offer $o) => $o->toApi($me))->values(),
        ]);
    }

    // POST /api/offers
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'barang_tujuan_id' => ['required', 'integer', 'exists:items,id'],
            'barang_penawaran_id' => ['required', 'integer', 'exists:items,id', 'different:barang_tujuan_id'],
        ]);
        $me = $request->user()->id;

        $tujuan = Item::findOrFail($data['barang_tujuan_id']);
        $penawaran = Item::findOrFail($data['barang_penawaran_id']);

        if ($tujuan->user_id === $me) {
            return $this->fail('Anda tidak dapat menawar barang milik sendiri', 422);
        }
        if ($penawaran->user_id !== $me) {
            return $this->fail('Barang penawaran harus milik Anda', 403);
        }
        if ($tujuan->status !== 'tersedia' || $penawaran->status !== 'tersedia') {
            return $this->fail('Salah satu barang sudah tidak tersedia', 422);
        }

        $duplikat = Offer::where('barang_tujuan_id', $tujuan->id)
            ->where('barang_penawaran_id', $penawaran->id)
            ->where('status_penawaran', Offer::MENUNGGU)
            ->exists();
        if ($duplikat) {
            return $this->fail('Penawaran yang sama masih menunggu keputusan pemilik', 422);
        }

        $offer = Offer::create([
            'barang_tujuan_id' => $tujuan->id,
            'barang_penawaran_id' => $penawaran->id,
            'pengaju_id' => $me,
            'pemilik_id' => $tujuan->user_id,
            'status_penawaran' => Offer::MENUNGGU,
        ]);

        return $this->ok('Penawaran berhasil diajukan', [
            'id' => $offer->id,
            'status_penawaran' => $offer->status_penawaran,
        ], 201);
    }

    // PUT /api/offers?id=1  body: {"status_penawaran": "Diterima" | "Ditolak"}
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'status_penawaran' => ['required', 'in:'.Offer::DITERIMA.','.Offer::DITOLAK],
        ]);
        $offer = Offer::findOrFail($request->query('id'));

        if ($offer->pemilik_id !== $request->user()->id) {
            return $this->fail('Hanya pemilik barang yang dapat menanggapi penawaran', 403);
        }
        if ($offer->status_penawaran !== Offer::MENUNGGU) {
            return $this->fail('Penawaran ini sudah diproses', 422);
        }

        $status = $request->input('status_penawaran');

        DB::transaction(function () use ($offer, $status) {
            $offer->update(['status_penawaran' => $status]);

            if ($status === Offer::DITERIMA) {
                $ids = [$offer->barang_tujuan_id, $offer->barang_penawaran_id];
                Item::whereIn('id', $ids)->update(['status' => 'ditukar']);

                // Penawaran lain yang masih menunggu dan memakai barang yang sama otomatis ditolak
                Offer::where('id', '!=', $offer->id)
                    ->where('status_penawaran', Offer::MENUNGGU)
                    ->where(fn ($q) => $q->whereIn('barang_tujuan_id', $ids)->orWhereIn('barang_penawaran_id', $ids))
                    ->update(['status_penawaran' => Offer::DITOLAK]);
            }
        });

        return $this->ok('Status penawaran berhasil diperbarui', ['status_penawaran' => $status]);
    }

    // DELETE /api/offers?id=1 -> batalkan penawaran yang masih menunggu
    public function destroy(Request $request): JsonResponse
    {
        $offer = Offer::findOrFail($request->query('id'));

        if ($offer->pengaju_id !== $request->user()->id) {
            return $this->fail('Hanya pengaju yang dapat membatalkan penawaran', 403);
        }
        if ($offer->status_penawaran !== Offer::MENUNGGU) {
            return $this->fail('Hanya penawaran yang masih menunggu yang dapat dibatalkan', 422);
        }

        $offer->delete();

        return $this->ok('Penawaran berhasil dibatalkan');
    }
}

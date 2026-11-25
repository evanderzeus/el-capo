<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    public const MENUNGGU = 'Menunggu';
    public const DITERIMA = 'Diterima';
    public const DITOLAK = 'Ditolak';

    protected $fillable = [
        'barang_tujuan_id', 'barang_penawaran_id', 'pengaju_id', 'pemilik_id', 'status_penawaran',
    ];

    public function barangTujuan(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'barang_tujuan_id');
    }

    public function barangPenawaran(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'barang_penawaran_id');
    }

    public function pengaju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengaju_id');
    }

    public function pemilik(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemilik_id');
    }

    public function toApi(int $viewerId): array
    {
        $menunggu = $this->status_penawaran === self::MENUNGGU;
        $diterima = $this->status_penawaran === self::DITERIMA;
        $lawan = $this->pengaju_id === $viewerId ? $this->pemilik : $this->pengaju;

        return [
            'id' => $this->id,
            'status_penawaran' => $this->status_penawaran,
            'barang_tujuan' => $this->barangTujuan?->load('user')->toApi($viewerId),
            'barang_penawaran' => $this->barangPenawaran?->load('user')->toApi($viewerId),
            'pengaju' => ['id' => $this->pengaju->id, 'username' => $this->pengaju->username],
            'pemilik' => ['id' => $this->pemilik->id, 'username' => $this->pemilik->username],
            'can_respond' => $menunggu && $this->pemilik_id === $viewerId,
            'can_cancel' => $menunggu && $this->pengaju_id === $viewerId,
            // kontak hanya dibuka setelah penawaran diterima agar kedua pihak bisa janjian
            'kontak' => $diterima ? ['username' => $lawan->username, 'email' => $lawan->email] : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

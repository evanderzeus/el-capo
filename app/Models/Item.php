<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    public const KONDISI = ['New', 'Like new', 'Good condition', 'Fair'];

    protected $fillable = [
        'user_id', 'nama_barang', 'deskripsi', 'kondisi',
        'harga_min', 'harga_max', 'foto', 'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function offersReceived(): HasMany
    {
        return $this->hasMany(Offer::class, 'barang_tujuan_id');
    }

    public function toApi(?int $viewerId = null): array
    {
        return [
            'id' => $this->id,
            'nama_barang' => $this->nama_barang,
            'deskripsi' => $this->deskripsi,
            'kondisi' => $this->kondisi,
            'kisaran_harga' => $this->harga_min.'-'.$this->harga_max,
            'harga_min' => $this->harga_min,
            'harga_max' => $this->harga_max,
            'foto_url' => $this->foto ? asset('uploads/items/'.$this->foto) : null,
            'status' => $this->status,
            'pemilik' => $this->user ? ['id' => $this->user->id, 'username' => $this->user->username] : null,
            'is_mine' => $viewerId !== null && $this->user_id === $viewerId,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

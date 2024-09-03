<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    use HasFactory;
    protected $table = "suppliers";
    protected $primaryKey = "id";
    protected $keyType = "int"; // Tipe data dari primary key
    public $timestamps = true; // Apakah model menggunakan timestamp
    public $incrementing = true; // Apakah primary key auto-increment

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',
        'account_number',
        'created_at',
        'updated_at'
    ];
    // Definisi relasi one-to-many dengan model user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}

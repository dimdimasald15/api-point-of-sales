<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model implements Authenticatable
{
    use HasFactory;

    protected $table = "employees";
    protected $primaryKey = "id";
    protected $keyType = "int"; // Tipe data dari primary key
    public $timestamps = true; // Apakah model menggunakan timestamp
    public $incrementing = true; // Apakah primary key auto-increment

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'user_id',
        'username',
        'password',
        'created_at',
        'updated_at'
    ];
    // Definisi relasi one-to-many dengan model user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    // Metode untuk memenuhi kontrak Authenticatable
    public function getAuthIdentifierName()
    {
        return 'username'; // Nama kolom yang digunakan sebagai identifier
    }

    public function getAuthIdentifier()
    {
        return $this->username; // Nilai identifier yang digunakan
    }

    public function getAuthPassword()
    {
        return $this->password; // Nilai password yang digunakan untuk autentikasi
    }

    public function getRememberToken()
    {
        return $this->token; // Nilai token "remember me"
    }

    public function setRememberToken($value)
    {
        $this->token = $value; // Mengatur nilai token "remember me"
    }

    public function getRememberTokenName()
    {
        return 'token'; // Nama kolom yang digunakan untuk token "remember me"
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Model
{
    use HasFactory;

    protected $table = "users"; // Nama tabel di database
    protected $primaryKey = "id"; // Nama kolom primary key di tabel
    protected $keyType = "int"; // Tipe data dari primary key
    public $timestamps = true; // Apakah model menggunakan timestamp
    public $incrementing = true; // Apakah primary key auto-increment

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'firstname',
        'lastname',
        'phone_number',
        'email',
        'role',
        'address',
        'city',
        'province',
        'country',
        'postal_code',
        'comments',
        'created_at',
        'updated_at'
    ];

    // Definisi relasi one-to-many dengan model employee
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, "user_id", "id");
    }

    // Definisi relasi one-to-many dengan model customer
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, "user_id", "id");
    }

    // Definisi relasi one-to-many dengan model supplier
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, "user_id", "id");
    }
}

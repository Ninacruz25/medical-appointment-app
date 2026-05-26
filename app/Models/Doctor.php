<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $fillable = [
        'specialty_id',
        'medical_license_number',
        'biography',
    ];

    // Relación uno a uno inversa con User
    public function user(){
        return $this->belongsTo(User::class);
    }

    // Relación uno a muchos con Specialty
    public function specialty(){
        return $this->belongsTo(Specialty::class);
    }
}

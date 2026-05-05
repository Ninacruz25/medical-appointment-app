<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'allergies',
        'chronic_diseases',
        'surgerical_history',
        'family_history',
        'observations',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relationship',
    ];

    // Relación uno a uno inversa con Patient
    public function user(){
        return $this->belongsTo(User::class);
    }

    // Relación uno a muchos con BloodType
    public function bloodType(){
        return $this->belongsTo(BloodType::class);
    }
}

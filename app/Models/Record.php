<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Record extends Model
{
    use HasFactory;

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d F Y H:i:s');
    }
    public function getGuestTypeAttribute($value) 
    {
        switch ($value) {
            case 1:
              return 'Temporary Member';
            case 2:
              return 'Guest of Member';
        }
    }
}

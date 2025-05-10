<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PositionCompany extends Model
{
    protected $table = 'position_companies'; // Explicitly define table name

    protected $fillable = [
        'name', // Assuming there is a name column
        // Add other fillable attributes if they exist
    ];

    // Define relationships if any. For instance, if it relates back to CompanyUser:
    // public function companyUser()
    // {
    //    return $this->belongsTo(CompanyUser::class, 'id', 'id'); // This is speculative
    // }
}

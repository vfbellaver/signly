<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'slugname'
    ];

    protected $casts = [

    ];

    protected $dates = [

    ];

    #region Relationships{relationships}
    public function clients(){
        return $this->belongsToMany(Client::class);
    }

    #endregion

    #region Custom Attributes

    #endregion

    #region Queries

    #endregion

    #region Conversions
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slugname' => $this->slugname,
        ];
    }
    #endregion

}

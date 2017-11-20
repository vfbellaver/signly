<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'team_id',
        'proposal_id',
		'user_id',
		'from_name',
		'comment',
    ];

	public function team()
	{
		return $this->belongsTo(Team::class);
    }
    
	public function user()
	{
		return $this->belongsTo(User::class);
	}

    public function proposal()
    {
        return $this->belongsTo(proposal::class);
    }

    public function toArray() 
    {
        return array_merge(parent::toArray(), [
            'name' => $this->user ? $this->user->name : $this->from_name,
            'created_at' => $this->created_at->format('m.d.Y h:i:s A')
        ]);
    }

}
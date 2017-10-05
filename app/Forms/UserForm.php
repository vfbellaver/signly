<?php

namespace App\Forms;

use App\Models\Team;

class UserForm extends BaseForm
{
    public function name()
    {
        return $this->request->get('name');
    }

    public function photo_url()
    {
        return $this->request->get('photo_url');
    }

    public function email()
    {
        return $this->request->get('email');
    }

    public function role()
    {
        if( ! $this->request->has('role') ) return null;

        $role = (object)$this->request->get('role');
        return \Defender::findRoleById($role->id);
    }

    public function team()
    {
        $team = (object)$this->request->get('team');
        return Team::query()->findOrFail($team->id);
    }
}

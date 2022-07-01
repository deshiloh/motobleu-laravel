<?php

namespace App\Services;

use App\Models\User;

class FormHelperService
{
    /**
     * @return array
     */
    public function getUsersSelectDatas(): array
    {
        $selectDatas = [];

        foreach (User::all() as $data) {
            $selectDatas[$data->id] = $data->nom;
        }
        return $selectDatas;
    }
}

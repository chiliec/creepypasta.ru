<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;

final class AutoCreateUserProvider extends EloquentUserProvider {

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if ($user = parent::retrieveByCredentials($credentials)) {
            return $user;
        }
        if (!in_array('name', $credentials)) {
            $credentials['name'] = 'Guest';
        }
        $model = $this->createModel();
        $model->fill($credentials);
        if ($model->save()) {
            return $model;
        }
    }
}

<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Str;

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
        if ((count($credentials) === 1 && Str::contains($this->firstCredentialKey($credentials), 'email'))) {
            $model = $this->createModel();
            $credentials['name'] = strstr($credentials['email'], '@', true) ?: 'Guest';
            $model->fill($credentials);
            if ($model->save()) {
                return parent::retrieveByCredentials($credentials);
            }
        }
    }
}

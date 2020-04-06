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
        if (array_key_exists('email', $credentials)) {
            $credentials = ['email' => $credentials['email']];
            if (!parent::retrieveByCredentials($credentials)) {
                $model = $this->createModel();
                $credentials['name'] = 'Guest';
                $model->fill($credentials);
                if ($model->save()) {
                    return parent::retrieveByCredentials($credentials);
                }
            }
        }
    }
}

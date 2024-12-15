<?php

namespace Framework\Middleware;

use Framework\Session;

class Authorize {

    // Check if user is authenticated
    public function isAuthenticated(): bool {
        return Session::has('user');
    }
    
    // Handle the user requests
    public function handle(string $role): void {
        if('guest' === $role && $this->isAuthenticated()) {
            redirect('/');
        } elseif('auth' === $role && !$this->isAuthenticated()) {
            redirect('/auth/login');
        }
    }
}
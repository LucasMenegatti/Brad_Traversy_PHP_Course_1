<?php

namespace Framework;

use Framework\Session;

class Authorization {
    // Check if current logged in user owns a resource
    public static function isOwner(int $resourceId): bool {
        $sessionUser = Session::get('user');

        if(null !== $sessionUser && isset($sessionUser['id'])) {
            $sessionUserId = (int) $sessionUser['id'];
            return $sessionUserId === $resourceId;
        }
        return false;
    }
}
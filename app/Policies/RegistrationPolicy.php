<?php

namespace App\Policies;

use App\Models\Registration;
use App\Models\User;

class RegistrationPolicy
{
    /**
     * Determine whether the user can download or preview the certificate for this registration.
     */
    public function downloadCertificate(User $user, Registration $registration): bool
    {
        return $user->isAdmin() || $registration->user_id === $user->id;
    }
}

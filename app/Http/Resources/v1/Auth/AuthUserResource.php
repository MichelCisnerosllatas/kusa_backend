<?php

namespace App\Http\Resources\v1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\User
 */
class AuthUserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'dni' => $this->dni,
            'name' => $this->name,
            'email' => $this->email,
            'created_at' => $this->created_at,
        ];
    }
}

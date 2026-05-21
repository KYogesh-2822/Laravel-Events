<?php

namespace App\Http\Resources;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'today_quote' => $this->quote,
            'inbuild_quote' => $this->inbuildQuote,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}

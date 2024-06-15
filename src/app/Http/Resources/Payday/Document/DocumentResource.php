<?php

namespace App\Http\Resources\Payday\Document;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Carbon;

class DocumentResource extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'documents' => $this->collection->map(function ($data) {
                return [
                    'id' => intval($data->id),
                    'user_id' => intval($data->user_id),
                    'name' => $data->name,
                    'path' => $data->path,
                    'full_url' => $data->full_url,
                    'created_at' => is_string($data->created_at) ? Carbon::parse($data->created_at)->format('Y-m-d H:i:s') : null,
                    'updated_at' => is_string($data->updated_at) ? Carbon::parse($data->updated_at)->format('Y-m-d H:i:s') : null,
                    'created_by' => [
                        'id' => intval($data->createdBy->id ?? 0),
                        'first_name' => $data->createdBy->first_name ?? '',
                        'last_name' => $data->createdBy->last_name ?? '',
                        'full_name' => $data->createdBy->full_name ?? ''
                    ],
                ];
            }),
            'links' => [
                "first" => request()->url() . "?page=1",
                "last" => request()->url() . "?page=" . $this->lastPage(),
                "prev" => $this->previousPageUrl(),
                "next" => $this->nextPageUrl()
            ],
            'meta' => [
                'total' => $this->total(),
                'count' => $this->count(),
                'per_page' => $this->perPage(),
                'current_page' => $this->currentPage(),
                'total_pages' => $this->lastPage()
            ]
        ];
    }
}

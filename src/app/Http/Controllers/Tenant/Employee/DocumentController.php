<?php

namespace App\Http\Controllers\Tenant\Employee;

use App\Exceptions\GeneralException;
use App\Filters\Tenant\DocumentFilter;
use App\Helpers\Core\Traits\FileHandler;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Employee\DocumentRequest;
use App\Models\Tenant\Employee\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    use FileHandler;

    public function __construct(DocumentFilter $filter)
    {
        $this->filter = $filter;
    }

    public function index()
    {
        return Document::query()
            ->filters($this->filter)
            ->with('createdBy:id,first_name,last_name')
            ->latest()
            ->paginate(\request('per_page', 10));
    }

    public function store(DocumentRequest $request)
    {

        $file_path = $this->uploadImage(
            request()->file('file'),
            'documents',
            null
        );

        Document::query()->create(array_merge(
            $request->only('user_id', 'name'),
            [
                'created_by' => auth()->id(),
                'path' => $file_path
            ]
        ));

        return created_responses('document');
    }


    public function show(Document $document)
    {
        return $document;
    }


    public function update(Request $request, Document $document)
    {
        $file_path = $document->path;
        if ($request->hasFile('file')) {
            $this->deleteImage($document->path);
            $file_path = $this->uploadImage(
                request()->file('file'),
                'documents'
            );
        }

        $document->update([
            'name' => $request->name,
            'path' => $file_path
        ]);

        return updated_responses('document');
    }


    public function destroy(Document $document)
    {
        $this->deleteImage($document->path);

        $document->delete();

        return deleted_responses('document');
    }

    public function downloadDocument($id)
    {
        $document = Document::query()
            ->filters($this->filter)
            ->where('id', $id)
            ->firstOrFail();

        if ($document) {
            return Storage::disk(config('filesystems.default'))
                    ->download(str_replace('/storage', '', $document->path));
        }

        throw new GeneralException(__t('action_not_allowed'));
    }
}

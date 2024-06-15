<?php

namespace App\Http\Controllers\Api\Document;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\Employee\DocumentController as EmployeeDocumentController;
use App\Http\Requests\Tenant\Employee\DocumentRequest;
use App\Http\Resources\Payday\Document\DocumentResource;
use App\Models\Tenant\Employee\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentController extends Controller
{
    public function index()
    {

        try {
            request()->merge(['user_id' => Auth::id()]);
            $documents = resolve(EmployeeDocumentController::class)->index();
            return success_response('Document List', new DocumentResource($documents));
        } catch (\Exception $ex) {
            return error_response('Internal Server Error!', [], 500);
        }
    }

    public function store(DocumentRequest $request)
    {
        try {
            $response = resolve(EmployeeDocumentController::class)->store($request);
            $response['data'] = [];
            return $response;
        } catch (\Exception $ex) {
            return error_response('Internal Server Error!', [], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $document = Document::findOrFail($request->id);
            $response = resolve(EmployeeDocumentController::class)->update($request, $document);
            $response['data'] = [];
            return $response;
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $document = Document::findOrFail($request->id);
            $response = resolve(EmployeeDocumentController::class)->destroy($document);
            $response['data'] = [];
            return $response;
        } catch (\Exception $ex) {
            return error_response($ex->getMessage(), [], 500);
        }
    }
}

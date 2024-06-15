<?php

namespace App\Http\Controllers\Tenant\Settings;

use App\Exceptions\GeneralException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\StorageRequest;
use App\Services\Core\Setting\SettingService;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Artisan;

class StorageSettingController extends Controller
{

    public function __construct(SettingService $setting)
    {
        $this->service = $setting;
    }


    public function index()
    {
        return $this->service->getFormattedSettings('storage_configuration');
    }


    public function update(StorageRequest $request)
    {
        try {

            $attributes = $this->getAttributes($request);
            $this->service->saveSettings($attributes, 'storage_configuration');

            Artisan::call('optimize:clear');
        } catch (\Exception $exception) {
            throw new GeneralException(trans('default.incorrect_storage_credential'));
        }

        return updated_responses('storage_setting');
    }

    private function getS3Attributes($request)
    {
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $request->aws_region,
            'credentials' => [
                'key' => $request->aws_access_key,
                'secret' => $request->aws_secret_key,
            ],
        ]);

        if (!$this->bucketExists($s3Client, $request->aws_bucket_name)) {
            throw new GeneralException(trans('default.incorrect_storage_credential'));
        }

        return $request->only([
            'storage_type',
            'aws_bucket_name',
            'aws_region',
            'aws_access_key',
            'aws_secret_key',
            'aws_use_path_style_end_point',
        ]);
    }

    private function getAttributes($request)
    {
        if ($request->storage_type === 's3') {
            return $this->getS3Attributes($request);
        }
        return $request->only(['storage_type']);
    }

    private function bucketExists($s3Client, $bucketName)
    {
        return $s3Client->doesBucketExist($bucketName);
    }
}
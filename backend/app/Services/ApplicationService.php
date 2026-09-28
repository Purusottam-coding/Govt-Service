<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ApplicationService
{
    /**
     * Create a new citizen application with optional documents.
     */
    public function createApplication(User $user, Service $service, array $data, array $files = [], array $documentNames = []): Application
    {
        return DB::transaction(function () use ($user, $service, $data, $files, $documentNames) {
            $application = Application::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'applicant_name' => $data['applicant_name'] ?? $user->name,
                'applicant_email' => $data['applicant_email'] ?? $user->email,
                'applicant_phone' => $data['applicant_phone'] ?? $user->phone,
                'applicant_address' => $data['applicant_address'] ?? $user->address,
                'status' => ApplicationStatus::PENDING->value,
                'submitted_at' => now(),
            ]);

            $this->attachDocuments($application, $files, $documentNames);

            return $application;
        });
    }

    /**
     * Attach uploaded documents to an application.
     *
     * @param Application $application
     * @param array<int, UploadedFile> $files
     * @param array<int, string> $documentNames
     */
    public function attachDocuments(Application $application, array $files, array $documentNames = []): void
    {
        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile && $file->isValid()) {
                $docName = $documentNames[$index] ?? $file->getClientOriginalName();
                $path = $file->store('application_documents/' . $application->id, 'public');

                ApplicationDocument::create([
                    'application_id' => $application->id,
                    'document_name' => $docName,
                    'file_path' => $path,
                ]);
            }
        }
    }

    /**
     * Update application status.
     */
    public function updateStatus(Application $application, ApplicationStatus|string $status, ?string $remarks = null): Application
    {
        $statusValue = $status instanceof ApplicationStatus ? $status->value : $status;

        $application->update([
            'status' => $statusValue,
            'remarks' => $remarks,
        ]);

        return $application;
    }
}

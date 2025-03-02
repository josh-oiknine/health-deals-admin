<?php

declare(strict_types=1);

namespace App\Services;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Exception;
use Psr\Http\Message\UploadedFileInterface;

class S3Service
{
    private S3Client $s3Client;
    private string $bucket;
    private string $region;
    private string $baseUrl;

    public function __construct()
    {
        $this->region = $_ENV['AWS_REGION'] ?? '';
        $this->bucket = $_ENV['AWS_S3_BUCKET'] ?? '';
        $this->baseUrl = $_ENV['AWS_S3_URL'] ?? "https://{$this->bucket}.s3.{$this->region}.amazonaws.com";

        $this->s3Client = new S3Client([
            'version' => 'latest',
            'region' => $this->region,
            'credentials' => [
                'key' => $_ENV['AWS_ACCESS_KEY_ID'] ?? '',
                'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? '',
            ],
        ]);
    }

    /**
     * Upload a file to S3
     * 
     * @param UploadedFileInterface $file The uploaded file
     * @param string $directory The directory to store the file in (e.g., 'blog-images/')
     * @return string|null The URL of the uploaded file or null on failure
     */
    public function uploadFile(UploadedFileInterface $file, string $directory = 'uploads/'): ?string
    {
        try {
            // Generate a unique filename
            $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
            $filename = sprintf(
                '%s/%s.%s',
                rtrim($directory, '/'),
                uniqid('', true),
                $extension
            );

            // Upload the file
            $result = $this->s3Client->putObject([
                'Bucket' => $this->bucket,
                'Key' => $filename,
                'Body' => $file->getStream(),
                'ACL' => 'public-read',
                'ContentType' => $file->getClientMediaType(),
            ]);

            // Return the URL of the uploaded file
            return $result['ObjectURL'] ?? "{$this->baseUrl}/{$filename}";
        } catch (S3Exception $e) {
            error_log("S3 Upload Error: " . $e->getMessage());
            return null;
        } catch (Exception $e) {
            error_log("Upload Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a file from S3
     * 
     * @param string $fileUrl The URL of the file to delete
     * @return bool Whether the deletion was successful
     */
    public function deleteFile(string $fileUrl): bool
    {
        try {
            // Extract the key from the URL
            $key = str_replace("{$this->baseUrl}/", '', $fileUrl);

            // Delete the file
            $this->s3Client->deleteObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);

            return true;
        } catch (S3Exception $e) {
            error_log("S3 Delete Error: " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Delete Error: " . $e->getMessage());
            return false;
        }
    }
} 
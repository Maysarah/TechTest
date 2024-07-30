<?php


namespace App\Services;

use App\Models\Article;
use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ArticleImageService
{
    /**
     * Store images for an article on S3 and create records in the database.
     *
     * @param Article $article
     * @param array $images
     * @return void
     */
    public function storeImages(Article $article, array $images): void
    {
        foreach ($images as $image) {
            if ($image instanceof UploadedFile) {
                // Generate a unique file name and path
                $filename = uniqid('', true) . '.' . $image->getClientOriginalExtension();
                $path = "articles/images/{$article->id}/{$filename}";

                // Store the image on S3
                Storage::disk('s3')->put($path, file_get_contents($image));

                // Create an image record associated with the article
                $article->images()->create(['path' => $path]);
            }
        }
    }

    /**
     * Delete images from S3 and the database.
     *
     * @param iterable $images
     * @return void
     */
    public function deleteImages(iterable $images): void
    {
        foreach ($images as $image) {
            if ($image instanceof Image) {
                // Delete the image from S3
                Storage::disk('s3')->delete($image->path);

                // Delete the image record from the database
                $image->delete();
            }
        }
    }
}


<?php

namespace App\Common;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Attach this Trait to a User (or other model) for easier read/writes on Replies
 *
 * @author someones
 */
trait ImageUploadManual
{
	public function saveDigitalSignImage($hash, $unique_number ,$type = null)
	{
		$folderPath = "images/".$unique_number.'/';

		$image_parts = explode(";base64,", $hash);
				
		$image_type_aux = explode("image/", $image_parts[0]);
		
		$image_type = $image_type_aux[1];
		
		$image_base64 = base64_decode($image_parts[1]);

		$filename = $folderPath . uniqid('image_') . ".png"; // Generate a unique filename with .png extension

        $converted = convert_img_to($image_base64, 'webp');

		$item = Storage::put($filename, $image_base64);
		return $filename;
	}
    /**
     * Save images
     *
     * @param  file  $image
     * @return image model
     */
    public function saveImage($image, $unique_number = null,$type = null)
    {
        $ext = 'webp';
        $converted = convert_img_to($image->getRealPath(), $ext);
        // $converted = InterventionImage::make($image->getRealPath())->stream($ext, 100);

        // On google drive the folder id is being used instead of directory name
        // $dir = config('filesystems.default') == 'google' ? '' : image_storage_dir();
		$dir = "images/".$unique_number;
        
        // if (!Storage::exists($dir))
        // 	Storage::makeDirectory($dir, 0775, true, true);

        $path = $dir . '/' . uniqid() . '.' . $ext;

        Storage::put($path, $converted);

        $originalName = $image->getClientOriginalName();
        $imageSize = $image->getSize();
        //$extension = $image->getClientOriginalExtension();

        return $this->createImage($path, $originalName, $ext, $imageSize, $type);
    }

    /**
     * Update images
     *
     * @param  file  $image
     * @return image model
     */
    public function updateImage($image, $type = null)
    {
        // Delete the old image if exist
        $this->deleteImageTypeOf($type);

        return $this->saveImage($image, $type);
    }

    /**
     * Save images from external URL
     *
     * @param  file  $image
     *
     * @return image model
     */
    public function saveImageFromUrl($url, $type = null)
    {
        // Get file info and validate
        $file_headers = get_headers($url, true);
        $pathinfo = pathinfo($url);
        // $size = getimagesize($url);

        // when server not found
        if ($file_headers === false) {
            return;
        }

        // Get file extension
        $extension = isset($pathinfo['extension']) ? $pathinfo['extension'] : substr($url, strrpos($url, '.', -1) + 1);

        // Check if the file is a valid image file
        if (!in_array($extension, config('image.mime_types', ['jpg', 'png', 'jpeg']))) {
            return;
        }

        // Get file name
        $name = isset($pathinfo['filename']) ? $pathinfo['filename'] . '.' . $extension : substr($url, strrpos($url, '/', -1) + 1);

        // Get the original file
        $file_content = file_get_contents($url);

        // Get file size in Bite
        $size = isset($file_headers['Content-Length']) ? $file_headers['Content-Length'] : strlen($file_content);

        if (is_array($size)) {
            $size = array_key_exists(1, $size) ? $size[1] : $size[0];
        }

        // Make path and upload
        $path = image_storage_dir() . '/' . uniqid() . '.' . $extension;

        Storage::put($path, $file_content);

        return $this->createImage($path, $name, $extension, $size, $type);
    }

    /**
     * Deletes the given image.
     *
     * @return bool
     */
    public function deleteImage($image = null)
    {
        if (!$image) {
            $image = $this->image;
        }

        if (optional($image)->path) {
            Storage::delete($image->path);

            Storage::deleteDirectory(image_cache_path($image->path));

            return $image->delete();
        }
    }

    /**
     * Deletes the Featured Image of this model.
     *
     * @return bool
     */
    public function deleteFeaturedImage()
    {
        if ($img = $this->featuredImage) {
            $this->deleteImage($img);
        }
    }

    /**
     * Deletes the Featured Image of this model.
     *
     * @return bool
     */
    public function deleteCoverImage()
    {
        if ($img = $this->coverImage) {
            $this->deleteImage($img);
        }
    }

    /**
     * Deletes the special type of image of this model.
     *
     * @return bool
     */
    public function deleteImageTypeOf($type)
    {
        if ($type) {
            // Delete the old image if exist
            $rel = $type . 'Image';

            if ($img = $this->$rel) {
                $this->deleteImage($img);
            }
        }
    }

    /**
     * Deletes the Brand Logo Image of this model.
     *
     * @return bool
     */
    public function deleteLogo()
    {
        // Will be removed
        if ($img = $this->logo) {
            $this->deleteImage($img);
        }

        if ($img = $this->logoImage) {
            $this->deleteImage($img);
        }
    }

    /**
     * Deletes all the images of this model.
     *
     * @return bool
     */
    public function flushImages()
    {
        foreach ($this->images as $image) {
            $this->deleteImage($image);
        }

        $this->deleteLogo();

        $this->deleteFeaturedImage();
    }

    /**
     * Create image model
     *
     * @return array
     */
    private function createImage($path, $name, $ext = '.jpeg', $size = null, $type = null)
    {
        return [
            'path' => $path,
            'name' => $name,
            'type' => $type,
            'extension' => $ext,
            'size' => $size,
		];
    }

    /**
     * Prepare the previews for the dropzone
     *
     * @return array
     */
    public function previewImages()
    {
        $urls = '';
        $configs = '';

        foreach ($this->images as $image) {
            // $path = Storage::url($image->path);
            $path = url('image/' . $image->path);
            $deleteUrl = route('image.delete', $image->id);
            $urls .= '"' . $path . '",';
            $configs .= '{caption:"' . $image->name . '", size:' . $image->size . ', url: "' . $deleteUrl . '", key:' . $image->id . '},';
        }

        return [
            'urls' => rtrim($urls, ','),
            'configs' => rtrim($configs, ','),
        ];
    }
}

<?php

namespace App\Helpers;

use App\Http\Controllers\Controller;
use App\Models\Design;
use App\Models\LinkedStyleProp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class RecursiveFunctions
{
    public static function flattenWithParentId(array $elements): array
    {
        $flattened = [];

        foreach ($elements as $element) {


            // Prepare the current element without its children
            $currentElement = $element;
            $children = $element['children'] ?? [];

            unset($currentElement['children']);
//            unset($currentElement['styles']);
            Log::info("element: ", (array)$currentElement);
            // Add the current element to the flattened array
            $flattened[] = $currentElement;

            // Recursively add children elements to the flattened array
            if (!empty($children)) {
                $flattened = array_merge($flattened, self::flattenWithParentId($children));
            }
        }

        return $flattened;
    }

    public static function handleUpdateDesigns(
        &$design,
        Request $request,
        string $imagesFolderName,
        string $imagesInsideDesignFolderName,
        $parentKey = '',
        &$controller = true // Pass controller as a reference
    ): void {
        DB::beginTransaction();
        try {

            // Find or create the design
            $resource = Design::find($design['id']);

            // Handle design_image file upload
            if ($request->hasFile("design_image")) {
                if (!empty($resource['design_image'])) {
                    FilesFunctions::deleteFile($resource['design_image'], imagesFolderName: $imagesFolderName);
                }
                $fileValue = $request->file("design_image");
                $fileName = FilesFunctions::saveFile($fileValue, imagesFolderName: $imagesFolderName);
                $design['design_image'] = $fileName;
            }

            // Handle element_content file upload
            if ($request->hasFile("{$parentKey}element_content")) {
                //delete the image in old record if exists
                if (!empty($resource['element_content'])) {
                    FilesFunctions::deleteFile($resource['element_content'], imagesFolderName: $imagesInsideDesignFolderName);
                }
                $fileValue = $request->file("{$parentKey}element_content");
                $fileName = FilesFunctions::saveFile($fileValue, imagesFolderName: $imagesInsideDesignFolderName);
                $design['element_content'] = $fileName;
            }

            if (!$resource) {
                $new = Design::create($design);
                if (!$new) {
                    $controller = false;
                    return;
                }
                $resource = $new;
            } else {

                // Update existing design
                $updated = $resource->update($design);
                if (!$updated) {
                    $controller = false;
                    return;
                }
            }

            // Handle styles
            if (!empty($design['styles'])) {
                $resource->styles()->delete();
                foreach ($design['styles'] as $style) {
                    $style['design_id'] = $resource->id;
                    $newStyle = LinkedStyleProp::create($style);
                    if (!$newStyle) {
                        $controller = false;
                        return;
                    }
                }
            }

            if (empty($design['children'])) {
                // Delete all child records where parent_id is the current design id
                $resource->children()->forceDelete();
            }else  {
                // Get existing children IDs
                $existingChildrenIds = $resource->children()->pluck('id')->toArray();
                $updatedChildrenIds = collect($design['children'])->pluck('id')->toArray();
                $childrenToDelete = array_diff($existingChildrenIds, $updatedChildrenIds);

                // Delete removed children
                if (!empty($childrenToDelete)) {
                    Design::whereIn('id', $childrenToDelete)->forceDelete();
                }

                // Update or create children
                foreach ($design['children'] as $index => &$child) {
                    self::handleUpdateDesigns($child, $request, $imagesFolderName, $imagesInsideDesignFolderName, "{$parentKey}children.{$index}.", $controller);
                    if (!$controller) {
                        DB::rollBack();
                        return;
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public static function handleSaveDesigns(
        &$design,
        Request $request,
        string $imagesFolderName,
        string $imagesInsideDesignFolderName,
        $parentKey = '',
        &$controller = true // Pass controller as a reference
    ): void {
        DB::beginTransaction();
        try {
            // Handle design_image file upload
            if ($request->hasFile("design_image")) {
                if (!empty($design['design_image'])) {
                    FilesFunctions::deleteFile($design['design_image'], imagesFolderName: $imagesFolderName);
                }
                $fileValue = $request->file("design_image");
                $fileName = FilesFunctions::saveFile($fileValue, imagesFolderName: $imagesFolderName);
                $design['design_image'] = $fileName;
            }




            // Handle element_content file upload
            if ($request->hasFile("{$parentKey}element_content")) {

                $fileValue = $request->file("{$parentKey}element_content");
                $fileName = FilesFunctions::saveFile($fileValue, imagesFolderName: $imagesInsideDesignFolderName);
                $design['element_content'] = $fileName;
            }

            $new = Design::create($design);
            if (!$new) {
                $controller = false;
                return;
            }

            // Handle styles
            if (!empty($design['styles'])) {
                foreach ($design['styles'] as $style) {
                    $style['design_id'] = $new->id;
                    $newStyle = LinkedStyleProp::create($style);
                    if (!$newStyle) {
                        $controller = false;
                        return;
                    }
                }
            }



            // Handle children
            if (!empty($design['children'])) {

                // Update or create children
                foreach ($design['children'] as $index => &$child) {
                    self::handleSaveDesigns($child, $request, $imagesFolderName, $imagesInsideDesignFolderName, "{$parentKey}children.{$index}.", $controller);
                    if (!$controller) {
                        DB::rollBack();
                        return;
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

}

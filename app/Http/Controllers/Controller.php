<?php

namespace App\Http\Controllers;

use App\Helpers\DataProcess;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function indexTemplate($title, Request $request, string $modelClass, $with = [], $perPage = 5): JsonResponse
    {
        // Resolve the model class
        $model = new $modelClass;

        // Get the page number from the request query parameters
        $pageNumber = $request->query('pageNumber', 1);

        // Get the per page from the request query parameters
        $perPageQuery = $request->query('perPage', $perPage);

        // Get the filters from the request
        $filters = $request->query("filters", []);
//        Log::info("filters: ".json_encode($filters));

        // Get the sorts from the request
        $sorts = $request->query("sorts", []);

        // Get the search query
        $searchQuery = $request->query("searchQuery", null);

        // Initialize query builder
        $query = $model::query();

        // Apply filters if they exist
        if (!empty($filters)) {
            $query = DataProcess::filterData($query, $filters);
        }

        // Apply sorts if they exist
        if (!empty($sorts)) {
            $query = DataProcess::sortData($query, $sorts);
        }

        if($searchQuery) {
            $query = DataProcess::searchData($query, $searchQuery);
        }

        // Get the page data with applied filters and pagination
        $pageData = $query->with($with)
            ->skip(($pageNumber - 1) * $perPageQuery)
            ->take($perPageQuery)
            ->get();

//        Log::info("filteredData: " . json_encode($pageData));

        return DefaultResponseBodies::getSuccessResponse(data: $pageData, message: "Get All $title Successfully");
    }


    public function fetchDeletedTemplate($title, Request $request, string $modelClass, $with = [], $perPage = 10): JsonResponse
    {
        // Resolve the model class
        $model = new $modelClass;

        // Get the page number from the request query parameters
        $pageNumber = $request->query('pageNumber', 1);

        // Get the per page from the request query parameters
        $perPageQuery = $request->query('perPage', $perPage);

        // Calculate the offset to skip rows based on the page number
        $offset = ($pageNumber - 1) * $perPageQuery;

        // Retrieve the ElementType data with pagination
        $pageData = $model::onlyTrashed()
            ->with($with)
            ->skip($offset)
            ->take($perPageQuery)
            ->get();

        return DefaultResponseBodies::getSuccessResponse(data: $pageData, message: "Get All deleted $title Successfully");
    }

    public function destroyTemplate($ids, string  $modelClass, $title): JsonResponse
    {
        try {
            // Convert comma-separated IDs to an array
            $idArray = explode(',', $ids);

            // Resolve the model class
            $model = new $modelClass;

            // Delete the selected element props
            $deletedCount = $model::whereIn('id', $idArray)->delete();

            if ($deletedCount > 0) {
                return DefaultResponseBodies::getSuccessResponse(message: $title." deleted successfully");
            } else {
                return DefaultResponseBodies::getNotFoundResponse(message: $title." not found");
            }
        } catch (ModelNotFoundException $e) {
            // Related model not found
            return DefaultResponseBodies::getNotFoundResponse(error: $e->getMessage(), message: "Related model not found");

        } catch (QueryException $e) {
            // Database query error
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unable to delete the record due to linked records.");

        } catch (ValidationException $e) {
            // Validation error
            return DefaultResponseBodies::getValidationErrorResponse(
                error: $e->getMessage(),
                message: "Validation Error",
                errors: $e->validator->errors()
            );

        } catch (\Exception $e) {
            // Other unexpected errors
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unexpected error");
        }
    }

    public function restoreTemplate($ids, string  $modelClass, $title): JsonResponse
    {
        try {
            // Convert comma-separated IDs to an array
            $idArray = explode(',', $ids);

            // Resolve the model class
            $model = new $modelClass;

            // Delete the selected element props
            $deletedCount = $model::whereIn('id', $idArray)->restore();

            if ($deletedCount > 0) {
                return DefaultResponseBodies::getSuccessResponse(message: $title." deleted successfully");
            } else {
                return DefaultResponseBodies::getNotFoundResponse(message: $title." not found");
            }
        }catch (ModelNotFoundException $e) {
            // Related model not found
            return DefaultResponseBodies::getNotFoundResponse(error: $e->getMessage(), message: "Related model not found");

        } catch (QueryException $e) {
            // Database query error
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unable to delete the record due to linked records.");

        } catch (ValidationException $e) {
            // Validation error
            return DefaultResponseBodies::getValidationErrorResponse(
                error: $e->getMessage(),
                message: "Validation Error",
                errors: $e->validator->errors()
            );

        } catch (\Exception $e) {
            // Other unexpected errors
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unexpected error");
        }
    }

    public function permanentDeleteTemplate(
        $ids,
        string  $modelClass,
        $title,
        array $fileColumnNames = [],
        string $filesFolderName = null,
        string $imagesFolderName = null,
    ): JsonResponse
    {
        try {
            // Convert comma-separated IDs to an array
            $idArray = explode(',', $ids);

            // Resolve the model class
            $model = new $modelClass;

            if(!empty($fileColumnNames)) {
                // Get the records to be deleted
                $recordsToDelete = $model::whereIn('id', $idArray)->get();

                // Loop through the records to delete
                foreach ($fileColumnNames as $fileColumnName) {
                    foreach ($recordsToDelete as $record) {
                        $fileName = $record[$fileColumnName];
                        $this->deleteFile($fileName, $imagesFolderName, $filesFolderName);
                    }
                }
            }

            // Delete the selected element props
            $deletedCount = $model::whereIn('id', $idArray)->forceDelete();

            if ($deletedCount > 0) {
                return DefaultResponseBodies::getSuccessResponse(message: $title." deleted successfully");
            } else {
                return DefaultResponseBodies::getNotFoundResponse(message: $title." not found");
            }
        } catch (ModelNotFoundException $e) {
            // Related model not found
            return DefaultResponseBodies::getNotFoundResponse(error: $e->getMessage(), message: "Related model not found");

        } catch (QueryException $e) {
            // Database query error
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unable to delete the record due to linked records.");

        } catch (ValidationException $e) {
            // Validation error
            return DefaultResponseBodies::getValidationErrorResponse(
                error: $e->getMessage(),
                message: "Validation Error",
                errors: $e->validator->errors()
            );

        } catch (\Exception $e) {
            // Other unexpected errors
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unexpected error");
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param string $title
     * @param Request $request
     * @param string $modelClass
     * @param array $validationRules
     * @param array $relationships
     * @param array $files
     * @param string|null $filesFolderName
     * @param string|null $imagesFolderName
     * @return JsonResponse
     */
    public function storeTemplate(
        string $title,
        Request $request,
        string $modelClass,
        array $validationRules = [],
        array $relationships = [],
        array $files = [],
        string $filesFolderName = null,
        string $imagesFolderName = null,
    ): JsonResponse
    {
        try {
            // Resolve the model class
            $model = new $modelClass;

            // Validate request data
            $validatedData = $request->validate($validationRules);

            if(!empty($files)) {
                foreach ($files as $file) {
                    if($request->hasFile($file)){
                        $fileValue = $request->file($file, null);
                        $fileName = $this->saveFile(
                            file: $fileValue,
                            filesFolderName: $filesFolderName,
                            imagesFolderName: $imagesFolderName,
                        );
                        // Add fileName to validated data
                        $validatedData[$file] = $fileName;
                    }
                }
            }

            // Create the resource
            $createdResource = $model::create($validatedData);

            // Handle relationships
            foreach ($relationships as $relationName) {
                $relatedIds = $request->input($relationName, null);
                if (!empty($relatedIds) && $request->filled($relationName)) {
                    Log::info("relatedIds: ". json_encode($relatedIds));
                    // Resolve the relation
                    $relation = $createdResource->{$relationName}();

                    // Handle different types of relationships
                    if ($relation instanceof BelongsToMany) {
                        // Many-to-Many Relationship
                        $relation->sync($relatedIds);
                    } elseif ($relation instanceof HasMany || $relation instanceof MorphMany) {
                        //TODO: I don't think I will use this
                        // One-to-Many Relationship
                        $relatedModelClass = $relation->getRelated();

                        foreach ($relatedIds as $relatedId) {
                            $relatedModelClass::create([
                                $relation->getForeignKeyName() => $createdResource->getKey(),
                                // Other attributes as needed
                            ]);
                        }
                    }
//                    } elseif ($relation instanceof BelongsTo) {
//                        // Many-to-One Relationship
//                        $relatedModelClass = $relation->getRelated();
//                        //*
//                        $relatedModel = $relatedModelClass::findOrFail($relatedIds); //in belong to you have to send array with one element id
//                        Log::info("relatedModel: ". json_encode($relatedModel));
//
//                        $createdResource->{$relation->getForeignKeyName()} = $relatedModel->getKey();
//                        // I can't get the id from createdResource
//
//                        $createdResource->save();
//                    }
                }
            }
            return DefaultResponseBodies::getSuccessResponse(data: $createdResource, message: $title." created successfully");
        }  catch (ModelNotFoundException $e) {
            // Related model not found
            return DefaultResponseBodies::getNotFoundResponse(error: $e->getMessage(), message: "Related model not found");
        } catch (QueryException $e) {

            // Database query error
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Database query error");
        } catch (ValidationException $e) {
            return DefaultResponseBodies::getValidationErrorResponse(
                error: $e->getMessage(),
                message: "Validation Error",
                errors: $e->validator->errors()
            );

        } catch (\Exception $e) {
            // Other unexpected errors
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unexpected error");
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param string $title
     * @param Request $request
     * @param Model $resource
     * @param array $validationRules
     * @param array $relationships
     * @param array $files
     * @param string|null $filesFolderName
     * @param string|null $imagesFolderName
     * @return JsonResponse
     */
public function updateTemplate(
    string  $title,
    Request $request,
    Model   $resource,
    array   $validationRules = [],
    array   $relationships = [],
    array $files = [],
    string $filesFolderName = null,
    string $imagesFolderName = null,
): JsonResponse
{
    try {
        // Validate request data
        $validatedData = $request->validate($validationRules);
        Log::info('Validated data: ' . json_encode($validatedData));

        // Update the resource
        $resource->update($validatedData);
        Log::info('Updated resource: ' . json_encode($resource));

        // Handle relationships
        //TODO: in the last, function don't enter the relations condition, another thin in many-to-one in front-end, when change data the row become undefined.
        foreach ($relationships as $relationName) {
                $relatedObjects = $request->input($relationName, null);

                // Resolve the relation
                $relation = $resource->{$relationName}();

                // Handle different types of relationships
                if ($relation instanceof BelongsToMany) {
                    $Ids = collect($relatedObjects)->pluck('id')->toArray();
                    Log::info('in many to many:'. json_encode($Ids));
                    // Many-to-Many Relationship
                    $relation->sync($Ids);
                } elseif ($relation instanceof HasMany || $relation instanceof MorphMany) {
                    //TODO: won't use this.
                    // Log::info('in One to many:'. json_encode($relatedObjects));
                    // // One-to-Many Relationship
                    // $relatedModelClass = $relation->getRelated();

                    // // Get the foreign key name
                    // $foreignKeyName = $relation->getForeignKeyName();

                    // // Get the IDs of related objects from the request
                    // $relatedIds = collect($relatedObjects)->pluck('id')->toArray();

                    // // Get the IDs of existing related objects
                    // $existingRelatedIds = $resource->{$relationName}->pluck('id')->toArray();

                    // // Determine which related objects to update, which to remove, and which to add
                    // $objectsToUpdate = array_intersect($relatedIds, $existingRelatedIds);
                    // $objectsToRemove = array_diff($existingRelatedIds, $relatedIds);
                    // $objectsToAdd = array_diff($relatedIds, $existingRelatedIds);

                    // // Update related objects
                    // foreach ($objectsToUpdate as $objectId) {
                    //     $relatedModel = $relatedModelClass::findOrFail($objectId);
                    //     // Update attributes as needed
                    //     $relatedModel->update([
                    //         $foreignKeyName => $resource->id
                    //     ]);
                    // }

                    // // Remove related objects
                    // $relatedModelClass::whereIn('id', $objectsToRemove)->delete();

                    // Add related objects (if necessary)
                    // foreach ($objectsToAdd as $objectId) {
                    //     // Create new related objects if needed
                    //     $relatedModelClass::create([
                    //         $foreignKeyName => $resource->id,
                    //         // Set other attributes as needed
                    //     ]);
                    // }

                } elseif ($relation instanceof BelongsTo) {
                    if($relatedObjects) {
                        Log::info('in Many to One:'. json_encode($relatedObjects));
                        // Many-to-One Relationship
                        $relatedModelClass = $relation->getRelated();

                        // Update the related model
                        $relatedModel = $relatedModelClass::findOrFail($relatedObjects['id']); // In BelongsTo, only one related model is expected
                        $resource->{$relation->getForeignKeyName()} = $relatedModel->getKey();
                    } else {
                        // Set the parent_id foreign key to null
                        $resource->{$relation->getForeignKeyName()} = null;
                    }
                    $resource->save();
                }
        }

        //check if there is files sent with the request
        foreach ($files as $fileFieldName) {
            if($request->hasFile($fileFieldName)) {
                $fileValue = $request->file($fileFieldName, null);

                $oldFileName = $resource[$fileFieldName];
                $this->deleteFile($oldFileName, $imagesFolderName, $filesFolderName);

                $newFileName = $this->saveFile(
                    file: $fileValue,
                    filesFolderName: $filesFolderName,
                    imagesFolderName: $imagesFolderName,
                );
                $resource[$fileFieldName] = $newFileName;
                $resource->save();
            }
        }

        return DefaultResponseBodies::getSuccessResponse(data: $resource, message: $title." updated successfully");
    } catch (ModelNotFoundException $e) {
        // Related model not found
        return DefaultResponseBodies::getNotFoundResponse(error: $e->getMessage(), message: "Related model not found");
    } catch (QueryException $e) {
        // Database query error
        return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Database query error");
    } catch (\Exception $e) {
        // Other unexpected errors
        return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unexpected error");
    }
}

    public function saveFile($file, $filesFolderName = null, $imagesFolderName = null) : string {
        $imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp']; // Add more if needed
        $storagePath = "public/assets";

        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $mimeType = $file->getMimeType();

        if($mimeType === 'application/zip') {
            $storagePath .= "/files/".$filesFolderName;
        }

        if(in_array($mimeType, $imageMimeTypes)) {
            $storagePath .= "/images/".$imagesFolderName;
        }
        //TODO: adding checking malicious files if there in zip file
        $file->storeAs($storagePath, $fileName);
        return $fileName;
    }

    private function deleteFile(string $fileName, string $imagesFolderName = null, string $filesFolderName = null): void
    {
        // Check if the record has an associated image
        if ($fileName != null) {
            // Assuming $fileName contains only the name of the file
            $imagePath = "public/assets/images/$imagesFolderName/$fileName";
            $filePath = "public/assets/files/$filesFolderName/$fileName";
            if (Storage::exists($imagePath)) {
                // Delete the associated image
                Storage::delete($imagePath);
            }

            if (Storage::exists($filePath)) {
                // Delete the associated file
                Storage::delete($filePath);
            }

        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Helpers\DataProcess;
use App\Helpers\StringsHelper;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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


        // Get the sorts from the request
        $sorts = $request->query("sorts", []);

        // Get the search query
        $searchQuery = $request->query("searchQuery", null);

        // Initialize query builder
        $query = $model::query();

        Log::info("filters: ", $filters);
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
                        if($fileName) {
                            $this->deleteFile($fileName, $imagesFolderName, $filesFolderName);
                        }
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
    ): JsonResponse {
        try {
            // Resolve the model class
            $model = new $modelClass;

            // Validate request data
            $validatedData = Validator::make($request->all(), $validationRules)->validate();
            Log::info("validated data: ", $validatedData);
            // Handle file uploads
            foreach ($files as $file) {
                if ($request->hasFile($file)) {
                    $fileValue = $request->file($file);
                    $fileName = $this->saveFile(
                        file: $fileValue,
                        filesFolderName: $filesFolderName,
                        imagesFolderName: $imagesFolderName,
                    );
                    $validatedData[$file] = $fileName;
                }
            }

            // Create the resource
            //!!! The create method create the fields that are exist in the filled in the model
            $createdResource = $model::create($validatedData);

            // Handle relationships
            foreach ($relationships as $relationName) {
                $relatedIds = $request->input(StringsHelper::camelToSnake($relationName));
                if (!empty($relatedIds)) {
                    $relation = $createdResource->{$relationName}();
                    if ($relation instanceof BelongsToMany) {
                        $relation->sync($relatedIds);
                    } elseif ($relation instanceof BelongsTo) {
                        $relatedModel = $relation->getRelated()->findOrFail($relatedIds[0]);
                        $createdResource->{$relation->getForeignKeyName()} = $relatedModel->getKey();
                        $createdResource->save();
                    }
                }
            }
            return DefaultResponseBodies::getSuccessResponse(data: $createdResource, message: $title . " created successfully");
        } catch (ModelNotFoundException $e) {
            return DefaultResponseBodies::getNotFoundResponse(error: $e->getMessage(), message: "Related model not found");
        } catch (QueryException $e) {
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Database query error");
        } catch (ValidationException $e) {
            return DefaultResponseBodies::getValidationErrorResponse(
                error: $e->getMessage(),
                message: "Validation Error",
                errors: $e->validator->errors()
            );
        } catch (MassAssignmentException $e) {
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Fillable Error");
        } catch (\Exception $e) {
            return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unexpected error");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param string $title
     * @param Request $request
     * @param string $resourceId
     * @param string $modelClass
     * @param array $validationRules
     * @param array $relationships
     * @param array $files
     * @param string|null $filesFolderName
     * @param string|null $imagesFolderName
     * @return JsonResponse
     */
    public function updateTemplate(
        string $title,
        Request $request,
        string $resourceId,
        string $modelClass,
        array $validationRules = [],
        array $relationships = [],
        array $files = [],
        string $filesFolderName = null,
        string $imagesFolderName = null,
    ): JsonResponse {
        try {
            // Resolve the model class
            $model = new $modelClass;

            //get the resource
            $resource = $model::findOrFail($resourceId);

            // Validate request data
            $validatedData = $request->validate($validationRules);

            // Process file uploads
            foreach ($files as $fileFieldName) {
                if ($request->hasFile($fileFieldName)) {
                    $fileValue = $request->file($fileFieldName);
                    $oldFileName = $resource->{$fileFieldName};

                    if ($oldFileName) {
                        $this->deleteFile($oldFileName, $imagesFolderName, $filesFolderName);
                    }

                    $newFileName = $this->saveFile(
                        file: $fileValue,
                        filesFolderName: $filesFolderName,
                        imagesFolderName: $imagesFolderName,
                    );
                    $validatedData[$fileFieldName] = $newFileName;
                }
            }

            // Handle relationships
            foreach ($relationships as $relationName) {
                $relatedObjects = $request->input(StringsHelper::camelToSnake($relationName), null);
                $relation = $resource->{$relationName}();

                if ($relation instanceof BelongsToMany) {
                    // Many-to-Many Relationship
                    $Ids = collect($relatedObjects)->pluck('id')->toArray();
                    $relation->sync($Ids);
                } elseif ($relation instanceof BelongsTo) {
                    // Many-to-One Relationship
                    if ($relatedObjects) {
                        $relatedModelClass = $relation->getRelated();
                        $relatedModel = $relatedModelClass::findOrFail($relatedObjects['id']);
                        $resource->{$relation->getForeignKeyName()} = $relatedModel->getKey();
                    } else {
                        // Set the foreign key to null
                        $resource->{$relation->getForeignKeyName()} = null;
                    }
                }
            }

            // Update the resource
            $resource->update($validatedData);
//            $resource->save();
            return DefaultResponseBodies::getSuccessResponse(data: $resource, message: $title . " updated successfully");
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

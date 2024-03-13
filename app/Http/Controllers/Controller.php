<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use Dotenv\Exception\ValidationException;
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

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function destroyTemplate($ids, string  $modelClass, $title) {
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
    }

    public function restoreTemplate($ids, string  $modelClass, $title) {
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
    }

    public function permanentDeleteTemplate($ids, string  $modelClass, $title) {
        // Convert comma-separated IDs to an array
        $idArray = explode(',', $ids);

        // Resolve the model class
        $model = new $modelClass;

        // Delete the selected element props
        $deletedCount = $model::whereIn('id', $idArray)->forceDelete();

        if ($deletedCount > 0) {
            return DefaultResponseBodies::getSuccessResponse(message: $title." deleted successfully");
        } else {
            return DefaultResponseBodies::getNotFoundResponse(message: $title." not found");
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  string $title
     * @param  \Illuminate\Http\Request  $request
     * @param  BaseModel  $model
     * @param  array  $validationRules
     * @param  array  $relationships
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeTemplate($title, Request $request, string $modelClass, array $validationRules = [], array $relationships = [])
    {
        try {
            // Resolve the model class
            $model = new $modelClass;

            // Validate request data
            $validatedData = $request->validate($validationRules);

            // Create the resource
            $createdResource = $model::create($validatedData);

            // Handle relationships
            foreach ($relationships as $relationName => $relatedIds) {
                if (!empty($relatedIds) && $request->filled($relationName)) {

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
                    } elseif ($relation instanceof BelongsTo) {
                        // Many-to-One Relationship
                        $relatedModelClass = $relation->getRelated();
                        //*
                        $relatedModel = $relatedModelClass::findOrFail($relatedIds[0]); //in belong to you have to send array with one element id

                        $createdResource->{$relation->getForeignKeyName()} = $relatedModel->getKey();
                        // I can't get the id from createdResource

                        $createdResource->save();
                    }
                }
            }
            return DefaultResponseBodies::getSuccessResponse(data: $createdResource, message: $title." created successfully");
        }  catch (ModelNotFoundException $e) {
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


    /**
 * Update the specified resource in storage.
 *
 * @param  string $title
 * @param  \Illuminate\Http\Request  $request
 * @param  \Illuminate\Database\Eloquent\Model  $resource
 * @param  array  $validationRules
 * @param  array  $relationships
 * @return \Illuminate\Http\JsonResponse
 */
public function updateTemplate($title, Request $request, $resource, array $validationRules = [], array $relationships = [])
{
    try {
        // Validate request data
        $validatedData = $request->validate($validationRules);
        Log::info('Validated data:', $validatedData);

        // Update the resource
        $resource->update($validatedData);
        Log::info('Updated resource:'. $resource);


        // Handle relationships
        //TODO: in the last, function don't enter the relations condition, another thin in many-to-one in front-end, when change data the row become undefined.
        foreach ($relationships as $relationName => $relatedIds) {
            if (!empty($relatedIds) && $request->filled($relationName)) {
                Log::info('in general:'. $relatedIds);


                // Resolve the relation
                $relation = $resource->{$relationName}();

                // Handle different types of relationships
                if ($relation instanceof BelongsToMany) {
                    Log::info('in many to many:'. $relatedIds);
                    // Many-to-Many Relationship
                    $relation->sync($relatedIds);
                } elseif ($relation instanceof HasMany || $relation instanceof MorphMany) {
                    Log::info('in One to many:'. $relatedIds);
                    // One-to-Many Relationship
                    $relatedModelClass = $relation->getRelated();

                    // Update or create related models
                    foreach ($relatedIds as $relatedId) {
                        $relatedModelClass::updateOrCreate(
                            [$relation->getForeignKeyName() => $resource->getKey()],
                            // Other attributes as needed
                            []
                        );
                    }
                } elseif ($relation instanceof BelongsTo) {
                    Log::info('in Many to One:'. $relatedIds);
                    // Many-to-One Relationship
                    $relatedModelClass = $relation->getRelated();

                    // Update the related model
                    $relatedModel = $relatedModelClass::findOrFail($relatedIds[0]); // In BelongsTo, only one related model is expected
                    $resource->{$relation->getForeignKeyName()} = $relatedModel->getKey();
                    $resource->save();
                }
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
}

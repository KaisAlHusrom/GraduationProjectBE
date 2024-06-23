<?php

namespace App\Http\Controllers;

use App\Helpers\FilesFunctions;
use App\Models\Product;
use App\Models\ProductsCategory;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
            private const TITLE = "Products";
            private const MODEL = Product::class;
            private const FILES_FIELD_NAMES = ["product_main_image_name", "product_file_name"];
            private const FETCH_WITH = [
                'user',
                'productMedia',
                'productUsedSkills',
                'productFeatures',
                'productReviews',
                'categories',
                'messages',
            ];
            private const IMAGES_FOLDER_NAME = "ProductsImages";
            private const FILES_FOLDER_NAME = "ProductsFiles";

            /**
             * Display a listing of the resource.
             */
            public function index(Request $request): JsonResponse
            {
                return $this->indexTemplate(
                    title: $this::TITLE,
                    request: $request,
                    modelClass: $this::MODEL,
                    with: $this::FETCH_WITH,
                    perPage: 10,
                );
            }

            /**
             * Display a specific record.
             */
            public function fetchSpecificRecord($id): JsonResponse
            {

                return $this->fetchSpecificRecordTemplate(
                    title: $this::TITLE,
                    id: $id,
                    modelClass: $this::MODEL,
                    with: $this::FETCH_WITH
                );
            }

            /**
             * Display a listing of the deleted resource.
             */
            public function fetchDeleted(Request $request): JsonResponse
            {
                return $this->fetchDeletedTemplate(
                    title: $this::TITLE,
                    request: $request,
                    modelClass: $this::MODEL,
                    with: $this::FETCH_WITH,
                    perPage: 10,
                );
            }

            /**
             * Store a newly created resource in storage.
             */
            public function store(Request $request): JsonResponse
            {
                $validationRules = [
                    'product_name'=>"required",
                    'user_id'=>"required",
                    'product_short_description'=>"required",
                    'product_long_description'=>"required",
                    'product_price'=>"required",
                    'product_main_image_name'=>"required",
                    'product_file_name'=>"required",
                ];

                //relations
                $relationships = [
                    'categories'
                ];

                // store the prop
                return $this->storeTemplate(
                    title: $this::TITLE,
                    request: $request,
                    modelClass: $this::MODEL,
                    validationRules: $validationRules,
                    relationships: $relationships,
                    files: $this::FILES_FIELD_NAMES,
                    filesFolderName: $this::FILES_FOLDER_NAME,
                    imagesFolderName: $this::IMAGES_FOLDER_NAME,
                );
            }

            /***
             *  Store for normal users
             */
            public function saveUser(Request $request): JsonResponse{
                try {
                    $validationRules = [
                        'product_name'=>"required",
                        'user_id'=>"required",
                        'product_short_description'=>"required",
                        'product_long_description'=>"required",
                        'product_price'=>"required",
                        'product_main_image_name'=>"required",
                        'product_file_name'=>"required",
                        'product_features'=>"required|array",
                        'product_used_skills'=>"required|array",
                        'product_media'=>"",
                        'categories' =>"required|array",
                    ];

                    $validatedData = Validator::make($request->all(), $validationRules)->validate();

                    //Save File
                    $fileValue = $request->file('product_file_name');
                    $productFileName = FilesFunctions::saveFile(
                        file: $fileValue,
                        filesFolderName: $this::FILES_FOLDER_NAME,
                    );
                    $validatedData['product_file_name'] = $productFileName;

                    //Save Image
                    $mainImageValue = $request->file('product_main_image_name');
                    $mainImageName = FilesFunctions::saveFile(
                        file: $mainImageValue,
                        imagesFolderName: $this::IMAGES_FOLDER_NAME,
                    );
                    $validatedData['product_main_image_name'] = $mainImageName;

                    //Add new product
                    $product = Product::create($validatedData);

                    //save product categories
                    if (!empty($request->categories)) {
                        foreach ($request->categories as $cat) {
                            $newCategory = [
                                'product_id' => $product->id,
                                'category_id' => $cat['id'], // Access 'id' directly from array
                            ];

                            ProductsCategory::create($newCategory);
                        }
                    }

                    //save product features
                    if (!empty($request->product_features)) {
                        foreach ($request->product_features as $feature) {

                            $product->productFeatures()->create($feature);
                        }
                    }

                    //save product used skills
                    if (!empty($request->product_used_skills)) {
                        foreach ($request->product_used_skills as $skill) {

                            $product->productUsedSkills()->create($skill);
                        }
                    }

                    //save product media
                    if (!empty($request->product_media)) {
                        Log::info("inside not empty");
                        foreach ($request->product_media as $index => $media) {
                            if ($request->hasFile('product_media.' . $index . '.product_media_name')) {
                                Log::info("inside download");

                                $fileValue = $request->file('product_media.' . $index . '.product_media_name');
                                $fileName = FilesFunctions::saveFile(
                                    file: $fileValue,
                                    imagesFolderName: 'ProductsMedia'
                                );
                                $newMedia = [
                                    'product_media_name' => $fileName,
                                    'is_video' => $media['is_video'] ? 1 : 0
                                ];
                                $product->productMedia()->create($newMedia);
                            }
                        }
                    }

                    return DefaultResponseBodies::getSuccessResponse(data: $product, message: "Product created successfully");
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
             */
            public function update(Request $request, $id): JsonResponse
            {
                $validationRules = [
                    'product_name'=>"required",
                    'product_short_description'=>"required",
                    'product_long_description'=>"required",
                    'product_price'=>"required",
                    'product_main_image_name'=>"required",
                    'product_file_name'=>"required",
                ];

                //relations
                $relationships = [
                    'categories',
                    'user'
                ];


                // Call storeTemplate function to store the post and its related models
                return $this->updateTemplate(
                    title: $this::TITLE,
                    request: $request,
                    resourceId: $id,
                    modelClass: $this::MODEL,
                    validationRules: $validationRules,
                    relationships: $relationships,
                    files: $this::FILES_FIELD_NAMES,
                    filesFolderName: $this::FILES_FOLDER_NAME,
                    imagesFolderName: $this::IMAGES_FOLDER_NAME,
                );
            }


            /**
             * Remove the specified resources from storage.
             */
            public function destroy($ids): JsonResponse
            {
                return $this->destroyTemplate(
                    ids: $ids,
                    modelClass: $this::MODEL,
                    title: $this::TITLE,
                );
            }


            /**
             * Restore the deleted specified resource from storage.
             */
            public function restore($ids): JsonResponse
            {
                return $this->restoreTemplate(
                    ids: $ids,
                    modelClass: $this::MODEL,
                    title: $this::TITLE,
                );
            }

            /**
             * Permanently delete the specified resources.
             */
            public function permanentDelete($ids): JsonResponse
            {

                return $this->permanentDeleteTemplate(
                    ids: $ids,
                    modelClass: $this::MODEL,
                    title: $this::TITLE,
                    fileColumnNames: $this::FILES_FIELD_NAMES,
                    filesFolderName: $this::FILES_FOLDER_NAME,
                    imagesFolderName: $this::IMAGES_FOLDER_NAME,
                );
            }


}

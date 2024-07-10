<?php

namespace App\Http\Controllers;

use App\Helpers\RecursiveFunctions;
use App\Models\Design;
use App\Models\LinkedStyleProp;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DesignController extends Controller
{
            private const TITLE = "Designs";
            private const MODEL = Design::class;
            private const FILES_FIELD_NAMES = ['design_image'];
            private const FETCH_WITH = [
                'styles.styleProp',
                'styles.styleResponsiveBreakpoint',
                'styles.styleStatus',
                'elementType',
                'styles',
                'parent',
                'page',
                'webProject',
                'category',
                'designPropValues.elementProp',
            ];
            private const IMAGES_FOLDER_NAME = "DesignsImages";

            private const IMAGES_INSIDE_DESIGNS_FOLDER_NAME = "ImagesInsideDesigns";
            private const FILES_FOLDER_NAME = null;

            //to upload files inside



    /**
             * Display a listing of the resource.
             */
            public function index(Request $request): JsonResponse
            {
                return $this->indexTemplate(
                    title: $this::TITLE,
                    request: $request,
                    modelClass: $this::MODEL,
                    with: [...$this::FETCH_WITH, 'children.children.children.children.children.children.children'],
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
                    with: [...$this::FETCH_WITH, 'children.children.children.children.children.children.children.children'],
                );
            }

            private function loadRecursive($model, $depth)
            {
                if ($depth > 0) {
                    $model->load([
                        'children' => function ($query) use ($depth) {
                            $query->with([
                                'styles.styleProp',
                                'styles.styleResponsiveBreakpoint',
                                'styles.styleStatus',
                                'elementType',
                                'styles',
                                'parent',
                                'pages',
                                'children' => function ($query) use ($depth) {
                                    $this->loadRecursive($query, $depth - 1);
                                },
                                'designPropValues'
                            ]);
                        }
                    ]);
                }
            }

            /**
             * check a specific record.
             */
            public function checkSpecificRecord($id): JsonResponse
            {
                return $this->checkSpecificRecordTemplate(
                    title: $this::TITLE,
                    id: $id,
                    modelClass: $this::MODEL,
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
                try {
                    $validationRules = [
                        'id' => "required",
                        'element_type_id'=> "required",
                        'parent_id'=> "nullable",
                        'category_id'=>"required",
                        'design_title' => "",
                        'design_description' => "",
                        'design_image' => "",
                        'design_type' => "",
                        'is_template' => "",
                        'is_child' => "",
                        'sequence_number' => "",
                        'element_content' => "",
                        'children' => "array",
                        'styles' => "array",
                        'page_id' => "",
                        'design_prop_values'=>"",
                        'web_project_id' =>"",
                        'position'=>"",
                    ];


                    $validatedData = $request->validate($validationRules);

                    // Start the save process
                    $controller = true;
                    RecursiveFunctions::handleSaveDesigns(
                        design : $validatedData,
                        request : $request,
                        imagesFolderName : $this::IMAGES_FOLDER_NAME,
                        imagesInsideDesignFolderName : $this::IMAGES_INSIDE_DESIGNS_FOLDER_NAME,
                        parentKey : '',
                        controller : $controller

                    );


                    if ($controller) {
                        return DefaultResponseBodies::getSuccessResponse(data: $validatedData, message: "Design saved successfully");
                    } else {
                        return DefaultResponseBodies::getInternalServerErrorResponse(error: "Unexpected Error from save function", message: "Unexpected error from save function");
                    }

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

            public function UpdateSorting (Request $request) {
                try {
                    $designs = $request->input('designs');
                    $validator = Validator::make($request->all(), [
                        'designs' => 'required|array',

                    ]);
                    Log::info("designs: " , $designs);

                    foreach ($designs as $design) {
                        $res = Design::find($design['id']);
                        $res->sequence_number = $design['sequence_number'];
                        $res->save();

                    }
                    return DefaultResponseBodies::getSuccessResponse(data: [], message: $this::TITLE . " updated successfully");

                }

             catch (\Exception $e) {
                return DefaultResponseBodies::getInternalServerErrorResponse(error: $e->getMessage(), message: "Unexpected error");
            }
            }

            /**
             * Update the specified resource in storage.
             */
            public function update(Request $request, $id): JsonResponse
            {
                try {
                    $validationRules = [
                        'id' => "required",
                        'element_type_id'=> "required",
                        'parent_id'=> "nullable",
                        'category_id'=>"required",
                        'design_title' => "",
                        'design_description' => "",
                        'design_image' => "", // Ensure file validation
                        'design_type' => "",
                        'is_template' => "",
                        'is_child' => "",
                        'sequence_number' => "",
                        'element_content' => "", // Ensure file validation
                        'children' => "array",
                        'styles' => "array",
                        'page_id' => "",
                        'web_project_id'=>'',
                        'position'=>'',
                        'design_prop_values' => "",
                    ];

                    $validatedData = $request->validate($validationRules);
                    // Start the update process
                    $controller = true;
                    RecursiveFunctions::handleUpdateDesigns(
                        design : $validatedData,
                        request : $request,
                        imagesFolderName : $this::IMAGES_FOLDER_NAME,
                        imagesInsideDesignFolderName : $this::IMAGES_INSIDE_DESIGNS_FOLDER_NAME,
                        parentKey : '',
                        controller : $controller

                        );

                    if ($controller) {
                        return DefaultResponseBodies::getSuccessResponse(data: $validatedData, message: "Design updated successfully");
                    } else {
                        return DefaultResponseBodies::getInternalServerErrorResponse(error: "Unexpected Error", message: "Unexpected error");
                    }

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

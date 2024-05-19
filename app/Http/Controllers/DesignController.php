<?php

namespace App\Http\Controllers;

use App\Helpers\RecursiveFunctions;
use App\Models\Design;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DesignController extends Controller
{
            private const TITLE = "Designs";
            private const MODEL = Design::class;
            private const FILES_FIELD_NAMES = ['design_image'];
            private const FETCH_WITH = ['styles', 'parent', 'children', 'pages', 'category', 'elementType'];
            private const IMAGES_FOLDER_NAME = "DesignsImages";
            private const FILES_FOLDER_NAME = null;

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
                ];

                //relations
                $relationships = [
                    "children"
                ];

                $validatedData = $request->validate($validationRules);
//                Log::info("validation rules: ", $validatedData);
                //TODO: I remove styles from object, and I want to link the data that will add to designs
                //TODO: with style props and style statuses and style breakpoints using linked_style_props table
                $flattedData = RecursiveFunctions::flattenWithParentId([$validatedData]);

                Log::info("validation rules: ", $flattedData);
//                // store the prop
//                return $this->storeTemplate(
//                    title: $this::TITLE,
//                    request: $request,
//                    modelClass: $this::MODEL,
//                    validationRules: $validationRules,
//                    relationships: $relationships,
//                    files: $this::FILES_FIELD_NAMES,
//                    filesFolderName: $this::FILES_FOLDER_NAME,
//                    imagesFolderName: $this::IMAGES_FOLDER_NAME,
//                );
            }



            /**
             * Update the specified resource in storage.
             */
            public function update(Request $request, $id): JsonResponse
            {
                $validationRules = [
                ];

                //relations
                $relationships = [
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

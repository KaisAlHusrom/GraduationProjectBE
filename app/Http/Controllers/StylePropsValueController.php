<?php

namespace App\Http\Controllers;

use App\Models\StylePropsValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StylePropsValueController extends Controller
{
            private const IMAGES_FOLDER_NAME = "StylePropValuesImages";
            private const FILES_FOLDER_NAME = null;

            /**
             * Display a listing of the resource.
             */
            public function index(Request $request): JsonResponse
            {
                return $this->indexTemplate(
                    title: "Style Prop Values",
                    request: $request,
                    modelClass: StylePropsValue::class,
                    with: ["styleProp"],
                    perPage: 10,
                );
            }

            /**
             * Display a listing of the deleted resource.
             */
            public function fetchDeleted(Request $request): JsonResponse
            {
                return $this->fetchDeletedTemplate(
                    title: "Style Prop Values",
                    request: $request,
                    modelClass: StylePropsValue::class,
                    with: ["styleProp"],
                    perPage: 10,
                );
            }

            /**
             * Store a newly created resource in storage.
             */
            public function store(Request $request): JsonResponse
            {
                $validationRules = [
                    'style_prop_value_normal_name' => "required|unique:style_props_values",
                    'style_prop_value_css_name' => "required|unique:style_props_values",
                    'style_prop_value_image' => "",
                    'style_prop_id' => "",
                ];

                //relations
                $relationships = [
                ];

                //model files
                $files = [
                    'style_prop_value_image'
                ];

                // store the prop
                return $this->storeTemplate(
                    title: "Style Prop Values",
                    request: $request,
                    modelClass: StylePropsValue::class,
                    validationRules: $validationRules,
                    relationships: $relationships,
                    files: $files,
                    filesFolderName: $this::FILES_FOLDER_NAME,
                    imagesFolderName: $this::IMAGES_FOLDER_NAME,
                );
            }



            /**
             * Update the specified resource in storage.
             */
            public function update(Request $request, StylePropsValue $stylePropsValue): JsonResponse
            {
                //TODO: there is an problem with the unique values, when updating field another than style_prop_value_normal_name, it give me that its unique
                $validationRules = [
                    'style_prop_value_normal_name' => "required|unique:style_props_values,style_prop_value_normal_name,".$stylePropsValue['id'],
                    'style_prop_value_css_name' => "required|unique:style_props_values,style_prop_value_css_name,".$stylePropsValue['id'],
                    'style_prop_value_image' => "",
                    'style_prop_id' => "",
                ];

                //relations
                $relationships = [
                ];

                //get files in model
                $files = [
                    'style_prop_value_image'
                ];

                // Call storeTemplate function to store the post and its related models
                return $this->updateTemplate(
                    title: "Style Prop Values",
                    request: $request,
                    resource: $stylePropsValue,
                    validationRules: $validationRules,
                    relationships: $relationships,
                    files: $files,
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
                    modelClass: StylePropsValue::class,
                    title: "Style Prop Values",
                );
            }


            /**
             * Restore the deleted specified resource from storage.
             */
            public function restore($ids): JsonResponse
            {
                return $this->restoreTemplate(
                    ids: $ids,
                    modelClass: StylePropsValue::class,
                    title: "Style Prop Values",
                );
            }

            /**
             * Permanently delete the specified resources.
             */
            public function permanentDelete($ids): JsonResponse
            {
                $files = ['style_prop_value_image'];
                return $this->permanentDeleteTemplate(
                    ids: $ids,
                    modelClass: StylePropsValue::class,
                    title: "Style Prop Values",
                    fileColumnNames: $files,
                    filesFolderName: $this::FILES_FOLDER_NAME,
                    imagesFolderName: $this::IMAGES_FOLDER_NAME,
                );
            }


}

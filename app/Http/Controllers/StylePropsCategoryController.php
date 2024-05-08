<?php
namespace App\Http\Controllers;

use App\Models\StylePropsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StylePropsCategoryController extends Controller
{
        private const FILES_FIELD_NAMES = ["category_image"];
        private const IMAGES_FOLDER_NAME = "StylePropsCategoryImages";
        private const FILES_FOLDER_NAME = null;
        /**
         * Display a listing of the resource.
         */
        public function index(Request $request): JsonResponse
        {
            return $this->indexTemplate(
                title: "Style Props Category",
                request: $request,
                modelClass: StylePropsCategory::class,
                with: ["styleProps.options", "styleProps.children.options"],
                perPage: 10,
            );
        }

        /**
         * Display a listing of the deleted resource.
         */
        public function fetchDeleted(Request $request): JsonResponse
        {
            return $this->fetchDeletedTemplate(
                title: "Style Props Category",
                request: $request,
                modelClass: StylePropsCategory::class,
                with: ["styleProps"],
                perPage: 10,
            );
        }

        /**
         * Store a newly created resource in storage.
         */
        public function store(Request $request): JsonResponse
        {
            $validationRules = [
                'category_name'=>"required|unique:style_props_categories",
                'category_description'=>"required",
            ];

            //relations
            $relationships = [
            ];

            // store the prop
            return $this->storeTemplate(
                title: "Style Props Category",
                request: $request,
                modelClass: StylePropsCategory::class,
                validationRules: $validationRules,
                relationships: $relationships,
                files: $this::FILES_FIELD_NAMES,
                filesFolderName: $this::FILES_FOLDER_NAME,
                imagesFolderName: $this::IMAGES_FOLDER_NAME
            );
        }



        /**
         * Update the specified resource in storage.
         */
        public function update(Request $request, StylePropsCategory $stylePropsCategory): JsonResponse
        {
            $validationRules = [
                'category_name'=>"required|unique:style_props_categories,category_name,".$stylePropsCategory["id"],
                'category_description'=>"required",
            ];

            //relations
            $relationships = [
            ];

            // Call storeTemplate function to store the post and its related models
            return $this->updateTemplate(
                title: "Style Props Category",
                request: $request,
                resource: $stylePropsCategory,
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
                modelClass: StylePropsCategory::class,
                title: "Style Props Category",
            );
        }


        /**
         * Restore the deleted specified resource from storage.
         */
        public function restore($ids): JsonResponse
        {
            return $this->restoreTemplate(
                ids: $ids,
                modelClass: StylePropsCategory::class,
                title: "Style Props Category",
            );
        }

        /**
         * Permanently delete the specified resources.
         */
        public function permanentDelete($ids): JsonResponse
        {
            return $this->permanentDeleteTemplate(
                ids: $ids,
                modelClass: StylePropsCategory::class,
                title: "Style Props Category",
                fileColumnNames: $this::FILES_FIELD_NAMES,
                filesFolderName: $this::FILES_FOLDER_NAME,
                imagesFolderName: $this::IMAGES_FOLDER_NAME
            );
        }


}

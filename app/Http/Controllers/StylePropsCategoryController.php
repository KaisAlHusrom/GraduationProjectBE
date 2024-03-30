<?php
namespace App\Http\Controllers;

use App\Models\StylePropsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StylePropsCategoryController extends Controller
{
        /**
         * Display a listing of the resource.
         */
        public function index(Request $request): JsonResponse
        {
            return $this->indexTemplate(
                title: "Style Props Category",
                request: $request,
                modelClass: StylePropsCategory::class,
                with: ["styleProps"],
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
                relationships: $relationships
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
                relationships: $relationships
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
            );
        }


}

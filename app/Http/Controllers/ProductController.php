<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

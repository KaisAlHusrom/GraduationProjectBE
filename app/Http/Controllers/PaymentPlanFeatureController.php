<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlanFeature;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentPlanFeatureController extends Controller
{
        private const TITLE = "Payment Plan Features";
            private const MODEL = PaymentPlanFeature::class;
            private const FILES_FIELD_NAMES = ['payment_plan_feature_image'];
            private const FETCH_WITH = ["paymentPlan"];
            private const IMAGES_FOLDER_NAME = "payment_plan_feature_images";
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
                    'payment_plan_feature_name'=> "required",
                    'payment_plan_feature_image'=>"nullable",
                    'payment_plan_feature_description'=>"required",
                    'payment_plan_id'=>"required"
                ];

                //relations
                $relationships = [
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
                    'payment_plan_feature_name'=> "required",
                    'payment_plan_feature_image'=>"nullable",
                    'payment_plan_feature_description'=>"required",
                ];

                //relations
                $relationships = [
                    'paymentPlan'
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

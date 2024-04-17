<?php

namespace App\Http\Controllers;

use App\Models\StyleProp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StylePropController extends Controller
{

    private const TITLE = "Style Props";
    private const MODEL = StyleProp::class;
    private const IMAGES_FOLDER_NAME = "StylePropsImages";
    private const FILES_FOLDER_NAME = null;
    private const FILES_FIELD_NAMES = ["style_prop_image"];
    private const FETCH_WITH = ["category", "options", "children", "parent"];
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
                'style_prop_normal_name'=>"required|unique:style_props",
                'style_prop_css_name'=>"required|unique:style_props",
                'style_prop_image'=>"",
                'style_prop_description'=>"required",
                'style_prop_value_type'=>"required",
                'is_section'=>"boolean",
                'is_component'=>"boolean",
                'is_element'=>"boolean",
                "category_id"=>"required",
                'is_child'=>"",
                'parent_id'=>"",
                'locateTypes'=>"",
            ];

            //relations
            $relationships = [

            ];


//            Log::info("style_prop_image". json_encode($request->input("style_prop_image", null),));

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
        public function update(Request $request, StyleProp $styleProp): JsonResponse
        {
            Log::info("propValue: ". json_encode($styleProp));
            $validationRules = [
                'style_prop_normal_name'=>"required|unique:style_props,style_prop_normal_name,".$styleProp['id'],
                'style_prop_css_name'=>"required|unique:style_props,style_prop_css_name,".$styleProp["id"],
                'style_prop_image'=>"",
                'style_prop_description'=>"required",
                'style_prop_value_type'=>"required",
                'is_section'=>"boolean",
                'is_component'=>"boolean",
                'is_element'=>"boolean",
                'is_child'=>"",
                'parent_id'=>"",
                'category_id'=>"",
                'locateTypes'=>"",
            ];


            //relations
            $relationships = [
                "children"
            ];


            // Call storeTemplate function to store the post and its related models
            return $this->updateTemplate(
                title: $this::TITLE,
                request: $request,
                resource: $styleProp,
                validationRules: $validationRules,
                relationships: $relationships,
                files: $this::FILES_FIELD_NAMES,
                filesFolderName: $this::FILES_FOLDER_NAME,
                imagesFolderName: $this::IMAGES_FOLDER_NAME
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
                imagesFolderName:$this::IMAGES_FOLDER_NAME,
            );
        }


}

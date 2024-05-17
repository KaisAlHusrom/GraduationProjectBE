<?php

namespace App\Http\Controllers;

use App\Models\ElementType;
use Illuminate\Http\Request;
use App\Http\Controllers\DefaultResponseBodies;
use App\Models\ElementProp;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class ElementTypeController extends Controller
{

    private const FILES_FIELD_NAMES = ['element_type_image'];
    private const IMAGES_FOLDER_NAME = "ElementTypesImages";

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->indexTemplate(
            title: "Element Types",
            request: $request,
            modelClass: ElementType::class,
            with: ['children.children.children.children', 'elementProps', 'parent', 'category'],
            perPage: 10,
        );
    }

    /**
     * Display a listing of the deleted resource.
     */
    public function fetchDeleted(Request $request): JsonResponse
    {
        return $this->fetchDeletedTemplate(
            title: "Element Types",
            request: $request,
            modelClass: ElementType::class,
            with: ['children', 'elementProps', 'parent', 'category'],
            perPage: 10,
        );
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validationRules = [
            "element_type_name" => 'required|unique:element_types',
            "element_type_description" => 'required',
            "sequence_number" => '',
            "not_has_end_tag" => '',
            'is_child' => "",
            'parent_id' => "nullable",
            "category_id" => "required",
            "element_type_image" => "nullable"
        ];

        $relationships = [
            "elementProps",
        ];

        // Call storeTemplate function to store the post and its related models
        return $this->storeTemplate(
            title: "Element Types",
            request: $request,
            modelClass: ElementType::class,
            validationRules: $validationRules,
            relationships: $relationships,
            files: $this::FILES_FIELD_NAMES,
            imagesFolderName: $this::IMAGES_FOLDER_NAME,
        );

    }

    /**
     * Display the specified resource.
     */
    public function show(ElementType $elementType)
    {
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validationRules = [
            "element_type_name" => 'required|unique:element_types,element_type_name,'.$id,
            "element_type_description" => 'required',
            "sequence_number" => '',
            "not_has_end_tag" => '',
            'is_child' => "",
            "element_type_image" => "nullable"
//            'parent_id' => "",
//            'category_id' => "",
        ];

        //the rows came from index function, in $request->input("make sure the name here be the same with the name in database table")
        $relationships = [
            "elementProps",
            "children",
            "parent",
            "category",
        ];

        // Call storeTemplate function to store the post and its related models
        return $this->updateTemplate(
            title: "Element Types",
            request: $request,
            resourceId: $id,
            modelClass: ElementType::class,
            validationRules: $validationRules,
            relationships: $relationships,
            files: $this::FILES_FIELD_NAMES,
            imagesFolderName: $this::IMAGES_FOLDER_NAME,
        );
    }

        /**
     * Remove the specified resource from storage.
     */
    public function destroy($ids): JsonResponse
    {
        return $this->destroyTemplate(
            ids: $ids,
            modelClass: ElementType::class,
            title: "Element Types",
        );
    }

    /**
     * Restore the deleted specified resource from storage.
     */
    public function restore($ids): JsonResponse
    {
        return $this->restoreTemplate(
            ids: $ids,
            modelClass: ElementType::class,
            title: "Element Types",

        );
    }

    /**
     * Permanently delete the specified resources.
     */
    public function permanentDelete($ids): JsonResponse
    {
        return $this->permanentDeleteTemplate(
            ids: $ids,
            modelClass: ElementType::class,
            title: "Element Types",
            fileColumnNames: $this::FILES_FIELD_NAMES,
            imagesFolderName: $this::IMAGES_FOLDER_NAME,
        );
    }

}

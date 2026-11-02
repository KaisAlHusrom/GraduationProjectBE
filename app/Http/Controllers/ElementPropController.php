<?php

namespace App\Http\Controllers;

use App\Models\ElementProp;
use App\Models\ElementType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ElementPropController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        return $this->indexTemplate(
            title: "Element Props",
            request: $request,
            modelClass: ElementProp::class,
            with: ['elementTypes'],
            perPage: 10,
        );
    }

    /**
     * Display a listing of the deleted resource.
     */
    public function fetchDeleted(Request $request): JsonResponse
    {
        return $this->fetchDeletedTemplate(
            title: "Element Props",
            request: $request,
            modelClass: ElementProp::class,
            with: ['elementTypes'],
            perPage: 10,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validationRules = [
            "element_prop_name" => 'required|unique:element_props',
            "element_prop_description" => 'required',
            "elementTypes" => 'array', // Ensure element_types is an array
        ];

        //relations
        $relationships = [
            "elementTypes",
        ];



        // store the prop
        return $this->storeTemplate(
            title: "Element Props",
            request: $request,
            modelClass: ElementProp::class,
            validationRules: $validationRules,
            relationships: $relationships,
        );
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validationRules = [
            "element_prop_name" => 'required|unique:element_props,element_prop_name,' . $id,
            "element_prop_description" => 'required',
            "element_types" => 'array', // Ensure element_types is an array
        ];

        //relations
        $relationships = [
            "elementTypes",
        ];

        // Call storeTemplate function to store the post and its related models
        return $this->updateTemplate(
            title: "Element Props",
            request: $request,
            resourceId: $id,
            modelClass: ElementProp::class,
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
            modelClass: ElementProp::class,
            title: "Element Props",
        );
    }


    /**
     * Restore the deleted specified resource from storage.
     */
    public function restore($ids): JsonResponse
    {
        return $this->restoreTemplate(
            ids: $ids,
            modelClass: ElementProp::class,
            title: "Element Props",
        );
    }

    /**
     * Permanently delete the specified resources.
     */
    public function permanentDelete($ids): JsonResponse
    {
        return $this->permanentDeleteTemplate(
            ids: $ids,
            modelClass: ElementProp::class,
            title: "Element Props",
        );
    }
}

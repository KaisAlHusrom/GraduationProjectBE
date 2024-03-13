<?php

namespace App\Http\Controllers;

use App\Models\ElementType;
use Illuminate\Http\Request;
use App\Http\Controllers\DefaultResponseBodies;
use App\Models\ElementProp;

class ElementTypeController extends Controller
{



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DefaultResponseBodies::getSuccessResponse(ElementType::all()->load(["children", "elementProps", "parent"]), "Get All Element Types Successfully");
    }

    /**
     * Display a listing of the deleted resource.
     */
    public function fetchDeleted()
    {
        $deletedElementTypes = ElementType::onlyTrashed()->with(["children", "elementProps", "parent"])->get();

        return DefaultResponseBodies::getSuccessResponse($deletedElementTypes, "Get All Deleted Element Types Successfully");
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validationRules = [
            "element_type_name" => 'required|unique:element_types',
            "element_type_description" => 'required',
            'is_child' => "",
        ];

        $relationships = [
            "elementProps" => $request->input('elementProps', []),
            "parent" => [$request->input('parent', null)],
        ];

        // Call storeTemplate function to store the post and its related models
        return $this->storeTemplate(
            title: "Element Types",
            request: $request,
            modelClass: ElementType::class,
            validationRules: $validationRules,
            relationships: $relationships
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
    public function update(Request $request, ElementType $elementType)
    {
        $validationRules = [
            "element_type_name" => 'required|unique:element_types,element_type_name,'.$elementType->id,
            "element_type_description" => 'required',
            'is_child' => "",
        ];

        //the rows came from index function, in $request->input("make sure the name here be the same with the name in database table")
        $relationships = [
            "elementProps" => $request->input('elementProps', []),
            "children" => $request->input('children', []),
            "parent" => [$request->input('parent_id', null)],
        ];

        // Call storeTemplate function to store the post and its related models
        return $this->updateTemplate(
            title: "Element Types",
            request: $request,
            resource: $elementType,
            validationRules: $validationRules,
            relationships: $relationships
        );
    }

        /**
     * Remove the specified resource from storage.
     */
    public function destroy($ids)
    {
        // Convert comma-separated IDs to an array
        $idArray = explode(',', $ids);

        // Delete the selected element props
        $deletedCount = ElementType::whereIn('id', $idArray)->delete();

        if ($deletedCount > 0) {
            return DefaultResponseBodies::getSuccessResponse(message: "Element Types deleted successfully");
        } else {
            return DefaultResponseBodies::getNotFoundResponse(message: "Element Types not found");
        }
    }

    /**
     * Restore the deleted specified resource from storage.
     */
    public function restore($ids)
    {
        // Convert comma-separated IDs to an array
        $idArray = explode(',', $ids);

        // Delete the selected element props
        $restoredCount = ElementType::whereIn('id', $idArray)->restore();

        if ($restoredCount > 0) {
            return DefaultResponseBodies::getSuccessResponse(message: "Element Types restored successfully");
        } else {
            return DefaultResponseBodies::getNotFoundResponse(message: "Element Types not found");
        }
    }

    /**
     * Permanently delete the specified resources.
     */
    public function permanentDelete($ids)
    {
        // Convert comma-separated IDs to an array
        $idArray = explode(',', $ids);

        // Permanently delete the selected element props
        $deletedCount = ElementType::whereIn('id', $idArray)->forceDelete();

        if ($deletedCount > 0) {
            return DefaultResponseBodies::getSuccessResponse(message: "Element Types permanently deleted successfully");
        } else {
            return DefaultResponseBodies::getNotFoundResponse(message: "Element Types not found");
        }
    }

}

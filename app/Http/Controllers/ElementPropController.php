<?php

namespace App\Http\Controllers;

use App\Models\ElementProp;
use App\Models\ElementType;
use Illuminate\Http\Request;

class ElementPropController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DefaultResponseBodies::getSuccessResponse(ElementProp::all()->load(["elementTypes"]), "Get All Element Props Successfully");
    }

    /**
     * Display a listing of the deleted resource.
     */
    public function fetchDeleted()
    {
        $deletedElementProps = ElementProp::onlyTrashed()->with(["elementTypes"])->get();

        return DefaultResponseBodies::getSuccessResponse($deletedElementProps, "Get All Deleted Element Props Successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validationRules = [
            "element_prop_name" => 'required|unique:element_props',
            "element_prop_description" => 'required',
            "elementTypes" => 'array', // Ensure element_types is an array
        ];

        //relations
        $relationships = [
            "elementTypes" => $request->input('elementTypes', [])
        ];

        // store the prop
        return $this->storeTemplate(
            title: "Element Props",
            request: $request,
            modelClass: ElementProp::class,
            validationRules: $validationRules,
            relationships: $relationships
        );
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ElementProp $elementProp)
    {
        $request->validate([
            "element_prop_name" => 'required|unique:element_props,element_prop_name,' . $elementProp->id,
            "element_prop_description" => 'required',
            "element_types" => 'array', // Ensure element_types is an array
        ]);

        // Update the basic attributes
        $elementProp->update([
            'element_prop_name' => $request->input('element_prop_name'),
            'element_prop_description' => $request->input('element_prop_description'),
        ]);


        // Sync ElementTypes if provided
        $elementTypesData = $request->input('element_types', []);
        $elementTypeIds = collect($elementTypesData)->pluck('id')->toArray();
        $elementProp->elementTypes()->sync($elementTypeIds);


        return DefaultResponseBodies::getSuccessResponse($elementProp, "Update Element Prop Successfully");
    }


    /**
     * Remove the specified resources from storage.
     */
    public function destroy($ids)
    {
        // Convert comma-separated IDs to an array
        $idArray = explode(',', $ids);

        // Delete the selected element props
        $deletedCount = ElementProp::whereIn('id', $idArray)->delete();

        if ($deletedCount > 0) {
            return DefaultResponseBodies::getSuccessResponse(message: "Element Props deleted successfully");
        } else {
            return DefaultResponseBodies::getNotFoundResponse(message: "Element Props not found");
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
        $restoredCount = ElementProp::whereIn('id', $idArray)->restore();

        if ($restoredCount > 0) {
            return DefaultResponseBodies::getSuccessResponse(message: "Element Props restored successfully");
        } else {
            return DefaultResponseBodies::getNotFoundResponse(message: "Element Props not found");
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
        $deletedCount = ElementProp::whereIn('id', $idArray)->forceDelete();

        if ($deletedCount > 0) {
            return DefaultResponseBodies::getSuccessResponse(message: "Element Props permanently deleted successfully");
        } else {
            return DefaultResponseBodies::getNotFoundResponse(message: "Element Props not found");
        }
    }
}

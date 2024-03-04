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
     * Display trashed values only
     */
    public function indexTrashedOnly()
    {

    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "element_type_name" => 'required|unique:element_types',
            "element_type_description" => 'required',

        ]);

        $elementType = ElementType::create([
            "element_type_name" => $request->element_type_name,
            "element_type_description" => $request->element_type_description,
            "is_child" => $request->is_child,
            "parent_id" => $request->parent_id,
        ]);

        // If children are provided in the request, associate them
        if ($request->has('element_props')) {
            $elementPropsIds = $request->input('element_props', []);

            $elementType->elementProps()->attach($elementPropsIds);


        }

        return DefaultResponseBodies::getSuccessResponse($elementType, "Save Element Type Successfully");
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
        $request->validate([
            "element_type_name" => 'required|unique:element_types,element_type_name,' . $elementType->id,
            "element_type_description" => 'required',
        ]);

        // Update the basic attributes
        $elementType->update([
            'element_type_name' => $request->input('element_type_name'),
            'element_type_description' => $request->input('element_type_description'),
            'is_child' => $request->input('is_child'),
        ]);

        // Update parent if provided in the request
        if ($request->has('parent')) {
            $parentData = $request->input('parent');

            // Make sure parent data is provided and is not empty
            if (!empty($parentData)) {
                $elementType->parent_id = $parentData['id'];
            } else {
                // If parent data is empty or not provided, set parent_id to null
                $elementType->parent_id = null;
            }
        }

        // Update children if provided in the request
        if ($request->has('children')) {
            $childrenData = $request->input('children');

            // Check if children data is provided and contains more than one child
            if (!empty($childrenData)) {
                // Update the parent_id of each child to the new parent_id
                ElementType::whereIn('id', array_column($childrenData, 'id'))
                    ->update(['parent_id' => $elementType->id]);
            }
        }

        // If children are provided in the request, associate them
        if ($request->has('element_props')) {
            // Sync ElementTypes if provided
            $elementPropsData = $request->input('element_props', []);
            $elementPropIds = collect($elementPropsData)->pluck('id')->toArray();
            $elementType->elementProps()->sync($elementPropIds);


        }

        // Save the changes
        $elementType->save();

        // Optionally, you can return a response indicating success
        return DefaultResponseBodies::getSuccessResponse($elementType, "Update Element Type Successfully");
    }

        /**
     * Remove the specified resource from storage.
     */
    public function destroy(ElementType $elementType)
    {
        if ($elementType->delete()) {
            return DefaultResponseBodies::getSuccessResponse(message: "Element Type deleted successfully");
        } else {
            return DefaultResponseBodies::getNotFoundResponse(message: "Element Type not found");
        }
    }

    /**
     * Restore the deleted specified resource from storage.
     */
    public function restore($elementTypeId)
    {
        $elementType = ElementType::withTrashed()->where('id', $elementTypeId)->first();
        if ($elementType->restore()) {
            return DefaultResponseBodies::getSuccessResponse(message: "Element Type restored successfully");
        } else {
            return DefaultResponseBodies::getNotFoundResponse(message: "Element Type not found");
        }
    }

}

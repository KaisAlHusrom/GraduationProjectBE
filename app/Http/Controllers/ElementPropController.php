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
     * showWithTrashed
     */
    public function indexWithOutTrashed()
    {

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
            "element_prop_name" => 'required|unique:element_props',
            "element_prop_description" => 'required',
            "element_types" => 'array', // Ensure element_types is an array
        ]);

        // Create the ElementProp
        $elementProp = ElementProp::create([
            "element_prop_name" => $request->element_prop_name,
            "element_prop_description" => $request->element_prop_description,
        ]);

        // Ensure ElementProp is created successfully
        if ($elementProp) {

            // Attach ElementTypes if provided
            $elementTypesIds = $request->input('element_types', []);
            $elementProp->elementTypes()->attach($elementTypesIds);

            return DefaultResponseBodies::getSuccessResponse($elementProp, "Save Element Prop Successfully");
        } else {
            // Handle error if ElementProp creation fails
            return DefaultResponseBodies::getBadRequestResponse(message: "Failed to create Element Prop");
        }
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

        // Optionally, you can return a response indicating success
        return DefaultResponseBodies::getSuccessResponse($elementProp, "Update Element Prop Successfully");
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ElementProp $elementProp)
    {
        if ($elementProp->delete()) {
            return DefaultResponseBodies::getSuccessResponse(message: "Element Prop deleted successfully");
        } else {
            return DefaultResponseBodies::getNotFoundResponse(message: "Element Prop not found");
        }
    }
}

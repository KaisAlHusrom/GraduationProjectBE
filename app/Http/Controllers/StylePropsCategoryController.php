<?php

namespace App\Http\Controllers;

use App\Models\StyleProp;
use App\Models\StylePropsCategory;
use Illuminate\Http\Request;

class StylePropsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DefaultResponseBodies::getSuccessResponse(StylePropsCategory::all()->load(["styleProps"]), "Get All Style Prop Categories Successfully");

    }

    /**
     * Display a listing of the deleted resource.
     */
    public function fetchDeleted()
    {
        $deletedStylePropsCategory = StylePropsCategory::onlyTrashed()->with(["styleProps"])->get();

        return DefaultResponseBodies::getSuccessResponse($deletedStylePropsCategory, "Get All Deleted Style Prop Categories Successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation rules for the post
        $stylePropsCategoryValidationRules = [
            "category_name" => 'required|unique:style_props_categories',
            "category_description" => 'required',
        ];

        // Relationships and their IDs
        $relationships = [
        ];

        // Call storeTemplate function to store the post and its related models
        return $this->storeTemplate(
            title: "Style Prop Category",
            request: $request,
            modelClass: StylePropsCategory::class,
            validationRules: $stylePropsCategoryValidationRules,
            relationships: $relationships
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(StylePropsCategory $stylePropsCategory)
    {
        // Your logic here
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StylePropsCategory $stylePropsCategory)
    {
        $request->validate([
            "category_name" => 'required|unique:style_props_categories,category_name,'. $stylePropsCategory->id,
            "category_description" => 'required',
        ]);

        // Update the basic attributes
        $stylePropsCategory->update([
            'category_name' => $request->input('category_name'),
            'category_description' => $request->input('category_description'),
        ]);

        // Update children if provided in the request
        if ($request->has('style_props')) {
            $childrenData = $request->input('style_props');

            // Check if children data is provided and contains more than one child
            if (!empty($childrenData)) {
                // Update the parent_id of each child to the new parent_id
                StyleProp::whereIn('id', array_column($childrenData, 'id'))
                    ->update(['category_id' => $stylePropsCategory->id]);
            }
        }

        return DefaultResponseBodies::getSuccessResponse($stylePropsCategory, "Update Style Prop Category Successfully");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ids)
    {
        return $this->destroyTemplate($ids, StylePropsCategory::class, "Style Prop Categories");
    }

    /**
     * Restore the deleted specified resource from storage.
     */
    public function restore($ids)
    {
        return $this->restoreTemplate($ids, StylePropsCategory::class, "Style Prop Categories");
    }

    /**
     * Permanently delete the specified resources.
     */
    public function permanentDelete($ids)
    {
        return $this->permanentDeleteTemplate($ids, StylePropsCategory::class, "Style Prop Categories");
    }
}

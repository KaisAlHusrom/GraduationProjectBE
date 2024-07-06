<?php

namespace App\Http\Controllers;

use App\Models\Design;
use App\Models\WebProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WebProjectController extends Controller
{
    private const TITLE = "Web Project";
    private const MODEL = WebProject::class;
    private const FILES_FIELD_NAMES = ["project_logo"];
    private const FETCH_WITH = ["user" , "pages" , "databases" , "designs"];
    private const IMAGES_FOLDER_NAME = "web_project_Logos";
    private const FILES_FOLDER_NAME = null;

    /*
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
            with: $this::FETCH_WITH,
        );
    }

    /*
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

    /*
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Validation rules
        $validationRules = [
            'user_id' => "required",
            'project_title' => "required",
            'project_logo' => "required",
            'project_type' => "required",
            'project_job' => "required",
            'project_description' => "required",
            'is_template' => "",
            'is_own_project' => "",
            'is_published' => "",
        ];

        // Validate request data
        $validatedData = Validator::make($request->all(), $validationRules)->validate();

        // Create the new web project
        $newWebProject = WebProject::create($validatedData);

        // Fetch the template web project along with its pages and designs
        $templateWebProject = WebProject::with(['pages', 'designs'])
            ->where("is_template", "1")
            ->first();

        // Duplicate each design from the template
        foreach ($templateWebProject->designs as $design) {
            // Create a new design for the new web project
            $newDesign = $design->replicate();
            $newDesign->web_project_id = $newWebProject->id;
            $newDesign->is_template = 0;

            $newDesign->save();

            // Duplicate each style associated with the design
            foreach ($design->styles as $style) {
                $newStyle = $style->replicate();
                $newStyle->design_id = $newDesign->id;
                $newStyle->save();
            }

            // Duplicate each child design recursively
            $this->duplicateChildren($design, $newDesign);
        }

        // Duplicate each page from the template
        foreach ($templateWebProject->pages as $page) {
            // Create a new page for the new web project
            $newPage = $page->replicate();
            $newPage->web_project_id = $newWebProject->id;
            $newPage->is_template = 0;

            $newPage->save();

            // Duplicate each design associated with the page
            foreach ($page->designs as $design) {
                $newDesign = $design->replicate();
                $newDesign->page_id = $newPage->id;
                $newDesign->is_template = 0;

                $newDesign->save();

                // Duplicate each style associated with the design
                foreach ($design->styles as $style) {
                    $newStyle = $style->replicate();
                    $newStyle->design_id = $newDesign->id;
                    $newStyle->save();
                }

                // Duplicate each child design recursively
                $this->duplicateChildren($design, $newDesign);
            }
        }

        return DefaultResponseBodies::getSuccessResponse(data: $newWebProject, message: "web project created successfully");
    }

    /**
     * Recursively duplicate children designs.
     *
     * @param \App\Models\Design $originalDesign
     * @param \App\Models\Design $newDesign
     */
    protected function duplicateChildren($originalDesign, $newDesign)
    {
        foreach ($originalDesign->children as $childDesign) {
            $newChildDesign = $childDesign->replicate();
            $newChildDesign->parent_id = $newDesign->id;
            $newChildDesign->save();

            // Duplicate styles for the child design
            foreach ($childDesign->styles as $style) {
                $newStyle = $style->replicate();
                $newStyle->design_id = $newChildDesign->id;
                $newStyle->save();
            }

            // Recursively duplicate the children of this child design
            $this->duplicateChildren($childDesign, $newChildDesign);
        }
    }


    /*
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validationRules = [
                    'project_title' => "required",
                    'project_logo' => "required",
                    'project_type'  => "required",
                    'project_job'   => "required",
                    'project_description' => "required",
                    'is_template'   => "",
                    'is_own_project' => "",
                    'is_published' => "",

                ];

        //relations
        $relationships = [
            "user"
        ];

        // Call storeTemplate function to store the post and its related models
        return $this->updateTemplate(
            title: $this::TITLE,
            request: $request,
            resourceId: $id,
            modelClass : $this::MODEL,
            validationRules: $validationRules,
            relationships: $relationships,
            files: $this::FILES_FIELD_NAMES,
            filesFolderName: $this::FILES_FOLDER_NAME,
            imagesFolderName: $this::IMAGES_FOLDER_NAME,
        );
    }

    /*
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

    /*
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

    /*
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

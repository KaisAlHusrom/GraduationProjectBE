<?php

namespace App\Http\Controllers;

use App\Models\WebProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebProjectController extends Controller
{
    private const TITLE = "Web Project";
    private const MODEL = WebProject::class;
    private const FILES_FIELD_NAMES = ["project_logo"];
    private const FETCH_WITH = ["user" , "pages" , "databases" ];
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
        $validationRules = [
            'user_id' => "required",
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

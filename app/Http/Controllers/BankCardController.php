<?php

namespace App\Http\Controllers;

use App\Models\BankCard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankCardController extends Controller
{

    private const TITLE = "Bank Cards";
    private const MODEL = BankCard::class;
    private const FILES_FIELD_NAMES = [];
    private const FETCH_WITH = ['user'];
    private const IMAGES_FOLDER_NAME = null;
    private const FILES_FOLDER_NAME = null;


    /**
     * Display a listing of the user's bank cards.
     *
     * @param Request $request
     * @return JsonResponse
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
    public function fetchUserBankCards(): JsonResponse
    {
        $user = Auth::user();
        $bankCards = $user->bankCards->map(function($card) {
            return [
                'id' => $card->id,
                'user_id' => $card->user_id,
                'card_holder_name' => $card->card_holder_name,
                'card_number' => $card->card_number_formatted, // Use the accessor
                'created_at' => $card->created_at,
                'updated_at' => $card->updated_at
            ];
        });

        return DefaultResponseBodies::getSuccessResponse(data: $bankCards, message: "Get All Cards Successfully");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validationRules = [
            'card_number' => 'required|string|max:19',
            'card_holder_name' => 'required|string|max:255',
            'expiry_date' => 'required|string|max:5', // MM/YY format
            'cvv' => 'required|string|max:4',
            'user_id' => 'required'
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



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validationRules = [
            'card_number' => 'string',
            'card_holder_name' => 'required|string|max:255',
            'expiry_date' => 'required|string|max:5', // MM/YY format
            'cvv' => 'string',
        ];

        //relations
        $relationships = [
            'user',
        ];

        $user = Auth::user();
        if($user->is_admin) {
            return $this->updateTemplate(
                title: $this::TITLE,
                request: $request,
                resourceId: $id,
                modelClass: $this::MODEL,
                validationRules: $validationRules,
                relationships: $relationships,
                files: $this::FILES_FIELD_NAMES,
                filesFolderName: $this::FILES_FOLDER_NAME,
                imagesFolderName: $this::IMAGES_FOLDER_NAME,
            );
        } else {
            $bankCard = $user->bankCards()->find($id);
            if($bankCard) {
                return $this->updateTemplate(
                    title: $this::TITLE,
                    request: $request,
                    resourceId: $id,
                    modelClass: $this::MODEL,
                    validationRules: $validationRules,
                    relationships: $relationships,
                    files: $this::FILES_FIELD_NAMES,
                    filesFolderName: $this::FILES_FOLDER_NAME,
                    imagesFolderName: $this::IMAGES_FOLDER_NAME,
                );
            }
        }

        return DefaultResponseBodies::getUnauthorizedResponse();

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
            imagesFolderName: $this::IMAGES_FOLDER_NAME,
        );
    }
}

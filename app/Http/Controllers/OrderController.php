<?php

namespace App\Http\Controllers;

use App\Helpers\DataProcess;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psy\Util\Json;

class OrderController extends Controller
{
        private const TITLE = "Orders";
            private const MODEL = Order::class;
            private const FILES_FIELD_NAMES = [];
            private const FETCH_WITH = ['user', "products"];
            private const IMAGES_FOLDER_NAME = null;
            private const FILES_FOLDER_NAME = null;

            /**
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

            /*
             * coming orders counts
             */
            public function fetchComingOrdersCounts(): JsonResponse {
                $user = Auth::user();

                // Fetch orders that have products belonging to the authenticated user using a join
                $countComingOrders = Order::withoutGlobalScope('order')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->where('products.user_id', $user->id)
                    ->orderBy('orders.created_at', 'desc') // Explicitly specify table for created_at in the main query
                    ->count();


                return DefaultResponseBodies::getSuccessResponse(data: ['count' => $countComingOrders], message: "Get all coming orders successfully");

            }

            /*
             * coming orders
             */
            public function fetchComingOrders(Request $request): JsonResponse {
                $user = Auth::user();

                // Get the page number from the request query parameters
                $pageNumber = $request->query('pageNumber', 1);

                // Get the per page from the request query parameters
                $perPage = $request->query('perPage', 15); // Default to 15 if not specified

                // Get the filters from the request
                $filters = $request->query('filters', []);

                // Get the sorts from the request
                $sorts = $request->query('sorts', []);

                // Get the search query
                $searchQuery = $request->query('searchQuery', null);

                // Initialize query builder for orders
                $query = Order::withoutGlobalScope('order')
                    ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->where('products.user_id', $user->id)
                    ->select('orders.*') // Ensure only order columns are selected to avoid ambiguity
                    ->distinct() // Ensure distinct orders
                    ->with('products') // Eager load products
                    ->with("user");

                // Apply filters if they exist
                if (!empty($filters)) {
                    $query = DataProcess::filterData($query, $filters);
                }

                // Apply sorts if they exist
                if (!empty($sorts)) {
                    $query = DataProcess::sortData($query, $sorts);
                }

                // Apply search query if it exists
                if ($searchQuery) {
                    $query = DataProcess::searchData($query, $searchQuery);
                }

                // Apply pagination
                $comingOrders = $query->orderBy('orders.created_at', 'desc')
                    ->skip(($pageNumber - 1) * $perPage)
                    ->take($perPage)
                    ->get();
                return DefaultResponseBodies::getSuccessResponse(data: $comingOrders, message: "Get all coming orders successfully");
            }



            /**
             * Check if the user has bought and/or reviewed a specific product
             */
            public function hadBuyProduct($productId): JsonResponse {
                $product = Product::findOrFail($productId);
                $user = Auth::user();

                // Check if the user has already reviewed the product
                $isReviewedBefore = $product->productReviews()->where('user_id', $user->id)->exists();

                if ($isReviewedBefore) {
                    return DefaultResponseBodies::getSuccessResponse(
                        data: [
                            'reviewedBefore' => true,
                            'boughtBefore' => true,
                        ],
                        message: 'Reviewed Before'
                    );
                }


                // Check if the user has bought the product
                $orders = Order::where('user_id', $user->id)->get();
                $hasBoughtProduct = false;

                foreach ($orders as $order) {
                    foreach ($order->products as $product) {
                        if($product->id == $productId) {
                            $hasBoughtProduct = true;
                            break 2;
                        }
                    }
                }

                if ($hasBoughtProduct) {
                    return DefaultResponseBodies::getSuccessResponse(
                        data: [
                            'reviewedBefore' => false,
                            'boughtBefore' => true,
                        ],
                        message: 'Bought Before'
                    );
                }

                return DefaultResponseBodies::getSuccessResponse(
                    data: [
                        'reviewedBefore' => false,
                        'boughtBefore' => false,
                    ],
                    message: 'Not Bought'
                );
            }




    /**
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

            /**
             * Store a newly created resource in storage.
             */
            public function store(Request $request): JsonResponse
            {
                $validationRules = [
                    'user_id'=> "required",
                    'total_amount'=> "required",
                    'status'=> "required",
                    'stripe_session_id' => "required",
                    'currency'=> "required"
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
                    'total_amount'=> "required",
                    'status'=> "required",
                    'stripe_session_id' => "required",
                    'currency'=> "required"
                ];

                //relations
                $relationships = [
                    'user',
                ];


                // Call storeTemplate function to store the post and its related models
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

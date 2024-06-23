<?php

use App\Helpers\UtilsRoutes;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankCardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DesignCategoryController;
use App\Http\Controllers\DesignController;
use App\Http\Controllers\ElementPropController;
use App\Http\Controllers\ElementTypeCategoryController;
use App\Http\Controllers\ElementTypeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StylePropController;
use App\Http\Controllers\StylePropsCategoryController;
use App\Http\Controllers\StylePropsValueController;
use App\Http\Controllers\StyleResponsiveBreakpointController;
use App\Http\Controllers\StyleStatusController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WebProjectController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


//TODO: I have to add middleware or something to ensure that noraml user can't delete, change or update other's users' info
//Sanctum::routes();
// ! WITH NO AUTHENTICATIONS MIDDLEWARES
Route::post("auth/register", [AuthController::class, 'register']);
Route::post("auth/login", [AuthController::class, 'login']);

UtilsRoutes::generateResourceUsersRoutes(
    "products-categories",
    CategoryController::class,
    excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete'],
);

UtilsRoutes::generateResourceUsersRoutes(
    "products",
    \App\Http\Controllers\ProductController::class,
    excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
);

// ! SANCTUM AUTHENTICATION
Route::middleware(['customAuth'])->group(function () {
    // ------- ! AUTH ! ------------
    Route::get('auth/user', [AuthController::class, "fetchUserData"]);
    Route::get('auth/logout', [AuthController::class, "logout"]);

    //user orders
    UtilsRoutes::generateResourceUsersRoutes(
        "orders",
        \App\Http\Controllers\OrderController::class,
        excepts: ['restore', 'fetchDeleted', 'permanentDelete', 'update', 'delete']
    );

    //Bank cards
    UtilsRoutes::generateResourceUsersRoutes(
        'bank-cards',
        BankCardController::class,
        excepts: ['restore', 'fetchDeleted', 'permanentDelete', 'delete', 'index']
    );
    Route::get('user/bank-cards', [BankCardController::class, "fetchUserBankCards"]);


    // ----- ! Tables normal users can see, add, update and delete ----- ! //

    //Designs Prop Values
    UtilsRoutes::generateResourceUsersRoutes(
        "design-prop-values",
        \App\Http\Controllers\DesignedElementsPropValueController::class,
        excepts: ['restore', 'fetchDeleted', 'permanentDelete']
    );

    //Web Projects for users
    UtilsRoutes::generateResourceUsersRoutes(
        "pages",
        PageController::class,
        excepts: ['restore', 'fetchDeleted', 'permanentDelete']

    );

    UtilsRoutes::generateResourceUsersRoutes(
        "web-projects",
        WebProjectController::class,
        excepts: ['restore', 'fetchDeleted', 'permanentDelete']
    );

    //Designs for users
    UtilsRoutes::generateResourceUsersRoutes(
        "designs",
        DesignController::class,
        excepts: ['restore', 'fetchDeleted', 'permanentDelete']
    );

    //Products
    UtilsRoutes::generateResourceUsersRoutes(
        "products",
        ProductController::class,
        excepts: ['store', 'update', 'restore', 'fetchDeleted', 'permanentDelete', 'index', 'fetchSpecificRecord']
    );
    Route::patch('user/products/{id}', [ProductController::class, 'updateUser']);
    Route::post('user/products', [ProductController::class, 'saveUser']);



    //Users Payments plans
    UtilsRoutes::generateResourceUsersRoutes(
        "users-payment-plans",
        \App\Http\Controllers\UsersPaymentPlanController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
    );


    // ------ ! Tables Can Normal Users Just See ! ------- //
    //Users Payments
    UtilsRoutes::generateResourceUsersRoutes(
        "users-payments",
        \App\Http\Controllers\UsersPaymentController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete'],
    );


    //payment plans
    UtilsRoutes::generateResourceUsersRoutes(
        "payment-plans",
        \App\Http\Controllers\PaymentPlanController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete'],
    );

    //style props categories
    UtilsRoutes::generateResourceUsersRoutes(
        "style-props-categories",
        StylePropsCategoryController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
    );

    //style props
    UtilsRoutes::generateResourceUsersRoutes(
        "style-props",
        StylePropController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
    );

    //style props Values
    UtilsRoutes::generateResourceUsersRoutes(
        "style-prop-values",
        StylePropsValueController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
    );

    //style statuses
    UtilsRoutes::generateResourceUsersRoutes(
        "style-statuses",
        StyleStatusController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
    );

    //style breakpoints
    UtilsRoutes::generateResourceUsersRoutes(
        "style-breakpoints",
        StyleResponsiveBreakpointController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
    );

    //element types categories
    UtilsRoutes::generateResourceUsersRoutes(
        "element-types-categories",
        ElementTypeCategoryController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
    );

    //Element Types
    UtilsRoutes::generateResourceUsersRoutes(
        "element-types",
        ElementTypeController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
    );

    //Element Props
    UtilsRoutes::generateResourceUsersRoutes(
        "element-props",
        ElementPropController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
    );


    //Designs Categories for users
    UtilsRoutes::generateResourceUsersRoutes(
        "design-categories",
        DesignCategoryController::class,
        excepts: ['store', 'update', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
    );



    // * USERS CAN UPDATE and fetch specific ONLY * //
    UtilsRoutes::generateResourceUsersRoutes(
        'users',
        UsersController::class,
        excepts: ['store', 'index', 'destroy', 'restore', 'fetchDeleted', 'permanentDelete']
    );


    // ! ADMIN AUTHENTICATION
    Route::middleware(['is_admin'])->group(function () {
        //Designs Categories for admins

        //USERS
        UtilsRoutes::generateResourceAdminRoutes(
            'users',
            UsersController::class
        );

        //Bank cards
        UtilsRoutes::generateResourceAdminRoutes(
            'bank-cards',
            BankCardController::class
        );

        //style props categories
        UtilsRoutes::generateResourceAdminRoutes(
            "style-props-categories",
            StylePropsCategoryController::class)
        ;

        //style props
        UtilsRoutes::generateResourceAdminRoutes(
            "style-props",
            StylePropController::class
        );

        //style props Values
        UtilsRoutes::generateResourceAdminRoutes(
            "style-prop-values",
            StylePropsValueController::class
        );

        //style statuses
        UtilsRoutes::generateResourceAdminRoutes(
            "style-statuses",
            StyleStatusController::class
        );

        //style breakpoints
        UtilsRoutes::generateResourceAdminRoutes(
            "style-breakpoints",
            StyleResponsiveBreakpointController::class
        );

        //element types categories
        UtilsRoutes::generateResourceAdminRoutes(
            "element-types-categories",
            ElementTypeCategoryController::class
        );

        //Element Types
        UtilsRoutes::generateResourceAdminRoutes(
            "element-types",
            ElementTypeController::class
        );

        //Element Props
        UtilsRoutes::generateResourceAdminRoutes(
            "element-props",
            ElementPropController::class
        );

        //Web Projects
        UtilsRoutes::generateResourceAdminRoutes(
            "web-projects",
            WebProjectController::class
        );

        //Web Projects
        UtilsRoutes::generateResourceAdminRoutes(
            "pages",
            PageController::class
        );

        UtilsRoutes::generateResourceAdminRoutes(
            "design-categories",
            DesignCategoryController::class,
        );

        //Designs
        UtilsRoutes::generateResourceAdminRoutes(
            "designs",
            DesignController::class
        );

        //Designs Prop Values
        UtilsRoutes::generateResourceAdminRoutes(
            "design-prop-values",
            \App\Http\Controllers\DesignedElementsPropValueController::class
        );

        // --- E Commerce Routes --- //
        //Designs
        UtilsRoutes::generateResourceAdminRoutes(
            "products-categories",
            CategoryController::class
        );

        //Products
        UtilsRoutes::generateResourceAdminRoutes(
            "products",
            \App\Http\Controllers\ProductController::class
        );

        //Products Features
        UtilsRoutes::generateResourceAdminRoutes(
            "products-features",
            \App\Http\Controllers\ProductFeatureController::class
        );

        //Products Media
        UtilsRoutes::generateResourceAdminRoutes(
            "products-media",
            \App\Http\Controllers\ProductMediaController::class
        );

        //Product Reviews
        UtilsRoutes::generateResourceAdminRoutes(
            "products-reviews",
            \App\Http\Controllers\ProductReviewController::class
        );

        //Product discounts
        UtilsRoutes::generateResourceAdminRoutes(
            "products-discounts",
            \App\Http\Controllers\ProductsDiscountController::class
        );

        //Product Used Skills
        UtilsRoutes::generateResourceAdminRoutes(
            "products-used-skills",
            \App\Http\Controllers\ProductUsedSkillController::class
        );

        //Orders
        UtilsRoutes::generateResourceAdminRoutes(
            "orders",
            \App\Http\Controllers\OrderController::class
        );

        //Orders items
        UtilsRoutes::generateResourceAdminRoutes(
            "order-items",
            \App\Http\Controllers\OrderItemController::class
        );

        //Payment Plans
        UtilsRoutes::generateResourceAdminRoutes(
            "payment-plans",
            \App\Http\Controllers\PaymentPlanController::class
        );

        //Payment Plan features
        UtilsRoutes::generateResourceAdminRoutes(
            "payment-plan-features",
            \App\Http\Controllers\PaymentPlanFeatureController::class
        );

        //Users Payments
        UtilsRoutes::generateResourceAdminRoutes(
            "users-payments",
            \App\Http\Controllers\UsersPaymentController::class
        );

        //Users Payments plans
        UtilsRoutes::generateResourceAdminRoutes(
            "users-payment-plans",
            \App\Http\Controllers\UsersPaymentPlanController::class
        );

    });
});


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserPlan extends Controller
{
    public static function webProjectCreation() {
        $user = Auth::user();

        $features = [
            'database_control' => $user->checkFeature('database_control'),
            'max_tables' => $user->checkFeature('max_tables'),
            'max_projects' => $user->checkFeature('max_projects'),
            'max_pages_per_project' => $user->checkFeature('max_pages_per_project'),
            'design_control' => $user->checkFeature('design_control'),
            'template_access' => $user->checkFeature('template_access'),
        ];
    }
}

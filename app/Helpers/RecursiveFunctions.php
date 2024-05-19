<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class RecursiveFunctions
{
    public static function flattenWithParentId(array $elements): array
    {
        $flattened = [];

        foreach ($elements as $element) {


            // Prepare the current element without its children
            $currentElement = $element;
            $children = $element['children'] ?? [];

            unset($currentElement['children']);
//            unset($currentElement['styles']);
            Log::info("element: ", (array)$currentElement);
            // Add the current element to the flattened array
            $flattened[] = $currentElement;

            // Recursively add children elements to the flattened array
            if (!empty($children)) {
                $flattened = array_merge($flattened, self::flattenWithParentId($children));
            }
        }

        return $flattened;
    }
}

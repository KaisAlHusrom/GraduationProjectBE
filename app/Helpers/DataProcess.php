<?php

namespace App\Helpers;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class DataProcess
{
    public static function sortData(Builder $query, array $appliedSorts): Builder
    {


        // Apply sorts
        foreach ($appliedSorts as $appliedSort) {
            // Ensure 'sort' key exists before accessing it
            if (!isset($appliedSort['sort'])) {
                continue; // Skip this sort if 'sort' key is missing
            }

            $sort = $appliedSort['sort'];
            $type = $appliedSort['type'];

            // Apply the sort
            $query->orderBy($sort['value'], $type === 'ascending' ? 'asc' : 'desc');
            //TODO: fix the multi sorts problem;
//            Log::info("Sort Rule: ".json_encode($appliedSort));
//
//            Log::info("SortedData: ".json_encode($query->get()));
        }

        return $query;
    }

    public static function filterData(Builder $query, array $appliedFilters): Builder
    {

        // Apply filters
        foreach ($appliedFilters as $appliedFilter) {
            // Ensure 'filter' key exists before accessing it
            if (!isset($appliedFilter['filter'])) {
                continue; // Skip this filter if 'filter' key is missing
            }

            $filter = $appliedFilter['filter'];
            $process = $appliedFilter['process'];
            $value = $appliedFilter['value'];
            $startDate = $appliedFilter['startDate'];
            $endDate = $appliedFilter['endDate'];
            $period = $appliedFilter['period'];
            $relationValue = $appliedFilter['relationValue'] ?? null;

            // Apply the appropriate condition based on filter type
            switch ($filter['type']) {
                case 'string':
                case 'text':
                case 'email':
                case 'mobileNumber':
                case 'password':
                case 'pk':
                    $query->where($filter['value'], 'LIKE', '%' . $value . '%');
                    break;
                case 'bool':
                    $convert = ($value == "true") ? 1 : 0;
                    $query->where($filter['value'], '=', $convert);
                    break;
                case 'date':
                case 'dateTime':
                    switch ($process) {
                        case '=':
                            $query->whereDate($filter['value'], '=', $value);
                            break;
                        case '>':
                            $query->whereDate($filter['value'], '>', $value);
                            break;
                        case '<':
                            $query->whereDate($filter['value'], '<', $value);
                            break;
                        case '>=':
                            $query->whereDate($filter['value'], '>=', $value);
                            break;
                        case '<=':
                            $query->whereDate($filter['value'], '<=', $value);
                            break;
                        case 'between':
                            $query->whereBetween($filter['value'], [$startDate, $endDate]);
                            break;
                        case 'relative to today':
                            // Adjust this part based on your specific date filtering logic
                            break;
                    }
                    break;
                case 'int':
                case 'decimal':
                    $value = (int)$value;
                    switch ($process) {
                        case '=':
                            $query->where($filter['value'], '=', $value);
                            break;
                        case '>':
                            $query->where($filter['value'], '>', $value);
                            break;
                        case '<':
                            $query->where($filter['value'], '<', $value);
                            break;
                        case '>=':
                            $query->where($filter['value'], '>=', $value);
                            break;
                        case '<=':
                            $query->where($filter['value'], '<=', $value);
                            break;
                    }
                    break;
                case 'one-to-many':
                    // Apply relational conditions
                    if ($relationValue) {
                        $query->whereHas($filter['value'], function ($query) use ($relationValue) {
                            $query->where(function ($query) use ($relationValue) {
                                foreach ($relationValue as $relationItem) {
                                    // Check for any of the specified relation IDs
                                    $query->orWhere('id', '=', $relationItem['id']);
                                }
                            });
                        });
                    }
                    break;
                    //TODO: fix many to many state
                case 'many-to-many':
                    // Apply relational conditions
                    if ($relationValue) {
                        $query->whereHas($filter['value'], function ($query) use ($relationValue) {
                            foreach ($relationValue as $relationItem) {
                                // Check for any of the specified relation IDs
                                $query->orWhere('id', '=', $relationItem['id']);
                            }
                        });
                    }
                    break;
                    //todo: complete many to one
                case 'many-to-one':
                    if ($relationValue) {
                        $isEndWithId = str_ends_with($filter['value'], 'id');
                        if($isEndWithId) {
                            $query->where($filter['value'], $process, $relationValue);
                        } else {
                            $relatedModel = $query->getRelation($filter['value'])->getRelated();
                            $query->where($relatedModel->getForeignKey(), $process, $relationValue);
                        }

                    }
                    break;
            }
        }
        return $query;
    }

    public static function searchData(Builder $query, $searchQuery) : Builder
    {
        if ($searchQuery) {
            $columnName = $searchQuery["columnName"];
            $searchTerm = str_replace(' ', '', strtolower($searchQuery["searchTerm"]));
            $query->whereRaw("LOWER(REPLACE($columnName, ' ', '')) LIKE ?", ['%'.$searchTerm.'%']);
        }


        return $query;
    }
}

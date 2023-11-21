<?php

namespace App\Query\Employee;

use App\Filter\VacationRequests\MonthlyShiftFilter;
use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;

class GetMonthlyShiftQuery
{
    public function getMonthlyData($filter = null)
    {
        $allData = $this->getAllRecords();
        $absentIdsGroup = $this->getAbsentIds();
        $result = $this->getRes($allData, $absentIdsGroup);

        if ($filter && property_exists($filter, 'duration')) {
            $duration = $filter->duration;

            if ($duration === 1) {
                $filteredData = array_filter($result['data'], function ($item) {
                    return isset($item['relatedAbsent']) && count($item['relatedAbsent']) === 1;
                });
                return ['data' => array_values($filteredData)];
            } elseif ($duration === 2) {
                $filteredData = array_filter($result['data'], function ($item) {
                    return isset($item['relatedAbsent']) && count($item['relatedAbsent']) > 1;
                });
                return ['data' => array_values($filteredData)];
            }
        }

        return $result;
    }

    private function getAllRecords()
    {
        $allRecords = Attendance::query()->where('user_id', Auth::id())->select('id', 'date', 'status', 'login_time', 'logout_time')->get();

        return $allRecords;
    }

    private function getAbsentIds()
    {
        $absentDays = Attendance::where('user_id', Auth::id())->where('status', '0')->pluck('date')->toArray();

        $absentIds = [];

        foreach ($absentDays as $day) {
            $absentIds[] = date('j', strtotime($day));
        }


        return $this->getSequentialNumberGroups(array_values($absentIds));
    }


    function getSequentialNumberGroups($array)
    {
        $count = count($array);
        $groups = [];
        $currentGroup = [];
        for ($i = 0; $i < $count; $i++) {
            if (!empty($currentGroup) && $array[$i] != end($currentGroup) + 1) {
                $groups[] = $currentGroup;
                $currentGroup = [];
            }
            $currentGroup[] = $array[$i];
        }
        $groups[] = $currentGroup;

        $groups = array_filter($groups, function ($group) {
            return count($group) >= 1;
        });

        return $groups;
    }

    private function getRes($allRecords, $absentIdsGroup)
    {
        $resData = [];
        $relatedAbsentSets = [];
        foreach ($allRecords as $record) {
            $relatedAbsentSet = null;

            if ($record->status == 0) {
                foreach ($absentIdsGroup as $group) {
                    $groupDayNumbers = array_map(function ($day) {
                        return substr($day, -2);
                    }, $group);

                    $recordDayNumber = substr($record->date, -2);
                    if (in_array($recordDayNumber, $groupDayNumbers)) {
                        $relatedAbsentSet = $group;
                        break;
                    }
                }

                if (!empty($relatedAbsentSet)) {
                    $relatedAbsentSetKey = implode(',', $relatedAbsentSet);
                    if (in_array($relatedAbsentSetKey, $relatedAbsentSets)) {
                        $record->relatedAbsent = null;
                    } else {
                        $relatedAbsentSets[] = $relatedAbsentSetKey;

                        $relatedRecords = Attendance::where('user_id', Auth::id())
                            ->whereRaw('DAY(date) IN (' . implode(',', $relatedAbsentSet) . ')')
                            ->select('id', 'date', 'status')
                            ->get();

                        $record->relatedAbsent = $relatedRecords;
                    }
                } else {
                    $record->relatedAbsent = null;
                }
            }
            if ($record->status == 0 && $record->relatedAbsent === null) {
                continue;
            }
            $resData[] = $record;
        }
        return ['data' => $resData];
    }
}
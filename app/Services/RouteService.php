<?php
/**
 * Created by PhpStorm.
 * User: Дима
 * Date: 23.07.2019
 * Time: 19:42
 */

namespace App\Services;


class RouteService
{

    public function sortRoute($startPoint, &$route)
    {
        $flag = true;
        $sortRoute = [$startPoint];
        $breakPoints = [];
        while ($flag) {
            foreach ($route as $key => $point) {
                if($point['board'] == $startPoint['board'] && $point['departure']['date'] == $startPoint['departure']['date']) {
                    unset($route[$key]);
                    continue;
                }
                if($point['board'] == $startPoint['Off']) {
                    $sortRoute[] = $point;
                    unset($route[$key]);
                }
                $startPoint = $point;
            }
            if (count($route) != 0) {
                $nextPoint = $this->getStartPoint($route, $startPoint);
                list($recursiveSort, $recursiveBreakPoint) = static::sortRoute($nextPoint, $route);
                if($nextPoint['board'] != end($sortRoute)['Off']) {
                    $breakPoints[] = end($sortRoute);
                }
                $sortRoute = array_merge($sortRoute, $recursiveSort);
                $breakPoints = array_merge($breakPoints, $recursiveBreakPoint);
            }
            $flag = false;
        }
        return [$sortRoute, $breakPoints];
    }


    public function getStartPoint($route, $startPointCandidate = null)
    {
        $startPoint = null;
        foreach ($route as $key => $point) {
            if(!is_null($startPointCandidate) && $startPointCandidate['Off'] == $point['board']) {
                return $point;
            }
            if (is_null($startPoint) || (strtotime($startPoint['departure']['date']) > strtotime($point['departure']['date']) && strtotime($startPoint['departure']['time']) > strtotime($point['departure']['time']))) {
                $startPoint = $point;
            }
        }
        return $startPoint;
    }

    public function getTripInfo($route)
    {
        list($sortRoute, $breakPoints) = $this->sortRoute($this->getStartPoint($route), $route);
        $tripStr = [];
        $spendTime = new \DateTime('00:00:00');
        foreach ($sortRoute as $arr) {
            if(end($tripStr) != $arr['board']) {
                $tripStr[] = $arr['board'];
            }
            $tripStr[] = $arr['Off'];
            $departure = $arr['departure']['date'] . ' ' . $arr['departure']['time'];
            $arrival = $arr['arrival']['date'] . ' ' . $arr['arrival']['time'];
            $departure = new \DateTime($departure);
            $arrival = new \DateTime($arrival);
            $time = $departure->diff($arrival);
            $spendTime->add($time);
        }
        $breakPoints = array_column($breakPoints, 'Off');
        return [implode($tripStr, '------>'), $breakPoints, $spendTime->format("H:i")];
    }

}
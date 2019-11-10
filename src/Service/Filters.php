<?php

namespace Tuc0w\TimeularPublicApiBundle\Service;

use Tuc0w\TimeularPublicApiBundle\Service\Client as Timeular;

class Filters {
    const FILTER_ACTIVITIES = 'filterByActivities';
    const FILTER_MENTIONS = 'filterByMentions';
    const FILTER_TAGS = 'filterByTags';

    private $filters = [];

    private $timeular;

    /**
     * Filters constructor.
     */
    public function __construct(Timeular $timeular) {
        $this->timeular = $timeular;
    }

    /**
     * @param $filters
     * @param $arrayToFilter
     *
     * @return mixed
     */
    public function applyFilters($filters, $arrayToFilter) {
        if (null === $filters) {
            return $arrayToFilter;
        }

        $this->configureFilters($filters);

        return $this->filter($arrayToFilter);
    }

    /**
     * @usage
     * Declaration of filters should be done like this:
     *  [
     *      FILTER_ACTIVITIES => [
     *          'Coding',
     *          'Brainstorming',
     *          'Meeting'
     *      ]
     *  ]
     *
     * @param $filters
     */
    private function configureFilters($filters) {
        foreach ($filters as $filterMethod => $filterArray) {
            $this->{$filterMethod}($filterArray);
        }
    }

    private function filter($arrayToFilter) {
        // filter logic..

        return $arrayToFilter;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param $filterArray
     */
    private function filterByActivities($filterArray) {
        /**
         *  {
         *      "activities": [
         *          {
         *              "id": "343890",
         *              "name": "Coden",
         *              "color": "#673bb5",
         *              "integration": "zei",
         *              "deviceSide": 7
         *          }
         *      ]
         *  }.
         */
        $activities = $this->timeular->getActivities()->activities;
        foreach ($activities as $activity) {
            if (in_array($activity->name, $filterArray)) {
                if (array_key_exists('FILTER_ACTIVITIES', $this->filters)) {
                    $this->filters['FILTER_ACTIVITIES'][] = $activity->id;
                } else {
                    $this->filters['FILTER_ACTIVITIES'] = [];
                    $this->filters['FILTER_ACTIVITIES'][] = $activity->id;
                }
            }
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param $filterArray
     */
    private function filterByMentions($filterArray) {
        // filter logic..
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param $filterArray
     */
    private function filterByTags($filterArray) {
        // filter logic..
    }
}

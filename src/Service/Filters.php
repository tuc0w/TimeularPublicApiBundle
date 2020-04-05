<?php

namespace Tuc0w\TimeularPublicApiBundle\Service;

use Tuc0w\TimeularPublicApiBundle\Service\Client as Timeular;

class Filters {
    const FILTER_ACTIVITIES = 'filterByActivities';
    const FILTER_MENTIONS = 'filterByMentions';
    const FILTER_TAGS = 'filterByTags';

    const ENTRY_STRUCTURE = [
        'FILTER_ACTIVITIES' => 'activity',
        'FILTER_MENTIONS' => 'note->mentions',
        'FILTER_TAGS' => 'note->tags',
    ];

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
        if ($filters === null) {
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
        $filteredArray = [];
        foreach ($arrayToFilter as $entry) {
            foreach ($this->filters as $filterType => $filterArray) {
                $structure = self::ENTRY_STRUCTURE[$filterType];
                foreach ($filterArray as $filter) {
                    if (is_array($entry[$structure])) {
                        foreach ($entry[$structure] as $tagOrMention) {
                            // if (array_key_exists('key', $))
                            if ($tagOrMention['key'] == $filter) {
                                if (!array_key_exists($entry->id, $filteredArray)) {
                                    $filteredArray[$entry->id] = $entry;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $filteredArray;
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
        $this->setFilters(
            'FILTER_ACTIVITIES',
            $filterArray,
            $activities,
            'id'
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param $filterArray
     */
    private function filterByMentions($filterArray) {
        $mentions = $this->timeular->getTagsAndMentions()->mentions;
        $this->setFilters(
            'FILTER_MENTIONS',
            $filterArray,
            $mentions,
            'key'
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param $filterArray
     */
    private function filterByTags($filterArray) {
        $tags = $this->timeular->getTagsAndMentions()->tags;
        $this->setFilters(
            'FILTER_TAGS',
            $filterArray,
            $tags,
            'key'
        );
    }

    private function setFilters(string $type, array $filterArray, array $filterSource, string $sourceKey) {
        if (!array_key_exists($type, $this->filters)) {
            $this->filters[$type] = [];
        }

        foreach ($filterSource as $source) {
            if (in_array($source->name, $filterArray)) {
                $this->filters[$type][] = $source->$sourceKey;
            }
        }
    }
}

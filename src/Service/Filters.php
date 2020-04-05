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

    /**
     * filter.
     *
     * @return array
     */
    private function filter(array $timeEntries): array {
        $filteredTimeEntries = [];

        foreach ($timeEntries as $entry) {
            foreach ($this->filters as $filterType => $filterArray) {
                $structure = self::ENTRY_STRUCTURE[$filterType];
                $properties = explode('->', $structure);
                $hasProperty = false;

                foreach ($properties as $property) {
                    if (property_exists($entry, $property)) {
                        $hasProperty = true;
                    }
                }

                if ($hasProperty) {
                    if (count($properties) > 1) {
                        $structureObject = $entry;
                        foreach ($properties as $property) {
                            $structureObject = $structureObject->$property;
                        }
                    } else {
                        $structureObject = $entry->{$structure};
                    }

                    foreach ($filterArray as $filter) {
                        // if we filter by mentions or tags it will be collection of objects
                        if (is_array($structureObject)) {
                            foreach ($structureObject as $item) {
                                if ($item->key == $filter) {
                                    if (!array_key_exists($entry->id, $filteredTimeEntries)) {
                                        $filteredTimeEntries[$entry->id] = $entry;
                                    }
                                }
                            }
                            // if we filter by activities it will only be one object
                        } else {
                            if ($structureObject->id == $filter) {
                                if (!array_key_exists($entry->id, $filteredTimeEntries)) {
                                    $filteredTimeEntries[$entry->id] = $entry;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $filteredTimeEntries;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param $filterArray
     *
     * @return void
     */
    private function filterByActivities($filterArray): void {
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
            'id',
            'name'
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param $filterArray
     *
     * @return void
     */
    private function filterByMentions($filterArray): void {
        $mentions = $this->timeular->getTagsAndMentions()->mentions;
        $this->setFilters(
            'FILTER_MENTIONS',
            $filterArray,
            $mentions,
            'key',
            'label'
        );
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     *
     * @param $filterArray
     *
     * @return void
     */
    private function filterByTags($filterArray): void {
        $tags = $this->timeular->getTagsAndMentions()->tags;
        $this->setFilters(
            'FILTER_TAGS',
            $filterArray,
            $tags,
            'key',
            'label'
        );
    }

    /**
     * setFilters.
     *
     * @return void
     */
    private function setFilters(string $type, array $filterArray, array $filterSource, string $sourceKey, string $sourceFilter): void {
        if (!array_key_exists($type, $this->filters)) {
            $this->filters[$type] = [];
        }

        foreach ($filterSource as $source) {
            if (in_array($source->$sourceFilter, $filterArray)) {
                $this->filters[$type][] = $source->$sourceKey;
            }
        }
    }
}

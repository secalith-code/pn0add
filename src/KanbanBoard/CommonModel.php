<?php

namespace App\KanbanBoard;

use App\Utilities;

/**
 *  Common functions for Issues and Milestones
 */
abstract class CommonModel
{

    /**
     * @param $items
     *
     * @return array|null
     */
    public function fetchAll($items): ?array
    {
        if (! empty($items)) {
            $data = [];

            foreach ($items as $itm) {
                $title = htmlspecialchars($itm['title']);
                // return array will be sorted alphabetically by the items title.
                $itm['title'] = (string) $title;
                // fetch single issue
                $data[$title] = $this->fetchOne($itm);
            }

            return Utilities::sortArrayByKey($data, 'title');
        }

        return null;
    }

    /**
     *  Hydrates Item data
     *
     * @param  $item
     * @return array
     */
    abstract public function fetchOne(array $item): array;
}

<?php

namespace App\KanbanBoard;

class CommonModel
{

    public function fetchAll($items): ?array
    {
        if( ! empty($items)) {
            $data = [];

            foreach($items as $itm) {
                $title=htmlspecialchars($itm['title']);
                // return array will be sorted alphabetically by the items title.
                $itm['title'] = $title;
                // fetch single issue
                $data[$title] = $this->fetchOne($itm);
            }

            return Utilities::sortArrayByKey($data,'title');
        }

        return null;
    }
}
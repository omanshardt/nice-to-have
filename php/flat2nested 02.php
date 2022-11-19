<?php
    // This is the second of two implementations of the flat2Nested function.
    // For detailed comments please see the php-version 01 of this function.

    function flat2Nested2(array $data, string $idName = 'id', string $parentIdName = 'parent_id', string $rootIdentifier = null, string $collectionName = 'children', bool $addParentReference = false) {
        $result = [];

        $lvl = 0;
        function fold(&$col, array &$data, string $idName, string $parentIdName, string $parentIdentifier, string $collectionName, bool $addParentReference) {
            global $lvl;
            $lvl++;
            $col[$collectionName] = [];
            foreach($data as $key => &$row) {
                if ($row[$parentIdName] === $parentIdentifier) {
                    if($lvl > 1 && $addParentReference) {
                        $row['_parent'] = &$col;
                        $row['oxtitle'] = $row['oxtitle'].' |'.$lvl;
                    }
                    $col[$collectionName][] = &$row;
                    unset($data[$key]);
                    fold($row, $data, $idName, $parentIdName, $row[$idName], $collectionName, $addParentReference);
                }
            }
            $lvl--;
        }

        fold($result, $data, $idName, $parentIdName, $rootIdentifier, $collectionName, $addParentReference);
        return $result[$collectionName];
    }
?>
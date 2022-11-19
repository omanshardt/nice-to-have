<?php

    // This is the first of two implementations of the flat2Nested function.

    /**
     * @param $data, this is a numeric array that contains an arbitrary number of items (most likely assoziative arrays) with an identical structure or at least with arrays that all have an id and a parent id (could be considered as foreign key)
     * @param $idName, this is the identifier of the id key of the inner item arrays.
     * @param $parentIdName, this is the name of the parent id key of the inner item arrays.
     * @param $rootIdentifier, this is the value for the parent id of the root elements
     * @param $collectionName, to nest the inner arrays by the assignment of id and parent id we need to crate an additional property (that itself is an array) to hold the descendants of each item.
     * @param $addParentReference, This determines if the each element should contain a reference to it's parent. This would be assigned to a property called _parent (This is not changeable).
     * @return array, this method returns an array (a collection) with all root arrays and their nested arrays.
     */
    function flat2Nested(array $data, string $idName = 'id', string $parentIdName = 'parent_id', string $rootIdentifier = null, string $collectionName = 'children', bool $addParentReference = false) {
        $result = []; // This will be the returned array and is going to be build up within this method

        // 2. This method iterates repeatedly over the provided data.
        /**
         * @param $p
         * @param array $data, see wrapping method's description.
         * @param string $idName, see wrapping method's description.
         * @param string $parentIdName, see wrapping method's description.
         * @param string $parentIdentifier, see $rootIdentifier of wrapping method's description.
         * @param string $collectionName, The identifier that is used for the collection of sub-elements. (On the first-level-elements this would be the top-level-result array.)
         * @param array|null $itm, The currently processed element that is passed to the next level.
         * @param bool $addParentReference, see wrapping method's description.
         * @return void, As we work with assignments by reference, we don't have a return value here.
         *
         * This function transforms a flat list of items that are organized in perent-children-relations into an object-tree by assigning an parent' sobject id to an object id.
         */
        function iterate(&$p, array &$data, string $idName, string $parentIdName, string $parentIdentifier, string $collectionName, array $itm = null, bool $addParentReference) {
            foreach($data as $key => &$val) {
                if ($val[$parentIdName] === $parentIdentifier) {
                    // 3. Whenever an item is found where the parent_id equals the id of the parent or the root-id value
                    // this item gets a new property with an array that is for holding the descendants.
                    if($itm && $addParentReference) {
                        $val['_parent'] = &$itm;
                    }
                    $val[$collectionName] = [];
                    // 4. The found item is going to be deleted from the provided data structure
                    // so with each found item the provided data array is going to be smaller, so the iteration is getting faster.
                    // This reduces execution time by approximately 50 percent.
                    unset($data[$key]);
                    // 5. To assign a child item to it's parent, the fold-method is called
                    fold($p, $val, $data, $idName, $parentIdName, $collectionName, $addParentReference);
                }
            }
        }

        // 6. This method is responsible for the assignment of the children to their parents
        /**
         * @param array $parent
         * @param array $itm
         * @param array $data, see wrapping method's description.
         * @param string $idName, see wrapping method's description.
         * @param string $parentIdName, see wrapping method's description.
         * @param string $parentIdentifier, see $rootIdentifier of wrapping method's description.
         * @param string $collectionName, The identifier that is used for the collection of sub-elements. (On the first-level-elements this would be the top-level-result array.)
         * @param bool $addParentReference, see wrapping method's description.
         * @return void, As we work with assignments by reference, we don't have a return value here.
         */
        function fold(array &$parent, array $itm, array &$data, string $idName, string $parentIdName, string $collectionName, bool $addParentReference) {
            $parent[] = &$itm;
            // 7. After the child-parent-assignment we again call the iterate method.
            // From within the fold method we provide the current child item's child-collection property as the parent to the iterate-methods.
            // Instead of the rootIdentifier we pass over the current item's id as the parentIdentifier.
            // This leads into a recursion where the whole original data structure is re-assembled to a nested data structure.
            iterate($itm[$collectionName], $data, $idName, $parentIdName, $itm[$idName], $collectionName, $itm, $addParentReference);
        };

        // 1. We call the iterate method and pass the result array in, that acts as the new root element
        iterate($result, $data, $idName, $parentIdName, $rootIdentifier, $collectionName, null, $addParentReference);
        return $result;
    }
?>
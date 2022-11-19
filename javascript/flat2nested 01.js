// This is the first of two implementations of the flat2Nested function. This implementation seems to be a tiny bit faster than the other one.
// For detailed comments please see the php-version of this function.

function flat2Nested(data, idName = 'id', parentIdName = 'parent_id', rootIdentifier = null, collectionName = 'children', addParentReference = false) {
    let result = [];
    function iterate(p, data, idName, parentIdName, parentIdentifier, collectionName, itm = null) {
        data.forEach(function(val, index, arr) {
            if (val[parentIdName] === parentIdentifier) {
                if(itm && addParentReference) {
                    val['myParent'] = itm;
                }
                val[collectionName] = [];
                // Deleting the element reduces exection time by approximately 50%
                delete data[index];

                fold(p, val, data, idName, parentIdName, parentIdentifier, collectionName);
            }
        });
    }
    function fold(myParent, itm, data, idName, parentIdName, parentIdentifier, collectionName) {
        myParent.push(itm);
        iterate(itm[collectionName], data, idName, parentIdName, itm[idName], collectionName, itm);
    };
    iterate(result, data, idName, parentIdName, rootIdentifier, collectionName);
    return result;
}

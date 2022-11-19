// This is the second of two implementations of the flat2Nested function. This implementation seems to be a tiny bit slower than the other one.
// For detailed comments please see the php-version 01 of this function.

function flat2Nested2(data, idName = 'id', parentIdName = 'parent_id', rootIdentifier = null, collectionName = 'children', addParentReference = false) {
    let result = [];
    let lvl = 0;
    function fold(col, data, idName, parentIdName, parentIdentifier, collectionName, addParentReference) {
        lvl++;
        col[collectionName] = [];
        data.forEach(function(row, index, arr) {
            if (row[parentIdName] === parentIdentifier) {
                if(lvl > 1 && addParentReference) {
                    row['_parent'] = col;
                }
                col[collectionName].push(row);
                delete data[index];
                fold(row, data, idName, parentIdName, row[idName], collectionName, addParentReference);
            }
        });
        lvl--;
    }
    fold(result, data, idName, parentIdName, rootIdentifier, collectionName, addParentReference);
    return result[collectionName];
}

/**
ARRAY EXTENSIONS
by Caio Chassot (http://v2studio.com/k/code/)
*/

Object.extend(Array,{

/** 
 * Searches Array for <b>value</b>. 
 * returns the index of the first item 
 * which matches <b>value</b>, or -1 if not found.
 * searching starts at index 0, or at <b>start</b>, if specified.
 *
 * Here are the rules for an item to match <b>value</b>
 * if strict is false or not specified (default):
 * if <b>value</b> is a:
 *  # <b>function</b>    -> <b>value(item)</b> must be true
 *  # <b>RegExp</b>      -> <b>value.test(item)</b> must be true
 *  # anything else -> <b>item == value</b> must be true
 * @param value the value (function, regexp) to search
 * @param start where to start the search
 * @param strict use strict comparison (===) for everything
 */
indexOf : function(list, value, start, strict) {
    start = start || 0;
    for (var i=start; i<list.length; i++) {
        var item = list[i];
        if (strict            ? item === value   :
            isRegexp(value)   ? value.test(item) :
            isFunction(value) ? value(item)      :
            item == value)
            return i;
    }
    return -1;
},

/** 
 * searches Array for <b>value</b> returns the first matched item, or null if not found
 * Parameters work the same as indexOf
 * @see #indexOf
 */
find : function(list, value, start, strict) {
    var i = Array.indexOf(list, value, start, strict);
    if (i != -1) return list[i];
    return null;
},

/** 
 * aliases: has, include
 * returns true if <b>value</b> is found in Array, otherwise false;
 * relies on indexOf, see its doc for details on <b>value</b> and <b>strict</b>
 * @see #indexOf
 */
contains : function(list,value,strict) {
    return Array.indexOf(list,value,0,strict) !== -1;
},

/** 
 * counts occurences of <b>value</b> in Array
 * relies on indexOf, see its doc for details on <b>value</b> and <b>strict</b>
 * @see #indexOf
 */ 
count : function(list, value, strict) {
    var pos, start = 0, count = 0;
    while ((pos = Array.indexOf(list, value, start, strict)) !== -1) {
        start = pos + 1;
        count++;
    }
    return count;
},

/** 
 * if <b>all</b> is false or not provied:
 *        removes first occurence of <b>value</b> from Array
 *   if <b>all</b> is provided and true:
 *       removes all occurences of <b>value</b> from Array
 *   returns the array
 *   relies on indexOf, see its doc for details on <b>value</b> and <b>strict</b>
 * @see #indexOf
 */
remove : function(list, value,all,strict) {
    while (Array.contains(list,value,strict)) {
        list.splice(Array.indexOf(list, value,0,strict),1);
        if (!all) break;
    }
    return list;
}
});


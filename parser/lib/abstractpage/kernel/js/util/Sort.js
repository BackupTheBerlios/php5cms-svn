/*
+----------------------------------------------------------------------+
|This program is free software; you can redistribute it and/or modify  |
|it under the terms of the GNU General Public License as published by  |
|the Free Software Foundation; either version 2 of the License, or     |
|(at your option) any later version.                                   |
|                                                                      |
|This program is distributed in the hope that it will be useful,       |
|but WITHOUT ANY WARRANTY; without even the implied warranty of        |
|MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
|GNU General Public License for more details.                          |
|                                                                      |
|You should have received a copy of the GNU General Public License     |
|along with this program; if not, write to the Free Software           |
|Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.             |
+----------------------------------------------------------------------+
|Authors: Markus Nix <mnix@docuverse.de>                               |
+----------------------------------------------------------------------+
*/


/**
 * @package util
 */
 
/**
 * Constructor
 *
 * @access public
 */
Sort = function()
{
	this.Base = Base;
	this.Base();
};


Sort.prototype = new Base();
Sort.prototype.constructor = Sort;
Sort.superclass = Base.prototype;

/**
 * @access public
 * @static
 */
Sort.trace = "";

/**
 * Selection sort maintains a growing 'front' section of the array
 * which is (i) sorted and (ii) less than the remainder of the array.
 * At each step, the smallest element in the 'remainder' is selected
 * and moved to enlarge the 'front' section.
 *
 * @access public
 * @static
 */
Sort.selection = function( inputArray )
{
	var i, j;
	var N = inputArray.length - 1;
	
	Sort.trace = "";
	
	for ( i = 0; i < N; i++ )
	{
		var smallest = i;
		
		for ( j = i + 1; j <= N; j++ )
		{
			if ( inputArray[j]-0 < inputArray[smallest] )
				smallest = j;
		}
		
		var temp = inputArray[i];
		inputArray[i] = inputArray[smallest]; 
		inputArray[smallest] = temp; // swap

		// trace the steps
		Sort.trace += '[';
		
		for ( j = 0; j < i + 1; j++ )
			Sort.trace += inputArray[j] + ' ';
			
		Sort.trace += '] ';
		
		for ( j = i + 1; j <= N; j++ )
			Sort.trace += inputArray[j] + ' ';
		
		Sort.trace += '\n';
	}
	
	return inputArray;
};

/**
 * Insertion sort maintains a sorted front section of the
 * array [1..i-1]. At each stage, a[i] is inserted at the
 * appropriate point in this sorted section and i is increased.
 *
 * @access public
 * @static
 */
Sort.insertion = function( inputArray )
{
	var i, j;
	var N = inputArray.length - 1;
	
	Sort.trace = "";
	
	// sentinel
	// inputArray[0] = -Number.MAX_VALUE;
	
	for ( i = 1; i <= N; i++ )
	{
		var ai = inputArray[i];
		j = i - 1;
		
		while ( inputArray[j] - 0 > ai )
		{
			inputArray[j+1] = inputArray[j];
			j--;
		}
		
		inputArray[j+1] = ai;

		// trace the steps
		Sort.trace += '[';
		
		for ( j = 0; j < i + 1; j++ )
			Sort.trace += inputArray[j] + ' ';
			
 		Sort.trace += '] ';
		
		for ( j = i + 1; j <= N; j++ )
			Sort.trace += inputArray[j] + ' ';
			
		Sort.trace += '\n';
	}
	
	return inputArray;
};

/**
 * The idea is to scan through the array, swapping pairs of elements,
 * until the array is sorted. On the first pass, we compare and order
 * all n-1 adjacent pairs, which has the effect of bubbling the
 * largest element to the top. In the next pass, we do not have to
 * check the last element, so we compare only n-2 pairs. This brings
 * the second highest element to its place. We repeat until there is
 * only one pair left to check.
 *
 * @access public
 * @static
 */
Sort.bubblesort = function( inputArray )
{
	var start = 0;
	var end = inputArray.length - 1;
	
	for ( var i = end - 1; i >= start;  i--)
	{
		for ( var j = start; j <= i; j++ )
		{
			if ( inputArray[j+1] < inputArray[j] )
			{
				var tempValue = inputArray[j];
				
				inputArray[j]   = inputArray[j+1];
				inputArray[j+1] = tempValue;
			}
		}
	}
	
	// No trace here...
	Sort.trace = "No trace information.";
	
	return inputArray;
};

/**
 * Quick sort partitions the array into two sections, the first
 * of "small" elements and the second of "large" elements. It
 * then sorts the small and large elements separately.
 *
 * @access public
 * @static
 */
Sort.quicksort = function( inputArray )
{
	Sort.trace = "";
	Sort._quick( inputArray, 0, inputArray.length - 1, inputArray.length - 1 );
	
	return inputArray;
};

/**
 * Merge sort divides the array into two halves which are sorted
 * recursively and then merged to form a sorted whole. The array
 * is divided into equal sized parts (up to truncation) so there
 * are log2(N) levels of recursion.
 *
 * @access public
 * @static
 */
Sort.mergesort = function( inputArray )
{
	var i;
	var b = new Array();
	var N = inputArray.length - 1;
	
	Sort.trace = "";
	
	for ( i = 0; i <= N; i++ )
		b[i] = inputArray[i];

	Sort._merge( b, 0, N, inputArray, N );
	return inputArray;
};


// private methods

/**
 * @access private
 * @static
 */
Sort._quick = function( a, lo, hi, N )
{
	// at least 2 elements
	if ( hi > lo  )
	{
		var left = lo,
		right  = hi,
		median = a[lo];  // partition a[lo..hi]
		
		// a[lo..left-1] <= median and a[right+1..hi] >= median
		while ( right >= left )
		{
			while ( a[left] < median )
				left++;

			while ( a[right] > median )
				right--;

			if ( left > right )
				break;

			var temp = a[left];
			a[left]  = a[right];
			a[right] = temp; //swap
			
			left++;
			right--
		}

		// trace the steps
		var i, line = "";
		
		for ( i = 0; i <= N; i++ )
		{
			if ( i == lo )
				line += '[';
			
			line += a[i];
			
			if ( i == hi )
				line += '] ';
			else
				line += ' ';
		}
		
		Sort.trace += line + '\n';
	   
		Sort._quick( a,   lo, right, N ); // sort the small elements divide and conquer
		Sort._quick( a, left,    hi, N );
	}
};

/**
 * @access private
 * @static
 */
Sort._merge = function( inA, lo, hi, opA,  N )
{
	var i, j, k, mid;

	// at least 2 elements
	if ( hi - 0 > lo )
	{
		var mid = Math.floor( ( lo + hi ) / 2 ); // lo <= mid < hi
		Sort._merge( opA,    lo, mid, inA, N );
		Sort._merge( opA, mid+1,  hi, inA, N );

		i = lo;
		j = mid + 1;
		k = lo;

		while( true )
		{
			if ( inA[i] - 0 <= inA[j] ) // smaller?
			{
				opA[k] = inA[i];
				
				i++;
				k++;
				
				if ( i - 0 > mid )
				{
					for ( ; j <= hi;  j++ )
					{
						opA[k] = inA[j];
						k++;
					}
				
					break;
				}
			}
			else
			{
				opA[k] = inA[j];
				
				j++;
				k++;
				
				if ( j - 0 > hi )
				{
					for ( ; i <= mid; i++)
					{
						opA[k] = inA[i];
						k++;
					}
					
					break;
				}
			}
		}
	}
	
	// trace the steps
	for ( i = 0; i <= N; i++ )
	{
		if ( i == lo )
			Sort.trace += '[';
		
		Sort.trace += opA[i];
		
		if ( i == hi )
			Sort.trace += ']';
		else
			Sort.trace += ' ';
	}

	Sort.trace += '\n';
};

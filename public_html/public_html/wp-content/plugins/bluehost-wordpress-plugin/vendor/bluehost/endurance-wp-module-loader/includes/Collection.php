<?php

/**
 * Class Endurance_Collection
 *
 * A PHP 5.2 compatible, simplified Laravel Collection
 * @link https://github.com/illuminate/support/blob/master/Collection.php
 * @link https://laravel.com/docs/5.6/collections
 */
class Endurance_Collection implements ArrayAccess, Countable, IteratorAggregate {

	/**
	 * The items contained in the collection.
	 *
	 * @var array
	 */
	protected $items = array();

	/**
	 * Collection constructor.
	 *
	 * @param array $items
	 */
	public function __construct( array $items = array() ) {
		$this->items = $items;
	}

	/**
	 * Static method for creating a collection.
	 *
	 * @param array $items
	 *
	 * @return self
	 */
	public static function make( array $items = array() ) {
		return new self( $items );
	}

	/**
	 * Get all of the items in the collection.
	 *
	 * @return array
	 */
	public function all() {
		return $this->items;
	}

	/**
	 * Push all of the given items onto the collection.
	 *
	 * @param array $items
	 *
	 * @return $this
	 */
	public function concat( array $items ) {
		$collection = new self( $this->all() );
		foreach ( $items as $item ) {
			$collection->push( $item );
		}

		return $collection;
	}

	/**
	 * Determine if an item exists in the collection.
	 *
	 * @param  mixed $key
	 *
	 * @param bool $strict
	 *
	 * @return bool
	 */
	public function contains( $key, $strict = true ) {
		return in_array( $key, $this->items, $strict );
	}

	/**
	 * Get the total number of items in the collection.
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->items );
	}

	/**
	 * Get the items in the collection that are not present in the given items.
	 *
	 * @param  array $items
	 *
	 * @return self
	 */
	public function diff( array $items ) {
		return new self( array_diff( $this->items, $items ) );
	}

	/**
	 * Execute a callback over each item.
	 *
	 * @param  callable $callback
	 *
	 * @return $this
	 */
	public function each( callable $callback ) {
		foreach ( $this->items as $key => $item ) {
			$callback( $item, $key );
		}

		return $this;
	}

	/**
	 * Get all items except for those with the specified keys.
	 *
	 * @param array|string $keys
	 *
	 * @return self
	 */
	public function except( $keys ) {

		$results = array();
		$keys    = (array) $keys;

		foreach ( $this->all() as $key => $value ) {
			if ( ! in_array( $key, $keys, true ) ) {
				$results[ $key ] = $this->get( $key );
			}
		}

		return new self( $results );
	}

	/**
	 * Run a filter over each of the items.
	 *
	 * @param callable|null $callback
	 *
	 * @return self
	 */
	public function filter( callable $callback = null ) {
		if ( null === $callback ) {
			return new self( array_filter( $this->items ) );
		}

		return new self( array_filter( $this->items, $callback, ARRAY_FILTER_USE_BOTH ) );
	}

	/**
	 * Get the first item from the collection.
	 *
	 * @return mixed
	 */
	public function first() {
		return $this->slice( 0, 1 )->shift();
	}

	/**
	 * Flip the items in the collection.
	 *
	 * @return self
	 */
	public function flip() {
		return new self( array_flip( $this->items ) );
	}

	/**
	 * Remove a one or more items from the collection by key.
	 *
	 * @param  string|array $keys
	 *
	 * @return $this
	 */
	public function forget( $keys ) {
		foreach ( (array) $keys as $key ) {
			$this->offsetUnset( $key );
		}

		return $this;
	}

	/**
	 * "Paginate" the collection by slicing it into a smaller collection.
	 *
	 * @param  int $page
	 * @param  int $perPage
	 *
	 * @return self
	 */
	public function forPage( $page, $perPage ) {
		$offset = max( 0, ( $page - 1 ) * $perPage );

		return $this->slice( $offset, $perPage );
	}

	/**
	 * Get an item from the collection by key.
	 *
	 * @param mixed $key
	 * @param mixed $default
	 *
	 * @return mixed
	 */
	public function get( $key, $default = null ) {

		$value = $default;

		if ( $this->offsetExists( $key ) ) {
			$value = $this->items[ $key ];
		}

		return $value;
	}

	/**
	 * Get an iterator for the items.
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new ArrayIterator( $this->items );
	}

	/**
	 * Group an associative array by a field.
	 *
	 * @param  callable|string $groupBy
	 *
	 * @return self
	 */
	public function groupBy( $groupBy ) {

		$results = array();
		foreach ( $this->items as $item ) {
			if ( is_array( $item ) && array_key_exists( $groupBy, $item ) ) {
				$results[ $item[ $groupBy ] ][] = $item;
			} else if ( is_object( $item ) && property_exists( $item, $groupBy ) ) {
				$results[ $item->{$groupBy} ][] = $item;
			}
		}

		return new self( $results );
	}

	/**
	 * Determine if one or more items exist in the collection by key.
	 *
	 * @param mixed $key
	 *
	 * @return bool
	 */
	public function has( $key ) {
		$keys = is_array( $key ) ? $key : func_get_args();
		foreach ( $keys as $value ) {
			if ( ! $this->offsetExists( $value ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Concatenate values of a given key as a string.
	 *
	 * @param  string $glue
	 *
	 * @return string
	 */
	public function implode( $glue = null ) {
		return implode( $glue, $this->items );
	}

	/**
	 * Index an associative array by a field.
	 *
	 * @param  callable|string $indexBy
	 *
	 * @return self
	 */
	public function indexBy( $indexBy ) {

		$results = array();
		foreach ( $this->items as $item ) {
			if ( is_array( $item ) && array_key_exists( $indexBy, $item ) ) {
				$results[ $item[ $indexBy ] ] = $item;
			} else if ( is_object( $item ) && property_exists( $item, $indexBy ) ) {
				$results[ $item->{$indexBy} ] = $item;
			}
		}

		return new self( $results );
	}

	/**
	 * Insert a value or key/value pair after a specific key in an array.  If key doesn't exist, value is appended
	 * to the end of the array.
	 *
	 * @param mixed $key
	 * @param array $value An array containing the key(s) and value(s) to be inserted.
	 *
	 * @return $this
	 */
	public function insertAfter( $key, array $value ) {

		if ( ! $this->has( $key ) ) {
			return $this->push( $value );
		}

		$index       = array_search( $key, array_keys( $this->items ), true );
		$pos         = false === $index ? count( $this->items ) : $index + 1;
		$this->items = array_merge( array_slice( $this->items, 0, $pos ), $value, array_slice( $this->items, $pos ) );

		return $this;
	}

	/**
	 * Insert a value or key/value pair before a specific key in an array.  If key doesn't exist, value is prepended
	 * to the beginning of the array.
	 *
	 * @param mixed $key
	 * @param array $value An array containing the key(s) and value(s) to be inserted.
	 *
	 * @return $this
	 */
	public function insertBefore( $key, array $value ) {

		if ( ! $this->has( $key ) ) {
			return $this->prepend( $value );
		}

		$pos         = (int) array_search( $key, array_keys( $this->items ), true );
		$this->items = array_merge( array_slice( $this->items, 0, $pos ), $value, array_slice( $this->items, $pos ) );

		return $this;
	}

	/**
	 * Intersect the collection with the given items.
	 *
	 * @param  array $items
	 *
	 * @return self
	 */
	public function intersect( array $items ) {
		return new self( array_intersect( $this->items, $items ) );
	}

	/**
	 * Intersect the collection with the given items by key.
	 *
	 * @param array $items
	 *
	 * @return self
	 */
	public function intersectByKeys( array $items ) {
		return new self( array_intersect_key( $this->items, $items ) );
	}

	/**
	 * Convert the object into something JSON serializable.
	 *
	 * @return array
	 */
	public function jsonSerialize() {
		return $this->items;
	}

	/**
	 * Get the keys of the collection items.
	 *
	 * @return array
	 */
	public function keys() {
		return array_keys( $this->items );
	}

	/**
	 * Get the last item from the collection.
	 *
	 * @return mixed
	 */
	public function last() {
		return $this->slice( - 1, 1 )->pop();
	}

	/**
	 * Run the map over each of the items.
	 *
	 * @param callable $callback
	 *
	 * @return self
	 */
	public function map( callable $callback ) {
		$keys  = array_keys( $this->items );
		$items = array_map( $callback, $this->items, $keys );

		return new self( array_combine( $keys, $items ) );
	}

	/**
	 * Merge the collection with the given items.
	 *
	 * @param  array $items
	 *
	 * @return self
	 */
	public function merge( array $items ) {
		return new self( array_merge( $this->items, $items ) );
	}

	/**
	 * Determine if an item exists at an offset.
	 *
	 * @param mixed $key
	 *
	 * @return bool
	 */
	public function offsetExists( $key ) {
		return array_key_exists( $key, $this->items );
	}

	/**
	 * Get an item at a given offset.
	 *
	 * @param mixed $key
	 *
	 * @return mixed
	 */
	public function offsetGet( $key ) {
		return $this->items[ $key ];
	}

	/**
	 * Set the item at the given offset.
	 *
	 * @param mixed $key
	 * @param mixed $value
	 */
	public function offsetSet( $key, $value ) {
		if ( null === $key ) {
			$this->items[] = $value;
		} else {
			$this->items[ $key ] = $value;
		}
	}

	/**
	 * Unset the item at a given offset.
	 *
	 * @param mixed $key
	 */
	public function offsetUnset( $key ) {
		unset( $this->items[ $key ] );
	}

	/**
	 * Get the items with the specified keys.
	 *
	 * @param mixed $keys
	 *
	 * @return self
	 */
	public function only( $keys ) {

		$results = array();
		$keys    = (array) $keys;

		foreach ( $this->all() as $key => $value ) {
			if ( in_array( $key, $keys, true ) ) {
				$results[ $key ] = $this->get( $key );
			}
		}

		return new self( $results );
	}

	/**
	 * Get the values of a given key.
	 *
	 * @param  string|array $value
	 * @param  string|null $key
	 *
	 * @return self
	 */
	public function pluck( $value, $key = null ) {
		return new self( array_column( $this->items, $value, $key ) );
	}

	/**
	 * Get and remove the last item from the collection.
	 *
	 * @return mixed
	 */
	public function pop() {
		return array_pop( $this->items );
	}

	/**
	 * Push an item onto the beginning of the collection.
	 *
	 * @param  mixed $value
	 *
	 * @return $this
	 */
	public function prepend( $value ) {
		array_unshift( $this->items, $value );

		return $this;
	}

	/**
	 * Get and remove an item from the collection.
	 *
	 * @param  mixed $key
	 * @param  mixed $default
	 *
	 * @return mixed
	 */
	public function pull( $key, $default = null ) {
		$value = $this->get( $key, $default );
		$this->offsetUnset( $key );

		return $value;
	}

	/**
	 * Push an item onto the end of the collection.
	 *
	 * @param  mixed $value
	 *
	 * @return $this
	 */
	public function push( $value ) {
		$this->offsetSet( null, $value );

		return $this;
	}

	/**
	 * Put an item in the collection by key.
	 *
	 * @param mixed $key
	 * @param mixed $value
	 *
	 * @return $this
	 */
	public function put( $key, $value ) {
		$this->offsetSet( $key, $value );

		return $this;
	}

	/**
	 * Get one or a specified number of items randomly from the collection.
	 *
	 * @param int $count
	 *
	 * @return self
	 */
	public function random( $count = 1 ) {
		$values = array();
		$keys   = (array) array_rand( $this->items, min( $count, $this->count() ) );
		foreach ( $keys as $key ) {
			$values[ $key ] = $this->offsetGet( $key );
		}

		return new self( $values );
	}

	/**
	 * Reverse items order.
	 *
	 * @return self
	 */
	public function reverse() {
		return new self( array_reverse( $this->items, true ) );
	}

	/**
	 * Search the collection for a given value and return the corresponding key if successful.
	 *
	 * @param  mixed $value
	 * @param  bool $strict
	 *
	 * @return mixed
	 */
	public function search( $value, $strict = false ) {
		return array_search( $value, $this->items, $strict );
	}

	/**
	 * Get and remove the first item from the collection.
	 *
	 * @return mixed
	 */
	public function shift() {
		return array_shift( $this->items );
	}

	/**
	 * Shuffle the items in the collection.
	 *
	 * @return self
	 */
	public function shuffle() {
		return new self( shuffle( $this->items ) );
	}

	/**
	 * Slice the underlying collection array.
	 *
	 * @param  int $offset
	 * @param  int $length
	 *
	 * @return self
	 */
	public function slice( $offset, $length = null ) {
		return new self( array_slice( $this->items, $offset, $length, true ) );
	}


	/**
	 * Sort through each item with a callback.
	 *
	 * @param  callable|null $callback
	 *
	 * @return self
	 */
	public function sort( callable $callback = null ) {
		$items = $this->items;
		$callback ? uasort( $items, $callback ) : asort( $items );

		return new self( $items );
	}

	/**
	 * Sort the collection keys.
	 *
	 * @param  int $options
	 * @param  bool $descending
	 *
	 * @return self
	 */
	public function sortKeys( $options = SORT_REGULAR, $descending = false ) {
		$items = $this->items;
		$descending ? krsort( $items, $options ) : ksort( $items, $options );

		return new self( $items );
	}

	/**
	 * Take the first or last {$limit} items.
	 *
	 * @param  int $limit
	 *
	 * @return self
	 */
	public function take( $limit ) {
		if ( $limit < 0 ) {
			return $this->slice( $limit, abs( $limit ) );
		}

		return $this->slice( 0, $limit );
	}

	/**
	 * Pass the collection to the given callback and then return it.
	 *
	 * @param  callable $callback
	 *
	 * @return $this
	 */
	public function tap( callable $callback ) {
		$callback( new self( $this->items ) );

		return $this;
	}

	/**
	 * Get the collection of items as an array.
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->all();
	}

	/**
	 * Get the collection of items as JSON.
	 *
	 * @param  int $options
	 *
	 * @return string
	 */
	public function toJson( $options = 0 ) {
		return json_encode( $this->jsonSerialize(), $options );
	}

	/**
	 * Convert the collection to its string representation.
	 *
	 * @return string
	 */
	public function toString() {
		return $this->toJson();
	}

	/**
	 * Transform each item in the collection using a callback.
	 *
	 * @param  callable $callback
	 *
	 * @return $this
	 */
	public function transform( callable $callback ) {
		$this->items = $this->map( $callback )->all();

		return $this;
	}

	/**
	 * Return only unique items from the collection array.
	 *
	 * @return self
	 */
	public function unique() {
		return new self( array_unique( $this->items ) );
	}

	/**
	 * Reset the keys on the underlying array.
	 *
	 * @return array
	 */
	public function values() {
		return array_values( $this->items );
	}

	/**
	 * Apply the callback if the value is truthy. Otherwise, call the fallback (if set).
	 *
	 * @param  bool $value
	 * @param  callable $callback
	 * @param  callable $fallback
	 *
	 * @return mixed
	 */
	public function when( $value, callable $callback, callable $fallback = null ) {
		return $value ? $callback( $this, $value ) : ( $fallback ? $fallback( $this, $value ) : $this );
	}

	/**
	 * Filter items by the given key value pair.
	 *
	 * @param  string $key
	 * @param  mixed $operator
	 * @param  mixed $value
	 *
	 * @return self
	 */
	public function where( $key, $operator, $value = null ) {

		$results = array();

		if ( func_num_args() === 2 ) {
			$value    = $operator;
			$operator = '=';
		}

		foreach ( $this->items as $index => $item ) {

			$retrieved = null;
			if ( is_array( $item ) ) {
				$retrieved = isset( $item[ $key ] ) ? $item[ $key ] : null;
			} else if ( is_object( $item ) ) {
				$retrieved = property_exists( $item, $key ) ? $item->{$key} : null;
			}

			switch ( $operator ) {
				case '=':
				case '==':
					$valid = $retrieved == $value;
					break;
				case '!=':
				case '<>':
					$valid = $retrieved != $value;
					break;
				case '<':
					$valid = $retrieved < $value;
					break;
				case '>':
					$valid = $retrieved > $value;
					break;
				case '<=':
					$valid = $retrieved <= $value;
					break;
				case '>=':
					$valid = $retrieved >= $value;
					break;
				case '===':
					$valid = $retrieved === $value;
					break;
				case '!==':
					$valid = $retrieved !== $value;
					break;
				default:
					$valid = false;
			}

			if ( $valid ) {
				$results[ $index ] = $item;
			}

		}

		return new self( $results );
	}

	/**
	 * Convert the collection to its string representation.
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}

}

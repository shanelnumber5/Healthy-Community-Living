<?php

/**
 * @file
 * Exception classes for Ubercart Addresses.
 */

/**
 * UcAddressException class.
 *
 * Base class for all uc_addresses exceptions.
 */
class UcAddressesException extends Exception { }

/**
 * UcAddressesInvalidFieldException.
 *
 * Used when trying to access an address field that doesn't exist.
 */
class UcAddressesInvalidFieldException extends UcAddressesException { }

/**
 * UcAddressesDbException.
 *
 * Used when a database error occurs.
 */
class UcAddressesDbException extends UcAddressesException { }

/**
 * UcAddressesUndefinedFunctionException.
 *
 * Used when a function from outside the class does not
 * exists. This can happen if the classes are used early
 * in the Drupal bootstrap phase.
 */
class UcAddressesUndefinedFunctionException extends UcAddressesException { }

/**
 * UcAddressesInvalidParameterException.
 *
 * Used when a method parameter is invalid.
 */
class UcAddressesInvalidParameterException extends UcAddressesException { }

/**
 * UcAddressesIncompatibleHandlerException.
 *
 * Thrown when requesting a handler with an UcAddressesSchemaAddress that only
 * supports an UcAddressesAddress.
 */
class UcAddressesIncompatibleHandlerException extends UcAddressesException { }

/**
 * UcAddressesNameCollisionException.
 *
 * Used when an address attempts to use the same name as another
 * address in the same address book.
 */
class UcAddressesNameCollisionException extends UcAddressesException { }

/**
 * UcAddressesUnownedException.
 *
 * Used when an address is not owned yet.
 */
class UcAddressesUnownedException extends UcAddressesException { }

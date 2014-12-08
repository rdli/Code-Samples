<?php
/**
* Description:	get_obj_properties extends PHP internal function get_object_vars
* Author:		Richard Li
*/

/**
*
* @param Object $obj PHP object
* @return array $props an array contains properties of the object in details
*/
function get_obj_properties($obj) {
	
	if(is_object($obj)) {
		$reflector = new ReflectionClass ( $obj );
		$properties = $reflector->getDefaultProperties ();
		$preflector = $reflector->getParentClass ();
		
		if ($preflector) {
			$pprops = $preflector->getDefaultProperties ();
			$pcsts = $preflector->getConstants ();
			$pprops = array_merge ( $pprops, $pcsts );
		}
		foreach ( $properties as $key => $value ) {
			
			$prop = $reflector->getProperty ( $key );
			
			if ($prop->isStatic ()) {
				$properties ['static: ' . $key] = $properties [$key];
				unset ( $properties [$key] );
			}
			
			if ($prop->isPrivate ()) {
				if (array_key_exists ( 'static: ' . $key, $properties )) {
					$properties ['private static: ' . $key] = $properties ['static: ' . $key];
					unset ( $properties ['static: ' . $key] );
				} else {
					$properties ['private: ' . $key] = $properties [$key];
					unset ( $properties [$key] );
				}
			}
			
			if ($prop->isProtected ()) {
				if (array_key_exists ( 'static: ' . $key, $properties )) {
					$properties ['protected static: ' . $key] = $properties ['static: ' . $key];
					unset ( $properties ['static: ' . $key] );
				} else {
					$properties ['protected: ' . $key] = $properties [$key];
					unset ( $properties [$key] );
				}
			}
			
			if ($prop->isPublic ()) {
				if (array_key_exists ( 'static: ' . $key, $properties )) {
					$properties ['public static: ' . $key] = $properties ['static: ' . $key];
					unset ( $properties ['static: ' . $key] );
				} else {
					$properties ['public: ' . $key] = $properties [$key];
					unset ( $properties [$key] );
				}
			}
		
		}
		
		$csts = $reflector->getConstants ();
		foreach ( $csts as $key => $value ) {
			$csts ['constant: ' . $key] = $csts [$key];
			unset ( $csts [$key] );
		}
		
		$props = array_merge ( $properties, $csts );
		
		foreach ( $props as $prop => $value ) {
			$key = rtrim ( substr ( strstr ( $prop, ': ' ), 2 ) );
			if (($preflector) && (array_key_exists ( $key, $pprops ))) {
				$props ['parent ' . $prop] = $props [$prop];
				unset ( $props [$prop] );
			}
		}
		
		ksort ( $props );
		return $props;
	}
}

/**
* Description:	get_obj_methods extends PHP internal function get_class_methods
* Author:		Richard Li
*/

/**
*
* @param Object $obj PHP object
* @return array $objmethods an array contains methods of the object in details
*/ 
function get_obj_methods($obj) {
	
	if(is_object($obj)) {
		$reflector = new ReflectionClass ( $obj );
		
		$preflector = $reflector->getParentClass ();
		if ($preflector) {
			$prtmethods = $preflector->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE | ReflectionMethod::IS_ABSTRACT);
			$pmethods = array ();
			foreach ( $prtmethods as $prtmethod ) {
				$pmethods [] = $prtmethod->getName ();
			}
		}
		
		$tmethods = $reflector->getMethods(ReflectionMethod::IS_STATIC | ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE | ReflectionMethod::IS_FINAL);
		$themethods = array ();
		foreach ( $tmethods as $tmethod ) {
			$themethods [] = $tmethod->getName ();
		}
		
		$objmethods = array ();
		foreach ( $themethods as $themethod ) {
			
			$refunc = new ReflectionMethod ( $obj, $themethod );
			$modifiers = implode (' ', Reflection::getModifierNames ( $refunc->getModifiers () ));
			$params = getparams ( $obj, $themethod );
			if (isset($pmethods) && in_array ( $themethod, $pmethods )) {
				if ($refunc->getDeclaringClass () == $reflector) {
					$objmethods [] = 'overridden ' . $modifiers . ': ' . $themethod . $params;
				} else {
					$objmethods [] = 'parent ' . $modifiers . ': ' . $themethod . $params;
				}
			} else {
				$objmethods [] = $modifiers . ': ' . $themethod . $params;
			}
		
		}
		sort ( $objmethods );
		return $objmethods;
	}
}

/**
*
* @param Object $obj PHP object
* @return string A tring that contains class name
*/
function get_class_info($obj) {

	if(is_object($obj)) {
		$reflector = new ReflectionClass ( $obj );
		$fname = $reflector->getFileName ();
		$sname = $reflector->getName ();
		$str = "Class name: " . $sname;
		
		if ($fname)
			return $str;
		else
			return "The class is defined in the PHP core or in a PHP extension";
	}
}

/**
*
* @param Object $obj PHP object
* @param Object $meth Name of the method
* @return string A tring that contains parameter names of the method
*/
function getparams($obj, $meth) {

	if(is_object($obj)) {
		$refunc = new ReflectionMethod ( $obj, $meth );
		$params = $refunc->getParameters ();
		$str = '(';
		
		foreach ( $params as $param ) {
			
			switch (true) {
				case ($param->isArray ()) :
					$str .= 'Array $' . $param->getName () . ', ';
					break;
				case ($param->getClass ()) :
					$str .= $param->getClass ()->getName () . ' $' . $param->getName () . ', ';
					break;
				case ($param->isPassedByReference ()) :
					$str .= '&$' . $param->getName () . ', ';
					break;
				case ($param->isOptional ()) :
					$str .= 'Optional $' . $param->getName () . ', ';
					break;
				default :
					$str .= '$' . $param->getName () . ', ';
			}
		
		}
		
		return (strlen ( $str ) > 2) ? $str = substr ( $str, 0, strlen ( $str ) - 2 ) . ')' : $str = '()';
	}
}

?>

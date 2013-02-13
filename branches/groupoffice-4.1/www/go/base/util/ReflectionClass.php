<?php

class GO_Base_Util_ReflectionClass extends ReflectionClass {

	/**
	 * Returns all methods that override a parent method.
	 * 
	 * @return array
	 */
	public function getOverriddenMethods() {

		$methods = array();

		if (!$parentClass = $this->getParentClass())
			return $methods;

		//find all public and protected methods in ParentClass
		$parentMethods = $parentClass->getMethods(
						ReflectionMethod::IS_PUBLIC ^ ReflectionMethod::IS_PROTECTED
		);
		
		//find all parentmethods that were redeclared in ChildClass
		foreach ($parentMethods as $parentMethod) {
			$declaringClass = $this->getMethod($parentMethod->getName())
							->getDeclaringClass()
							->getName();

			if ($declaringClass === $this->getName()) {
				$methods[]=$parentMethod->getName(); // print the method name
			}
		}
		
		return $methods;
	}
	
	/**
	 * Check if a method is overriding a parent method
	 * 
	 * @param string $method
	 * @return boolean
	 */
	public function methodIsOverridden($method){
		$overrides = $this->getOverriddenMethods();
		return in_array($method, $overrides);
	}

}
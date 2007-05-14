<?php

require_once 'HTMLPurifier/Doctype.php';

class HTMLPurifier_DoctypeRegistry
{
    
    /**
     * Hash of doctype names to doctype objects
     * @protected
     */
    var $doctypes;
    
    /**
     * Lookup table of aliases to real doctype names
     * @protected
     */
    var $aliases;
    
    /**
     * Registers a doctype to the registry
     * @note Accepts a fully-formed doctype object, or the
     *       parameters for constructing a doctype object
     * @param $doctype Name of doctype or literal doctype object
     * @param $modules Modules doctype will load
     * @param $modules_for_modes Modules doctype will load for certain modes
     * @param $aliases Alias names for doctype
     * @return Reference to registered doctype (usable for further editing)
     */
    function &register($doctype, $modules = array(),
        $modules_for_modes = array(), $aliases = array()
    ) {
        if (!is_array($modules)) $modules = array($modules);
        if (!is_array($aliases)) $aliases = array($aliases);
        if (!is_object($doctype)) {
            $doctype = new HTMLPurifier_Doctype(
                $doctype, $modules, $modules_for_modes, $aliases
            );
        }
        $this->doctypes[$doctype->name] =& $doctype;
        $name = $doctype->name;
        // hookup aliases
        foreach ($doctype->aliases as $alias) {
            if (isset($this->doctypes[$alias])) continue;
            $this->aliases[$alias] = $name;
        }
        // remove old aliases
        if (isset($this->aliases[$name])) unset($this->aliases[$name]);
        return $doctype;
    }
    
    /**
     * Retrieves reference to a doctype of a certain name
     * @note This function resolves aliases
     * @param $doctype Name of doctype
     * @return Reference to doctype object
     */
    function &get($doctype) {
        if (isset($this->aliases[$doctype])) $doctype = $this->aliases[$doctype];
        if (!isset($this->doctypes[$doctype])) {
            trigger_error('Doctype ' . htmlspecialchars($doctype) . ' does not exist');
            $null = null; return $null;
        }
        return $this->doctypes[$doctype];
    }
    
}

?>
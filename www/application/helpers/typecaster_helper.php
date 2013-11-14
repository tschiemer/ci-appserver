<?php
/**
  * Casts specified fields of an active record array to specified types.
  * Possibles values of type  are:
  * "boolean" (or, since PHP 4.2.0, "bool")
  * "integer" (or, since PHP 4.2.0, "int")
  * "float" (only possible since PHP 4.2.0, for older versions use the deprecated variant "double")
  * "string"
  * "array"
  * "object"
  * "null" (since PHP 4.2.0)
  * @param array $record Array from an active record
  * @param array $fieldTypes Array where key=property name, value=type to cast.
  * @return array Returns modified active record array with cast properties
*/
if ( !function_exists('cast_objfields'))
{
    function cast_objfields($record, $fieldTypes) {
        foreach($fieldTypes as $F => $type)
        {
            if(isset($record->$F))
            {
                $value = $record->$F;
                switch ($type)
                {
                    case 'boolean':
                    case 'bool':
                        $value = (bool) $value;
                        break;

                    case 'integer':
                    case 'int':
                        $value = (int) $value;
                        break;

                    case 'float':
                        $value = (float) $value;
                        break;

                    case 'string':
                        $value = (string) $value;
                        break;

                    case 'object':
                        $value = unserialize($value);
                        break;

                    default:
                        break;
                }
                $record->$F = $value;
            }
        }
        return $record;
    }
}

if ( !function_exists('cast_dbfields'))
{
    /**
     * 
     * @copyright (c) 2013, Philip Tschiemer
     * @license http://www.gnu.org/licenses/lgpl-3.0.txt LGPLv3
     * @package ci-appserver
     * @link https://github.com/tschiemer/ci-appserver 
     * @param array $record
     * @param array $fieldTypes
     * @return array
     */
    function cast_dbfields($record, $fieldTypes, $field=NULL) {
        if ($field !== NULL)
        {
            if (isset($fieldTypes[$field]))
            {
                switch($fieldTypes[$field])
                {
                    case 'struct':
                        $record = serialize($record);
                        break;
                }
            }
        }
        elseif (is_array($record))
        {
            foreach($fieldTypes as $F => $type)
            {
                if(isset($record[$F]))
                {
                    $value = $record[$F];
                    switch ($type)
                    {
                        case 'struct':
                            $value = serialize($value);
                            break;
                    }
                    $record[$F] = $value;
                }
            }
        }
        elseif (is_object($record))
        {
            foreach($fieldTypes as $F => $type)
            {
                if(isset($record->$F))
                {
                    $value = $record->$F;
                    switch ($type)
                    {
                        case 'struct':
                            $value = serialize($value);
                            break;
                    }
                    $record->$F = $value;
                }
            }
        }
        
        return $record;
    }
}


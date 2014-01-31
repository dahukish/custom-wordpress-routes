<?php namespace Helper\Html;

class Form
{
    public static function select($fieldName, array $options, $selected, array $args=array())
    {
        $args_str = "";

        if (!empty($args)) {
            $args_temp = array();
            foreach ($args as $key => $value) {
                $args_temp[] = sprintf('%s="%s"', $key, $value);
            }
            if(!empty($args_temp)) $args_str = implode(' ', $args_temp);
        }

         $html = '<select name="'.$fieldName.'" id="'.$fieldName.'" '.$args_str.' >';

         foreach ($options as $value => $text) {

             $is_selected = (strcmp($selected, $value) === 0)? 'selected="selected"': '';

             $html .= sprintf('<option %s value="%s">%s</option>', $is_selected, $value, $text);
         }

        $html .= '</select>';

        return $html;
    }
}

<?php
/**
 * field_ckeditor
 * @author Vladimir Shestakov
 * @version 1.0
 */
namespace boolive\forms\field_ckeditor;

use boolive\core\values\Rule;
use boolive\forms\field\field;

class field_ckeditor extends field
{
    function startRule()
    {
        return parent::startRule()->mix(
            Rule::arrays([
                'REQUEST' => Rule::arrays([
                    'object' => Rule::entity(['is','/vendor/boolive/basic/text'])->required()
                ])
            ])
        );
    }
}
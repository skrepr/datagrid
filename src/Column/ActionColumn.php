<?php
/*
 * This file is part of the abbert/datagrid package.
 *
 * (c) Albert Bakker <hello@abbert.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Abbert\Datagrid\Column;

/**
 * Class ActionColumn
 * @package Abbert\Datagrid\Column
 */
class ActionColumn extends AbstractColumn
{
    /**
     * @var array
     */
    protected $closure;

    public function __construct($closure)
    {
        if (!is_callable($closure)) {
            throw new \InvalidArgumentException('Parameter must be callable');
        }

        $this->closure = $closure;

        // default options
        $this->setOption('hideName', true);
    }

    /**
     * @param $row
     * @return mixed
     */
    public function format($row)
    {
        $actions = $this->closure->__invoke($row);

        $html = '<div class="btn-group pull-right">';

        foreach ($actions as $action) {

            $href = !empty($action['href']) ? $action['href'] : '#';
            $extra = !empty($action['extra']) ? $action['extra'] : null;
            $label = !empty($action['label']) ? $action['label'] : 'label missing';

            $class = 'btn ';
            if (!empty($action['class'])) {
                $class .= ' ' . $action['class'];
            } else {
                $class .= ' btn-default';
            }

            if (strstr($label, 'dg-icon')) {
                $label = '<span class="' . str_replace('dg-icon:', '', $label) . '"></span>';
            }

            if (array_key_exists('dropdown-items', $action)) {
                $class .= ' dropdown-toggle';
                $action['data-toggle'] = 'dropdown';
                $label .= ' <span class="fa fa-caret-down"></span>';
            }

            $skip = ['class', 'label', 'href', 'extra', 'dropdown-items'];

            $attributes = '';
            foreach ($action as $key => $val) {
                if (!in_array($key, $skip)) {
                    $attributes .= ' ' . $key . '="' . $val . '"';
                }
            }

            $link = "<a $extra $attributes href=\"$href\" class=\"$class\">$label</a>";

            $html .= $link;

            if (array_key_exists('dropdown-items', $action)) {
                $html .= '<ul class="dropdown-menu">';

                foreach ($action['dropdown-items'] as $item) {

                    if (isset($item['type']) && $item['type'] == 'divider') {
                        $html .= '<li class="divider"></li>';
                    } else {
                        $class = 'class="';

                        if (!empty($item['class'])) {
                            $class .= $item['class'];
                        }

                        $class .= '"';

                        $href = !empty($item['href']) ? $item['href'] : '#';
                        $link = '<a href="' . $href . '" ' . $class . ' ';

                        $skip = ['class', 'label', 'href'];

                        $attributes = '';
                        foreach ($item as $key => $val) {
                            if (!in_array($key, $skip)) {
                                $attributes .= ' ' . $key . '="' . $val . '"';
                            }
                        }

                        $link .= $attributes;
                        $link .= '>' . $item['label'] . '</a>';

                        $html .= '<li>' . $link . '</li>';
                    }
                }

                $html .= '</ul>';
            }
        }

        $html .= '</div>';

        return $html;
    }
}

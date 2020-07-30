<?php
namespace Hj\Adapter;

use Monolog\Formatter\NormalizerFormatter;

/**
 * Custom formatter copied from Monolog\Formatter\HtmlFormatter
 *
 * Class HtmlFormatterAdapter
 * @package Monolog\Formatter
 */
class HtmlFormatterAdapter extends NormalizerFormatter
{
    /**
     * @param string $th
     * @param string $td
     * @param bool $escapeTd
     * @return string
     */
    protected function addRow(string $th, string $td = ' ', bool $escapeTd = true): string
    {
        $th = htmlspecialchars($th, ENT_NOQUOTES, 'UTF-8');
        if ($escapeTd) {
            $td = '<pre>'.htmlspecialchars($td, ENT_NOQUOTES, 'UTF-8').'</pre>';
        }

        return "<tr style=\"padding: 4px;text-align: left;\">\n<th style=\"vertical-align: top;background: #ccc;color: #000\" width=\"100\">$th:</th>\n<td style=\"padding: 4px;text-align: left;vertical-align: top;background: #eee;color: #000\">".$td."</td>\n</tr>";
    }

    /**
     * Formats a log record.
     *
     * @param  array  $record A record to format
     * @return string The formatted record
     */
    public function format(array $record): string
    {
        $output = '<table cellspacing="1" width="100%" class="monolog-output">';

        $output .= $this->addRow('Message', (string) $record['message']);

        return $output.'</table>';
    }
}

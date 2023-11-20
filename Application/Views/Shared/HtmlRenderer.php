<?php

namespace Application\Views\Shared;
class HtmlRenderer
{

    /**
     * Renders form based on array input
     * This means that you can render as many input fields you want as long as you have enough memory
     * @param array $cookieNames Fill in as many cookie names that you want
     * @param array $labelText Fill inn as many labels as the amount of cookie names
     * @param array $values Fill in as many values as the amount of cookie names. Can either be an 2-dimensional array
     * with cookie as key, and with a true or false at the second index if the input is correct or not. Can also be a
     * normal array with number index.
     *
     * @return void echos the form
     */
    public static function renderFormArrayBased(array $cookieNames, array $labelText, array $values = []): void
    {
        // Return if programmer did not read the docs.
        if (!$cookieNames || count($cookieNames) !== count($labelText)) {
            return;
        }
        // Create the html form tag
        $form = '<form class="form-group" id="form" action="" method="POST">';
        $borderClass = '';
        $borderStyle = 'border-width: 3px !important; ';
        $i = 0; // This is so we can loop through an array with int indexes.
        // Loop through the cookie names and create the input fields with values if any
        foreach ($cookieNames as $cookie) {
            $value = '';
            if (!empty($values[$cookie]) && is_array($values[$cookie]) && count($values[$cookie]) >= 2) {
                $borderClass .= $values[$cookie][1] ? 'border border-success' : 'border border-danger';
                $value = $values[$cookie][0] ?? ''; // Set value to empty string if not set
            } else if (isset($values[$i])) { // If array doesn't have int indexes, we want to use the values as normal
                $value = $values[$i];
            }

            $form .= <<<EOT
                <label for="$cookie">{$labelText[$cookie]}</label>
                <input type="text" style="$borderStyle" class="form-control $borderClass" name="$cookie" id="$cookie" value="$value">
            EOT;
            $i++;
        }
        // Concat the rest of the form and input, so we can submit our info.
        $form .= <<<EOT
                <br>
                <input id="pointer" type="submit" value="Submit">
            </form>
        EOT;

        echo $form; // Finally we can just echo it, as returning would allocate more memory, and it will be used right away.
    }
}

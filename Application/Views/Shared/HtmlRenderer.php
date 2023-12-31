<?php

namespace Application\Views\Shared;
class HtmlRenderer
{

    /**
     * This function does way too much and should be split up, as I sometimes forget the optional 2d array
     *
     * Renders form based on array input
     * This means that you can render as many input fields you want as long as you have enough memory
     * @param array $inputNames Fill in as many input names that you want
     * @param array $labelText Fill inn as many labels as the amount of input names
     * @param array $values Fill in as many values as the amount of input names. Can either be an 2-dimensional array
     * with inputName as key, and with a true or false at the second index if the input is correct or not. Can also be a
     * normal array with number index. But then there is no support for checking if the input is correct or not.
     *
     * @return void echos the form
     */
    public static function renderFormArrayBased(array $inputNames, array $labelText, array $values = []): void
    {
        // Return if programmer did not read the docs.
        if (!$inputNames || count($inputNames) !== count($labelText)) {
            return;
        }
        // Create the html form tag
        $form = '<form class="form-group" id="form" action="" method="POST">';
        $borderClass = '';
        $borderStyle = 'border-width: 3px !important; ';
        $i = 0; // This is so we can loop through an array with int indexes.
        // Loop through the array and create the input fields with values if any
        foreach ($inputNames as $inputName) {
            $value = '';

            if (!empty($values[$inputName]) && is_array($values[$inputName]) && count($values[$inputName]) >= 2) {
                $borderClass .= $values[$inputName][1] ? 'border border-success' : 'border border-danger';
                $value = $values[$inputName][0] ?? ''; // Set value to empty string if not set
            } else if (isset($values[$i])) { // If array doesn't have int indexes, we want to use the values as normal
                $value = $values[$i];
            }

            // Set the type to text, unless it is a password, then set it to password.
            $type = 'text';
            if ($inputName === 'password') {
                $type = 'password';
            }

            $form .= <<<EOT
                <label for="$inputName">$labelText[$inputName]</label>
                <input type="$type" style="$borderStyle" class="form-control $borderClass" name="$inputName" id="$inputName" value="$value">
                EOT;
            $i++; // Need the increment if associative array is not used. Again, overcomplicated.
        }
        // Concat the rest of the form and input, so we can submit our info.
        $form .= <<<EOT
                <br>
                <input id="pointer" type="submit" value="Submit">
            </form>
            EOT;
        // Finally we can just echo it, as returning would allocate more memory.
        echo $form;
    }


    /**
     * Generate a response with a message and either green or red background.
     *
     * @param mixed $message The message to display. Can be string or array.
     * @param bool $status True for green, false for red color
     * @param int $timeAliveInMilliSeconds The time in milliseconds before the response disappears (will be doubled)
     *
     * @return void echos the response
     */
    public static function generateResponse(mixed $message, bool $status, int $timeAliveInMilliSeconds = 1200): void
    {
        $alertStatus = $status ? 'alert-success' : 'alert-danger';
        if (is_array($message)) {
            $message = implode('<br>', $message); // Implode the array with <br> as "glue".
        }
        // Simple html with javascript to display the message for a short time. Script deletes itself after it has run.
        // Ajax would be better used on the frontend here. And the code could just stay uploaded for the user at all
        // times instead of doing this. This would also save some bandwidth.
        echo <<<HTML
            <div id="messageBox" class="position-fixed start-50 translate-middle-x" style="z-index: 9999; width: 90%; left: 5%; top: 509.5px;">
                <div class="alert $alertStatus text-center p-3" role="alert"">
                    $message
                </div>
            </div>  
            <script id="messageScript">
                setTimeout(function() {
                    let element = document.getElementById('messageBox');
                    element.style.transition = "opacity 1s ease-in-out";
                    element.style.opacity = 0;
                
                    setTimeout(function() {
                        element.parentNode.removeChild(element);
                        let scriptElement = document.getElementById('messageScript');
                        scriptElement.parentNode.removeChild(scriptElement);
                    }, $timeAliveInMilliSeconds)
                }, $timeAliveInMilliSeconds)
            </script>
        HTML;
    }
}

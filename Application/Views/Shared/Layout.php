<?php
namespace Application\Views\Shared;
class Layout
{
    public static function displayTop(): void
    {
        echo <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <!-- Include Bootstrap CSS -->
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" type="text/css" href="../styles.css">
            <title> </title>
        </head>
        <body>
        <div class="container d-flex justify-content-center">
            <div class="flex-column">
                <div class="d-flex justify-content-center fixed-top">
                    <nav id="navbar" class="d-flex navbar navbar-expand-lg">
                    </nav>
                </div>
                <div id="messageContainer" class="d-flex flex-column align-items-center">
                </div>
                <div id="" class="flex-column justify-content-center text-center container">
                    <h2>
                        <?php //echo sharedFunctionsm5::generateParentFolderName(); ?>
                    </h2>
        HTML;
    }
    public static function displayBottom(): void{
        echo <<<HTML
                </div>
            </div>
        </div>
        </body>
        </html>
        HTML;
    }
}

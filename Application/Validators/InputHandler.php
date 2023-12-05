<?php

declare(strict_types=1); // Type safety

namespace Application\Validators;

if (__FILE__ == $_SERVER['SCRIPT_FILENAME']) {
    header('Location: ../index.php');
    exit;
}
session_start();
/**
 * InputHandler Class
 * This class provides functionalities to sanitize and validate input data based on dynamically configured rules.
 */
class InputHandler
{
    private array $inputConfig = [];  // Configuration storage for input processing


    /**
     * Constructor: Initializes configuration storage with the given array or an empty array.
     *
     * @param array $config Initial input processing configuration
     */
    public function __construct(array $config = [])
    {
        $this->inputConfig = $config;
    }


    /**
     * Adds a new configuration for input processing.
     *
     * We store both sanitization and validation methods without wrapping. This keeps
     * configuration separate from actual data processing.
     *
     * @param string $key Key identifier for the input
     * @param callable $validationMethod Method/function to validate the input
     * @param callable|null $sanitizeMethod Method/function to sanitize the input
     */
    public function addConfig(string $key, callable $validationMethod, callable $sanitizeMethod = null): void
    {
        $this->inputConfig[$key] = [
            'sanitize' => $sanitizeMethod,
            'validate' => $validationMethod
        ];
    }


    /**
     * Processes and validates the provided inputs based on the existing configuration.
     *
     * This method loops through the input configuration, sanitizes the input using the
     * provided sanitize method, and then validates it using the provided validate method.
     *
     * @param array $postData Raw input data
     * @return array Processed data and validation messages
     */
    public function processInputs(array $postData): array
    {
        $processedData = []; // Storage for processed data
        $notValidResponseMessage = []; // Storage for validation messages

        foreach ($this->inputConfig as $inputKey => $config) {
            // Sanitize using the stored method from configuration
            $value = $this->sanitizeInput($postData[$inputKey], $config['sanitize']);


            $isValid = true; // Default validity assumption

            // Check for the required input
            if (empty($value)) {
                $notValidResponseMessage[] = "$inputKey is required.";
                $isValid = false;
            } // Validate using the stored method from configuration, if provided
            elseif (isset($config['validate']) && !call_user_func_array(
                    $config['validate'],
                    [&$value, &$notValidResponseMessage])) {
                $isValid = false;
            }

            $processedData[$inputKey] = [$value, $isValid]; // Store results
        }

        return [$processedData, $notValidResponseMessage]; // Return processed data and validation messages
    }


    /**
     * Sanitizes a given value with an additional method if provided.
     *
     * @param string $value Raw input value
     * @param callable|null $additionalSanitization Additional sanitization method
     * @return string Sanitized value
     */
    private function sanitizeInput(string $value, callable $additionalSanitization = null): string
    {

        // Apply the additional sanitization, if provided
        if ($additionalSanitization) {
            $value = call_user_func($additionalSanitization, $value);
        }

        return $value;
    }
}

